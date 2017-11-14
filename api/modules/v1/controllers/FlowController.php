<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/19/16
 * Time: 3:47 PM
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\Goods;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\models\EventRule;
use common\helper\DateTimeHelper;

class FlowController extends BaseAuthActiveController
{
    public $modelClass = 'api\modules\v1\models\Goods';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['get-gifts', 'get-active-events'],
                'rules' => [
                    [
                        'actions' => ['get-active-events'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['get-gifts'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-gifts' => ['post'],
                    'get-active-events' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * 检查当前是否有生效的活动,获取赠品列表
     * 每次实时获取 还是 读取缓存
     * $goods_list [ ['goods_id' => ['goods_num' => 1, 'goods_price' => 1.23]], ]
     */
    public function actionGetGifts()
    {
        //  接口必要参数验证
        $goods_info_list = Yii::$app->request->post('data');
        if (!$goods_info_list) {
            die(json_encode([
                'code' => 1,
                'msg' => '没有选中商品'
            ]));
        } else {
            foreach ($goods_info_list as $item) {
                if (!isset($item['goods_id']) || !isset($item['goods_num']) || !isset($item['goods_price'])) {
                    die(json_encode([
                        'code' => 2,
                        'msg' => '商品信息不完整'
                    ]));
                }
            }
        }
        $params = Yii::$app->params;
        $cache_file_name = $params['caches_base_dir'].$params['cache_file_name']['active_events'];
        $events_json = file_get_contents($cache_file_name);
        $active_events = json_decode($events_json, true);

        $gifts = [];
        foreach ($active_events as $event) {
            if (!$gifts) {
                $gifts = $this->checkEvent($event, $goods_info_list);
            } else {
                $_gifts = $this->checkEvent($event, $goods_info_list);
                //  如果已存在有效的赠品，则加入新的赠品时，检查该赠品是否已存在，
                //  如果存在，则累加到赠品数组中;如果不存在，则合并到数组中
                if ($_gifts) {
                    if (isset($gifts[$_gifts['goods_id']])) {
                        $gifts[$_gifts['goods_id']]['goods_num'] += $_gifts['goods_num'];
                    } else {
                        $gifts = array_merge($gifts, $_gifts);
                    }
                }
            }
        }
        die(json_encode([
            'code' => 0,
            'data' => $gifts,
            'msg' => ''
        ]));
    }

    /**
     * 当前有效的活动
     */
    public function actionGetActiveEvents()
    {
        $now = DateTimeHelper::getFormatGMTTimesTimestamp(time());
        $active_events = Event::find()->where([
                ['is_active' => Event::IS_ACTIVE],
                ['>', 'start_time', $now],
                ['<', 'end_time', $now]
            ])->orderBy([
                'event_id' => SORT_DESC
            ])->asArray()
            ->all();

        $params = Yii::$app->params;
        $cache_file_name = $params['caches_base_dir'].$params['cache_file_name']['active_events'];
        if ($active_events) {
            foreach ($active_events as &$event) {

                $event_rule = EventRule::find()->select([
                    'rule_name', 'match', 'gift_id', 'gift_num', 'gift_show_peice', 'gift_need_pay'
                ])->where(['rule_id' => $event['rule_id']])
                    ->asArray()
                    ->one();
                //  如果赠品已下架、已删除或剩余数量不满足赠送的最小数量，则活动无效
                if ($event_rule) {
                    $goods = Goods::find()->select('goods_id')
                        ->where([
                            'goods_id' => $event_rule->gift_id,
                            'is_on_sale' => Goods::IS_ON_SALE,
                            'is_delete' => Goods::IS_NOT_DELETE
                        ])->andWhere([
                            '>=', 'goods_number', $event_rule->gift_num
                        ])->one();
                    if ($goods) {
                        $event['event_rule'] = $event_rule;
                    }
                }
                if (!isset($event['event_rule'])) {
                    unset($event);
                    continue;
                }

                $pkg = GoodsPkg::findOne(['pkg_id' => $event['pkg_id']]);
                //  优先判断 allow_goods_list，如果allow_goods_list为空，才验证 deny_goods_list,
                //  如果deny_goods_list， 也为空，则表示所有商品都参与活动
                if (isset($pkg['allow_goods_list']) && $pkg['allow_goods_list']) {
                    $goods_id_list = explode(',', $pkg['allow_goods_list']);
                    $active_goods = Goods::find()->select('goods_id')
                        ->where([
                            'goods_id' => $goods_id_list,
                            'is_on_sale' => Goods::IS_ON_SALE,
                            'is_delete' => Goods::IS_NOT_DELETE,
                        ])->andWhere([
                            '>', 'goods_number', 0
                        ])->asArray()
                        ->all();
                    if ($active_goods) {
                        $event['allow_goods_list'] = array_column($active_goods, 'goods_id');
                    } else {
                        unset($event);
                        continue;
                    }
                } elseif (isset($pkg['deny_goods_list']) && $pkg['deny_goods_list']) {
                    $event['deny_goods_list'] = explode(',', $pkg['deny_goods_list']);
                } else {
                    unset($event);
                    continue;
                }
            }

            file_put_contents($cache_file_name, json_encode($active_events));
        } else {
            echo '当前没有有效的活动';
            file_put_contents($cache_file_name, '');
            return;
        }
    }

    /**
     * 匹配策略, 获取赠品列表
     */
    private function checkEvent($event_rule, $goods_info_list)
    {
        //  单件商品满X件赠送B商品Y件
        if ($match['effect'] == 'each' && $match['type'] == 'goods_num') {
            foreach ($goods_info_list as $goods_id => $goods) {
                if ($goods['goods_num'] >= $match['value'] &&
                    isset($event['allow_goods_list']) && $event['allow_goods_list'] &&
                    in_array($goods_id, $event['allow_goods_list'])
                ) {
                    $gift[$event['gift_id']] = [
                        'goods_id' => $event['gift_id'],
                        'goods_num' => floor($goods['goods_num'] / $match['value']) * $event['gift_num']
                    ];
                } elseif ($goods['goods_num'] >= $match['value'] &&
                    isset($event['deny_goods_list']) && $event['deny_goods_list'] &&
                    !in_array($goods_id, $event['deny_goods_list'])
                ) {
                    $gift[$event['gift_id']] = [
                        'goods_id' => $event['gift_id'],
                        'goods_num' => floor($goods['goods_num'] / $match['value']) * $event['gift_num']
                    ];
                }
            }
        }
        //  多件商品总件数满X件赠送B商品Y件
        elseif ($match['effect'] == 'all' && $match['type'] == 'goods_num') {
            $goods_num = 0;
            foreach ($goods_info_list as $goods_id => $goods) {
                if (isset($event['allow_goods_list']) && $event['allow_goods_list'] &&
                    in_array($goods_id, $event['allow_goods_list'])
                ) {
                    $goods_num += $goods['goods_num'];
                } elseif (isset($event['deny_goods_list']) && $event['deny_goods_list'] &&
                    !in_array($goods_id, $event['deny_goods_list'])
                ) {
                    $goods_num += $goods['goods_num'];
                }
            }

            if ($goods_num >= $match['value']) {
                $gift[$event['gift_id']] = [
                    'goods_id' => $event['gift_id'],
                    'goods_num' => floor($goods_num / $match['value']) * $event['gift_num']
                ];
            }
        }
        //  单件商品总价满X元赠送B商品Y件
        elseif ($match['effect'] == 'each' && $match['type'] == 'goods_amount') {
            foreach ($goods_info_list as $goods_id => $goods) {
                $goods_amount = $goods['goods_num'] * $goods['goods_num'];
                if ($goods_amount >= $match['value'] &&
                    isset($event['allow_goods_list']) && $event['allow_goods_list'] &&
                    in_array($goods_id, $event['allow_goods_list'])
                ) {
                    $gift[$event['gift_id']] = [
                        'goods_id' => $event['gift_id'],
                        'goods_num' => floor($goods_amount / $match['value']) * $event['gift_num']
                    ];
                } elseif ($goods['goods_num'] >= $match['value'] &&
                    isset($event['deny_goods_list']) && $event['deny_goods_list'] &&
                    !in_array($goods_id, $event['deny_goods_list'])
                ) {
                    $gift[$event['gift_id']] = [
                        'goods_id' => $event['gift_id'],
                        'goods_num' => floor($goods_amount / $match['value']) * $event['gift_num']
                    ];
                }
            }
        }
        //  多件商品总价满X元赠送B商品Y件
        elseif ($match['effect'] == 'all' && $match['type'] == 'goods_amount') {
            $goods_amount = 0;
            foreach ($goods_info_list as $goods_id => $goods) {
                if (isset($event['allow_goods_list']) && $event['allow_goods_list'] &&
                    in_array($goods_id, $event['allow_goods_list'])
                ) {
                    $goods_amount += $goods['goods_num'] * $goods['goods_price'];
                } elseif (isset($event['deny_goods_list']) && $event['deny_goods_list'] &&
                    !in_array($goods_id, $event['deny_goods_list'])
                ) {
                    $goods_amount += $goods['goods_num'] * $goods['goods_price'];
                }
            }

            if ($goods_amount >= $match['value']) {
                $gift[$event['gift_id']] = [
                    'goods_id' => $event['gift_id'],
                    'goods_num' => floor($goods_amount / $match['value']) * $event['gift_num']
                ];
            }
        }

        return $gift;
    }
}

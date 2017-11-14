<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Brand;
use api\modules\v1\models\GoodsActivity;
use api\modules\v1\models\Shipping;
use api\modules\v1\models\Users;
use common\helper\GoodsHelper;
use common\helper\ImageHelper;
use common\helper\OrderGroupHelper;
use common\models\GiftPkg;
use \Yii;
use api\modules\v1\models\Goods;
use api\modules\v1\models\Tags;
use api\modules\v1\models\EventToGoods;
use api\modules\v1\models\Event;
use backend\models\GoodsCat;
use backend\models\Category;
use common\models\GoodsAttr;
use common\helper\CacheHelper;
use common\helper\UrlHelper;
use common\helper\DateTimeHelper;
use common\models\GoodsTag;
use yii\caching\Cache;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\rest\Serializer;
use yii\web\BadRequestHttpException;

/**
 * Site controller
 */
class GoodsController extends BaseActiveController
{
    public $modelClass = 'api\modules\v1\models\Goods';

    /**
     * 获取筛选结果的可筛选项目
     *
     * 【1】基础参数处理
     * 【2】拼接公用SQL join、where
     * 【3】遍历可筛选项目，有选定值的不需要显示可筛选项目，获取当前没有选定值的项目 可筛选项的值和对应数量
     * 【4】商品的可筛选项目按商品数量的多少逆序排序
     *
     * $params = [
     *      'cat_id' => integer|[integer],        一级分类ID
     *      'sub_cat_id' => [integer],  二级分类ID  —— app 还在用， pc端废弃
     *      'brand_id' => integer,      品牌ID
     *      'tag' => string,            商品标 in_array($tag, array_keys(Tags::$tag_name_map))
     *      'region' => string,         地区
     *      'effect' => string,         功效
     *      'keywords' => string,       关键词，匹配商品名称和商品关键词
     *      'start_num_max' => integer  起售数量最大值（即 起订量小于指定值）
     *      'recommend_type' => string  推荐类型 in_array($recommend_type, ['general', 'integral_exchange'])
     *      'notBuyByBox' => int        是否散批（反之为是否按箱发货） in_array($buy_by_box, ['0， 1])
     *      'is_shipping' => int        是否包邮 in_array($is_shipping, ['0， 1])
     * ]
     */
    public function actionSelect_item()
    {
        //  【1】基础参数处理
        $params = [
            'region' => '',         //  区域
            'sub_cat_id' => [],     //  子分类
            'brand_id' => [],       //  品牌
            'effect' => '',         //  功效
//            'notBuyByBox' => '',     //  是否散批
//            'is_shipping' => '',    //  是否包邮
        ];
        if (Yii::$app->request->isPost) {
            $request_params = Yii::$app->request->post();
        }
        elseif (Yii::$app->request->isGet) {
            $request_params = Yii::$app->request->get();
        }

        if ($request_params && is_array($request_params)) {
            $params = array_merge($params, $request_params);
        }

        $select_item = [];
        $tag_map = Tags::$tag_name_map;
        $freeShippingId = Shipping::getFreeShippingId();

        //  【2】拼接公用SQL join、where
        //  商品属性表不同维度（region、effect）组合筛选，把每个维度都当成一个临时表去join
        $from_join_sql = ' FROM `o_goods` '.
            ' LEFT JOIN `o_goods_tag` ON `o_goods`.`goods_id` = `o_goods_tag`.`goods_id` '.
            ' LEFT JOIN `o_tags` ON `o_goods_tag`.`tag_id` = `o_tags`.`id` '.
            ' LEFT JOIN `o_volume_price` ON `o_goods`.`goods_id` = `o_volume_price`.`goods_id` '.
            ' LEFT JOIN `o_category` ON `o_goods`.`cat_id` = `o_category`.`cat_id` '.
            ' LEFT JOIN `o_brand` ON `o_goods`.`brand_id` = `o_brand`.`brand_id` '.
            ' LEFT JOIN `o_moq` ON `o_goods`.`goods_id` = `o_moq`.`goods_id` '.
            ' LEFT JOIN `o_goods_attr` AS A ON (`o_goods`.`goods_id` = A.`goods_id` AND A.attr_id = 165) '.
            ' LEFT JOIN `o_goods_attr` AS B ON (`o_goods`.`goods_id` = B.`goods_id` AND B.attr_id = 211) ';
        $where_sql = ' WHERE `o_goods`.`is_on_sale`=1 AND `o_goods`.`is_delete`=0 ';

        //  1 cat_id    有一级分类并且有二级分类ID时只考虑二级分类，
        $sub_cat_id_str = '';
        if (!empty($params['sub_cat_id'])) {
            $sub_cat_id_str = $params['sub_cat_id'];
        } elseif (!empty($params['cat_id'])) {
            /*
            $categories = Category::find()->select(['cat_id'])
                ->where(['parent_id' => $params['cat_id']])
                ->asArray()
                ->all();
            $params['sub_cat_id'] = array_column($categories, 'cat_id');
            */

            $leaves = [];
            if (!is_array($params['cat_id'])) {
                $params['cat_id'] = explode(',', $params['cat_id']);;
            }
            foreach ($params['cat_id'] as $actId) {
                CacheHelper::getCategoryLeavesByCatId($actId, $leaves);
            }

            Yii::info(' 获取分类 $params.cat_id '.VarDumper::export($params['cat_id']).
                ' 的所有叶子节点 $leaves = '. VarDumper::export($leaves), __METHOD__);
            if (!empty($leaves)) {
                $params['sub_cat_id'] = array_column($leaves, 'cat_id');
            } else {
                $params['sub_cat_id'] = [];
            }

            $sub_cat_id_str = implode(',', $params['sub_cat_id']);
        }
        if ($sub_cat_id_str) {
            $where_sql .= ' AND `o_goods`.`cat_id` IN ('.$sub_cat_id_str.') ';
        }

        //  2 brand_id
        if (!empty($params['brand_id'])) {
            $where_sql .= ' AND `o_goods`.`brand_id` IN ('.$params['brand_id'].') ';
        }
        //  3 tag
        if (!empty($params['tag'])) {
            $tag = $tag_map[$params['tag']];
            $where_sql .= ' AND `o_tags`.`id` = '.$tag;
        } else {
            $where_sql .= ' AND `o_tags`.`enabled` = 1 ';
        }
        //  4 region
        if (!empty($params['region'])) {
            $where_sql .= " AND A.attr_value = '".$params['region']."'";
        }
        //  5 effect
        if (!empty($params['effect'])) {
            $where_sql .= " AND B.attr_value = '".$params['effect']."'";
        }
        //  6 keywords
        if (!empty($params['keywords'])) {
            $where_sql .= ' AND ( '.
                ' `o_goods`.`goods_name` LIKE "%'.$params['keywords'].'%" '.
                ' OR '.
                ' `o_goods`.`keywords` LIKE "%'.$params['keywords'].'%") ';
        }
        //  7 start_num 起订数量最大限制
        if (isset($params['start_num_max']) && intval($params['start_num_max'])) {
            $where_sql .= ' AND `o_goods`.`start_num` > '.intval($params['start_num_max']);
        }
        //  8 推荐(is_hot) 区分普通商品和积分商品
        if (isset($params['recommend_type']) && in_array($params['recommend_type'], ['general', 'integral_exchange'])) {
            $where_sql .= ' AND `o_goods`.`extension_code` = '.$params['recommend_type'].' AND  `o_goods`.`is_hot` = 1 ';
        }
        //  9 is_shipping
//        if (!empty($params['is_shipping'])) {
//            $where_sql .= ' AND ( '.
//                ' `o_goods`.`shipping_id` = '.$freeShippingId.
//                ' OR '.
//                ' (`o_goods`.`shipping_id` = 0 AND `o_brand`.`shipping_id` = '.$freeShippingId.') '
//            .')';
//        }

        //  是否按箱发货（反之为是否散批）
        if (!empty($params['notBuyByBox'])) {
            $where_sql .= ' AND ( `o_goods`.`buy_by_box` != '.$params['notBuyByBox'].' )';
        }

        //  【3】遍历可筛选项目，有选定值的不需要显示可筛选项目，获取当前没有选定值的项目 可筛选项的值和对应数量
        foreach ($params as $key => $value) {
            if ($value) {
                continue;
            }

            switch ($key) {
                case 'brand_id':
                    $group_by_sql = ' SELECT '.
                        ' DISTINCT `o_brand`.`brand_id`, `o_brand`.`brand_name`, COUNT(DISTINCT `o_goods`.`goods_id`) AS cnt '.
                        $from_join_sql.
                        $where_sql.' AND `o_brand`.`is_show` = 1 '.
                        ' GROUP BY brand_id '.
                        ' ORDER BY `o_brand`.`sort_order` DESC ';

                    $rs = Yii::$app->db->createCommand($group_by_sql)->queryAll();
                    if ($rs && is_array($rs)) {
                        foreach ($rs as $item) {
                            if ($item['cnt'] > 0 && !empty($item['brand_name'])) {
                                $select_item['brand_id'][] = [
                                    'brand_id' => (int)$item['brand_id'],
                                    'brand_name' => $item['brand_name'],
                                    'cnt' => (int)$item['cnt'],
                                ];
                            }
                        }
                    }
                    break;
                case 'sub_cat_id':
                    $group_by_sql = ' SELECT '.
                        ' DISTINCT `o_goods`.`goods_id`, `o_goods`.`cat_id`, `o_category`.`cat_name`, '.
                        ' `o_category`.`parent_id`, CP.cat_name AS parent_name '.
                        $from_join_sql.
                        ' LEFT JOIN `o_category` AS CP ON `o_category`.`parent_id` = CP.cat_id '.
                        $where_sql.' AND `o_category`.`is_show` = 1 ';

                    $rs = Yii::$app->db->createCommand($group_by_sql)->queryAll();
                    //  获取所有符合条件的商品放入所属品牌的数组中
                    if ($rs && is_array($rs)) {
                        foreach ($rs as $item) {
                            //  只记录有值的可筛选项目
                            if ($item['cat_name']) {
                                $select_item['sub_cat_id'][$item['cat_id']] = [
                                    'cat_id' => (int)$item['cat_id'],
                                    'cat_name' => $item['cat_name'],
                                    'parent_id' => (int)$item['parent_id'],
                                    'parent_name' => $item['parent_name'],
                                ];
                            }

                            $goods_list[$item['cat_id']][] = $item['goods_id'];
                        }

                        if ($goods_list && is_array($goods_list)) {
                            //  获取所有的符合条件的品牌和商品
                            foreach ($goods_list as $cat_id => &$item) {
                                $item = array_unique($item);
                            }

                            $goods_list_total = array_unique(array_column($rs, 'goods_id'));
                            $cat_id_total = array_column($select_item['sub_cat_id'], 'cat_id');
                            $goods_cat_result = GoodsCat::find()->where([
                                    'goods_id' => $goods_list_total,
                                    'cat_id' => $cat_id_total
                                ])->asArray()
                                ->all();
                        }

                        if ($goods_cat_result && is_array($goods_cat_result)) {
                            foreach ($goods_cat_result as $value) {
                                $goods_list[$value['cat_id']][] = $value['goods_id'];
                            }
                        }

                        if ($select_item['sub_cat_id'] && is_array($select_item['sub_cat_id'])) {
                            foreach ($select_item['sub_cat_id'] as &$sub_cat_item) {
                                //  只记录有值的可筛选项目
                                if ($sub_cat_item['cat_id']) {
                                    $goods_list[$sub_cat_item['cat_id']] = array_unique($goods_list[$sub_cat_item['cat_id']]);
                                    $sub_cat_item['cnt'] = count($goods_list[$sub_cat_item['cat_id']]);
                                }
                            }
                        }
                    }
                    break;
                case 'tag':
                    $group_by_sql = ' SELECT '.
                        ' DISTINCT `o_tags`.`id`, `o_tags`.`name`, COUNT(DISTINCT `o_goods`.`goods_id`) AS cnt '.
                        $from_join_sql.
                        $where_sql.
                        ' GROUP BY id';

                    $rs = Yii::$app->db->createCommand($group_by_sql)->queryAll();
                    if ($rs && is_array($rs)) {
                        foreach ($rs as $item) {
                            if ($item['cnt'] > 0 && !empty($item['name'])) {
                                $change_tag_map = array_flip($tag_map);
                                //  只记录有值的可筛选项目
                                if ($item['name']) {
                                    $select_item['tag'][] = [
                                        'tag_id' => (int)$item['id'],
                                        'tag_alias' => $change_tag_map[$item['id']],
                                        'tag_name' => $item['name'],
                                        'cnt' => (int)$item['cnt'],
                                    ];
                                }
                            }
                        }
                    }
                    break;
                case 'region':
                    $group_by_sql = ' SELECT A.attr_value, COUNT(DISTINCT `o_goods`.`goods_id`) AS cnt '.
                        $from_join_sql.
                        $where_sql.' AND A.attr_id = 165 '.
                        ' GROUP BY A.attr_value ';

                    $rs = Yii::$app->db->createCommand($group_by_sql)->queryAll();
                    if ($rs && is_array($rs)) {
                        foreach ($rs as $item) {
                            //  只记录有值的可筛选项目
                            if ($item['attr_value']) {
                                $select_item['region'][] = [
                                    'region_name' => $item['attr_value'],
                                    'cnt' => (int)$item['cnt'],
                                ];
                            }
                        }
                    }

                    break;
                case 'effect':
                    $group_by_sql = ' SELECT B.attr_value, COUNT(DISTINCT `o_goods`.`goods_id`) AS cnt '.
                        $from_join_sql.
                        $where_sql.' AND B.attr_id = 211 '.
                        ' GROUP BY B.attr_value ';
                    $rs = Yii::$app->db->createCommand($group_by_sql)->queryAll();
                    if ($rs && is_array($rs)) {
                        foreach ($rs as $item) {
                            //  只记录有值的可筛选项目
                            if ($item['attr_value']) {
                                $select_item['effect'][] = [
                                    'effect_name' => $item['attr_value'],
                                    'cnt' => (int)$item['cnt'],
                                ];
                            }
                        }
                    }

                    break;
//                case 'is_shipping':
//                    $group_by_sql = ' SELECT COUNT(DISTINCT `o_goods`.`goods_id`) AS cnt '.
//                        $from_join_sql.
//                        $where_sql.' AND ( '.
//                        ' `o_goods`.`shipping_id` = '.$freeShippingId.
//                        ' OR '.
//                        ' (`o_goods`.`shipping_id` = 0 AND `o_brand`.`shipping_id` = '.$freeShippingId.') '
//                        .')';
//                    $show_sql = $group_by_sql;
//                    $rs = Yii::$app->db->createCommand($group_by_sql)->queryOne();
//                    if ($rs && $rs['cnt'] > 0) {
//                        //  是否包邮也作为一个商品标签
//                        $select_item['is_shipping'][] = [
//                            'tag_id' => 0,  //  随意填写，保证出现在其他标签的后面即可
//                            'tag_alias' => 'is_shipping',
//                            'tag_name' => '包邮',
//                            'cnt' => (int)$rs['cnt'],
//                        ];
//                    }
//                    break;

                case 'notBuyByBox':
                    //  只显示散批的 选择项，不按箱购买 buy_by_box = 0
                    $group_by_sql = ' SELECT COUNT(DISTINCT `o_goods`.`goods_id`) AS cnt '.
                        $from_join_sql.$where_sql.' AND ( `o_goods`.`buy_by_box` = 0)';
//                    $show_sql = $group_by_sql;
                    $rs = Yii::$app->db->createCommand($group_by_sql)->queryOne();

                    if ($rs && $rs['cnt'] > 0) {
                        //  是否包邮也作为一个商品标签
                        $select_item['notBuyByBox'][] = [
                            'tag_id' => 0,  //  随意填写，保证出现在其他标签的后面即可
                            'tag_alias' => 'notBuyByBox',
                            'tag_name' => '散批',
                            'cnt' => (int)$rs['cnt'],
                        ];
                    }
                    break;

                default :
                    //  不显示的项目不做处理
                    break;
            }
        }

        //  【4】商品的可筛选项目按商品数量的多少逆序排序
        foreach ($select_item as &$item) {
            $item = array_filter($item);
            usort($item, function ($a, $b){
                if ($a['cnt'] == $b['cnt']) {
                    return 0;
                } else {
                    return $a['cnt'] > $b['cnt'] ? -1 : 1;
                }
            });
        }

        return $select_item;
//        return $show_sql;
    }

    /**
     * 获取筛选的商品
     * 注意，url里面不要出现sort关键字，会引发框架本身的sort机制，导致order无法正确获取
     *
     * 【1】基础参数处理    discount 通过token获取
     * 【2】获取结果集的DataProvider
     * 【3】获取结果集 和 分页信息
     * 【4】格式化结果集
     * $params = [
     *      'from_type' = str,  来源 in_array($from_type, ['pc', 'wechat', 'app'])
     *      'goods_id_list' => []   指定的商品列表，如果指定商品列表，则不做排序，在上层逻辑做排序
     *      'discount' => 1,    会员等级折扣
     *      'page' => 1,        默认显示第一页
     *      'size' => 30,       默认每页30条数据
     *      'brand_id' => [],   选中的品牌
     *      'sub_cat_id' => [], 选中的子分类
     *      'tag' => '',        选中的标签   in_array($tag, ['new', 'supply', 'gift', 'mixup', 'star', 'group'])
     *      'region' => '',     选中的区域
     *      'effect' => '',     选中的功效
     *      'keywords' => '',   筛选的关键词
     *      'extension_code' => string,   商品类型 ['general', 'integral_exchange']
     * ];
     *
     * return [
     *
     * ]
     */
    public function actionList()
    {
        if (!isset(Yii::$app->user->identity)) {
            $token = Yii::$app->request->getAuthUser();
            if (!empty($token)) {
                $userModel = Users::findIdentityByAccessToken($token);
                Yii::$app->user->login($userModel);
            }
        }
        else {
            $userModel = Yii::$app->user->identity;
        }

        //  【1】基础参数处理
        $params = [
//            'from_type' => 'pc',  //  for test
            'goods_id_list' => '',
            'page' => 1,
            'brand_id' => [],
            'cat_id' => '',
            'sub_cat_id' => [],
            'tag' => '',
            'region' => '',
            'effect' => '',
            'keywords' => '',
            'goods_sn' => '',
            'goods_name_length' => 0,//  商品名称显示长度
            'extension_code' => 'general',//  商品类型  区分普通商品和积分兑换商品
            'biz_type' => Goods::BIZ_TYPE_XMCP,
//            'notBuyByBox' => 0,  //  是否散批
            //            'is_shipping' => 0,//  是否包邮
            //  'random' => false,  //  是否随机  未实现
        ];

        if (Yii::$app->request->isPost) {
            $request_params = Yii::$app->request->post();
        } elseif (Yii::$app->request->isGet) {
            $request_params = Yii::$app->request->get();
        }
        if ($request_params && is_array($request_params)) {
            $params = array_merge($params, $request_params);
        }

        if (!isset($params['from_type']) || !in_array($params['from_type'], Yii::$app->params['support_platform'])) {
            Yii::error('缺少参数：from_type '. VarDumper::export($request_params), __METHOD__);
            throw new BadRequestHttpException('缺少参数：from_type', 1);
        }

        $user_rank_map = CacheHelper::getUserRankCache();
        if (isset($userModel) && isset($user_rank_map[$userModel->user_rank]['discount'])) {
            $params['user_rank_discount'] = $user_rank_map[$userModel->user_rank]['discount'] / 100;
        }
        else {
            $params['user_rank_discount'] = 1;
        }

        Yii::info('params = '. VarDumper::export($params), __METHOD__);

        if (!empty($params['page_size'])) {
            $page_size = $params['page_size'];
        } else {
            $page_size = 30;
        }

        $gTb = Goods::tableName();
        //  【2】获取结果集的DataProvider
        if (!empty($params['goods_id_list'])) {
            $query = $this->getSelectQuery($params)
                ->distinct($gTb.'.goods_id');
        } elseif (!empty($params['order_by_for_ar'])) {
            $query = $this->getSelectQuery($params)
                ->distinct($gTb.'.goods_id')
                ->orderBy($params['order_by_for_ar']);
        } else {
            $query = $this->getSelectQuery($params)
                ->distinct($gTb.'.goods_id')
                ->orderBy([
                    $gTb.'.sort_order' => SORT_DESC,
                    $gTb.'.complex_order' => SORT_DESC
                ]);
        }

        //  【3】获取结果集 和 分页信息  此处有坑：page = 1 获取的是第二页的内容
        if (isset($params['limit']) && $params['limit'] < $page_size) {
            $page_size = $params['limit'];
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $page_size,
                'page' => $params['page'] - 1,
            ]
        ]);

        $serializer = new Serializer();
        $serializer->collectionEnvelope = 'items';
        $result = $serializer->serialize($provider);

        $sql =$query->createCommand()->getRawSql();
        Yii::info(__METHOD__.' SQL: '.$sql);

        //  【4】格式化结果集
        $selected_list = $result['items'];
        $links = UrlHelper::formatLinks($result['_links']);

        return [
            'items' => $selected_list,
            '_links' => $links,
            '_meta' => $result['_meta'],
            'sql' => $sql,    //  测试
        ];
    }

    private function getListQuery() {
        $userDiscount = 1.0;
        if (!isset(Yii::$app->user->identity)) {
            $token = Yii::$app->request->getAuthUser();
            if (!empty($token)) {
                $userModel = Users::findIdentityByAccessToken($token);
                Yii::$app->user->login($userModel);
            }
        }
        else {
            $userModel = Yii::$app->user->identity;
        }

        if (!empty($userModel)) {
            $user_rank_map = CacheHelper::getUserRankCache();
            if (!empty($user_rank_map[$userModel->user_rank]['discount'])) {
                $userDiscount = $user_rank_map[$userModel->user_rank]['discount'] / 100.0;
            }
        }

        $data = Yii::$app->request->post('data');

        //参数处理 -- start
        if (!empty($data['keywords'])) {
            $keywords = $data['keywords'];
            if (!is_string($keywords)) {
                $keywords = null;
            }
        }

        //选品指南type
        if (!empty($data['guideTypeId'])) {
            $guideTypeId = $data['guideTypeId'];
            if (!is_numeric($guideTypeId)) {
                $guideTypeId = null;
            }
        }

        //标签
        if (!empty($data['tags'])) {
            $tags = $data['tags'];
            if (!is_array($tags)) {
                $tags = null;
            }
        }

        //价格区间
        if (!empty($data['startPrice'])) {
            $startPrice = floatval($data['startPrice']);
            if ($startPrice < 0.01) {
                $startPrice = null;
            }
        }

        if (!empty($data['endPrice'])) {
            $endPrice = floatval($data['endPrice']);
            if ($endPrice < 0.01) {
                $endPrice = null;
            }
        }

        //品牌所在国家
        $countryList = null;
        if (!empty($data['countryList'])) {
            $countryList = $data['countryList'];
            if (!is_array($countryList)) {
                $countryList = null;
            }
        }

        //产地
        $attrRegionList = null;
        if (!empty($data['attrRegionList'])) {
            $attrRegionList = $data['attrRegionList'];
            if (!is_array($attrRegionList)) {
                $attrRegionList = null;
            }
        }

        //区域
        $brandAreaList = null;
        if (!empty($data['brandAreaList'])) {
            $brandAreaList = $data['brandAreaList'];
            if (!is_array($brandAreaList)) {
                $brandAreaList = null;
            }
        }

        //功效
        $attrEffectList = null;
        if (!empty($data['attrEffectList'])) {
            $attrEffectList = $data['attrEffectList'];
            if (!is_array($attrEffectList)) {
                $attrEffectList = null;
            }
        }

        //品牌
        $brandIdList = null;
        if (!empty($data['brandIdsList'])) {
            $brandIdList = $data['brandIdsList'];
            if (!is_array($brandIdList)) {
                $brandIdList = null;
            }
        }


        //分类
        $catIdList = null;
        if (!empty($data['catIdList'])) {
            $catIdList = $data['catIdList'];
            if (!is_array($catIdList)) {
                $catIdList = null;
            }
        }

        //  活动id    根据活动获取商品
        $eventId = !empty($data['event_id']) ? $data['event_id'] : null;

        //排序
        $order = [];
        if (!empty($data['order'])) {
            $order = $data['order'];
            if (!is_array($order)) {
                $order = [];
            }
        }
        //参数处理 -- end

        Yii::warning('userDiscount = '. $userDiscount. ', countryList = '. VarDumper::export($countryList). ', brandIdList = '. VarDumper::export($brandIdList). ', catIdList = '. VarDumper::export($catIdList). ', eventId = '.$eventId.' order = '. VarDumper::export($order), __METHOD__);

        $query = Goods::find();
        $query->select([
            Goods::tableName(). '.*',
            //  修正价格要考虑商品是否使用全局折扣
            ' IF(discount_disable = 1, min_price, (min_price * '.$userDiscount.')) AS discountPrice',
            ' IF(discount_disable = 1, (min_price / market_price), (min_price * '.$userDiscount.'/ market_price)) AS discount',
        ])->joinWith([
            'brand brand',
            'category category',
            'goodsCat goodsCat',
            'moqs moqs',
            'goodsGallery goodsGallery',
            'volumePrice volumePrice',
            'goodsTag goodsTag',
            'tags tags',
            'groupBuy groupBuy',
            'guideGoods guideGoods',
            'goodsAttrRegion goodsAttrRegion',
            'goodsAttrEffect goodsAttrEffect',
        ])->where([
            'is_on_sale' => 1,
            'is_delete' => 0,
            'extension_code' => 'general',
        ])->andWhere([
            'not', ['prefix' => 'XY'],
        ])->orderBy(ArrayHelper::merge(
            $order,
            [
                Goods::tableName().'.sort_order' => SORT_DESC,
                Goods::tableName().'.complex_order' => SORT_DESC,
                Goods::tableName().'.goods_id' => SORT_DESC,
            ]
        ))->groupBy(Goods::tableName(). '.goods_id');

        //  优惠活动
        if (!empty($eventId)) {
            $event = Event::find()
                ->joinWith([
                    'eventToGoods',
                    'eventToBrand'
                ])->where([Event::tableName().'.event_id' => $eventId])
                ->one();

            //  effective_scope_type活动的生效范围['all', 'zhifa', 'brand', 'goods']
            if (!empty($event)) {
                switch ($event->effective_scope_type) {
                    case 'all':
                        break;
                    case 'zhifa':
                        $query->andWhere([Goods::tableName().'.supplier_user_id' => 1257]);
                        break;
                    case 'brand':
                        if (!empty($event->eventToBrand)) {
                            $brandIdList = ArrayHelper::getColumn($event->eventToBrand, 'brand_id');
                            $query->andWhere([Goods::tableName().'.brand_id' => $brandIdList]);
                        }
                        break;
                    case 'goods':
                    default :
                        //  默认为 指定商品
                        if (!empty($event->eventToGoods)) {
                            $goodsIdList = ArrayHelper::getColumn($event->eventToGoods, 'goods_id');
                            $query->andWhere([Goods::tableName().'.goods_id' => $goodsIdList]);
                        }
                        break;
                }
            }

            //  过滤团采、秒杀商品
            $groupBuyGoodsActMap = GoodsActivity::aliveActivityGoodsActMap();
            if (!empty($groupBuyGoodsActMap)) {
                $query->andWhere(['not in', Goods::tableName().'.goods_id', array_keys($groupBuyGoodsActMap)]);
            }
        }

        //筛选 -- start
        if (!empty($guideTypeId)) {
            $query->andWhere([
                'guideGoods.type' => $guideTypeId,
            ]);
        }

        //标签
        if (!empty($tags)) {
            $query->andWhere([
                'goodsTag.tag_id' => $tags,
            ]);
        }

        //价格筛选
        if (!empty($startPrice) && !empty($endPrice)) {
            $query->having([
                'between',
                'discountPrice',
                $startPrice,
                $endPrice,
            ]);
        }
        elseif (!empty($startPrice)) {
            $query->having([
                '>=',
                'discountPrice',
                $startPrice,
            ]);
        }
        elseif (!empty($endPrice)) {
            $query->having([
                '<=',
                'discountPrice',
                $endPrice,
            ]);
        }

        //品牌所在国家筛选
        if (!empty($countryList)) {
            $query->andWhere([
                'brand.country' => $countryList,
            ]);
        }

        //品牌所在区域
        if (!empty($brandAreaList)) {
            $query->andWhere([
                'brand.brand_area' => $brandAreaList,
            ]);
        }

        //产地筛选
        if (!empty($attrRegionList)) {
            $query->andWhere([
                'goodsAttrRegion.attr_value' => $attrRegionList,
            ]);
        }

        //功效筛选
        if (!empty($attrEffectList)) {
            $query->andWhere([
                'goodsAttrEffect.attr_value' => $attrEffectList,
            ]);
        }

        //品牌筛选
        if (!empty($brandIdList)) {
            $query->andWhere([
                Goods::tableName().'.brand_id' => $brandIdList,
            ]);
        }

        //品类筛选，要考虑扩展分类
        if (!empty($catIdList)) {
            $query->andWhere([
                'or',
                [
                    Goods::tableName(). '.cat_id' => $catIdList,
                ],
                [
                    'goodsCat.cat_id' => $catIdList,
                ]
            ]);
        }

        //keywords
        if (!empty($keywords)) {
            $query->andWhere([
                'OR',
                ['like', Goods::tableName().'.goods_name', $keywords],
                ['like', Goods::tableName().'.keywords', $keywords],
                ['like', 'category.cat_name', $keywords],
                ['like', 'category.keywords', $keywords],
            ]);
        }

        return $query;
    }

    public function actionQuery_count() {
        $query = $this->getListQuery();
        return [
            'count' => $query->count(),
        ];
    }

    public function actionList_v2() {

        $query = $this->getListQuery();

        $data = Yii::$app->request->post('data');

        //页码和一页的数量
        $page = null;
        if (!empty($data['page'])) {
            $page = intval($data['page']);
        }

        if (!empty($data['size'])) {
            $size = intval($data['size']);
        }
        if (empty($size)) {
            $size = 10;
        }

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $size,
                'page' => $page ?: 0,
            ]
        ]);

        Yii::warning('page = '. $page. ', size = '. $size, __METHOD__);

        return $provider;
    }

    /**
     * GET goods/view 获取商品详情
     *
     * 【1】参数处理
     *
     * $params = [
     *      'goods_id' => integer
     * ]
     */
    public function actionView()
    {
        //  【1】参数处理
        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
        } elseif (Yii::$app->request->isGet) {
            $params = Yii::$app->request->get();
        }

        if (!isset($params['goods_id']) || $params['goods_id'] < 1) {
            Yii::error('缺少必要参数：goods_id '. VarDumper::export($params), __METHOD__);
            throw new BadRequestHttpException('缺少必要参数：goods_id', 1);
        }
        $goods_id = trim($params['goods_id']);


        //  【2】获取数据 商品信息、相册、价格段
        $goods = Goods::find()
            ->joinWith('volumePrice')
            ->joinWith('brand')
            ->joinWith('goodsAttr')
            ->joinWith('goodsGallery')
            ->where(['o_goods.goods_id' => $goods_id])
            ->one();

        if ($goods) {
            return $goods;
        } else {
            throw new BadRequestHttpException('您要访问的商品不存在', 2);
        }
    }

    /**
     * apk 商品详情接口
     * 参数 get goods_id
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionView_v2()
    {
        if (!isset(Yii::$app->user->identity)) {
            $token = Yii::$app->request->getAuthUser();
            if (!empty($token)) {
                $userModel = Users::findIdentityByAccessToken($token);
                Yii::$app->user->login($userModel);
            }
        }
        $userId = Yii::$app->user->getId() ?: 0;
        //默认折扣
        $userDiscount = 1.0;
        if (!empty($userModel)) {
            $user_rank_map = CacheHelper::getUserRankCache();
            if (!empty($user_rank_map[$userModel->user_rank]['discount'])) {
                $userDiscount = $user_rank_map[$userModel->user_rank]['discount'] / 100.0;
            }
        }

        $goodsId = Yii::$app->request->get('goods_id');
        if (!$goodsId || $goodsId < 1) {
            Yii::error('缺少必要参数：goods_id ' . $goodsId, __METHOD__);
            throw new BadRequestHttpException('缺少必要参数：goods_id', 1);
        }

        $now = date('Y-m-d H:i:s');
        $goods = \common\models\Goods::find()->joinWith([
            'volumePrice',
            'brand',
            'goodsAttr',
            'goodsGallery',
            'brand.event',  //  品牌绑定的活动 用于优惠券 派券领券
            'brand.event.fullCutRule brandGetCouponRules',  //  品牌绑定的活动 用于优惠券 派券领券
            'brand.eventList brandEventList' => function ($eventQuery) use ($now) {
                $eventQuery->andOnCondition(['brandEventList.is_active' => Event::IS_ACTIVE])
                    ->andOnCondition([
                        'and',
                        ['<', 'brandEventList.start_time', $now],
                        ['>', 'brandEventList.end_time', $now]
                    ]);
            },
            'eventList goodsEventList' => function ($eventQuery) use ($now) {
                $eventQuery->andOnCondition(['goodsEventList.is_active' => Event::IS_ACTIVE])
                    ->andOnCondition([
                        'and',
                        ['<', 'goodsEventList.start_time', $now],
                        ['>', 'goodsEventList.end_time', $now]
                    ]);
            },
            'shipping',
            'giftPkgList giftPkgList' => function ($giftPkgListQuery) {
                $giftPkgListQuery->andOnCondition(['giftPkgList.is_on_sale' => GiftPkg::IS_ON_SALE]);
            },
        ])
            ->where(['o_goods.goods_id' => $goodsId])
            ->one();

        //格式化商品
        $goods = Goods::formatGoodsInfoForBuy($goods, 'general', $userId, 0);

        //格式化处理活动信息
        $assignEvent = Goods::assignEvent($goods);
        $event = [];
        //满减
        if (!empty($assignEvent['fullCutEvent'])) {
            $fullCutEvent = $assignEvent['fullCutEvent'];
            $fullCutRuleNameList = '';
            if (!empty($fullCutEvent) && $fullCutEvent['event_type'] == Event::EVENT_TYPE_FULL_CUT) {
                $fullCutRuleNameList = ArrayHelper::getColumn($fullCutEvent['fullCutRule'], 'rule_name');
            }
            $fullCutRuleNameMsg = implode($fullCutRuleNameList, '|');
            $event['full_cut_event'] = [
                'event_name' => $fullCutEvent['event_name'],
                'event_id' => $fullCutEvent['event_id'],
                'event_desc' => $fullCutEvent['event_desc'],
                'bgcolor' => $fullCutEvent['bgcolor'],
                'sub_type' => $fullCutEvent['sub_type'],
                'rule_name' => $fullCutRuleNameMsg,
            ];
        }
        //满赠
        if (!empty($assignEvent['giftEventList'])) {
            foreach ($assignEvent['giftEventList'] as $gift) {
                $giftList = [];
                if (!empty($gift['giftList'])) {
                    foreach ($gift['giftList'] as $giftItem) {
                        $giftList[] = [
                            'goods_id' => $giftItem['goods_id'],
                            'goods_name' => $giftItem['goods_name'],
                            'goods_thumb' => $giftItem['goods_thumb'],
                            'market_price' => $giftItem['market_price'],
                            'goods_price' => $giftItem['goods_price'],
                            'expire_date' => $giftItem['expire_date'],
                        ];
                    }
                }
                $giftEvent = $gift['event'];
                $event['gift_event'][] = [
                    'event' => [
                        'event_id' => $giftEvent['event_id'],
                        'event_name' => $giftEvent['event_name'],
                        'event_desc' => $giftEvent['event_desc'],
                        'sub_type' => $giftEvent['sub_type'],
                    ],
                    'gift_list' => $giftList,
                ];
            }
        }
        //物料
        if (!empty($assignEvent['wuliaoEventList'])) {
            foreach ($assignEvent['wuliaoEventList'] as $wuliaoEvent) {
                $wuliaoArr = [];
                if (!empty($wuliaoEvent['wuliaoList'])) {
                    foreach ($wuliaoEvent['wuliaoList'] as $wuliaoItem) {
                        $wuliaoArr[] = [
                            'goods_id' => $wuliaoItem['goods_id'],
                            'goods_name' => $wuliaoItem['goods_name'],
                            'goods_thumb' => $wuliaoItem['goods_thumb'],
                            'market_price' => $wuliaoItem['market_price'],
                            'goods_price' => $wuliaoItem['goods_price'],
                            'expire_date' => $wuliaoItem['expire_date'],
                        ];
                    }
                }
                $event['wu_liao_event'] = [
                    'event' => [
                        'event_id' => $wuliaoEvent['event']['event_id'],
                        'event_name' => $wuliaoEvent['event']['event_name'],
                        'event_desc' => $wuliaoEvent['event']['event_desc'],
                        'sub_type' => $wuliaoEvent['event']['sub_type'],
                    ],
                    'wu_liao_list' => $wuliaoArr,
                ];
            }
        }
        //套餐
        $giftPkgs = [];
        if (!empty($assignEvent['giftPkgList'])) {
            foreach ($assignEvent['giftPkgList'] as $pkg) {
                $item = [
                    'id' => $pkg['id'],
                    'name' => $pkg['name'],
                    'img' => $pkg->getUploadUrl('thumb_img'),
                    'price' => \common\helper\NumberHelper::price_format($pkg['price']),
                ];
                $giftPkgs[] = $item;
            }
        }
        $event['gift_pkgs'] = $giftPkgs;

        $goodsAttrFormat = Goods::assignGoodsAttr($goods->goodsAttr);
        //  修正商品的发货地和服务方
        $depotAreaAndService = Goods::resetDepotAreaAndService(
            $goodsId,
            $goods->supplier_user_id,
            $goods->brand['brand_depot_area']
        );

        $goodsInfo = [
            'original_img' => ImageHelper::get_image_path($goods->original_img),
            'country_icon' => GoodsHelper::getCountryIcon($goods->brand->country),
//            'country_icon' => GoodsHelper::getCountryIcon($goods->extInfo->countryIcon),
            'goods_name' => $goods->goods_name,
            'goods_brief' => $goods->goods_brief,
//            'min_price' => $goods->min_price * $userDiscount,
            'min_price' => $goods->min_price,
            'market_price' => $goods->market_price,
            'start_num' => $goods->start_num,
            'measure_unit' => $goods->measure_unit,
            'buy_by_box' => Goods::$buy_by_box_map[$goods->buy_by_box],
            'number_per_box' => $goods->number_per_box,

            'goods_sn' => $goods->goods_sn,
            'product_area' => $goodsAttrFormat['product_area'],
            'certificate' => $goods->certificate,
            'qty' => $goods->qty,
            'send_by' => $depotAreaAndService['sendBy'],
            'brand_depot_area' => $depotAreaAndService['brandDepotArea'],
            'shelf_life' => $goods->shelf_life,
            'expire_date' => $goods->expire_date,

            'goods_desc' => $goods->goods_desc,
        ];

        //商品详情
        list($catInfo, $points) = explode('|', $goods->brand->short_brand_desc);
        $brandInfo = [
            'logo' => $goods->brand->brand_logo_two,
            'brand_id' => $goods->brand->brand_id,
            'brand_name' => $goods->brand->brand_name,
            'brand_desc' => $goods->brand->brand_desc,
            'cat_info' => $catInfo,
            'brand_goods_count' => Brand::find()
                ->joinWith('brandGoodsList')
                ->where([
                    Brand::tableName() . '.brand_id' => $goods->brand_id,
                    Goods::tableName() . '.is_on_sale' => 1
                ])->count(),
            'points' => $points,
        ];

        //spu
        $spuList = [];
        $skuSizeStr = '';
        if (!empty($goods->spu_id) && !empty($goods->sku_size)) {
            $spuGoodsList = $goods->getSpuGoodsList($userId, Goods::IS_ON_SALE, ['general']);
            if (count($spuGoodsList) > 20) {
                $spuGoodsList = array_slice($spuGoodsList, 0, 20);
            }
            foreach ($spuGoodsList as $sku) {
                $skuSize = !empty($sku->sku_size) ? $sku->sku_size : $sku->goods_name;
                if (empty($skuSizeStr)) {
                    $skuSizeStr = $skuSize;
                } else {
                    $skuSizeStr .= '; ' . $skuSize;
                }

                $reminder = 0;      //  不考虑到货通知
                if (!empty($sku->arrivalReminder)) {
                    $reminder = 1;  //  已设置 到货通知
                } elseif ($sku->start_num > $sku->goods_number) {
                    $reminder = 2;  //  提示用户 可设置到货通知
                }
                $spuList[] = [
                    'goods_thumb' => ImageHelper::get_image_path($sku->goods_thumb),
                    'goods_id' => $sku->goods_id,
                    'sku_size' => $skuSize,
                    'goods_name' => $sku->goods_name,
                    'goods_sn' => $sku->goods_sn,
                    'min_price' => $sku->min_price * $userDiscount,
                    'goods_number' => $sku->goods_number,
                    'start_num' => $sku->start_num,
                    'measure_unit' => $sku->measure_unit,
                    'buy_by_box' => Goods::$buy_by_box_map[$sku->buy_by_box],
                    'number_per_box' => $sku->number_per_box,
                    'reminder' => $reminder,
                ];
            }
        }

        //  获取用户默认地址 的 省份
        $defaultAddress = Users::getUserDefaultAddress($userId);
        $address = [
            1,
            $defaultAddress['provinceId'],
            $defaultAddress['cityId'],
            $defaultAddress['districtId'],
        ];
        $rs = Brand::getShippingDesc($goods->brand_id, $goods->supplier_user_id, $address);
        if ($rs['code'] == 0) {
            $shippingInfo = $rs['data']['shippingDesc'];
        } else {
            $shippingInfo = $rs['msg'];
        }


        return [
            'goods_info' => $goodsInfo,
            'event' => $event,
            'address' => [
                'default_address' => $defaultAddress['provinceName'],
                'shipping_info' => $shippingInfo,
            ],
            'brand_info' => $brandInfo,
            'spu_goods_list' => $spuList,
        ];

    }

    /**
     * POST brand-goods 获取品牌对应的商品列表
     *
     * $params['data'] = [
     *      'brand_id' => int   【必填】品牌ID
     *      'extension_code' => string   【必填】商品类型   ['general', 'integral_exchange']
     *      'limit' => int      分页参数
     *      'offset' => int      分页参数
     * ]
     * return array
     */
    //增加参数 params['data']['orderBy'] 数组 字段 => 顺/逆序  对品牌下的商品进行排序、综合排序还是按照本来的额逻辑
    //综合排序传的参数为 sort_order => SORT_DESC
    //2017.08.08
    public function actionBrand_goods()
    {
        $params = [];
        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->post('data');
        }
        elseif (Yii::$app->request->isGet) {
            $params = Yii::$app->request->get('data');
        }

        $token = Yii::$app->request->getAuthUser();
        if (!empty($token)) {
            $user = Users::findIdentityByAccessToken($token);
            if (!empty($user)) {
                Yii::$app->user->login($user);
            } else {
                Yii::warning('token无法找到用户', __METHOD__);
            }
        }

        if (empty($params['brand_id'])) {
            Yii::error('参数错误，缺少品牌id', __METHOD__);
            throw new BadRequestHttpException('缺少品牌id', 1);
        }

        if (empty($params['extension_code'])) {
            $params['extension_code'] = 'general';
        }

        $brandId = $params['brand_id'];
        $limit = empty($params['limit']) ? 300 : $params['limit'];
        $offset = empty($params['offset']) ? 0 : $params['offset'];

        $user_rank_map = CacheHelper::getUserRankCache();
        if (isset($user) && isset($user_rank_map[$user->user_rank]['discount'])) {
            $params['user_rank_discount'] = $user_rank_map[$user->user_rank]['discount'] / 100;
        }
        else {
            $params['user_rank_discount'] = 1;
        }

        $query = Goods::find()->joinWith('tags')
            ->joinWith('volumePrice')
            ->joinWith('moqs')
            ->joinWith('category')
            ->with([
                'goodsCat',
            ])
            ->where([
                'brand_id' => $brandId,
                'is_on_sale' => Goods::IS_ON_SALE,
                'is_delete' => Goods::IS_NOT_DELETE,
                'extension_code' => $params['extension_code'],
            ])->andWhere([
                '>', 'market_price', 0
            ])->distinct(Goods::tableName().'.goods_id');

//        $query->orderBy([
//            Goods::tableName().'.sort_order' => SORT_DESC,
//            Goods::tableName().'.complex_order' => SORT_DESC
//        ]);
        //非默认排序则进行
        if(!isset($params['orderBy']['sort_order'])) {
            $query = $query->orderBy($params['orderBy']);
        }

        $brand_goods= $query->limit($limit)
            ->offset($offset)
            ->all();

        /**
         * 原本的排序规则修改为默认排序
         *
         */
        if(isset($params['orderBy']['sort_order'])) {
            Yii::warning(' 排序前 $brand_goods = ' . VarDumper::export($brand_goods), __METHOD__);
            //  品牌详情页面的商品 有标签的往前排 按商品标签的sort_order逆序排,如果标签order一致，按商品大列表顺序
            usort($brand_goods, function ($a, $b) {
                //  标签的权重相同 或 都为空时，判定商品的排序权重
                if (
                    (
                        !empty($a->getShowTagArray($a->tags)['show_tag_sort'])
                        && !empty($b->getShowTagArray($b->tags)['show_tag_sort'])
                        && ($a->getShowTagArray($a->tags)['show_tag_sort'] == $b->getShowTagArray($b->tags)['show_tag_sort'])
                    ) || (
                        empty($a->getShowTagArray($a->tags)['show_tag_sort'])
                        && empty($b->getShowTagArray($b->tags)['show_tag_sort'])
                    )
                ) {
                    if ($a['sort_order'] == $b['sort_order']) {
                        $compare[] = [$a['complex_order'], $b['complex_order']];
                        if ($a['complex_order'] == $b['complex_order']) {
                            return 0;
                        } else {
                            return $a['complex_order'] > $b['complex_order'] ? -1 : 1;
                        }
                    } else {
                        return $a['sort_order'] > $b['sort_order'] ? -1 : 1;
                    }
                } //  标签的权重不相同
                elseif (
                    !empty($a->getShowTagArray($a->tags)['show_tag_sort'])
                    && !empty($b->getShowTagArray($b->tags)['show_tag_sort'])
                    && ($a->getShowTagArray($a->tags)['show_tag_sort'] != $b->getShowTagArray($b->tags)['show_tag_sort'])
                ) {
                    return $a->getShowTagArray($a->tags)['show_tag_sort'] > $b->getShowTagArray($b->tags)['show_tag_sort'] ? -1 : 1;
                } //  只有一个有标签
                else {
                    if (!empty($a->getShowTagArray($a->tags)['show_tag_sort'])) {
                        return -1;
                    } elseif (!empty($b->getShowTagArray($b->tags)['show_tag_sort'])) {
                        return 1;
                    }
                }
            });
            Yii::warning(' 排序后 $brand_goods = ' . VarDumper::export($brand_goods), __METHOD__);
        }
//        return $compare;
        return $brand_goods;
    }



    /**
     * 判断当前商品是否有当前时段有效的活动
     *
     * $data = ['goods_id_list' => 一组商品id]
     *
     * return array 返回当前参与有效活动的商品id
     */
    public function actionIs_in_event()
    {
        $data = Yii::$app->request->post();

        if (!isset($data['goods_id_list']) || !$data['goods_id_list']) {
            Yii::error('无效参数1 '. VarDumper::export($data), __METHOD__);
            throw new BadRequestHttpException('无效参数', 1);
        }

        $goods_id_arr = [];
        $time = (isset($data['time']) && $data['time'])
            ? $data['time']
            : DateTimeHelper::getFormatGMTTimesTimestamp(time());
        $is_active = (isset($data['is_active']) && $data['is_active'])
            ? $data['is_active']
            : true;
        if ($event_list = EventToGoods::getEventByGoods($data['goods_id_list'], $time, $is_active)) {

            if (!isset($data['goods_id_list']) || !$data['goods_id_list']) {
                Yii::error('无效参数2 '. VarDumper::export($event_list), __METHOD__);
                throw new BadRequestHttpException('无效参数', 2);
            }

            foreach ($event_list as $event_id => $goods_id_list) {
                if ($goods_id_arr) {
                    $goods_id_arr = array_merge($goods_id_arr, $goods_id_list);
                } else {
                    $goods_id_arr = $goods_id_list;
                }
            }
            $goods_id_arr = array_unique($goods_id_arr);
            return [
                'code' => 0,
                'msg' => '满赠',
                'data' => $goods_id_arr
            ];
        } else {
            return [
                'code' => 0,
                'msg' => '',    //  当前没有参与活动
                'data' => []
            ];
        }
    }


    /**
     * 判断当前商品是否有当前时段有效的活动(eventToGoods 只获取一个有效的活动)
     *
     * $data ['goods_id' => 一个商品的ID]
     *
     * return array
     */
    public function actionEvent()
    {
        $data = Yii::$app->request->post();
        Yii::warning(' 入参 $data = '.json_encode($data), __METHOD__);
        if (!isset($data['goods_id']) || !$data['goods_id']) {
            Yii::error('无效参数');
            throw new BadRequestHttpException('无效参数', 1);
        }
        
        $goods = Goods::find()->where(['goods_id' => $data['goods_id']])->one();
        Yii::warning(' 商品基本信息 $goods = '.VarDumper::export($goods), __METHOD__);
        //  未上架、已删除、库存少于起售数量的商品 为无效商品
        if (!$goods->is_on_sale || $goods->is_delete) {
            Yii::error('该商品已下架');
            throw new BadRequestHttpException('该商品已下架', 2);
        } elseif ($goods->goods_number < $goods->start_num) {
            Yii::error('该商品库存不足');
            throw new BadRequestHttpException('该商品库存不足', 3);
        }

        //  默认当前时间，订单获取
        $time = (isset($data['time']) && $data['time'])
            ? $data['time']
            : date('Y-m-d H:i:s', time());
        $is_active = (isset($data['is_active']) && $data['is_active'])
            ? $data['is_active']
            : true;
        //  获取当前商品对应的有效活动
        $event_list = EventToGoods::getEventByGoods($data['goods_id'], $time, $is_active);
        Yii::warning(' 获取当前商品对应的有效活动 $event_list = '.VarDumper::export($event_list));

        if (!empty($event_list)) {
            $event_detail = Event::getEventDetail($event_list);
            Yii::warning(' 获取活动详情 $event_detail = '.json_encode($event_detail));
            return $event_detail;
        } else {
            return [];
        }
    }

    /**
     * 判断当前商品是否有当前时段有效的活动
     *
     * $data [
     *      'goods_id' => 一个sku的ID,
     *      'goods_number' => sku 当前的数量,
     * ]
     *
     * return array = [
     *      'fullCutEventList'      => [],    //  满减
     *      'fullGiftEventList'     => [],    //  满赠
     *      'couponEventList'       => [],    //  优惠券
     *      'signCouponEventList'   => [],    //  领券
     *      'giftPkgEventList'      => [],    //  礼包
     * ]
     */
    public function actionEvent_v2()
    {
        if (empty(Yii::$app->user->identity)) {
            $token = Yii::$app->request->getAuthUser();
            if (!empty($token)) {
                $userModel = Users::findIdentityByAccessToken($token);
                Yii::$app->user->login($userModel);
            }
        }

        //【1】入参
        $data = Yii::$app->request->post();
        Yii::warning(' 入参 $data = '.json_encode($data), __METHOD__);
        if (!isset($data['goods_id']) || empty($data['goods_id'])) {
            Yii::error('无效参数');
            throw new BadRequestHttpException('无效参数'.json_encode($data), 1);
        }

        //【2】获取商品 、商品的品牌信息
        $dateTime = date('Y-m-d H:i:s', time());
        $goods = Goods::find()
            ->joinWith([
                'brand',
                'brand.event',  //  品牌绑定的活动 用于优惠券 派券领券
                'brand.event.fullCutRule',  //  品牌绑定的活动 用于优惠券 派券领券
                'brand.eventList brandEventList' => function($brandEventListQuery) use ($dateTime) {
                    $brandEventListQuery->andOnCondition([
                            'brandEventList.is_active' => Event::IS_ACTIVE
                        ])->andOnCondition([
                            'and',
                            ['<', 'brandEventList.start_time', $dateTime],
                            ['>', 'brandEventList.end_time', $dateTime]
                        ]);
                },
                'eventList goodsEventList' => function($goodsEventListQuery) use ($dateTime) {
                    $goodsEventListQuery->andOnCondition([
                            'goodsEventList.is_active' => Event::IS_ACTIVE
                        ])->andOnCondition([
                            'and',
                            ['<', 'goodsEventList.start_time', $dateTime],
                            ['>', 'goodsEventList.end_time', $dateTime]
                        ]);
                },
                'giftPkgList giftPkgList' => function($giftPkgListQuery){
                    $giftPkgListQuery->andOnCondition(['giftPkgList.is_on_sale' => GiftPkg::IS_ON_SALE]);
                }
            ])->where([Goods::tableName().'.goods_id' => $data['goods_id']])
            ->one();
        if (empty($goods)) {
            Yii::error('无效参数 goods_id = '.$data['goods_id'].' 找不到对应商品');
            throw new BadRequestHttpException('商品不存在，参数错误', 2);
        }
        Yii::warning(' 商品基本信息 $goods = '.VarDumper::dumpAsString($goods), __METHOD__);


        //【3】获取商品对应的活动
        $fullGiftEventList      = [];
        $fullCutEventList       = [];
        $signCouponEventList    = [];
        $wuliaoEventList        = [];
        $giftPkgEventList       = [];
        //  没有传入商品数量则默认为商品的起售数量
        if (empty($data['goods_number'])) {
            $data['goods_number'] = $goods->start_num;
        }
        //  【3.1】获取商品对应的 满赠、满减、优惠券  eventToGoods brandToGoods in_array(o_event.effective_scope_type, ['all', 'zhifa'])
        $groupEventListQuery = Event::find();
        if ($goods->supplier_user_id == 1257) {
            $groupEventListQuery->where([
                'effective_scope_type' => [
                    EVENT::EFFECTIVE_SCOPE_TYPE_ALL,
                    EVENT::EFFECTIVE_SCOPE_TYPE_ZHIFA
                ]
            ]);
        } else {
            $groupEventListQuery->where(['effective_scope_type' => EVENT::EFFECTIVE_SCOPE_TYPE_ALL]);
        }

        $groupEventList = $groupEventListQuery
            ->andWhere(['<', Event::tableName().'.start_time', $dateTime])
            ->andWhere(['>', Event::tableName().'.end_time', $dateTime])
            ->andWhere(['event_type' => [Event::EVENT_TYPE_FULL_CUT, Event::EVENT_TYPE_COUPON]])
            ->all();
        $list = [
            $goods->eventList,          //  eventToGoods
            $goods->brand->eventList,   //  eventToBrand
            $groupEventList,            //  直发 或 全局 活动
        ];
        $eventList = OrderGroupHelper::uniqueEventList($list);
        if (!empty($eventList)) {
            foreach ($eventList as $event) {
                switch ($event->event_type) {
                    case Event::EVENT_TYPE_FULL_GIFT:
                        if (!empty($event->fullGiftRule)) {
                            $giftInfo = OrderGroupHelper::getGiftInfo($goods, $event, $data['goods_number']);

                            $fullGiftEventList[] = [
                                'event_id' => $event->event_id,
                                'event_type' => $event->event_type,
                                'event_name' => $event->event_name,
                                'event_desc' => $event->event_desc,
                                'effective_scope_type' => $event->effective_scope_type, //  生效范围
                                'sort_order' => $event->sort_order,

                                'giftInfo' => $giftInfo
                            ];
                        }
                        break;
                    case Event::EVENT_TYPE_FULL_CUT:
                        if (!empty($event->fullCutRule)) {
                            $fullCutEventList[] = [
                                'event_id' => $event->event_id,
                                'event_type' => $event->event_type,
                                'event_name' => $event->event_name,
                                'event_desc' => $event->event_desc,
                                'effective_scope_type' => $event->effective_scope_type, //  生效范围
                                'sort_order' => $event->sort_order,

                                'ruleNameStr' => $event->getRuleNameStr(),
                            ];
                        }
                        break;
                    case Event::EVENT_TYPE_COUPON:
                        $fullCutRule = $event->fullCutRule;
                        if (!empty($fullCutRule) && $event->receive_type == Event::RECEIVE_TYPE_DRAW) {
                            $ruleIdList = ArrayHelper::getColumn($fullCutRule, 'rule_id');
                            $signCouponEventList[] = [
                                'event_id' => $event->event_id,
                                'event_type' => $event->event_type,
                                'event_name' => $event->event_name,
                                'event_desc' => $event->event_desc,
                                'effective_scope_type' => $event->effective_scope_type, //  生效范围
                                'sort_order' => $event->sort_order,

                                'ruleNameStr' => $event->getRuleNameStr(),
                                'ruleIdList' => $ruleIdList,
                            ];
                        }
                        break;
                    //  物料配比 Event::
                    case Event::EVENT_TYPE_WULIAO:
                        if (!empty($event->fullGiftRule)) {
                            $giftInfo = OrderGroupHelper::getGiftInfo($goods, $event, $data['goods_number']);

                            $wuliaoEventList[] = [
                                'event_id' => $event->event_id,
                                'event_type' => $event->event_type,
                                'event_name' => $event->event_name,
                                'event_desc' => $event->event_desc,
                                'effective_scope_type' => $event->effective_scope_type, //  生效范围
                                'sort_order' => $event->sort_order,

                                'wuliaoList' => $giftInfo
                            ];
                        }
                        break;
                    default :
                        break;
                }

            }
        }

        //  【3.2】获取商品对应的 领券活动 o_brand.event_id

        //  【3.3】获取商品对应的 礼包活动 o_gift_pkg_goods.goods_id   gift_pkg_id

        if (!empty($goods->giftPkgList)) {
            foreach ($goods->giftPkgList as $giftPkg) {
                $giftPkgEventList[] =[
                    'id' => $giftPkg->id,
                    'name' => $giftPkg->name,
                    'thumb_img' => $giftPkg->getUploadUrl('thumb_img'),
                    'price' => $giftPkg->price,
                    'shipping_code' => $giftPkg->shipping_code,
                    'brief' => str_replace(',', ' ', $giftPkg->brief),

//                    'goodsList' => [],    礼包包含的商品列表
                ];
            }
            $giftPkgEventList = $goods->giftPkgList;
        }


        return [
            'fullCutEventList'      => $fullCutEventList,       //  满减
            'fullGiftEventList'     => $fullGiftEventList,      //  满赠
            'signCouponEventList'   => $signCouponEventList,    //  领券
            'wuliaoEventList'       => $wuliaoEventList,        //  物料配比
            'giftPkgEventList'      => $giftPkgEventList,       //  礼包
        ];
    }

    /**
     * 判断当前商品是否有当前时段有效的活动
     *
     * $data ['goods_id' => 一组商品的ID]
     *
     * return array
     */
    public function actionEvent_list()
    {
        $data = [];
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
        }
        elseif (Yii::$app->request->isGet) {
            $data = Yii::$app->request->get();
        }

        if (!isset($data['goods_id']) || !$data['goods_id']) {
            Yii::error('无效参数 data = '. VarDumper::export($data), 1);
            throw new BadRequestHttpException('无效参数', 1);
        }

        $goodsList = Goods::find()->where([
            'goods_id' => $data['goods_id'],
            'is_on_sale' => 1,
            'is_delete' => 0,
        ])->all();
        //  未上架、已删除、库存少于起售数量的商品 为无效商品
        foreach ($goodsList as $key => $goods) {
            if (!$goods->is_on_sale || $goods->is_delete) {
                unset($goodsList[$key]);
            } elseif ($goods->goods_number < $goods->start_num) {
                unset($goodsList[$key]);
            }
        }


        //  默认当前时间，订单获取
        $time = (isset($data['time']) && $data['time'])
            ? $data['time']
            : DateTimeHelper::getFormatGMTTimesTimestamp(time());
        $is_active = (isset($data['is_active']) && $data['is_active'])
            ? $data['is_active']
            : true;
        //  获取当前商品对应的有效活动
        if ($event_list = EventToGoods::getEventByGoods($goodsList, $time, $is_active)) {
            $event_detail = Event::getEventDetail($event_list);

            return $event_detail;
        } else {
            return [];
        }
    }

    /**
     * 根据特定条件goods_id 数组获取商品列表
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionSpec_list()
    {
        $params = [];
        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
        }
        elseif (Yii::$app->request->isGet) {
            $params = Yii::$app->request->get();
        }
        if (!isset($params['goods_id_list']) || !isset($params['discount'])) {
            Yii::error('参数错误 params='. VarDumper::export($params), 1);
            throw new BadRequestHttpException('参数错误', 1);
        }
        $query = Goods::find()->select([
                Goods::tableName().'.*'
            ])->joinWith('tags')
            ->joinWith('volumePrice')
            ->joinWith('moqs')
            ->joinWith('category')
            ->where([
                Goods::tableName().'.goods_id' => $params['goods_id_list'],
                'is_on_sale' => Goods::IS_ON_SALE,
                'is_delete' => Goods::IS_NOT_DELETE,
            ])->andWhere([
                '>', 'market_price', 0
            ]);

        if (isset($params['cats']) && $params['cats']) {
            $query->andWhere([Goods::tableName().'.cat_id' => $params['cats']]);
        }
        $query->distinct(Goods::tableName().'.goods_id');

        $query->orderBy([
                Goods::tableName().'.sort_order' => SORT_DESC,
                Goods::tableName().'.complex_order' => SORT_DESC
            ]);
        if (isset($params['limit']) && $params['limit']) {
            $query->limit($params['limit']);
        }

//        $goods_result = $query->asArray()->all();
//
//        $goods_list = Goods::formartGoodsList($goods_result, $params);
        $goods_list = $query->all();

        return $goods_list;
    }

    /**
     * 根据筛选参数拼接Query
     * @param $params
     * @return $this
     */
    private function getSelectQuery($params)
    {
        Yii::info('params = '. VarDumper::export($params), __METHOD__);
        $g_tb = Goods::tableName();
        $gc_tb = GoodsCat::tableName();
        $gt_tb = GoodsTag::tableName();
        $ga_tb = GoodsAttr::tableName();

        $AR_query = Goods::find()->select([
            Goods::tableName().'.*',
            '(10 * min_price / market_price) as discount'
        ]);

        //  2.8 匹配商品tag条件
        if (!empty($params['tag'])) {
            $AR_query->joinWith('goodsTag');
            switch ($params['tag']) {
                case 'new':
                    $AR_query->where([$gt_tb.'.tag_id' => 1]);
                    break;
                case 'supply':
                    $AR_query->where([$gt_tb.'.tag_id' => 2]);
                    break;
                case 'gift':
                    $AR_query->where([$gt_tb.'.tag_id' => 3]);
                    break;
//                case 'mix_up':
//                    $AR_query->where([$gt_tb.'.tag_id' => 4]);
//                    break;
                case 'star':
                    $AR_query->where([$gt_tb.'.tag_id' => 5]);
                    break;
                case 'group':
                    $AR_query->where([$gt_tb.'.tag_id' => 6]);
                    break;
                case 'full_cut':
                    $AR_query->where([$gt_tb.'.tag_id' => 7]);
                    break;
                case 'coupon':
                    $AR_query->where([$gt_tb.'.tag_id' => 8]);
                    break;
                default :
                    $AR_query->where(['>'. $gt_tb.'.tag_id', 0]);
                    break;
            }
            //  1 有效的商品
            $AR_query->andWhere([
                'is_on_sale' => Goods::IS_ON_SALE,
                'is_delete' => Goods::IS_NOT_DELETE
            ])->andWhere([
                '>', 'market_price', 0
            ]);
        } else {
            //  1 有效的商品
            $AR_query->joinWith('tags')->where([
                'is_on_sale' => Goods::IS_ON_SALE,
                'is_delete' => Goods::IS_NOT_DELETE
            ])->andWhere([
                '>', 'market_price', 0
            ]);
        }

        $AR_query->joinWith('volumePrice')
            ->joinWith('category')
            ->joinWith('brand')
            ->joinWith('moqs')
            ->with('extCategory');

        //  2.7 匹配商品属性的 产地 功效
        if (!empty($params['region']) && !empty($params['effect'])) {
            $AR_query->leftJoin(
                'o_goods_attr A',
                $g_tb.'.goods_id = A.goods_id AND A.attr_id = 165'
            )->leftJoin(
                'o_goods_attr B',
                'A.goods_id = B.goods_id AND B.attr_id = 211 AND A.goods_attr_id != B.goods_attr_id'
            );
            $AR_query->andWhere([
                'A.attr_value' => $params['region'],
                'B.attr_value' => $params['effect']
            ]);
        } else {
            $AR_query->joinWith('goodsAttr');

            if (!empty($params['region'])) {
                $AR_query->andWhere([$ga_tb.'.attr_id' => 165])
                    ->andWhere([$ga_tb.'.attr_value' => $params['region']]);
            } elseif (!empty($params['effect'])) {
                $AR_query->andWhere([$ga_tb.'.attr_id' => 211])
                    ->andWhere([$ga_tb.'.attr_value' => $params['effect']]);
            }
        }


        //  2.1 筛选已选中的商品分类对应的商品
        //  考虑扩展分类
        $sub_cat_list = [];
        $goods_id_list = [];
        //  如果已经选中了子分类，优先查询子分类及子分类对应的商品列表
        if (!empty($params['sub_cat_id'])) {
            $sub_cat_list = explode(',', $params['sub_cat_id']);
            $goods_id_result = GoodsCat::find()->select('goods_id')
                ->where([
                    $gc_tb.'.cat_id' => $sub_cat_list
                ])->asArray()
                ->all();
            $goods_id_list = array_column($goods_id_result, 'goods_id');
        } elseif (!empty($params['cat_id'])) {
            //原来的商品分类只支持2级，分类扩展后支持任意深入的分类树，sub_cat_id 参数 无效
            /*$cat_list = explode(',', $params['cat_id']);
            $categories = Category::find()->select(['o_category.cat_id'])
                ->joinWith('children children')
                ->where([
                    'o_category.cat_id' => $cat_list
                ])->asArray()
                ->all();

            foreach($categories as $category) {
                $catIds = [];
                $this->getCatIdByTree($catIds, $category);
                if(!empty($catIds)) {
                    $sub_cat_list = $catIds;
                }
            }

            Yii::info('$totalCatIds = '. VarDumper::export($sub_cat_list), __METHOD__);*/

            $leaves = [];
            if (!is_array($params['cat_id'])) {
                $params['cat_id'] = explode(',', $params['cat_id']);
            }
            foreach ($params['cat_id'] as $actId) {
                CacheHelper::getCategoryLeavesByCatId($actId, $leaves);
            }
            Yii::info(' 获取分类 $params.cat_id '.VarDumper::export($params['cat_id']).
                ' 的所有叶子节点 $leaves = '. VarDumper::export($leaves), __METHOD__);
            if (!empty($leaves)) {
                $sub_cat_list = array_column($leaves, 'cat_id');
            } else {
                $sub_cat_list = [];
            }
            Yii::info(' 获取分类的所有叶子节点的ID $sub_cat_list = '. VarDumper::export($sub_cat_list), __METHOD__);


            $goods_id_result = GoodsCat::find()->select('goods_id')
                ->where([
                    $gc_tb.'.cat_id' => $sub_cat_list
                ])->asArray()
                ->all();
            $goods_id_list = array_column($goods_id_result, 'goods_id');
        }

        if (!empty($params['sub_cat_id']) || !empty($params['cat_id'])) {
            if ($goods_id_list) {
                $AR_query->andWhere([
                    'OR',
                    [$g_tb.'.cat_id' => $sub_cat_list],
                    [$g_tb.'.goods_id' => $goods_id_list]
                ]);
            } else {
                $AR_query->andWhere([
                    $g_tb.'.cat_id' => $sub_cat_list
                ]);
            }
        }

        //  2.2 筛选已选中的品牌对应的商品
        if (isset($params['brand_id']) && $params['brand_id']) {
            if (!is_array($params['brand_id'])) {
                $params['brand_id'] = [$params['brand_id']];
            }
            $brand_id_list = array_map('trim', $params['brand_id']);
            $AR_query->andWhere([
                $g_tb.'.brand_id' => $brand_id_list
            ]);
        }
        //  2.3 筛选已选中的价格段对应的商品
        if (isset($params['stprice']) && $params['stprice'] &&
            isset($params['edprice']) && $params['edprice'])
        {
            Yii::info('stprice'. $params['stprice']. ', edprice'. $params['edprice'], __METHOD__);
            $AR_query->andWhere([
                '>', 'min_price', $params['stprice'],
            ])->andWhere([
                '<', 'min_price', $params['edprice']
            ]);
        }
        //  2.4 筛选已选中的关键词匹配的商品
        if (isset($params['keywords']) && $params['keywords']) {
            $AR_query->andWhere([
                'OR',
                ['like', 'goods_name', $params['keywords']],
                ['like', Goods::tableName().'.keywords', $params['keywords']],
                ['like', Category::tableName().'.cat_name', $params['keywords']],
                ['like', Category::tableName().'.keywords', $params['keywords']],
            ]);
        }
        //  2.5 起订数量最大限制
        if (isset($params['start_num_max']) && intval($params['start_num_max'])) {
            $AR_query->andWhere([
                '<=', 'start_num', intval($params['start_num_max'])
            ]);
        }

        //  2.6 旧的推荐
        if (!empty($params['recommend_type']) && in_array($params['recommend_type'], ['general', 'integral_exchange'])) {
            $AR_query->andWhere([
                'o_goods.extension_code' => $params['recommend_type'],
                'o_goods.is_hot' => 1,
            ]);
        }

        //  goods_id_list
        if (!empty($params['goods_id_list'])) {
            $AR_query->andWhere([
                'o_goods.goods_id' => $params['goods_id_list'],
            ]);
        }

        //  条形码
        if (!empty($params['goods_sn'])) {
            $AR_query->andWhere([
                'goods_sn' => $params['goods_sn'],
            ]);
        }

        //  业务类型（小美诚品，合资频道）
        if (!empty($params['biz_type'])) {
            $AR_query->andWhere([
                'biz_type' => $params['biz_type'],
            ]);
        }

        //  优惠活动  ——团采、秒杀活动 与优惠活动不叠加，需要从商品列表中过滤掉
        if (!empty($params['event_id'])) {
            $event = Event::find()
                ->joinWith([
                    'eventToGoods',
                    'eventToBrand'
                ])->where([Event::tableName().'.event_id' => $params['event_id']])
                ->one();
            Yii::warning(' $event = '.VarDumper::export($event), __METHOD__);

            //  effective_scope_type活动的生效范围['all', 'zhifa', 'brand', 'goods']
            if (!empty($event)) {
                switch ($event->effective_scope_type) {
                    case 'all':
                        break;
                    case 'zhifa':
                        $AR_query->andWhere([Goods::tableName().'.supplier_user_id' => 1257]);
                        break;
                    case 'brand':
                        if (!empty($event->eventToBrand)) {
                            $brandIdList = ArrayHelper::getColumn($event->eventToBrand, 'brand_id');
                            Yii::warning(' $event->eventToBrand = '.json_encode($event->eventToBrand), __METHOD__);
                            $AR_query->andWhere([Goods::tableName().'.brand_id' => $brandIdList]);
                        }
                        break;
                    case 'goods':
                    default :
                        //  默认为 指定商品
                        if (!empty($event->eventToGoods)) {
                            $goodsIdList = ArrayHelper::getColumn($event->eventToGoods, 'goods_id');
                            $AR_query->andWhere([Goods::tableName().'.goods_id' => $goodsIdList]);
                        }
                        break;
                }
            }

            //  过滤团采、秒杀商品
            $groupBuyGoodsActMap = GoodsActivity::aliveActivityGoodsActMap();
            if (!empty($groupBuyGoodsActMap)) {
                $AR_query->andWhere(['not in', Goods::tableName().'.goods_id', array_keys($groupBuyGoodsActMap)]);
            }
        }

        //  包邮 —— 除开架区以外商品全都不包邮
//        if (!empty($params['is_shipping'])) {
//            $freeShippingId = Shipping::getFreeShippingId();
//            $AR_query->andWhere([
//                'OR',
//                [$g_tb.'.shipping_id' => $freeShippingId],
//                [
//                    'AND',
//                    [$g_tb.'.shipping_id' => 0],
//                    ['o_brand.shipping_id' => $freeShippingId],
//                ]
//            ]);
//        }

        //  散批——不按箱购买的商品
        if (!empty($params['notBuyByBox'])) {
            $AR_query->andWhere([$g_tb.'.buy_by_box' => 0]);
        }

        //  是否小美直发
        if (!empty($params['supplier_user_id'])) {
            $AR_query->andWhere([$g_tb.'.supplier_user_id' => $params['supplier_user_id']]);
        }

        //  条数限制
        if (!empty($params['limit'])) {
            $AR_query->offset(0)->limit($params['limit']);
        }

        //  商品类型
        $AR_query->andWhere(['extension_code' => $params['extension_code']]);

        return $AR_query;
    }

    //递归获取节点下面的所有cat_id
    private function getCatIdByTree(&$array, $node) {
        if($node['cat_id'] > 0) {
            $array[] = $node['cat_id'];
        }

        if(isset($node['children']) && !empty($node['children'])) {
            foreach($node['children'] as $child) {
                $this->getCatIdByTree($array, $child);
            }
        }
    }

}

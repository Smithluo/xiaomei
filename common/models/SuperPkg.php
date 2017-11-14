<?php

namespace common\models;

use common\helper\DateTimeHelper;
use common\helper\NumberHelper;
use common\helper\SwiftMailerHelper;
use common\helper\TextHelper;
use common\models\Goods;
use Yii;

/**
 * This is the model class for table "o_super_pkg".
 *
 * @property integer $id
 * @property string $pag_name
 * @property string $pag_desc
 * @property integer $gift_pkg_id
 * @property integer $sort_order
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $is_show
 */
class SuperPkg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_super_pkg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gift_pkg_id', 'sort_order', 'is_show'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['pag_name','pag_desc','sort_order','start_time','end_time', 'gift_pkg_id'], 'required'],
            ['pag_name', 'string', 'max' => 40],
            ['pag_desc', 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pag_name' => '礼包别称',
            'pag_desc' => '礼包短描述',
            'gift_pkg_id' => '礼包ID',   //  对应 gift_pkg_id
            'sort_order' => '排序',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'is_show' => '是否显示',
        ];
    }

    public function getGiftPkg()
    {
        return $this->hasOne(GiftPkg::className(), ['id' => 'gift_pkg_id']);
    }

    /**
     * 获取礼包活动列表
     * @return array
     */
    public static function giftPkgList($limit = 0)
    {
        $pkgList = [];
        $time = DateTimeHelper::getFormatDate(time());
        $superPkgQuery = self::find()
            ->joinWith([
                'giftPkg giftPkg' => function ($query) {
                    return $query->andOnCondition(['is_on_sale' => 1]);
                },
                'giftPkg.giftPkgGoods',
                'giftPkg.giftPkgGoods.goods'
            ])->where(['is_show' => 1])
            ->andWhere(['>=', 'end_time', $time])
            ->andWhere(['<=', 'start_time', $time])
            ->orderBy([
//                new yii\db\Expression('FIELD (goods.goods_number, 0)'),     //库存为0的排到后面   套餐的数量需要计算才能得到
                'sort_order' => SORT_DESC,
                'id' => SORT_DESC,
            ]);

        if ($limit > 0) {
            $superPkgQuery->limit($limit);
        }

        $superPkg = $superPkgQuery->all();

        foreach($superPkg as $pkg)
        {
            //  计算 最大、最小 可购买数量
            $max_num = [];
            if (!empty($pkg->giftPkg->giftPkgGoods)) {
                $totalMarketPrice = 0;
                $totalShopPrice = 0;
                foreach ($pkg->giftPkg->giftPkgGoods as $giftPkgGoods) {
                    if ($giftPkgGoods->goods_num > 0) {
                        $max_num[] = floor($giftPkgGoods->goods->goods_number / $giftPkgGoods->goods_num);

                        $totalMarketPrice += $giftPkgGoods->goods->market_price * $giftPkgGoods->goods_num;
                        $totalShopPrice += $giftPkgGoods->goods->shop_price * $giftPkgGoods->goods_num;
                    }
                }
                $userRankSavePrice = $totalShopPrice - $pkg->giftPkg->price;

                $maxNum = min($max_num); //  礼包的套数 取所有商品匹配的最小值
                if ($maxNum == 0) {
                    //  修改缺货礼包的排序值为0并下架
                    /*$pkg->giftPkg->is_on_sale = 0;
                    $pkg->giftPkg->sort_order = 0;

                    if ($pkg->giftPkg->save()) {
                        $content = '礼包活动 商品库存不足,礼包活动已下架';
                    } else {
                        $content = '礼包活动 商品库存不足,礼包活动下架失败';
                    }

                    $setTo = Yii::$app->params['mailGroup']['goodsOperater'];
                    $subject = '礼包活动下架通知';
                    Yii::warning('发送邮件通知'.$subject.'; 收件人：'.json_encode($setTo).'; 邮件内容：'.$content, __METHOD__);
                    SwiftMailerHelper::sendMail($setTo, $subject, $content);*/

                    continue;   //  售罄的礼包不显示
                } else {
                    $minNum = 1;
                }

                if ($totalMarketPrice > $pkg->giftPkg->price) {
                    $discount = round($pkg->giftPkg->price / $totalMarketPrice * 10, 1);
                } else {
                    $discount = 10.0;
                }
            }

            if (empty($pkg->giftPkg->brief)) {
                $brief = '';
            } else {
                $brief = TextHelper::replaceDelimter($pkg->giftPkg->brief);
                $brief = str_replace(',', ' ', $brief);
            }

            if (!empty($pkg->giftPkg)) {
                $pkgList[] = [
                    'id' => $pkg->giftPkg->id,
                    'm_url' => '/default/giftpkg/index/id/'.$pkg->giftPkg->id.'.html',
                    'pc_url' => '/gift_pkg.php?id='.$pkg->giftPkg->id,
                    'img' => $pkg->giftPkg->getUploadUrl('img'),
                    'pkg_name' => $pkg->pag_name,
                    'status' => '活动正在进行',
                    'brief' => $brief,
                    'pag_desc' => TextHelper::formatRichText($pkg->pag_desc),   //  礼包显示配置的 短描述
                    'price' => NumberHelper::price_format($pkg->giftPkg->price),
                    'userRankSavePrice' => NumberHelper::price_format($userRankSavePrice),
                    'totalShopPrice' => NumberHelper::price_format($totalShopPrice),
                    'maxNum' => $maxNum,
                    'minNum' => $minNum,
                    'discount' => $discount,
                ];
            }

        }

        return $pkgList;
    }
}

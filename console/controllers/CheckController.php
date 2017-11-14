<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/8/30
 * Time: 15:17
 */

namespace console\controllers;

use common\models\Category;
use common\models\Goods;
use common\helper\SwiftMailerHelper;
use common\models\GoodsTag;
use common\models\Tags;
use yii\helpers\ArrayHelper;
use \Yii;

class CheckController extends \yii\console\Controller
{
    /**
     * 检查商品
     * 【1】上下架状态（is_on_sale is_delete 是否冲突）
     * 【2】SPU不为空时 sku_size 也不能为空
     * 【3】库存为低于起售数量，但是状态为在售
     * 【4】按箱购买的 起售数量 不合理（应是装箱规格的 整数倍）
     * 【5】商品名称中含有小美 供应商不是小美直发
     *
     * 【6】商品没有关联品牌
     * 【7】商品没有关联到分类
     * 【8】商品关联的分类不存在
     *
     * 修正直发商品的标签
     */
    public function actionGoodsInfo()
    {
        $warningGoodsList = [];
        $errorMap = [
            'is_on_sale'            => '检查上下架状态',
            'lack_info'             => '关联了SPU，没填写规格',
            'out_of_stock'          => '库存不足，上架状态',
            'start_num_error'       => '按箱购买,起售数量不是整箱',
            'start_num_zero'        => '起售数量为0',
            'box_num_error'         => '装箱规格 和 销售装箱数 之间不是倍数关系',
            'with_wrong_supplier'   => '直发商品，供应商不是小美',
            'direct_goods_add_tag'  => '直发商品添加直发标签失败',

            'unlinked_brand_id'     => '商品没有关联品牌',
            'unlinked_cat'          => '商品没有关联到商品分类',
            'link_unexist_cat'      => '商品关联的分类不存在',
        ];

        //  【1】上架状态 且 被逻辑删除 实际上不会显示在商城前台
        $statusErrorGoods = Goods::find()
            ->select(['goods_id', 'goods_name'])
            ->where([
                'is_on_sale' => Goods::IS_ON_SALE,
                'is_delete' => Goods::IS_DELETE
            ])->all();
        if (!empty($statusErrorGoods)) {
            foreach ($statusErrorGoods as $goods) {
                $warningGoodsList['is_on_sale'][] = $goods;
            }
        }

        //  【2】SPU不为空时 sku_size 也不能为空
        $skuSizeErrorGoods = Goods::find()
            ->select(['goods_id', 'goods_name'])
            ->where(['>', 'spu_id', 0])
            ->andWhere(['sku_size' => ''])
            ->all();
        if ($skuSizeErrorGoods) {
            foreach ($skuSizeErrorGoods as $goods) {
                $warningGoodsList['lack_info'][] = $goods;
            }
        }

        //  【3】库存为低于起售数量，但是状态为在售
        $outOfStockGoods = Goods::find()
            ->select(['goods_id', 'goods_name'])
            ->where(['is_on_sale' => Goods::IS_ON_SALE])
            ->andWhere(['<', 'goods_number', 'start_num'])
            ->all();
        if ($outOfStockGoods) {
            foreach ($outOfStockGoods as $goods) {
                $warningGoodsList['out_of_stock'][] = $goods;
            }
        }

        //  【4】按箱购买的 起售数量 不合理（应是销售装箱数的 整数倍）
        $buyByBoxErrorGoods = Goods::find()
            ->select(['goods_id', 'goods_name', 'start_num', 'number_per_box', 'qty'])
            ->where([
                'is_on_sale' => Goods::IS_ON_SALE,
                'buy_by_box' => Goods::BUY_BY_BOX,
            ]);
        foreach ($buyByBoxErrorGoods->batch(50) as $goodsList) {

            foreach ($goodsList as $goods) {
                if ($goods->start_num % $goods->number_per_box != 0) {
                    $warningGoodsList['start_num_error'][] = $goods;
                }
                //  装箱规格 和 销售装箱数 之间不是倍数关系
                elseif (
                    $goods->start_num % $goods->number_per_box != 0 &&
                    $goods->number_per_box % $goods->start_num != 0
                ) {
                    $warningGoodsList['box_num_error'][] = $goods;
                } elseif ($goods->start_num == 0) {
                    $warningGoodsList['start_num_zero'][] = $goods;
                }
            }

        }

        //  【5】商品名称中含有小美 供应商不是小美直发
        $withWrongSupplierGoods = Goods::find()
            ->select(['goods_id', 'goods_name'])
            ->where(['is_on_sale' => Goods::IS_ON_SALE])
            ->andWhere(['like', 'goods_name', '直发'])
            ->andWhere(['!=', 'supplier_user_id', 1257])
            ->all();
        if ($withWrongSupplierGoods) {
            foreach ($withWrongSupplierGoods as $goods) {
                $warningGoodsList['with_wrong_supplier'][] = $goods;
            }
        }

        //  【6】商品没有关联品牌
        $unlinkedBrandGoods = Goods::find()
            ->select(['goods_id', 'goods_name'])
            ->where(['is_on_sale' => Goods::IS_ON_SALE])
            ->andWhere(['brand_id' => 0])
            ->all();
        if ($unlinkedBrandGoods) {
            foreach ($unlinkedBrandGoods as $goods) {
                $warningGoodsList['unlinked_brand_id'][] = $goods;
            }
        }

        //  【7】商品没有关联分类
        $unlinkedCatGoods = Goods::find()
            ->select(['goods_id', 'goods_name'])
            ->where(['is_on_sale' => Goods::IS_ON_SALE])
            ->andWhere(['cat_id' => 0])
            ->all();
        if ($unlinkedCatGoods) {
            foreach ($unlinkedCatGoods as $goods) {
                $warningGoodsList['unlinked_cat'][] = $goods;
            }
        }

        //  【8】商品关联的分类不存在
        $unlinkedCatGoods = Goods::find()
            ->select(['goods_id', 'goods_name', 'cat_id'])
            ->where(['is_on_sale' => Goods::IS_ON_SALE])
            ->andWhere(['>', 'cat_id', 0]);

        foreach ($unlinkedCatGoods->batch(50) as $goodsList) {
            if (!empty($goodsList)) {
                $catIdList = ArrayHelper::getColumn($goodsList, 'cat_id');
                if (!empty($catIdList)) {
                    $existCatList = Category::find()->select(['cat_id'])->where(['cat_id' => $catIdList])->all();
                    if (!empty($existCatList)) {
                        $existCatIdList = ArrayHelper::getColumn($existCatList, 'cat_id');

                        if (!empty($existCatIdList)) {
                            foreach ($goodsList as $goods) {
                                if (!in_array($goods->cat_id, $existCatIdList)) {
                                    $warningGoodsList['link_unexist_cat'][] = $goods;
                                }
                            }
                        }
                        //  如果查询到的所有商品分类都不存在
                        else {
                            foreach ($goodsList as $goods) {
                                $warningGoodsList['link_unexist_cat'][] = $goods;
                            }
                        }
                    }
                }

            }
        }

        //  修正直发商品的标签
        $directGoods = Goods::find()
            ->where(['supplier_user_id' => 1257])
            ->indexBy('goods_id')
            ->all();
        if (!empty($directGoods)) {
            foreach ($directGoods as $goods) {
                $data = [
                    'goods_id' => $goods->goods_id,
                    'tag_id' => 2
                ];
                $goodsTagRecord = GoodsTag::find()->where($data)->one();
                if (empty($goodsTagRecord)) {
                    $goodsTag = new GoodsTag();
                    $goodsTag->setAttributes($data);
                    if (!$goodsTag->save()) {
                        //  没有添加成功的 输出错误信息
                        $warningGoodsList['direct_goods_add_tag'][] = $goods;
                    }
                }
            }


            $directGoodsIds = array_keys($directGoods);
            $errorTags = GoodsTag::find()
                ->where(['tag_id' => 2])
                ->andWhere([
                    'not in', 'goods_id', $directGoodsIds
                ])->all();
            if (!empty($errorTags)) {
                foreach ($errorTags as $tag) {
                    $tag->delete();
                    echo '删除不应该出现的直发标签，goods_id = '.$tag->goods_id.PHP_EOL;
                }
            }
        }


        //  告警信息输出
        if (!empty($warningGoodsList)) {
            $content = '';

            foreach ($warningGoodsList as $key => $warningList) {
                if (!empty($warningList)) {
                    $strTitle = PHP_EOL.'---------------- 告警原因：'.$errorMap[$key].' ----------------'.PHP_EOL;
                    $content .= $strTitle;
                    echo $strTitle;

                    foreach ($warningList as $goods) {
                        if (!empty($goods)) {
                            $strInfo = '-- 商品ID：'.$goods->goods_id.', 商品名称：'.$goods->goods_name.PHP_EOL;
                            $content .= $strInfo;
                            echo $strInfo;
                        }
                    }
                }
            }

            $setTo = Yii::$app->params['mailGroup']['goodsManager'];
            $subject = '商品信息告警';
            Yii::warning('发送邮件通知'.$subject.'; 收件人：'.json_encode($setTo).'; 邮件内容：'.$content, __METHOD__);
            SwiftMailerHelper::sendMail($setTo, $subject, $content);
        }
    }

    /**
     * 检查活动
     *
     * 团采、秒杀
     * 满减、优惠券
     * 慢赠、物料
     * 礼包活动
     */
    public function actionActivity()
    {

    }
}
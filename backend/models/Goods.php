<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 9:32
 */

namespace backend\models;

use common\behaviors\UploadImageBehavior;
use common\helper\GoodsHelper;
use common\helper\TextHelper;
use Yii;
use common\helper\DateTimeHelper;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class Goods extends \common\models\Goods
{
    public $tagIds = [];

    public $volume_number_0 = '';
    public $volume_number_1 = '';
    public $volume_number_2 = '';
    //  数量梯度 对应 价格
    public $volume_price_0 = '';
    public $volume_price_1 = '';
    public $volume_price_2 = '';

//    public $user_rank_vip = Users::USER_RANK_MEMBER;
//    public $user_rank_svip = Users::USER_RANK_VIP;
    //  会员等级 对应 起售数量
    public $moq_vip;
    public $moq_svip;

    public $cat_name = '';
    public $brand_name = '';
    public $url = '';

    public $dividePercent = null;           //分成比例
    public $parentDividePercent = null;      //品牌分成比例

    public $region; //  地区 对应id
    public $effect;
    public $sample; //  物料

    public $servicerStrategy; //  服务商分成比例

    public $linkGoodsList; //  关联商品列表
    public $goodsCatList; //  关联商品列表

    public $goodsGallery; //  商品轮播图
    public $goodsGalleryDesc; //  商品轮播图描述

    public $supplyPrice;    // 计算分成使用的价格

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['tagIds', 'supplyPrice'], 'safe'],
            [
                [
                    'region', 'effect', 'sample',
                    'volume_number_0', 'volume_number_1', 'volume_number_2',
                    'volume_price_0', 'volume_price_1', 'volume_price_2',
//                    'user_rank_vip', 'user_rank_svip',
                    'moq_vip', 'moq_svip', 'servicerStrategy',
                    'goodsGallery', 'goodsGalleryDesc', 'goodsCatList', 'linkGoodsList'
                ],
                'safe'
            ],
            [['region', 'effect', 'sample'], 'string'],
            [['volume_number_0', 'volume_number_1', 'volume_number_2', 'moq_vip', 'moq_svip'], 'integer'],
            [['volume_price_0', 'volume_price_1', 'volume_price_2'], 'number'],
            ['goodsGallery', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']]
        ]); // TODO: Change the autogenerated stub
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'tagIds' => '标签',
            'region' => '产地',
            'effect' => '功效',
            'sample' => '物料配比',

            'volume_number_0'   => '第一梯度数量',
            'volume_number_1'   => '第二梯度数量',
            'volume_number_2'   => '第三梯度数量',
            'volume_price_0'    => '第一梯度价格',
            'volume_price_1'    => '第二梯度价格',
            'volume_price_2'    => '第三梯度价格',

            'moq_vip'           => 'VIP会员起售数量',
            'moq_svip'          => 'SVIP会员起售数量',

            'goodsCatList'      => '扩展分类',
            'servicerStrategy'  => '服务商分成比例',
            'linkGoodsList'     => '关联商品列表',

            'supplyPrice'       => '服务商计算分成的价格',
        ]); // TODO: Change the autogenerated stub
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']); // TODO: Change the autogenerated stub
    }

    /**
     * 使用事务保存商品的 梯度价格和属性，最后保存商品基本信息
     * @param $model
     * @return bool
     */
    public static function safeSave($model, $post)
    {
        Yii::warning(__FILE__.' | '.__FUNCTION__.' -- start ');
        /*
         * 【1】修改 服务商分成比例
         * 不管新增还是修改，都要判定分成比例在 o_servicer_strategy 表中是否存在，
         *      不存在则生成新的记录
         *      o_goods.servicer_strategy_id 存储 o_servicer_strategy.id
         */
        if (!empty($model->servicerStrategy)) {
            $serStrModel = ServicerStrategy::find()
                ->where(['percent_total' => $model->servicerStrategy])
                ->one();
            if ($serStrModel) {
                $model->servicer_strategy_id = $serStrModel->id;
            } else {
                $serStrModel = new ServicerStrategy();
                $serStrModel->percent_total = $model->servicerStrategy;
                $serStrModel->save();
                Yii::trace('服务商分成设置成功');
                $model->servicer_strategy_id = $serStrModel->id;
            }
        }

        // 【2】 商品基础信息入库
        //  修正商品详情的存储路径
        $model->goods_desc = TextHelper::replaceImagePath($model->goods_desc);
        $model->last_update = DateTimeHelper::getFormatGMTTimesTimestamp(time());

        if (!$model->save()) {
            Yii::$app->session->setFlash('error', TextHelper::getErrorsMsg($model->errors));
            return false;
        }
        Yii::trace('商品基础信息保存成功');

        // 【3】 处理扩展分类
        GoodsCat::deleteAll(['goods_id' => $model->goods_id]);
        if (!empty($post['GoodsCat'])) {
            $goodsCatModel = new GoodsCat();
            $GoodsCatList = array_unique($post['GoodsCat']);
            foreach ($GoodsCatList as $cat_id) {
                //  过滤空输入
                if ($cat_id) {
                    $_goodsCatModel = clone $goodsCatModel;
                    $_goodsCatModel->goods_id = $model->goods_id;
                    $_goodsCatModel->cat_id = $cat_id;
                    $_goodsCatModel->save();
                }
            }
            Yii::trace('商品扩展分类保存成功');
        }

        //  清除关联商品
        LinkGoods::deleteAll(['goods_id' => $model->goods_id]);
        Yii::trace('清除关联商品');
        GoodsCat::deleteAll(['goods_id' => $model->goods_id]);
        Yii::trace('清除扩展分类');


        //  处理标签
        GoodsTag::deleteAll(['goods_id' => $model->goods_id]);
        if (!empty($model->tagIds)) {
            foreach($model->tagIds as $tagId) {
                $goodsTag = new GoodsTag();
                $goodsTag->goods_id = $model->goods_id;
                $goodsTag->tag_id = $tagId;
                $goodsTag->save();
            }
        }

        //  修正商品的直发标签
        $goodsTagRecord = GoodsTag::find()
            ->where([
                'goods_id' => $model->goods_id,
                'tag_id' => 2,
            ])->one();
        if ($model->supplier_user_id == 1257) {
            if (empty($goodsTagRecord)) {
                $goodsTag = new GoodsTag();
                $goodsTag->goods_id = $model->goods_id;
                $goodsTag->tag_id = 2;
                if ($goodsTag->save()) {
                    Yii::warning('商品打直发标成功， goods_id = '.$model->goods_id);
                } else {
                    Yii::warning('商品打直发标失败， goods_id = '.$model->goods_id);
                }
            }
        } else {
            if (!empty($goodsTagRecord)) {
                $goodsTagRecord->delete();
            }
        }

        //  修改原有的轮播图
        $GoodsGalleryList = $model->getGoodsGallery()->indexBy('img_id')->all();
        foreach ($GoodsGalleryList as $item) {
            $item->setScenario('update');

            $behavior = Yii::createObject([
                'class' => UploadImageBehavior::className(),
                'attribute' => 'img_original',
                'scenarios' => ['update'],
                'path' => '@mRoot/data/attached/goods-gallery/{goods_id}/',
                'storePrefix' => 'data/attached/goods-gallery/{goods_id}/',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/goods-gallery/{goods_id}',
                'thumbPath' => '@mRoot/data/attached/goods-gallery/{goods_id}/',
                //  thumb、preview 会出现在o_goods.goods_img、o_goods.goods_thumb字段中，不要改
                'thumbs' => [
                    'preview' => ['width' => 620, 'height' => 620, 'quality' => 100],
                    'thumb' => ['width' => 295, 'height' => 295, 'quality' => 100],
                ],
                'arrayKey' => $item->img_id,
            ]);

            $item->attachBehavior(UploadImageBehavior::className(), $behavior);
        }

        if (Model::loadMultiple($GoodsGalleryList, $post) && Model::validateMultiple($GoodsGalleryList)) {
            foreach ($GoodsGalleryList as $gallery) {
                //  商品信息为空则不保存(goods_id 肯定有值 count($validValue) 最小为1)
                $values = array_values($gallery->attributes);
                $validValue = array_filter($values);
                $gallery->save(false);
            }
        }

        //  处理新上传的图片
        $count = count(Yii::$app->request->post('MoreGoodsGallery', []));
        $moreGalleries = [];
        for($i = 0; $i < $count; $i++) {
            $newGallery = new MoreGoodsGallery();
            $newGallery->setScenario('insert');
            $newGallery->goods_id = $model->goods_id;

            $behavior = Yii::createObject([
                'class' => UploadImageBehavior::className(),
                'attribute' => 'img_original',
                'scenarios' => ['insert'],
                'path' => '@mRoot/data/attached/goods-gallery/{goods_id}/',
                'storePrefix' => 'data/attached/goods-gallery/{goods_id}/',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/goods-gallery/{goods_id}',
                'thumbPath' => '@mRoot/data/attached/goods-gallery/{goods_id}/',
                //  thumb、preview 会出现在o_goods.goods_img、o_goods.goods_thumb字段中，不要改
                'thumbs' => [
                    'preview' => ['width' => 620, 'height' => 620, 'quality' => 100],
                    'thumb' => ['width' => 295, 'height' => 295, 'quality' => 100],
                ],
                'arrayKey' => $i,
            ]);

            $newGallery->attachBehavior(UploadImageBehavior::className(), $behavior);
            $moreGalleries[] = $newGallery;
        }

        if (Model::loadMultiple($moreGalleries, $post)) {
            foreach ($moreGalleries as $gallery) {
                $gallery->save();
            }
        }

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            // 【4】 处理关联商品
            if (!empty($post['Goods']['linkGoodsList'])) {
                $insertLingGoodsSql = ' INSERT INTO '.LinkGoods::tableName().
                    ' (goods_id, link_goods_id, is_double, admin_id) '.
                    ' VALUES ';
                foreach ($post['Goods']['linkGoodsList'] as $link_goods_id) {
                    $insertLingGoodsSql .= '('.
                        $model->goods_id.', '.
                        (int)$link_goods_id.', 0, '.
                        Yii::$app->user->identity->id.'),';
                }
                $insertLingGoodsSql = trim($insertLingGoodsSql, ',');
                $insertLingGoodsSql .= ';';
                $connection->createCommand($insertLingGoodsSql)->execute();

                Yii::trace('关联商品入库成功');
            }

            //  处理扩展分类
            if (!empty($post['Goods']['goodsCatList'])) {
                $insertGoodsCatSql = ' INSERT INTO '.GoodsCat::tableName().
                    ' (goods_id, cat_id) '.
                    ' VALUES ';
                foreach ($post['Goods']['goodsCatList'] as $cat_id) {
                    $insertGoodsCatSql .= '('.
                        $model->goods_id.', '.
                        (int)$cat_id.'),';
                }
                $insertGoodsCatSql = trim($insertGoodsCatSql, ',');
                $insertGoodsCatSql .= ';';
                $connection->createCommand($insertGoodsCatSql)->execute();

                Yii::trace('关联商品入库成功');
            }


            //  【5】 处理每个会员级别的起订数量
            if (!empty($model->moq_vip)) {
                $insertVipMoqSql = ' INSERT INTO '.Moq::tableName().
                    ' (goods_id, moq, user_rank) VALUES ('.
                    $model->goods_id.', '.
                    $model->moq_vip.','.
                    UserRank::USER_RANK_MEMBER.')';

                $connection->createCommand($insertVipMoqSql)->execute();
                Yii::trace('更新vip对应的起订数量');
            }
            if (!empty($model->moq_svip)) {
                $insertSvipMoqSql = ' INSERT INTO '.Moq::tableName().
                    ' (goods_id, moq, user_rank) VALUES ('.
                    $model->goods_id.', '.
                    $model->moq_svip.','.
                    UserRank::USER_RANK_VIP.')';

                $connection->createCommand($insertSvipMoqSql)->execute();
                Yii::trace('更新svip对应的起订数量');
            }

            //  【5.1】 如果商品已存在，先上传梯度价格，未创建的商品 在库中没有梯度价格
            if (!empty($model->goods_id)) {
                //  梯度价格,没有主键，先删除再创建，不用检查是否重复
                $delPriceSql = ' DELETE FROM '.VolumePrice::tableName().' WHERE goods_id = '.$model->goods_id;
                $connection->createCommand($delPriceSql)->execute();
                $delMoqSql = ' DELETE FROM '.Moq::tableName().' WHERE goods_id = '.$model->goods_id;
                $connection->createCommand($delMoqSql)->execute();
            }
            //  【5.2】 梯度价格入库
            $insertPriceSqlValue = '';
            if (!empty($model->volume_number_1) && !empty($model->volume_price_1)) {
                $insertPriceSqlValue = "(1, $model->goods_id, $model->volume_number_1, $model->volume_price_1) ";

                if (!empty($model->volume_number_2) && !empty($model->volume_price_2)) {
                    $insertPriceSqlValue .= ", (1, $model->goods_id, $model->volume_number_2, $model->volume_price_2) ";
                }

                $insertPriceSqlValue .= ';';
            }

            if ($insertPriceSqlValue) {
                $insertPriceSql = ' INSERT INTO '.VolumePrice::tableName().
                    ' (price_type, goods_id, volume_number, volume_price) VALUES '.$insertPriceSqlValue;

                $connection->createCommand($insertPriceSql)->execute();
            }

            //  【5.3】 更新最小价格
            $min_price = GoodsHelper::getMinPrice($model->goods_id);
            $updateMinPriceSql = ' UPDATE '.Goods::tableName().
                ' SET min_price = '.$min_price.
                ' WHERE goods_id = '.$model->goods_id;
            $connection->createCommand($updateMinPriceSql)->execute();
            Yii::trace('更新最小价格'.$updateMinPriceSql);

            //  【6】商品属性,有主键goods_attr_id，有则更新，无则插入
            if (!empty($model->goods_id)) {
                $attr_id_map = Yii::$app->params['goods_attr_id'];
                $selectAttrSql = ' SELECT attr_id FROM '.GoodsAttr::tableName().' WHERE goods_id = '.$model->goods_id;
                $rs = $connection->createCommand($selectAttrSql)->queryAll();
                $attr_id_list = array_column($rs, 'attr_id');
            } else {
                $attr_id_list = [];
            }

            //  【6.1】 产地
            if (isset($model->region)) {
                if (in_array($attr_id_map['region'], $attr_id_list)) {
                    $is_update = true;
                } else {
                    $is_update = false;
                }
                $updateRegionSql = GoodsAttr::getUpdateSql(
                    $model->goods_id,
                    $attr_id_map['region'],
                    $model->region,
                    $is_update
                );

                $connection->createCommand($updateRegionSql)->execute();
            }
            //  【6.2】 功效
            if (isset($model->effect)) {
                if (in_array($attr_id_map['effect'], $attr_id_list)) {
                    $is_update = true;
                } else {
                    $is_update = false;
                }
                $updateEffectSql = GoodsAttr::getUpdateSql(
                    $model->goods_id,
                    $attr_id_map['effect'],
                    $model->effect,
                    $is_update
                );

                $connection->createCommand($updateEffectSql)->execute();
            }
            //  【6.3】 物料配比
            if (isset($model->sample)) {
                if (in_array($attr_id_map['sample'], $attr_id_list)) {
                    $is_update = true;
                } else {
                    $is_update = false;
                }
                $updateSampleSql = GoodsAttr::getUpdateSql(
                    $model->goods_id,
                    $attr_id_map['sample'],
                    $model->sample,
                    $is_update
                );

                $connection->createCommand($updateSampleSql)->execute();
            }
            Yii::trace('更新商品属性');

            $transaction->commit();

            //  商品链接

            Yii::$app->session->setFlash('success', '商品信息修改成功 '.' <span style="color:red"><strong>清空缓存后，到线上验证修改是否生效</strong></span>');
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', '商品属性或商品梯度价格更新失败');
            $transaction->rollBack();
        }

        Yii::trace(__FILE__.' | '.__FUNCTION__.' -- end ');
        return true;
    }

    /**
     * 获取商品 goods_id => goods_name 映射
     * @return array
     */
    public static function getGoodsMap()
    {
        $rs = self::find()
            ->select([
                'goods_id',
                'goods_name',
                'goods_sn'
            ])
            ->indexBy('goods_id')
            ->asArray()
            ->where([
                'is_on_sale' => self::IS_ON_SALE
            ])->all();

        foreach ($rs as $k => $value) {
            $rs[$k] = '('.$value['goods_id']. ') '. $value['goods_name']. '('. $value['goods_sn']. ')';
        }

        return $rs;
    }

    public static function getGiftGoodsMap() {
        $goodsList = self::find()->alias('goods')->select(['goods.goods_id', 'goods.goods_name', 'goods.goods_sn'])
            ->joinWith([
                'manzengEvent manzengEvent'
            ])->where([
                'is_on_sale' => 1,
                'is_delete' => 0,
            ])->all();

        $result = [];
        foreach ($goodsList as $value) {
            $result[$value['goods_id']] = '('.$value['goods_id']. ') '. $value['goods_name']. '('. $value['goods_sn']. ')';
        }

        return $result;
    }

    /**
     * 获取商品 goods_id => goods_name 映射
     * @return array
     */
    public static function getUnDeleteGoodsMap()
    {
        $goodsNameList = [];
        $rs = self::find()
            ->select(['goods_id', 'goods_name', 'goods_sn', 'is_delete', 'is_on_sale', 'goods_number'])
            ->where(['is_delete' => self::IS_NOT_DELETE])
            ->asArray()
            ->all();

        if (!empty($rs)) {
            foreach ($rs as $goods) {
                $goodsName = '';
//                if ($goods['is_delete']) {
//                    $goodsName .= '[已删除] ';
//                }
                if (!$goods['is_on_sale']) {
                    $goodsName .= '[已下架] ';
                }
                $goodsName .= ' ID:'.$goods['goods_id'].' | 条码: '.$goods['goods_sn'].' | 库存: '.$goods['goods_number'].' | '.$goods['goods_name'];

                $goodsNameList[$goods['goods_id']] = $goodsName;
            }
        }
        return $goodsNameList;
    }


    /**
     * 获取商品 goods_id => goods_name 映射
     * @return array
     */
    public static function getGoodsName($goodsList)
    {
        $GoodsName = [];
        $rs = self::find()
            ->select(['goods_id', 'goods_name', 'is_on_sale', 'is_delete'])
            ->where(['goods_id' => $goodsList])
            ->all();

        foreach ($rs as $goods) {
            $str = '';
            if ($goods->is_delete) {
                $str .= '[已删除] ';
            }
            if (!$goods->is_on_sale) {
                $str .= '[已下架] ';
            }
            $str .= ' goods_id:'.$goods->goods_id.' '.$goods->goods_name;
            $GoodsName[$goods->goods_id] = $str;
        }
        return $GoodsName;
    }


    /**
     * 获取商品 goods_id => goods_name 映射
     * @return array
     */
    public static function getALLGoodsMap()
    {
        $goodsNameList = [];
        $rs = self::find()
            ->select(['goods_id', 'goods_name', 'goods_sn', 'is_delete', 'is_on_sale', 'goods_number'])
            ->asArray()
            ->all();

        if (!empty($rs)) {
            foreach ($rs as $goods) {
                $goodsName = '';
//                if ($goods['is_delete']) {
//                    $goodsName .= '[已删除] ';
//                }
                if (!$goods['is_on_sale']) {
                    $goodsName .= '[已下架] ';
                }
                $goodsName .= ' ID:'.$goods['goods_id'].' | 条码: '.$goods['goods_sn'].' | 库存: '.$goods['goods_number'].' | '.$goods['goods_name'];

                $goodsNameList[$goods['goods_id']] = $goodsName;
            }
        }
        return $goodsNameList;
    }
}
<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_gift_pkg_goods".
 *
 * @property integer $id
 * @property integer $gift_pkg_id
 * @property integer $goods_id
 */
class GiftPkgGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_gift_pkg_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gift_pkg_id', 'goods_id'], 'required'],
            [['gift_pkg_id', 'goods_id', 'goods_num'], 'integer'],
            ['goods_num', 'default', 'value' => 1],

            ['gift_pkg_id', 'checkGiftPkg'],
            ['goods_id', 'checkGoods'],
        ];
    }

    /**
     * 礼包必须有效
     * 同一个礼包关联的商品必须属于同一个供应商（小美直发） 或 同属于一个品牌
     * @param $attribute
     */
    public function checkGiftPkg($attribute)
    {
        $giftPkg = GiftPkg::find()->where(['id' => $this->gift_pkg_id])->one();
        if (empty($giftPkg)) {
            $this->addError($attribute, '礼包活动不存在');
        }
    }

    /**
     * 商品必须有效
     *
     * @param $attribute
     */
    public function checkGoods($attribute)
    {
        $goods = Goods::find()->where(['goods_id' => $this->goods_id])->one();
        if (empty($goods)) {
            $this->addError($attribute, '商品不存在');
        } elseif ($goods->is_delete) {
            $this->addError($attribute, '商品已(逻辑)删除');
        }

        $giftPkgGoods = self::find()
            ->joinWith('goods')
            ->where(['gift_pkg_id' => $this->gift_pkg_id])
            ->all();

        $hasDirect = 0;
        $brandIds = [];
        foreach ($giftPkgGoods as $item) {
            if ($item->goods->supplier_user_id == 1257) {
                $hasDirect = 1;
            } else {
                $brandIds[] = $item->goods->brand_id;
            }
        }

        //  判定 要创建的新商品是否违反规则
        if ($goods->supplier_user_id == 1257) {
            $hasDirect = 1;
        } else {
            $brandIds[] = $goods->brand_id;
        }

        if (!empty($brandIds)) {
            $count = count(array_unique($brandIds));
            if ($count > 1) {
                $this->addError('gift_pkg_id', '当前不支持非直发商品有多个品牌在一个礼包活动中');
            } elseif ($count == 1 && $hasDirect) {
                $this->addError('gift_pkg_id', '当前不支持礼包活动中同时有直发商品和非直发商品');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gift_pkg_id' => '礼包活动',
            'goods_id' => '商品ID',
            'goods_num' => '商品数量',
        ];
    }

    public function getGiftPkg()
    {
        return $this->hasOne(GiftPkg::className(), ['id' => 'gift_pkg_id']);
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }
}

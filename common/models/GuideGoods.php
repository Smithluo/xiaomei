<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_guide_goods".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $goods_id
 * @property integer $sort_order
 */
class GuideGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_guide_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'goods_id', 'sort_order'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类别',
            'goods_id' => '商品名称',
            'sort_order' => '排序值'
        ];
    }
    public static function TypeMap()
    {
        $type = GuideType::find()->select(['id','title'])->orderBy(['sort_order' => SORT_DESC])->asArray()->all();
        return array_column($type, 'title', 'id');
    }

    public static function  Goods()
    {
        $goodsList = Goods::find()
            ->where([
                'is_on_sale' => 1,
                'is_delete' => 0,
            ])->asArray()->all();

        return  array_column($goodsList, 'goods_name', 'goods_id');
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id']);
    }

    public function getGuideType()
    {
        return $this->hasOne(GuideType::className(),['id' => 'type']);
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_season_goods".
 *
 * @property integer $id
 * @property string $goods_id
 * @property integer $sort_order
 * @property integer $is_show
 * @property integer $type
 */
class SeasonGoods extends \yii\db\ActiveRecord
{
    const IS_SHOW = 1;
    const IS_NOT_SHOW = 0;

    public static $is_show_map = [
        self::IS_SHOW => '是',
        self::IS_NOT_SHOW => '否',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_season_goods';
    }

    public function rules()
    {
        return [
            [['goods_id', 'sort_order', 'is_show','type'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['desc'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_id' => '商品',
            'sort_order' => '排序',
            'name' => '名字',
            'desc' => '描述',
            'is_show' => '是否显示',
            'type' => '标签'
        ];
    }

    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['goods_id' => 'goods_id' ] );
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

    public function getSeasonCategory()
    {
        return $this->hasOne(SeasonCategory::className(), ['id' => 'type']);
    }

    public static function Type()
    {
        return array_column(SeasonCategory::find()->asArray()->all(), 'title', 'id');
    }
}

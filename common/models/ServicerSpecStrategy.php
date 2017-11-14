<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_servicer_spec_strategy".
 *
 * @property integer $id
 * @property string $brand_id
 * @property string $goods_id
 * @property string $servicer_user_id
 * @property string $strategy_id
 * @property integer $percent_level_1
 * @property integer $percent_level_2
 */
class ServicerSpecStrategy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_servicer_spec_strategy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['brand_id', 'goods_id', 'servicer_user_id', 'strategy_id', 'percent_level_1', 'percent_level_2'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand_id' => '策略应用的品牌',
            'goods_id' => '策略应用的商品',
            'servicer_user_id' => '1级服务商id',
            'strategy_id' => '分成策略id，对应订单总价的百分比',
            'percent_level_1' => '一级服务商分成百分比',
            'percent_level_2' => '二级服务商分成百分比',
        ];
    }

    public function getBrand() {
        return $this->hasOne(\service\models\Brand::className(), ['brand_id' => 'brand_id']);
    }

    public function getStrategy() {
        return $this->hasOne(ServicerStrategy::className(), ['id' => 'strategy_id']);
    }
}

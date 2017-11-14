<?php

namespace common\models;

use Yii;
use common\models\GoodsActivity;
/**
 * This is the model class for table "o_index_group_buy".
 *
 * @property integer $id
 * @property string $activity_id
 * @property integer $sort_order
 * @property string $title
 */
class IndexGroupBuy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_group_buy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string' ,'max'=>'20'],
            [['activity_id', 'sort_order'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'activity_id' => '团采活动',
            'sort_order' => '排序值',
        ];
    }

    public function getGoodsActivity()
    {
        return $this->hasOne(GoodsActivity::className(), ['act_id' => 'activity_id']);
    }

    public static function GroupBuy(){
        $goodsActivity = GoodsActivity::find()->where([
            'act_type' => 1 ,
            ])->andWhere(['>', 'end_time', time()])
            ->asArray()
            ->all();

        return  array_column($goodsActivity,  'act_name','act_id');
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_servicer_strategy".
 *
 * @property integer $id
 * @property double $percent_total
 */
class ServicerStrategy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_servicer_strategy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['percent_total'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'percent_total' => '总的分成比例',
        ];
    }
}

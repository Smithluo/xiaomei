<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_event_user_count".
 *
 * @property string $user_id
 * @property string $event_id
 * @property string $count
 */
class EventUserCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_event_user_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'event_id'], 'required'],
            [['user_id', 'event_id', 'count'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户ID',
            'event_id' => '用户参与活动的ID',
            'count' => '参与活动的次数',
        ];
    }
}

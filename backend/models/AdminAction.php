<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "o_admin_action".
 *
 * @property integer $action_id
 * @property integer $parent_id
 * @property string $action_code
 * @property string $relevance
 */
class AdminAction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_admin_action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['action_code', 'relevance'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'action_id' => 'Action ID',
            'parent_id' => 'Parent ID',
            'action_code' => 'Action Code',
            'relevance' => 'Relevance',
        ];
    }
}

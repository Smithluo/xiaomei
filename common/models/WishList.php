<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_wish_list".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $state
 * @property string $created_at
 * @property string $updated_at
 * @property string $content
 */
class WishList extends \yii\db\ActiveRecord
{

    const STATE_UNFINISHED = 1;
    const STATE_FINISHED = 2;
    const STATE_CANCELED = 3;

    public static $stateMap = [
        self::STATE_UNFINISHED => '未达成',
        self::STATE_FINISHED => '已达成',
        self::STATE_CANCELED => '已取消',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_wish_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'content'], 'required'],
            [['user_id', 'state'], 'integer'],
            [['created_at', 'updated_at', 'content'], 'safe'],
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'state' => '状态 1：未达成2：已达成3：已取消',
            'created_at' => '添加时间',
            'updated_at' => '最后修改时间',
            'content' => '心愿内容',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }

}

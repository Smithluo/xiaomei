<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_activity_config".
 *
 * @property integer $id
 * @property string $title
 * @property integer $sort_order
 * @property integer $is_show
 * @property string $api
 */
class ActivityConfig extends \yii\db\ActiveRecord
{

    public static $is_show_map = [
        0 => '否',
        1 => '是'
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_activity_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order', 'is_show'], 'integer'],
            [['title'], 'string', 'max' => 20],
            [['api'], 'string', 'max' => 255],
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
            'sort_order' => '排序值',
            'api' => '接口地址',
            'is_show' => '是否显示',
        ];
    }
}

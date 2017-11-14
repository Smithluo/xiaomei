<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_app_ad".
 *
 * @property integer $id
 * @property string $position_id
 * @property string $title
 * @property string $desc
 * @property string $start_time
 * @property string $end_time
 * @property integer $enable
 * @property string $image
 * @property string $route
 * @property string $params
 * @property integer $sort_order
 */
class AppAd extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_app_ad';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position_id', 'enable', 'sort_order'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['params'], 'string'],
            [['title'], 'string', 'max' => 60],
            [['desc'], 'string', 'max' => 100],
            [['route'], 'string', 'max' => 255],
            [['image'], 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],
            [['image'], 'required', 'on' => ['insert']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'position_id' => '广告位',
            'title' => '标题',
            'desc' => '描述',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'enable' => '是否显示',
            'image' => '图片',
            'route' => 'app页面路由',
            'params' => '参数',
            'sort_order' => '排序值',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \mongosoft\file\UploadImageBehavior::className(),
                'attribute' => 'image',
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/app_ad_img/{id}',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/app_ad_img/{id}',
                'thumbs' => [],
                'unlinkOnSave' => true,
                'unlinkOnDelete' => true,
            ],
        ];
    }
}

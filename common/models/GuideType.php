<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_guide_type".
 *
 * @property integer $id
 * @property string $title
 * @property string $desc
 * @property integer $sort_order
 */
class GuideType extends \yii\db\ActiveRecord
{
    public static $guide_css_map = [
        1 => 'xm_yiliu',
        2 => 'xm_lirun',
        3 => 'xm_baopin',
        4 => 'xm_changxian',
    ];

    public static $guide_bg_map = [
        1 => 'bg_blue',
        2 => 'bg_orange',
        3 => 'bg_red',
        4 => 'bg_green',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_guide_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order'], 'integer'],
            [['title', 'desc'], 'string', 'max' => 20],
            [['title', 'desc', 'sort_order'] , 'required']
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
            'desc' => '选品描述',
            'sort_order' => '排序值',
        ];
    }

    public function getGuideGoods()
    {
        return $this->hasMany(GuideGoods::className() , ['type' => 'id'])->orderBy([
            'sort_order' => SORT_DESC,
        ]);
    }
}

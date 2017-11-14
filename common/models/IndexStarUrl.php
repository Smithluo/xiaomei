<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_star_url".
 *
 * @property integer $id
 * @property integer $tab_id
 * @property string $title
 * @property integer $is_hot
 * @property string $url
 * @property integer $sort_order
 */
class IndexStarUrl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_star_url';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tab_id', 'sort_order', 'is_hot'], 'integer'],
            [['title'], 'string', 'max' => 10],
            [['url'], 'string', 'max' => 255],
            [['sort_order'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tab_id' => '楼层',
            'title' => '标题',
            'is_hot' => '是否高亮热词',
            'url' => '跳转链接',
            'sort_order' => '排序值',
        ];
    }

    public function getTab() {
        return $this->hasOne(IndexStarGoodsTabConf::className(), ['id' => 'tab_id']);
    }
}

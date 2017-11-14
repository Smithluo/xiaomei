<?php

namespace common\models;

use common\helper\DateTimeHelper;
use Yii;

/**
 * This is the model class for table "o_goods_collection".
 *
 * @property integer $id
 * @property string $title
 * @property string $desc
 * @property string $create_time
 * @property integer $click_init
 * @property integer $click
 * @property string $color
 * @property string $is_show
 * @property string $keywords
 * @property integer $sort_order
 * @property integer $is_hot
 */
class GoodsCollection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods_collection';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['desc'], 'string'],
            [['create_time'], 'safe'],
            [['click', 'click_init', 'sort_order', 'is_show', 'is_hot'], 'integer'],
            [['title', 'keywords'], 'string', 'max' => 60],
            [['color'], 'string', 'max' => 10],
            [['title', 'desc'], 'required'],
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
            'desc' => '摘要',
            'create_time' => '创建时间',
            'click' => '点击次数',
            'click_init' => '初始点击次数',
            'color' => '颜色值',
            'sort_order' => '排序值',
            'is_show' => '是否显示',
            'keywords' => '关键词,用逗号分割',
            'is_hot' => '是否显示在聚合页',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if (empty($this->color)) {
            $this->color = '069fc8';
        }
        if (empty($this->sort_order)) {
            $this->sort_order = 1000;
        }
        if ($insert) {
            $this->create_time = DateTimeHelper::getFormatDateTime(time());
        }
        return true;
    }

    public function getItemList() {
        return $this->hasMany(GoodsCollectionItem::className(), [
            'coll_id' => 'id',
        ]);
    }

    public function getItemCount() {
        $count = 0;
        foreach ($this->itemList as $item) {
            if (!empty($item['goods'])) {
                ++$count;
            }
        }
        return $count;
    }
}

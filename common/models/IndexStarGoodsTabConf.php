<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_index_star_goods_tab_conf".
 *
 * @property integer $id
 * @property string $tab_name
 * @property integer $sort_order
 * @property string $m_url
 * @property string $pc_url
 * @property string $image
 */
class IndexStarGoodsTabConf extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_index_star_goods_tab_conf';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort_order'], 'integer'],
            [['tab_name'], 'required'],
            [['tab_name'], 'string', 'max' => 10],
            [['m_url', 'pc_url'], 'string', 'max' => 255],
            [['sort_order'], 'default', 'value' => 0],

            ['image', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tab_name' => '楼层名称',
            'sort_order' => '排序值',
            'm_url' => 'm站点击更多时候的跳转链接',
            'pc_url' => 'PC站点击更多时的跳转链接',
            'image' => 'PC站图片'
        ];
    }

    /**
     * 获取tab下所有的商品配置
     * @return \yii\db\ActiveQuery
     */
    public function getStarGoodsConf() {
        return $this->hasMany(IndexStarGoodsConf::className(), ['tab_id' => 'id'])->orderBy(['sort_order' => SORT_DESC]);
    }

    /**
     * 楼层中的品牌
     * @return \yii\db\ActiveQuery
     */
    public function getStarBrands() {
        return $this->hasMany(IndexStarBrandConf::className(), ['tab_id' => 'id'])->orderBy(['sort_order' => SORT_DESC]);
    }

    /**
     * 楼层中PC端的小链接
     * @return \yii\db\ActiveQuery
     */
    public function getStarUrls() {
        return $this->hasMany(IndexStarUrl::className(), ['tab_id' => 'id'])->orderBy(['sort_order' => SORT_DESC]);
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
                'path' => '@imgRoot/index_floor/{id}',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/index_floor/{id}',
                'thumbs' => [],
            ],
        ];
    }
}

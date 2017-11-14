<?php

namespace common\models;

use common\helper\DateTimeHelper;
use Yii;

/**
 * This is the model class for table "o_ad".
 *
 * @property integer $ad_id
 * @property integer $position_id
 * @property integer $media_type
 * @property string $ad_name
 * @property string $ad_link
 * @property string $ad_code
 * @property integer $start_time
 * @property integer $end_time
 * @property string $link_man
 * @property string $link_email
 * @property string $link_phone
 * @property string $click_count
 * @property integer $enabled
 */
class Ad extends \yii\db\ActiveRecord
{

    const IS_ENABLE = 1;
    const IS_NOT_ENABLE = 0;

    public static $enableMap = [
        self::IS_ENABLE => '启用',
        self::IS_NOT_ENABLE => '不启用',
    ];

    const MEDIA_TYPE_IMAGE = 0;
    const MEDIA_TYPE_FLASH = 1;
    const MEDIA_TYPE_CODE = 2;
    const MEDIA_TYPE_TEXT = 3;

    public static $mediaTypeMap = [
        self::MEDIA_TYPE_IMAGE => '图片',
        self::MEDIA_TYPE_FLASH => 'flash',
        self::MEDIA_TYPE_CODE => '代码',
        self::MEDIA_TYPE_TEXT => '文本',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_ad';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \mongosoft\file\UploadImageBehavior::className(),
                'attribute' => 'ad_code',
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/ad_pc_image/{ad_id}',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/ad_pc_image/{ad_id}',
                'thumbs' => [],
                'unlinkOnSave' => true,
                'unlinkOnDelete' => true,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position_id', 'media_type', 'start_time', 'end_time', 'click_count', 'enabled'], 'integer'],
            [['ad_name', 'link_man', 'link_email', 'link_phone'], 'string', 'max' => 60],
            [['ad_link'], 'string', 'max' => 255],
            ['ad_code', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],
            [['ad_name', 'ad_link', ], 'required'],
            [['ad_code'], 'required', 'on' => ['insert']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ad_id' => 'ID',
            'position_id' => '广告位置',
            'media_type' => '广告类型',
            'ad_name' => '名称',
            'ad_link' => '链接',
            'ad_code' => '图片',
            'start_time' => '开始时间',
            'end_time' => '结束时间',

            'click_count' => '点击次数',
            'enabled' => '是否启用',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        // 去掉一些包含敏感信息的字段
        unset($fields['link_man'], $fields['link_email'], $fields['link_phone']);

        return $fields;
    }

    public function getAdPosition() {
        return $this->hasOne(AdPosition::className(), ['position_id' => 'position_id']);
    }

    public static function mediaType() {
        return [
            0 => self::MEDIA_TYPE_IMAGE,
            1 => self::MEDIA_TYPE_FLASH,
            2 => self::MEDIA_TYPE_CODE,
            3 => self::MEDIA_TYPE_TEXT,
        ];
    }

    public function formatAd() {
        if ((strpos($this->ad_code, 'http://') === false && strpos($this->ad_code, 'https://') === false)) {
            $ad_code = 'http://www.xiaomei360.com/data/afficheimg/'.$this->ad_code;
        } else {
            $ad_code = $this->ad_code;
        }

        $adFormat = [
            'ad_name' => $this->ad_name,
            'ad_link' => $this->ad_link,
            'ad_code' => $ad_code,
        ];

        return $adFormat;
    }

    /**
     * 获取单张图片的广告
     * @param $positionId
     * @return array
     */
    public static function getFormatAdByPositionId($positionId)
    {
        $ad = self::find()
            ->select(['ad_name', 'ad_link', 'ad_code'])
            ->where(['position_id' => $positionId])
            ->one();

        if (!empty($ad)) {
            $adFormat = $ad->formatAd();
        } else {
            $adFormat = [];
        }

        return $adFormat;
    }
}

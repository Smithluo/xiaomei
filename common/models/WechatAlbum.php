<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_wechat_album".
 *
 * @property string $album_id
 * @property string $album_name
 * @property integer $image_width
 * @property integer $image_height
 * @property string $album_desc
 */
class WechatAlbum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_wechat_album';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['album_name', 'album_desc'], 'required'],
            [['image_width', 'image_height'], 'integer'],
            [['album_name', 'album_desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'album_id' => '相册ID',
            'album_name' => '相册名称',
            'image_width' => '图片宽度',
            'image_height' => '图片高度',
            'album_desc' => '相册描述',
        ];
    }
}

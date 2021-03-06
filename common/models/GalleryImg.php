<?php

namespace common\models;

use common\helper\TextHelper;
use mongosoft\file\UploadImageBehavior;
use Yii;


/**
 * This is the model class for table "o_gallery_img".
 *
 * @property integer $img_id
 * @property integer $gallery_id
 * @property string $img_url
 * @property string $img_original
 * @property string $img_desc
 * @property integer $sort_order
 */
class GalleryImg extends \yii\db\ActiveRecord
{
    const GALLERY_THUMB_SIZE = 248;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_gallery_img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gallery_id'], 'required'],
            [['sort_order'], 'integer', 'max' => 65535],
            ['sort_order', 'default', 'value' => 30000],
            [
                'img_original',
                'image',
                'extensions' => 'jpg, jpeg, gif, png',
                'maxSize' => 2 * 1024 * 1024,
                'on' => ['insert', 'update']
            ],
            ['img_original', 'required', 'on' => 'insert'],
//            ['img_url', 'required', 'on' => 'update'],
            [['img_url', 'img_desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'img_id' => '图片ID',
            'gallery_id' => '相册ID',
            'img_url' => '图片路径',
            'img_original' => '原图图片路径',
            'img_desc' => '图片描述',
            'sort_order' => '排序值',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            //  商品显示图和缩略图 路径修正
            if (!empty($this->img_original)) {
                $pathParts = pathinfo($this->img_original);
                $dirname = $pathParts['dirname'];
                $basename = $pathParts['basename'];
                $this->img_url = $dirname.'/preview-'.$basename;
            }
            return true;
        } else {
            return false;
        }
        // TODO: Change the autogenerated stub
    }

    public function behaviors()
    {
        return [
            [
                'class' => UploadImageBehavior::className(),
                'attribute' => 'img_original',
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/gallery/{gallery_id}/',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/gallery/{gallery_id}',
                'thumbPath' => '@mRoot/data/attached/gallery/{gallery_id}/',
                'thumbs' => [
                    'preview' => [
                        'width' => self::GALLERY_THUMB_SIZE,
                        'height' => self::GALLERY_THUMB_SIZE,
                        'quality' => 100
                    ],
                ],
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $tag = false;   //  缩略图是否创建成功

        //  图片有更新时 修正缩略图 按最窄边 在图片中心截取1:1的图片 缩放到 显示宽度的2倍
        if (!empty($this->img_original)) {
            $path = $this->getUploadPath('img_original');
            $imgInfo = getimagesize($path);
            $pathParts = pathinfo($path);
            $thumbName = '/preview-'.$pathParts['basename'];
            $thumbPath = $pathParts['dirname'].$thumbName;

            if (!empty($imgInfo)) {
                $width = $imgInfo[0];
                $height = $imgInfo[1];

                $dst_w = GalleryImg::GALLERY_THUMB_SIZE;
                $dst_h = GalleryImg::GALLERY_THUMB_SIZE;
                $thumb = imagecreatetruecolor($dst_w, $dst_h);

                //  确定图片的截取位置
                if ($width == $height) {
                    $src_x = 0;
                    $src_y = 0;
                    $minLength = $width;
                } elseif ($width > $height) {
                    $src_x = ($width - $height) / 2;
                    $src_y = 0;
                    $minLength = $height;
                } elseif ($width < $height) {
                    $src_x = 0;
                    $src_y = ($height - $width) / 2;
                    $minLength = $width;
                }

                //  区分图片类型
                switch ($imgInfo[2]) {
                    case 1:
                        $source = imagecreatefromgif($path);
                        imagecopyresampled($thumb, $source, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $minLength, $minLength);
                        if (imagegif($thumb, $thumbPath)) {
                            $this->img_url = $thumbName;
                            $tag = true;
                        }
                        break;
                    case 2:
                        $source = imagecreatefromjpeg($path);
                        imagecopyresampled($thumb, $source, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $minLength, $minLength);
                        if (imagejpeg($thumb, $thumbPath, 100)) {
                            $this->img_url = $thumbName;
                            $tag = true;
                        }
                        break;
                    case 3:
                        $source = imagecreatefrompng($path);
                        imagecopyresampled($thumb, $source, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $minLength, $minLength);
                        if (imagepng($thumb, $thumbPath)) {
                            $this->img_url = $thumbName;
                            $tag = true;
                        }
                    default :
                        break;
                }

            }

            if ($tag == true) {
                move_uploaded_file($_FILES['GalleryImg']['tmp_name']['img_original'] , $thumbPath);
                $_FILES['GalleryImg']['tmp_name']['img_original'] = $thumbPath;
//                Yii::$app->components['_behaviors'][0]['_file']['tempName'] = $thumbPath;
                error_reporting(0);
                if (!$this->save()) {
//                    if ($this->errors) {
//                        $msg = TextHelper::getErrorsMsg($this->errors);
//                    } else {
//                        $msg = '相册'.$this->gallery_id.'的 图片ID为 '.$this->img_id.' 的缩略图入库失败';;
//                    }
//                    Yii::$app->session->setFlash('error', $msg);
//                    Yii::error($msg);
//                    return false;
                }
            } else {
                $msg = '相册'.$this->gallery_id.'的 图片ID为 '.$this->img_id.' 的缩略图生成失败';
                Yii::$app->session->setFlash('error', $msg);
                Yii::error($msg);
                return false;
            }

        }

        // TODO: Change the autogenerated stub
    }

    /**
     * 图片关联相册
     * @return \yii\db\ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasOne(Gallery::className(), ['gallery_id' => 'gallery_id']);
    }


    /**
     * galleryImg 对象的格式化
     * @return array
     */
    public function galleryImgFormat()
    {
        $item = [];
        if ($this->img_url) {
            $item['img_url'] = $this->getUploadUrl('img_url');
            $item['filePath'] = $this->getUploadPath('img_original');

            if ($this->img_original) {
                $item['img_original'] = $this->getUploadUrl('img_original');
            } else {
                $item['img_original'] = $item['img_url'];
            }

            //  图片的描述暂时没有显示位置
        }

        return $item;
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "o_gallery".
 *
 * @property integer $gallery_id
 * @property string $gallery_name
 * @property integer $sort_order
 * @property integer $is_show
 */
class Gallery extends \yii\db\ActiveRecord
{
    const IS_SHOW = 1;
    const IS_NOT_SHOW = 0;

    public static $isShowMap = [
        self::IS_SHOW => '显示',
        self::IS_NOT_SHOW => '不显示',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_gallery';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gallery_name', 'sort_order', 'is_show'], 'required'],
            [['gallery_name'], 'string', 'max' => 40],
            [['sort_order'], 'integer', 'max' => 65535],
            [['is_show'], 'in', 'range' => [0, 1]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gallery_id' => '相册ID',
            'gallery_name' => '相册名称',
            'sort_order' => '排序值',
            'is_show' => '是否显示',
        ];
    }

    /**
     * 通过页码 获取相册列表
     * @param $page
     * @param $pageSize
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getListByPage($page, $pageSize)
    {
        $offset = ($page - 1) * $pageSize;

        return self::find()
            ->joinWith([
                'galleryImg' => function ($query) {
                    return $query->limit(3);
                }
            ])
            ->where(['is_show' => self::IS_SHOW])
            ->orderBy(['sort_order' => SORT_DESC])
            ->offset($offset)
            ->limit($pageSize)
            ->all();
    }

    public static function geGalleryDetail($galleryId, $page, $pageSize)
    {
        $offset = ($page - 1) * $pageSize;

        $return = Gallery::find()
            ->joinWith([
                'galleryImg' => function ($query) use ($offset, $pageSize) {
                    return $query->orderBy([GalleryImg::tableName().'.sort_order' => SORT_DESC])
                            ->offset($offset)
                            ->limit($pageSize);
                }
            ])
            ->where([self::tableName().'.gallery_id' => $galleryId])
            ->one();
        return $return;
    }

    public static function formatGalleryList($galleryList)
    {
        $format = [];
        foreach ($galleryList as $gallery) {
            $item = [];
            $item['gallery_id'] = (int)$gallery->gallery_id;
            $item['gallery_name'] = (string)$gallery->gallery_name;

            if (!empty($gallery->galleryImg)) {
                foreach ($gallery->galleryImg as $img) {
                    $item['imgList'][] = $img->getUploadUrl('img_url');
                }
            }
            $format[] = $item;
        }
    }

    /**
     * 获取相册对应的图片
     * @return \yii\db\ActiveQuery
     */
    public function getGalleryImg()
    {
        return $this->hasMany(GalleryImg::className(), ['gallery_id' => 'gallery_id']);
    }


}

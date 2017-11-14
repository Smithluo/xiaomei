<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/6/1
 * Time: 16:40
 */

namespace backend\models;

use common\helper\TextHelper;
use mongosoft\file\UploadImageBehavior;
use \Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class Gallery extends \common\models\Gallery
{

    /**
     * 保存相册 同时保存保存图片
     * @param $post
     */
    public static function safeSave($model, $post)
    {
        Yii::warning(__FILE__.' | '.__FUNCTION__.' -- start ');

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->load($post);
            if (!$model->save()) {
                Yii::$app->session->setFlash('error', TextHelper::getErrorsMsg($model->errors));
            }

            //  修改原有的图片
            $galleryImgList = $model->getGalleryImgList()->indexBy('img_id')->all();
            if (!empty($galleryImgList)) {
                foreach ($galleryImgList as $item) {
                    $item->setScenario('update');

                    $behavior = Yii::createObject([
                        'class' => UploadImageBehavior::className(),
                        'attribute' => 'img_original',
                        'scenarios' => ['insert', 'update'],
                        'path' => '@imgRoot/gallery/{gallery_id}/',
                        'url' => Yii::$app->params['shop_config']['img_base_url'].'/gallery/{gallery_id}',
                        'thumbPath' => '@mRoot/data/attached/gallery/{gallery_id}/',
                        'thumbs' => [
                            'preview' => ['width' => GalleryImg::GALLERY_THUMB_SIZE, 'quality' => 100],
                        ],
                    ]);

                    $item->attachBehavior(UploadImageBehavior::className(), $behavior);
                }

                if (Model::loadMultiple($galleryImgList, $post) && Model::validateMultiple($galleryImgList)) {
                    foreach ($galleryImgList as $galleryImg) {
                        //  商品信息为空则不保存(goods_id 肯定有值 count($validValue) 最小为1)
                        $values = array_values($galleryImg->attributes);
                        $validValue = array_filter($values);
                        $galleryImg->save(false);
                    }
                }
            }

            //  处理新上传的图片
            $newGalleryImg = new MoreGalleryImg();
            $newGalleryImg->setScenario('insert');
            $newGalleryImg->gallery_id = $model->gallery_id;

            $behavior = Yii::createObject([
                'class' => UploadImageBehavior::className(),
                'attribute' => 'img_original',
                'scenarios' => ['insert', 'update'],
                'path' => '@imgRoot/gallery/{gallery_id}/',
                'url' => Yii::$app->params['shop_config']['img_base_url'].'/gallery/{gallery_id}',
                'thumbPath' => '@mRoot/data/attached/gallery/{gallery_id}/',
                'thumbs' => [
                    'preview' => ['width' => GalleryImg::GALLERY_THUMB_SIZE, 'quality' => 100],
                ],
//                    'arrayKey' => $i,
            ]);

            $newGalleryImg->attachBehavior(UploadImageBehavior::className(), $behavior);

            $galleryImg->save();

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', '相册保存失败'.VarDumper::dump($e));
        }

        return $model;
    }

    /**
     * 获取相册列表
     * @return array
     */
    public static function getGalleryList()
    {
        $galleryMap = [];

        $galleryList = self::find()
            ->indexBy('gallery_id')
            ->orderBy(['gallery_id' => SORT_DESC])
            ->all();

        if (!empty($galleryList)) {
            $galleryMap = ArrayHelper::getColumn($galleryList, 'gallery_name');
        }

        return $galleryMap;
    }

}
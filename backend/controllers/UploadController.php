<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 10:29
 */

namespace backend\controllers;

use backend\models\UploadImageForm;
use common\helper\DateTimeHelper;
use yii\web\UploadedFile;
use yii\web\Controller;
use \Yii;

class UploadController extends Controller
{


    /**
     * 图片上传
     * @return mixed
     */
    public function actionImages($type)
    {
        $base_file_path = '/alidata/www/m.xiaomei360.com/data/attached/';

        //  获取图片保存路径
        if (in_array($type, ['image', 'images'])) {
            $file_path = $base_file_path.$type.'/'.DateTimeHelper::getFormatDateNow().'/';
            if (!is_dir($file_path)) {
//                die($file_path);
                mkdir($file_path);
            }
        } elseif (in_array($type, ['act', 'ad_img', 'article_pic', 'banner_image', 'brand_image', 'cat_image', 'cert', 'file', 'nav', 'store_image', 'video', 'voice', 'wechat_image'])) {
            $file_path = $base_file_path.$type.'/';
        } else {
            die(json_encode([
                'code' => 1,
                'msg' => '未知的图片目录',
            ]));
        }

        $model = new UploadImageForm();

        $result = [];
        if (Yii::$app->request->isPost) {

            $files = UploadedFile::getInstances($model, 'file');

            foreach ($files as $file) {

                $_model = new UploadImageForm();

                $_model->file = $file;

                if ($_model->validate()) {
                    $_model->file->saveAs($file_path . $_model->file->baseName . '.' . $_model->file->extension);
                    $result[] = $_model->file->baseName . '.' . $_model->file->extension;
                } else {
                    foreach ($_model->getErrors('file') as $error) {
                        $model->addError('file', $error);
                    }
                }
            }

            if ($model->hasErrors('file')){
                $model->addError(
                    'file',
                    count($model->getErrors('file')) . ' of ' . count($files) . ' files not uploaded'
                );
            }

        }

        return $this->render('images', [
            'model' => $model,
            'result' => $result,
        ]);
    }
}
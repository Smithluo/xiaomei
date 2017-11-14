<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/6/5
 * Time: 20:00
 */

namespace backend\models;

use yii\helpers\ArrayHelper;

class MoreGalleryImg extends GalleryImg
{
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['img_original', 'required', 'on' => ['insert']],
        ]); // TODO: Change the autogenerated stub
    }

    public function behaviors()
    {
        return [

        ];
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 11:07
 */

namespace backend\models;


use common\models\UploadForm;

class UploadImageForm extends UploadForm
{
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png',],
        ];
    }


}
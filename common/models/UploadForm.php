<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/13
 * Time: 11:30
 */

namespace common\models;

use yii\base\Model;

class UploadForm extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => '文件上传',
        ];
    }

}
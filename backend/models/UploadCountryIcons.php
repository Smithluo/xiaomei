<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/4 0004
 * Time: 10:18
 */

namespace backend\models;


use yii\base\Model;

class UploadCountryIcons extends  Model
{
    public $icon;

    public function rules()
    {
        return [
            [['icon'], 'image', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'icon' => '上传国旗图片'
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $path = \Yii::$app->params['country_dir'];
            if(!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $fileName = $this->icon->name;
            $this->icon->saveAs($path . $fileName);
            return true;
        } else {
            return false;
        }
    }
}
<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class DatepickerAsset extends AssetBundle
{
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $css = 'http://adminjs.xiaomei360.com/lib/css/plugins/datapicker/datepicker3.css?version='. Yii::$app->params['r_version'];
        $this->css = [
            $css,
        ];

        $js[] = 'http://adminjs.xiaomei360.com/lib/plugins/datapicker/bootstrap-datepicker.js?version='. Yii::$app->params['r_version'];
        $this->js = $js;
    }
}

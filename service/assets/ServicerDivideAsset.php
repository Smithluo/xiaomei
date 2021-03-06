<?php

namespace service\assets;

use Yii;
use yii\web\AssetBundle;

class ServicerDivideAsset extends AssetBundle
{
    public $depends = [
        'service\assets\BaseAsset',
    ];

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $customCss = 'http://adminjs.xiaomei360.com/components/service/orderList/orderList.css?version='. Yii::$app->params['r_version'];
        $this->css = [
            $customCss,
        ];

        $customJs[] = 'http://adminjs.xiaomei360.com/lib/grid.js?version='. Yii::$app->params['r_version'];
        $customJs[] = 'http://adminjs.xiaomei360.com/app/service/orderList.js?version='. Yii::$app->params['r_version'];
        $this->js = $customJs;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14 0014
 * Time: 16:06
 */

namespace backend\assets;

use Yii;
use yii\web\AssetBundle;

class OrderGroupDetailAsset extends AssetBundle
{

    public $depends = [
        'backend\assets\BaseAsset',
    ];

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $this->css = [
            'http://adminjs.xiaomei360.com/components/service/superManage/superManage.css?version='. Yii::$app->params['r_version'],
        ];

        $this->js = [
            'http://adminjs.xiaomei360.com/lib/grid.js?version='. Yii::$app->params['r_version'],
            'http://adminjs.xiaomei360.com/lib/suggest.js?version='. Yii::$app->params['r_version'],
            'http://adminjs.xiaomei360.com/app/service/superManage.js?version='. Yii::$app->params['r_version'],
        ];
    }
}
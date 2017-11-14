<?php

namespace api\modules\v2;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/8 0008
 * Time: 17:56
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'api\modules\v2\controllers';

    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

}
<?php

namespace api\modules\v1;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/8 0008
 * Time: 17:56
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'api\modules\v1\controllers';

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
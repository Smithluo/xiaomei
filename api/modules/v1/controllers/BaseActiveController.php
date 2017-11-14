<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/10 0010
 * Time: 10:19
 */

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;

class BaseActiveController extends ActiveController
{

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['update'], $actions['create'], $actions['view'], $actions['delete']);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/4/28
 * Time: 13:48
 */

namespace api\modules\v2\controllers;

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
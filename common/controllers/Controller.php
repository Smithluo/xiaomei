<?php

namespace common\controllers;

use Yii;
use yii\helpers\VarDumper;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19 0019
 * Time: 15:56
 */
class Controller extends \yii\web\Controller
{
    public function gotoView($id) {
        return $this->redirect(['view', 'id' => $id]);
    }

    public function flashSuccess($message) {
        Yii::$app->session->setFlash('success', $message);
    }

    public function flashError($model) {
        $msg = '操作失败 '. get_class($model). ', e = '. VarDumper::export($model->errors);
        Yii::$app->session->setFlash('error', $msg);
        Yii::warning($msg, __METHOD__);
    }

    public function flashErrorMessage($msg) {
        Yii::$app->session->setFlash('error', $msg);
        Yii::warning($msg, __METHOD__);
    }
}
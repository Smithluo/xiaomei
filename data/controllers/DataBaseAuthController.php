<?php
/**
 * Created by PhpStorm.
 * User: HongXunPan
 * Date: 2017/8/17
 * Time: 22:34
 */

namespace data\controllers;

use common\models\Users;
use yii\helpers\ArrayHelper;
use Yii;
use yii\web\UnauthorizedHttpException;

class DataBaseAuthController extends DataBaseController
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (Yii::$app->request->isOptions) {
                return true;
            }

            $post = Yii::$app->request->post();
            $get = Yii::$app->request->queryParams;
            $params = ArrayHelper::merge($post, $get);

            if (!isset($params['token'])) {
                throw new  UnauthorizedHttpException('用户验证失败，请重新登录', -1);
            }

            $token = $params['token'];
            $user = Users::find()->where(['access_token' => $token,])->one();
            if ($user) {
                Yii::$app->user->login($user);
                return true;
            } else {
                throw new  UnauthorizedHttpException('用户验证失败，请重新登录', -1);
            }
        } else {
            return false;
        }
    }

}
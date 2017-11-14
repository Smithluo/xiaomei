<?php
/**
 * Created by PhpStorm.
 * User: HongXunPan
 * Date: 2017/8/18
 * Time: 9:30
 */

namespace data\controllers;


use common\models\Users;
use data\models\DataLoginForm;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use Yii;
use yii\web\UnauthorizedHttpException;

class DataUserController extends DataBaseController
{
    public $enableCsrfValidation = false;

    public function actionAuthUser(){
        if(Yii::$app->request->isOptions) {
            return ['code' => 0];
        }
        $post = Yii::$app->request->post();
        $get = Yii::$app->request->queryParams;
        $params = ArrayHelper::merge($post, $get);
        if(empty($params['token'])) {
            return [
                'code' => 1,
                'msg' => '参数缺失',
                'param' => 'token',
            ];
        }

        $token = $params['token'];
        $user = Users::find()->where(['access_token' => $token,])->one();

        if ($user) {
            $user->updateAccessToken();
            return [
                'code' => 0,
                'msg' => 'success',
                'data' => [
                    'user_id' => intval($user->user_id),
                    'user_name' => $user->user_name,
                    'mobile_phone' => $user->mobile_phone,
                    'access_token' => $user->access_token,
                    'nickname' => $user->nickname,
                ],
            ];
        } else {
            throw new  UnauthorizedHttpException('登录验证失败，请检查用户名和密码是否正确', -1);
        }
    }


    public function actionLoginUser(){
        if(Yii::$app->request->isOptions) {
            return ['code' => 0];
        }
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');

        Yii::info('username = '. $username. ', password = '. $password, __METHOD__);

        $model = new DataLoginForm();
        $model->username = $username;
        $model->password = $password;

        if ($model->login()) {
            $user = Yii::$app->user->identity;
            if ($user != '') {
                $user->updateAccessToken();
            }
            $result = [
                'code' => 0,
                'msg' => 'success',
                'data' => [
                    'user_id' => intval($user->user_id),
                    'user_name' => $user->user_name,
                    'mobile_phone' => $user->mobile_phone,
                    'access_token' => $user->access_token,
                    'nickname' => $user->nickname,
                ],
            ];
            return $result;
        } else {
            throw new  UnauthorizedHttpException('登录验证失败，请检查用户名和密码是否正确', -1);
        }
    }


}
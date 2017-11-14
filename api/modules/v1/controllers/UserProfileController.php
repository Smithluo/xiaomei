<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/30 0030
 * Time: 9:44
 */

namespace api\modules\v1\controllers;

use common\helper\ImageHelper;
use common\models\User;
use common\models\Users;
use Yii;
use api\helper\ErrorHelper;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

class UserProfileController extends BaseAuthActiveController
{
    public $modelClass = 'modules\v1\models\Users';

    /**
     * 编辑信息
     * @return array
     * @throws ServerErrorHttpException
     */
    public function actionEdit() {
        $data = Yii::$app->request->post('data');

        $companyName = $data['company_name'];
        $officeTel = $data['office_tel'];
        $qq = $data['qq'];

        $user = Yii::$app->user->identity;

        $user->company_name = $companyName;
        $user->office_phone = $officeTel;
        $user->qq = $qq;

        if ($user->save()) {
            return [
                'message' => '更新成功',
            ];
        }
        else {
            throw new ServerErrorHttpException(ErrorHelper::getFirstError($user));
        }
    }

    public function actionView() {
        $user = Yii::$app->user->identity;

        $result = [
            'user_id' => intval($user->user_id),
            'user_name' => $user->user_name,
            'mobile_phone' => $user->mobile_phone,
            'office_phone' => $user->office_phone,
            'qq' => $user->qq,
            'user_rank' => $user->user_rank,
            'is_checked' => intval($user->is_checked),
            'nickname' => $user->nickname,
            'company_name' => $user->company_name,
            'shopfront_pic' => ImageHelper::get_image_path($user->shopfront_pic),
            'biz_license_pic' => ImageHelper::get_image_path($user->biz_license_pic),
            'channel' => intval($user->channel),
            'is_checked' => intval($user->is_checked),
        ];

        if (!empty($user->extension)) {
            $result['store_number'] = intval($user->extension['store_number']);
            $result['month_sale_count'] = intval($user->extension['month_sale_count']);
            $result['imports_per'] = intval($user->extension['imports_per']);
            $result['duty'] = intval($user->extension['duty']);
        }

        return $result;
    }

    public function actionServicer() {
        $user = Yii::$app->user->identity;
        $servicer = $user->servicerUser;
        if (!empty($servicer)) {
            return [
                'nickname' => $servicer->nickname,
                'mobile' => $servicer->mobile_phone,
            ];
        }
        return [];
    }

    public function actionChecked() {
        $user = Yii::$app->user->identity;
        return [
            'is_checked' => intval($user->is_checked),
        ];
    }
}
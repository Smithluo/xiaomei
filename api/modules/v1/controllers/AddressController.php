<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15 0015
 * Time: 18:20
 */

namespace api\modules\v1\controllers;

use Yii;
use api\modules\v1\models\Users;
use api\modules\v1\models\UserAddress;
use common\helper\TextHelper;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;


/**
 * 获取用的地址列表时判断是否有 有效的默认收货地址，如果没有，则设置最近一次添加的地址为默认地址
 * is_defautl字段废弃，永远为0，用户默认地址信息存储在o_users表
 *
 * Class AddressController
 * @package api\modules\v1\controllers
 */
class AddressController extends BaseAuthActiveController
{
    public $modelClass = 'api\modules\v1\models\UserAddress';

    /**
     * POST address/create  添加收货地址
     *
     * 检查是否有唯一的默认地址
     *
     * $params = [
     *      'consignee' => string,  //  【必填】收件人
     *      'province' => int,      //  【必填】省份ID
     *      'city' => int,          //  【必填】城市ID
     *      'district' => int,      //      区域ID
     *      'mobile' => int,        //  【必填】手机号
     *      'company_name' => string,    // 店铺名称
     *      'address' => string,    //      详细地址
     *      'tel' => string,        //      固定电话
     * ]
     *
     * return (int)address_id | (class)Exception
     */
    public function actionCreate() {
        $userModel = Yii::$app->user->identity;

        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->post('data');

            if (empty($params['user_id'])) {
                $params['user_id'] = $userModel->user_id;
            }

            $model = new UserAddress();
            $model->setAttributes($params);
            if ($model->validate()) {
                if ($model->save()) {
                    $address_id = Yii::$app->db->getLastInsertID();

                    //  判断是否有有效的默认地址，如果没有则修正
                    $hasDefault = UserAddress::checkDeafultAddress($userModel->user_id, $userModel->address_id);

                    //  如果设置当前为默认，则其他地址应置为非默认
                    if ($model->is_default == UserAddress::IS_DEFAULT || !$hasDefault) {
                        Users::setDefaultAddress($userModel->user_id, $address_id);
                    }

                    return (int)$address_id;
                } else {
                    $errors = TextHelper::getErrorsMsg($model->errors);
                    Yii::error('address/create  添加收货地址 失败。 $params = '.json_encode($params).' errors = '.$errors);
                    throw new ServerErrorHttpException('添加收货地址失败'.$errors, 3);
                }
            } else {
                $errors = TextHelper::getErrorsMsg($model->errors);
                Yii::error('address/create 数据验证失败。 $params = '.json_encode($params).' errors = '.$errors);
                throw new BadRequestHttpException('数据验证失败'.$errors, 2);
            }
        } else {
            Yii::error('POST address/create 接口访问方式错误');
            throw new BadRequestHttpException('接口访问方式错误。', 1);
        }
    }

    /**
     * GET address/list 添加收货地址列表
     *
     * 无参 显示所有收货地址，默认收货地址在第一位，其他地址按address_id 逆序
     *
     * @return array    所有字段
     */
    public function actionList() {
        $userModel = Yii::$app->user->identity;

        $list = UserAddress::getList($userModel->user_id);

        //  判断是否有有效的默认地址，如果没有则修正
        $hasDefault = UserAddress::checkDeafultAddress($userModel->user_id, $userModel->address_id);
        if (!$hasDefault && !empty($list)) {
            $last = end($list);
            $address_id = $last->address_id;
            Users::setDefaultAddress($userModel->user_id, $address_id);
        } else {
            $address_id = $userModel->address_id;
        }

        //  排序，把默认地址放在第一位
        $orderedAddress = [];
        foreach ($list as &$item) {
            if ($item->address_id == $address_id) {
                $item->is_default = UserAddress::IS_DEFAULT;
            } else {
                $item->is_default = UserAddress::IS_NOT_DEFAULT;
            }
        }

        usort($list, function ($a, $b) {
            if ($a->is_default == $b->is_default) {
                return 0;
            } else {
                return $a->is_default > $b->is_default ? -1 : 1;
            }
        });

        return $list;
    }

    /**
     * PUT address/update 编辑收货地址
     *
     * PUT 方式 传递的数据怎么接收？ Yii::$app->request->bodyParams;
     *
     * @return bool
     */
    /*public function actionUpdate() {
        return true;
    }*/

    /**
     * POST address/edit    编辑收货地址
     *
     * 如果当前没有收获地址，则设置当前编辑地址为默认收货地址
     *
     * @return array|null|\yii\db\ActiveRecord
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionEdit() {
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');

        if (empty($params['address_id'])) {
            Yii::error('请指定要变更的地址 address_id');
            throw new BadRequestHttpException('请指定要变更的地址', 1);
        } else {
            $model = UserAddress::find()->where([
                'user_id' => $userModel->user_id,
                'address_id' => $params['address_id']
            ])->one();

            if ($model) {
                $model->setAttributes($params);
                if ($model->validate()) {
                    if ($model->save()) {
                        //  判断是否有有效的默认地址，如果没有则修正
                        $hasDefault = UserAddress::checkDeafultAddress($userModel->user_id, $userModel->address_id);

                        //  如果设置当前为默认，则其他地址应置为非默认
                        if ($model->is_default == UserAddress::IS_DEFAULT || !$hasDefault) {
                            Users::setDefaultAddress($userModel->user_id, $params['address_id']);
                        }

                        return $model;
                    } else {
                        Yii::error('收获地址保存失败 $params = '.json_encode($params));
                        throw new ServerErrorHttpException('收获地址保存失败', 4);
                    }
                } else {
                    $errors = TextHelper::getErrorsMsg($model->errors);
                    Yii::error('收获地址信息格式不正确 $params = '.json_encode($params));
                    throw new BadRequestHttpException('收获地址信息格式不正确'.$errors, 3);
                }
            } else {
                Yii::error('您只能编辑自己的收获地址 $userModel->user_id = '.$userModel->user_id.' $params = '.json_encode($params));
                throw new BadRequestHttpException('您只能编辑自己的收获地址', 2);
            }
        }
    }

    /**
     * POST address/default 设为默认地址
     *
     * $params = ['address_id' => int]
     *
     * @return bool
     * @throws BadRequestHttpException
     */
    public function actionDefault() {
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');

        if (empty($params['address_id'])) {
            Yii::error('请指定要设置为默认的地址 address_id ');
            throw new BadRequestHttpException('请指定要设置为默认的地址', 1);
        }
        //  判定是当前用户的收货地址
        $model = UserAddress::find()->where([
                'user_id' => $userModel->user_id,
                'address_id' => $params['address_id'],
            ])->one();

        if ($model) {
            Users::setDefaultAddress($userModel->user_id, $params['address_id']);
            return true;
        } else {
            Yii::error('指定的地址不存在 $params = '.json_encode($params));
            throw new BadRequestHttpException('指定的地址不存在', 2);
        }
    }

    /**
     * POST address/delete 删除收货地址
     * @return bool
     */
    public function actionDelete() {
        return true;
    }

    /**
     * POST address/drop 删除收货地址，默认地址不能删除
     *
     * $params = ['address_id' => int]
     * @return bool
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDrop() {
        $userModel = Yii::$app->user->identity;
        $params = Yii::$app->request->post('data');

        if ($userModel->address_id == $params['address_id']) {
            Yii::error('默认地址不能删除');
            throw new BadRequestHttpException('默认地址不能删除', 1);
        } else {
            $model = UserAddress::find()->where([
                'user_id' => $userModel->user_id,
                'address_id' => $params['address_id'],
            ])->one();

            if ($model) {
                if ($model->delete()) {
                    return true;
                } else {
                    Yii::error('删除地址失败 $params = '.json_encode($params));
                    throw new ServerErrorHttpException('删除地址失败', 3);
                }
            } else {
                Yii::error('指定的地址不存在 $params = '.json_encode($params));
                throw new BadRequestHttpException('指定的地址不存在', 2);
            }
        }
    }

    /**
     * POST address/default_info    获取默认地址
     *
     * 如果当前有地址但是没有默认地址，则设置最近一个地址为默认地址
     */
    public function actionDefault_info()
    {
        $userModel = Yii::$app->user->identity;

        //  如果用户有设置默认地址，则获取
        if ($userModel->address_id) {
            $address = UserAddress::find()
                ->where([
                    'user_id' => $userModel->user_id,
                    'address_id' => $userModel->address_id
                ])->one();
            if ($address) {
                return $address;
            }
        }
        //  如果用户没有设置默认地址或默认地址无效，则设置最近添加的地址为默认
        $lastAddress = UserAddress::find()
            ->where([
                'user_id' => $userModel->user_id
            ])->orderBy([
                'address_id' => SORT_DESC
            ])->one();

        if ($lastAddress) {
            Users::setDefaultAddress($userModel->user_id, $lastAddress->address_id);

            return $lastAddress;
        } else {
            return [];
        }
    }

}
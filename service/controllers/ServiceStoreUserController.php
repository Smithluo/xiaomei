<?php

namespace service\controllers;

use common\helper\ImageHelper;
use common\helper\SMSHelper;
use common\models\OrderGroup;
use common\models\Region;
use common\models\UserExtension;
use service\models\OrderInfo;
use service\models\ServiceUserSearch;
use Yii;
use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use service\models\ServicerUserInfo;
use service\models\Users;
use service\models\UsersSearch;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;


/**
 * ServiceStoreUserController implements the CRUD actions for Users model.
 */
class ServiceStoreUserController extends XmController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'unchecked', 'check', 'edit','view','change-servicer-user', 'identify'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new UsersSearch();
        $isChecked = Yii::$app->request->get('is_checked');
        $queryParams = Yii::$app->request->queryParams;

        if ($isChecked == Users::IS_CHECKED_STATUS_IN_REVIEW) {

            $searchModel->load($queryParams);

            //地区列表 如果是服务商 那么是如下逻辑
            if(Yii::$app->user->can('service_boss'))
            {
                $regionList = Yii::$app->user->identity['userRegion'];
            }
            //如果是服务商经理 是一下逻辑
            elseif(Yii::$app->user->can('service_manager'))
            {
                $regionList = Yii::$app->user->identity['supserServicerUser']['userRegion'];
            }
            else {
                throw new ForbiddenHttpException('对不起 你没有权限');
            }

            if (!empty($regionList)) {

                $regions = [];
                foreach ($regionList as $region) {
                    $regions[] = $region['region_id'];
                }
                $hasHenan1662 = false;
                $key = array_search(1662, $regions);
                if ($key !== false) {
                    unset($regions[$key]);
                    $hasHenan1662 = true;
                }

                $query = \common\models\Users::find()->with([
                    'provinceRegion',
                    'cityRegion',
                    'extension'
                ]);

                //  河南省 特殊处理 两个服务商
                if ($hasHenan1662) {
                    $henanBreakPointStamp = Yii::$app->params['henanBreakPointStamp'];

                    if (Yii::$app->user->identity->id == 1701) {
                        $andTimeWhere = ['<', 'reg_time', $henanBreakPointStamp];
                    } else {
                        $andTimeWhere = ['>=', 'reg_time', $henanBreakPointStamp];
                    }
                    $where = [
                        'or',
                        [
                            'province' => $regions,
                        ],
                        [
                            'city' => $regions,
                        ],
                        [
                            'and',
                            [
                                'province' => 1662,
                            ],
                            $andTimeWhere
                        ]
                    ];
                }
                else {
                    $where = [
                        'or',
                        [
                            'province' => $regions,
                        ],
                        [
                            'city' => $regions,
                        ],
                    ];
                }

                $query->where($where)->andWhere([
                    '!=', 'user_id', Yii::$app->user->identity['user_id']
                ])->andWhere([
                    '!=', 'is_checked', Users::IS_CHECKED_STATUS_PASSED
                ])->andWhere([
                    '!=', 'mobile_phone', ''    //  验证密码有值，说明是注册用户，不是第三方账户
                ]);

                if (isset($queryParams['UsersSearch'])) {
                    $params = $queryParams['UsersSearch'];
                }
                if (!empty($params['user_id'])) {
                    $query->andWhere(['user_id' => $params['user_id']]);
                }
                if (!empty($params['company_name'])) {
                    $query->andWhere([
                        'like', 'company_name', $params['company_name']
                    ]);
                }
                if (!empty($params['user_name'])) {
                    $query->andWhere([
                        'like', 'user_name', $params['user_name']
                    ]);
                }
                if (!empty($params['mobile_phone'])) {
                    $query->andWhere(['like', 'mobile_phone', $params['mobile_phone']]);
                }
                if(!empty($params['citycode']))
                {
                    $query->andWhere(['city' => $params['citycode']]);
                }

                $models = $query->all();
            } else {
                $models = [];
            }
            // 业务员列表
            $query = ServicerUserInfo::find()->select('o_servicer_user_info.id, o_users.nickname')
                ->joinWith('users');
            if(Yii::$app->user->can('service_boss'))
            {
                  $service_user = $query->where([
                    'o_users.servicer_super_id' => Yii::$app->user->identity['user_id']
                ])->asArray()->all();
            }
            elseif(Yii::$app->user->can('service_manager'))
            {
                $service_user = $query->where([
                    'o_users.servicer_super_id' => Yii::$app->user->identity['servicer_super_id']
                ])->asArray()->all();
            }
            else
            {
                throw new ForbiddenHttpException('对不起, 你没有该权限!');
            }

            $service_user_map = array_column($service_user, 'nickname', 'id');
            return $this->render('unchecked', [
                'searchModel' => $searchModel,
                'models' => $models,
                'checked' => false,
                'service_user_map' => $service_user_map,//    业务员列表
            ]);

        }
        elseif ($isChecked == Users::IS_CHECKED_STATUS_PASSED) {
            $queryParams['is_checked'] = Users::IS_CHECKED_STATUS_PASSED;
            $dataProvider = $searchModel->search($queryParams);
            if (!empty($dataProvider)) {
                return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'checked' => true,
                ]);
            }
            else {
                throw new ForbiddenHttpException('缺少权限，请联系负责服务商对接的工作人员');
            }
        }
    }

    /**
     * 变更业务员
     * @return string
     */
    public function actionChangeServicerUser() {


        $userId = Yii::$app->request->post('user_id');
        $servicerUserId = Yii::$app->request->post('servicer_user_id');

        if (empty($userId) || empty($servicerUserId)) {
            Yii::error('请输入要绑定的用户和服务商', __METHOD__);
            return json_encode([
                'code' => 1,
                'msg' => '请输入要绑定的用户和服务商',
            ]);
        }

        $model = Users::findOne([
            'user_id' => $userId,
        ]);
        if (!$model) {
            Yii::error('未找到用户 userId = '. $userId, __METHOD__);
            return json_encode([
                'code' => 2,
                'msg' => '未找到用户',
            ]);
        }

        $servicer = Users::findOne([
            'user_id' => $servicerUserId,
        ]);
        if (empty($servicer)) {
            Yii::error('未找到业务员 userId = '. $userId. ' servicerUserId = '. $servicerUserId, __METHOD__);
            return json_encode([
                'code' => 3,
                'msg' => '未找到业务员',
            ]);
        }

        if(Yii::$app->user->can('service_boss'))
        {
            if ($servicer->servicer_super_id != Yii::$app->user->identity['user_id']) {
                Yii::error('要绑定的业务员必须是自己旗下的 userId ='.Yii::$app->user->identity['servicer_super_id'],__METHOD__);
                return json_encode([
                    'code' => 4,
                    'msg' => '要绑定的业务员必须是自己旗下的',
                ]);
            }
        }
        elseif(Yii::$app->user->can('service_manager'))
        {
            if ($servicer->servicer_super_id != Yii::$app->user->identity['servicer_super_id']) {
                Yii::error('要绑定的业务员必须是自己旗下的 servicerSuperId ='.Yii::$app->user->identity['servicer_super_id'],__METHOD__);
                return json_encode([
                    'code' => 4,
                    'msg' => '要绑定的业务员必须是自己旗下的',
                ]);
            }
        }


        $model->servicer_user_id = $servicerUserId;
        if ($model->save()) {
            return json_encode([
                'code' => 0,
                'msg' => '绑定业务员成功',
            ]);
        }
        else {
            Yii::error('绑定业务员失败 ,原因是'.$model->firstErrors,__METHOD__);
            return json_encode([
                'code' => 5,
                'msg' => '绑定业务员失败,'. json_encode($model->firstErrors),
            ]);
        }
    }

    /**
     * 解除绑定
     */
    public function actionUnbindUser()
    {
        $result = [];
        $user_id = Yii::$app->request->post('id');
        if($user_id == 0) {
            $result['code'] = 1;
            $result['msg'] = '解绑失败，请选择一个用户解绑';
        }
        else {
            $user = Users::findOne(['user_id' => $user_id]);
            if($user->servicer_user_id > 0) {
                $servicer = $user->servicerUser;

                if($servicer->servicer_super_id > 0) {
                    $superServicer = $servicer->supserServicerUser;

                    if($superServicer->user_id == Yii::$app->user->identity['user_id']) {
                        $user->servicer_user_id = 0;
                        if($user->save()) {
                            Yii::info('解绑门店成功', __METHOD__);
                            $result = [
                                'code' => 0,
                                'msg' => '解绑门店成功',
                            ];
                        }
                        else {
                            $result = [
                                'code' => 2,
                                'msg' => '解绑门店失败',
                            ];
                            Yii::info('解绑门店失败 code=2 errors = '. VarDumper::export($user->errors), __METHOD__);
                        }
                    }
                    else {
                        $result = [
                            'code' => 5,
                            'msg' => '只能解绑已绑定的门店',
                        ];
                        Yii::info('只能解绑已绑定的门店 user = '. VarDumper::export($user), __METHOD__);
                    }
                } else {
                    Yii::info('该门店绑定的业务员没有服务商 user = '. VarDumper::export($user), __METHOD__);
                    $result = [
                        'code' => 3,
                        'msg' => '该门店绑定的业务员没有服务商',
                    ];
                }
            } else {
                Yii::info('该门店没有绑定服务商 user = '. VarDumper::export($user), __METHOD__);
                $result = [
                    'code' => 4,
                    'msg' => '该门店没有绑定服务商',
                ];
            }
        }

        Yii::info('result = '. VarDumper::export($result));
        die(json_encode($result));
    }

    /**
     * 通过Ajax编辑用户信息
     *
     * $params = [
     *      'user_id'       => int,     //  被编辑的用户ID
     *      'field_name'    => string,  //  被编辑的字段名称
     *      'value'         => string,  //  被编辑的字段值
     * ]
     */
    public function actionEdit()
    {
        if (Yii::$app->request->isAjax) {

            $enable_fields = ['company_name', 'checked_note'];

            $params = Yii::$app->request->post();
            if (empty($params['user_id'])) {
                return json_encode([
                    'code' => 1,
                    'msg' => '请指定要编辑的门店',
                    'data' => $params,
                ]);
            } elseif (empty($params['field_name'])) {
                $return = json_encode([
                    'code' => 2,
                    'msg' => '请指定要编辑的字段名',
                    'data' => $params,
                ]);
            } elseif (!in_array($params['field_name'], $enable_fields)) {
                $return = json_encode([
                    'code' => 3,
                    'msg' => '当前字段名不支持编辑',
                    'data' => $params,
                ]);
            } elseif (empty($params['value'])) {
                if ($params['field_name'] == 'company_name') {
                    $msg = '门店名称不能为空';
                } elseif ($params['field_name'] == 'checked_note') {
                    $msg = '备注信息不能为空';
                }
                $return = json_encode([
                    'code' => 5,
                    'msg' => $msg,
                    'data' => $params,
                ]);
            } elseif (mb_strlen($params['value']) > 255) {
                $return = json_encode([
                    'code' => 6,
                    'msg' => '内容过长，最大支持字符数为255',
                    'data' => $params,
                ]);
            } else {
                //  验证当前编辑的用户属于当前服务商
                $user = Users::find()->joinWith(['servicerUser su'])
                    ->where([
                        'o_users.user_id' => $params['user_id'],
                    ])->one();
                if ($user) {
                        $user->$params['field_name'] = $params['value'];

                    if ($user->save()) {
                        $return = json_encode([
                            'code' => 0,
                            'msg' => '保存成功',
                            'data' => $params,
                        ]);
                    } else {
                        $return = json_encode([
                            'code' => 7,
                            'msg' => '保存失败，请重试',
                            'data' => $params,
                        ]);
                    }
                } else {
                    $return = json_encode([
                        'code' => 4,
                        'msg' => '当前用户无效',
                        'data' => $params,
                    ]);
                }
            }

        } else {
            $return = json_encode([
                'code' => 6,
                'msg' => '本接口只支持Ajax',
                'data' => [],
            ]);
        }

        die($return);
    }

    /**
     * 审核门店
     * @return mixed|string
     * @throws BadRequestHttpException
     */
    public function actionCheck()
    {
        if (Yii::$app->request->isAjax) {
            //获取参数
            $userId = Yii::$app->request->post('user_id');
            $isChecked = Yii::$app->request->post('is_checked');
            $companyName = Yii::$app->request->post('company_name') ?: ' ';
            $servicerId = Yii::$app->request->post('servicer_id');
            $checkedNote = Yii::$app->request->post('checked_note');
            //验证合法性
            if (!$checkedNote) {
                return json_encode([
                    'code' => 9,
                    'msg' => '请填写备注',
                ]);
            }
            //  如果要审核通过，则门店名称、业务员 必须填写完整
            if ($isChecked == Users::IS_CHECKED_STATUS_PASSED) {
                //业务员
                $servicerUser = Users::find()->joinWith([
                    'supserServicerUser supserServicerUser'
                ])->where([
                    Users::tableName().'.user_id' => $servicerId
                ])->one();

                if (!$servicerUser) {
                    return json_encode([
                        'code' => 3,
                        'msg' => '请绑定有效的业务员',
                    ]);
                }
                if (!$companyName) {
                    return json_encode([
                        'code' => 1,
                        'msg' => '请完善门店名称',
                    ]);
                } elseif (!$servicerId) {
                    return json_encode([
                        'code' => 2,
                        'msg' => '请绑定业务员',
                    ]);
                }
                // 分为服务商和经理 区别
                if(Yii::$app->user->can('service_boss')) {
                    if(Yii::$app->user->identity['user_id'] !=  $servicerUser->servicer_super_id) {
                        return json_encode([
                            'code' => 4,
                            'msg' => '当前操作门店不在您的服务区域内',
                        ]);
                    }
                }
                //如果是服务商经理 是一下逻辑
                elseif(Yii::$app->user->can('service_manager')) {
                    if(Yii::$app->user->identity['servicer_super_id'] !=  $servicerUser->servicer_super_id) {
                        return json_encode([
                            'code' => 4,
                            'msg' => '当前操作门店不在您的服务区域内',
                        ]);
                    }
                } else {
                    throw new ForbiddenHttpException('对不起 你没有权限');
                }
            }
            // 分为服务商和经理 区别
            if(Yii::$app->user->can('service_boss')) {
                $regionList = Yii::$app->user->identity['userRegion'];
            }
            //如果是服务商经理 是一下逻辑
            elseif(Yii::$app->user->can('service_manager')) {
                $regionList = Yii::$app->user->identity['supserServicerUser']['userRegion'];
            } else {
                throw new ForbiddenHttpException('对不起 你没有权限');
            }
            //获取服务商管辖区域
            $regions = [];
            foreach ($regionList as $region) {
                $regions[] = $region['region_id'];
            }
            //  优先判断地址是否属于服务商，如果没地址，则判断是否绑定了服务商ID
            $user = Users::find()
                ->joinWith(['extension extension'])
                ->where([Users::tableName().'.user_id' => $userId])
                ->one();

            if (!in_array($user->province, $regions) && !in_array($user->city, $regions)) {
                return json_encode([
                    'code' => 4,
                    'msg' => '当前操作门店不在您的服务区域内',
                ]);
            }

            $user->is_checked = $isChecked;
            $user->checked_note = $checkedNote;
            //审核通过的逻辑
            if ($isChecked == Users::IS_CHECKED_STATUS_PASSED) {
                $user->company_name = $companyName ? : ' ';
                $user->servicer_user_id = $servicerUser->user_id;
                $user->user_rank = Users::USER_RANK_REGISTED;
                if($user->check()) {
                    $cfg = CacheHelper::getShopConfigParams('sms_user_rank_check_success');
                    $content = str_replace('#user_name#', $user->getUserShowName(), $cfg['value']);
                    Yii::info(DateTimeHelper::getFormatCNDate(time()).'向'.$user->mobile_phone.'发送 会员审核短信：'.json_encode($content));
                    SMSHelper::sendSms($user->mobile_phone, $content);
                    return json_encode([
                        'code' => 0,
                        'msg' => '审核成功',
                    ]);
                }
                //拒绝
            } elseif ($isChecked == Users::IS_CHECKED_STATUS_REFUSED) {
                if($user->reject()) {
                    return json_encode([
                        'code' => 0,
                        'msg' => '驳回成功',
                    ]);
                }
            }
        } else {
            die(json_encode([
                'code' => 5,
                'msg' => '请求异常',    //  只支持Ajax方式访问
            ]));
        }
    }

//    /**
//     * 门店详情
//     * @param string $id
//     * @return mixed
//     */
    public function actionView($id)
    {
        $searchModel = new ServiceUserSearch();
        $searchModel->date_added = DateTimeHelper::getFormatDate(time() - 30 * 24 * 60 * 60);
        $searchModel->date_modified = DateTimeHelper::getFormatDate(time());
        $searchModel->id =$id;

        $dataProvider = $searchModel->searchByOrderGroup(Yii::$app->request->queryParams);
        //累计总金额
        $totalAcount = OrderInfo::find()
        ->select('Sum(goods_amount) as total_amount ,Sum(discount) as total_discount')
        ->where(['user_id'=>$id])
        ->andWhere([
            'order_status'=>OrderInfo::ORDER_STATUS_REALLY_DONE,
            'pay_status' => OrderInfo::PAY_STATUS_PAYED,
            'shipping_status' => OrderInfo::SHIPPING_STATUS_RECEIVED,
        ])
        ->one();

        $orderGroup = new OrderGroup();
        $divide = $orderGroup->getAlreadyDivide();
        //top 3
        $info = \common\models\OrderGoods::find()
            ->select('Sum(o_order_goods.goods_number) as number ,o_order_goods.goods_id,o_order_goods.goods_name')
            ->joinWith([
                'orderInfo orderInfo',
            ])
            ->groupBy(\common\models\OrderGoods::tableName().'.goods_id')
            ->where(['orderInfo.user_id'=>$id])
            ->orderBy(['number'=>SORT_DESC])
            ->limit(3)
            ->all();
        // 该商店商品总数
        $goodsTotal = \common\models\OrderGoods::find()
            ->select('Sum(o_order_goods.goods_number) as number')
            ->joinWith([
                'orderInfo orderInfo',
            ])
            ->where(['orderInfo.user_id'=>$id])
            ->one();

        return $this->render('view', [
           'model' => $this->findModel($id),
           'totalAcount'=>$totalAcount,
           'searchModel'=>$searchModel,
           'dataProvider'=>$dataProvider,
           'totaldivide'=>$divide,
           'top3' =>$info,
           'allgoods'=>$goodsTotal
        ]);
    }


    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $id
     */
    public function actionIdentify($id)
    {
        $userInfo = Users::find()
        ->joinWith(['extension extension'])
        ->where([\common\models\Users::tableName().'.user_id' => $id])
        ->one();

        if(!empty($userInfo->extension)) {
            $userInfo->extension->duty = UserExtension::$duty_map[$userInfo->extension['duty']];
            $userInfo->extension->month_sale_count = UserExtension::$sale_count_map[$userInfo->extension['month_sale_count']];
            $userInfo->extension->imports_per = UserExtension::$import_map[$userInfo->extension['imports_per']];
        }
        //联系地址
        $address = $userInfo->defaultAddress;

        if(!empty($address)) {
            $userAddress = Region::getAddress($address, $address->address);
        } else {
            $userAddress = '';
        }

        if(in_array($userInfo->channel, [1, 2, 3, 4, 5, 6])) {
            $userInfo->channel = Users::$channel_map[$userInfo->channel];
        } else {

        }
        //业务员列表
        if(Yii::$app->user->can('service_boss')) {
            $servicerUsers = Users::find()
                ->select(['user_id','nickname'])
                ->where([
                    'servicer_super_id'=>Yii::$app->user->identity['user_id']])
                ->all();
        } elseif(Yii::$app->user->can('service_manager')) {
            $servicerUsers = Users::find()
                ->select(['user_id','nickname'])
                ->where([
                    'servicer_super_id'=>Yii::$app->user->identity['servicer_super_id']])
                ->all();
        } else {
            $servicerUsers='';
        }
        $userInfo->shopfront_pic = ImageHelper::get_image_path($userInfo->shopfront_pic);
        $userInfo->biz_license_pic = ImageHelper::get_image_path($userInfo->biz_license_pic);

        return $this->render('identify',[
           'model' => $userInfo,
           'address' => $userAddress,
           'servicerUsers' => $servicerUsers
        ]);
    }

}




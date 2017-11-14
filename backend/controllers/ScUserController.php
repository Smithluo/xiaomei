<?php

namespace backend\controllers;

use backend\models\CouponRecord;
use backend\models\Event;
use backend\models\OrderGroupSearch;
use backend\models\UserRegion;
use common\models\EventUserCount;
use common\models\OrderGoods;
use common\models\OrderInfo;
use common\models\UserExtension;
use Yii;
use backend\models\Integral;
use backend\models\Users;
use backend\models\ScUsersSearch;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use backend\models\Region;
use common\models\UserAddress;
use common\helper\CacheHelper;
use common\helper\DateTimeHelper;
use common\helper\SMSHelper;
use common\helper\OfficeHelper;
use common\helper\ServicerHelper;
use kartik\grid\EditableColumnAction;
use yii\web\Response;

/**
 * ScUserController implements the CRUD actions for Users model.
 */
class ScUserController extends Controller
{
    /**
     * @return mixed
     */
    public  function actions()
    {
        return ArrayHelper::merge(parent::actions(),[
            'edit-province' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => Users::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.Region::getRegionName($model->$attribute);
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => ''],
            ],

            //以下action 为修改收货地址的信息
            'editAddressProvinceCityDistrict' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => UserAddress::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.Region::getRegionName($model->$attribute);
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => ''],
            ],
            'editAddressText' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => UserAddress::className(),
                'outputValue' => function($model, $attribute, $key, $index) {
                    return ''.$model->$attribute;
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';
                },
                'showModelErrors' => true,
                'errorOptions' => ['header' => '']
            ],
        ]);
    }

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
                        'actions' => [
                            'index', 'update', 'create', 'view',
                            'delete', 'check', 'export',
                            'edit-province', 'user-list', 'send-coupon',
                            'region','editAddressProvinceCityDistrict','editAddressText',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 小B端 采购商
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ScUsersSearch();
        $queryParams = Yii::$app->request->queryParams;
        $queryParams['buyer'] = true;
        $dataProvider = $searchModel->search($queryParams);

        $provinceMap = Region::getProvinceMap();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'provinceMap' => $provinceMap,
        ]);
    }

    /**
     * Displays a single Users model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = \common\models\Users::find()->with([
            'extension'
        ])->where(['user_id' => $id])->one();
        if ($model->int_balance == 0) {
            //  修正用户积分
            $balance = Integral::getBalance($model->user_id);
            if ($balance > 0) {
                $model->setAttribute('int_balance', $balance);
                if (!$model->save()) {
                    Yii::$app->session->setFlash('error', 'user_id:'.$model->user_id.' int_balance 更新失败');
                }
            }
        }
        /**
         *添加收货地址模块
         *2017/7/27
         *HongXunPan
         */
        $userAddress = UserAddress::find()->where([
            'user_id' => $id
        ])->orderBy('is_default desc');
        $userAddress = new ActiveDataProvider([
            'query' => $userAddress,
        ]);
        $provinceMap = Region::getProvinceMap();

        //订单数据
        $params = ArrayHelper::merge(Yii::$app->request->queryParams, [
            'OrderGroupSearch' => [
                'user_id' => $id,
            ],
        ]);
        $searchModel = new OrderGroupSearch();
        $provider = $searchModel->search($params);

        //top 12
        $topGoods = \common\models\OrderGoods::find()
            ->select([
                OrderGoods::tableName(). '.order_id',
                'Sum(o_order_goods.goods_number) as number, o_order_goods.goods_id',
            ])
            ->joinWith([
                'orderInfo orderInfo',
                'goods goods',
            ])
            ->groupBy(\common\models\OrderGoods::tableName().'.goods_id')
            ->where(['orderInfo.user_id'=>$id])
            ->andWhere([
                'orderInfo.pay_status' => OrderInfo::PAY_STATUS_PAYED,
            ])
            ->orderBy(['number'=>SORT_DESC])
            ->limit(12)
            ->asArray()
            ->all();
        // 该商店商品总数
        $goodsTotal = \common\models\OrderGoods::find()
            ->select([
                OrderGoods::tableName(). '.order_id',
                'Sum(o_order_goods.goods_number) as number'
            ])
            ->joinWith([
                'orderInfo orderInfo',
            ])
            ->where(['orderInfo.user_id'=>$id])
            ->andWhere([
                'orderInfo.pay_status' => OrderInfo::PAY_STATUS_PAYED,
            ])
            ->asArray()
            ->one();

        return $this->render('view', [
            'model' => $model,
            'provider' => $provider,
            'searchModel' => $searchModel,
            'topGoods' => $topGoods,
            'totalNum' => $goodsTotal['number'],
            'userAddress' => $userAddress,
            'provinceMap' => $provinceMap,
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Users();

        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword($model->password);
            if (empty($model->address_id)) {
                $model->address_id = 0;
            }
            if (empty($model->visit_count)) {
                $model->visit_count = 0;
            }
            if (empty($model->sex)) {
                $model->sex = 0;
            }
            if (empty($model->is_special)) {
                $model->is_special = 0;
            }
            if (empty($model->is_validated)) {
                $model->is_validated = 0;
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->user_id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $oldPassword = $model->password;

        $regions = $model->regions;
        foreach ($regions as $region) {
            $model->regionList[] = $region['region_id'];
        }

        if ($model->load(Yii::$app->request->post())) {

            if ($oldPassword != $model->password) {
                $model->setPassword($model->password);
                $model->ec_salt = null;
            }

            //  容错：如果用户没用绑定服务商，servicer_user_id传入为null，保存会出错
            if (empty($model->servicer_user_id)) {
                $model->servicer_user_id = 0;
            }
            if (empty($model->servicer_super_id)) {
                $model->servicer_super_id = 0;
            }

            if ($model->save()) {

                UserRegion::deleteAll([
                    'user_id' => $model->user_id,
                ]);

                if (!empty($model->regionList)) {
                    foreach ($model->regionList as $regionId) {
                        $userRegion = new UserRegion();
                        $userRegion->user_id = $model->user_id;
                        $userRegion->region_id = $regionId;
                        $userRegion->save();
                    }
                }

                return $this->redirect(['update', 'id' => $model->user_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 用户审核
     * @param $id
     * @return url
     */
    public function actionCheck($id)
    {
        $params = Yii::$app->request->post();
        $model = Users::find()
            ->where([\common\models\Users::tableName().'.user_id' => $id])
            ->joinWith(['extension'])
            ->one();
        //  用户的默认收货地址
        $default_address = '';
        if ($model->address_id) {
            $default_address = UserAddress::getCompleteAddress($model->address_id);
        }

        $area = '';
        if ($model->province && $model->city) {
            $region_name = Region::getRegionNames([$model->province, $model->city]);
            $area = implode(' ', $region_name);
        }
        //  post提交 只用于审核失败
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                //  通过审核
                if ($model->is_checked == Users::IS_CHECKED_STATUS_PASSED ) {
                    //通过审核
                    if(!empty($model->extension)) {
                        $model->extension->load(Yii::$app->request->post());
                    }
                    if ($model->check()) {
                        if($params['Users']['send_sms']) {
                            //  审核通过成为普通会员 和会员等级升级 的短信做区分
                            if ($model->user_rank == Users::USER_RANK_REGISTED) {
                                $cfg     = CacheHelper::getShopConfigParams('sms_user_rank_check_success');
                                $content = str_replace('#user_name#', $model->getUserShowName(), $cfg['value']);
                            } elseif (in_array($model->user_rank, [Users::USER_RANK_MEMBER, Users::USER_RANK_VIP])) {
                                $cfg       = CacheHelper::getShopConfigParams('sms_user_rank_update');
                                $user_rank = Users::$user_rank_map[$model->user_rank];
                                $content   = str_replace('#user_rank#', $user_rank, $cfg['value']);
                            }
                            Yii::info(DateTimeHelper::getFormatDateTime(time()).'向'.$model->mobile_phone.'发送 会员审核短信：'.$content);
                            SMSHelper::sendSms($model->mobile_phone, $content);
                        }
                        Yii::$app->session->setFlash('success', '操作成功');
                        return $this->redirect(['check', 'id' => $model->user_id]);
                    } else {
                        Yii::$app->session->setFlash('failed', '操作失败');
                        return $this->redirect(['check', 'id' => $model->user_id]);
                    }
                //拒绝审核通过
                } elseif ($model->is_checked == Users::IS_CHECKED_STATUS_REFUSED ) {
                    if( !empty($model->extension)) {
                        $model->extension->load(Yii::$app->request->post());
                    }
                    if($model->reject()) {
                        if($params['Users']['send_sms']) {
                            $cfg = CacheHelper::getShopConfigParams('sms_user_rank_check_fail');
                            //  审核不通过  有服务商的区域要显示服务商的联系方式
                            $contact = ServicerHelper::getServicerContact($model->province, $model->city, $model->reg_time);

                            $content = str_replace(
                                ['#user_name#', '#tel#'],
                                [$model->getUserShowName(), $contact['officePhone']],
                                $cfg['value']
                            );
                            Yii::$app->session->setFlash('success', '操作成功');
                            Yii::info(DateTimeHelper::getFormatDateTime(time()).'向'.$model->mobile_phone.'发送 会员审核短信：'.$content);
                            SMSHelper::sendSms($model->mobile_phone, $content);
                        }
                        return $this->redirect(['check', 'id' => $model->user_id]);
                    }  else {
                        Yii::$app->session->setFlash('failed', '操作失败');
                        return $this->redirect(['check', 'id' => $model->user_id]);
                    }
                //拉黑状态
                } elseif($model->is_checked == Users::IS_CHECKED_STATUS_BLACK) {
                    if(!empty($model->extension)) {
                        $model->extension->load(Yii::$app->request->post());
                    }
                    if($model->black()) {
                        Yii::$app->session->setFlash('success', '拉黑成功');
                        return $this->redirect(['index']);
                    } else {
                        Yii::$app->session->setFlash('failed', '拉黑失败');
                        return $this->redirect(['check', 'id' => $model->user_id]);
                    }
                    //未审核
                } else {
                    if($model->unCheck()) {
                        Yii::$app->session->setFlash('success', '操作成功');
                        return $this->redirect(['index']);
                    } else {
                        Yii::$app->session->setFlash('failed', '操作失败');
                        return $this->redirect(['index']);
                    }
                }
            }
        }

        return $this->render('check', [
            'model' => $model,
            'area' => $area,
            'defult_address' => $default_address ?: '尚未填写收获地址',
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
     * 用户信息导出
     */
    public function actionExport()
    {
        ini_set('memory_limit', '1G');
        Yii::trace(__FUNCTION__.'导出用户列表开始');
        //  定义表头，方便在调整各列显示顺序的时候 只调整表头数组就，不用调整各列具体值的数组顺序
        $data_header = [
            'user_id'       => '会员ID',
            'user_name'     => '用户名',
            'nick_name'     => '昵称',
            'mobile'        => '移动电话',
            'company_name'  => '店铺名称',


            'reg_time'      => '注册时间',
            'is_checked'    => '审核状态',
            'checked_note'  => '审核意见',
            'is_identify'   => '是否认证',
            'province'      => '所在省份',
            'city'          => '所在城市',

            'user_rank'     => '会员等级',
            'user_type'     => '用户类别',
            'last_login'    => '最近登录',
            'visit_count'   => '点击次数',

            'totalAmount'   => '完成订单金额',
            'payTimes'      => '实际支付次数',
            'lastPayTime'   => '最后支付时间',

            'servicer_user_id' => '服务商ID',
            'servicerUser' => '业务员',
            'superServicerUser' => '服务商',
        ];

        $data_array[] = array_values($data_header);
        $searchModel = new ScUsersSearch();
        $params = Yii::$app->request->queryParams;
        //  排除  品牌商、代理商、服务商
        $params['user_type'] = [
            Users::USER_TYPE_SHOP,
            Users::USER_TYPE_E_COM,
            Users::USER_TYPE_WECHAT_BIZ,
            Users::USER_TYPE_OTHER
        ];
        $params['page_size'] = 0;
        $dataProvider = $searchModel->searchForExport($params);
        $model_list = $dataProvider->getModels();
        Yii::trace('导出用户列表——获取modelList成功');
        if ($model_list) {
            foreach ($model_list as $model) {
                //  没绑定过手机号的微信用户名是加密的，其他是正常的
                if ($model->openid && !$model->mobile_phone) {
                    $user_name = base64_decode($model->user_name);
                } else {
                    $user_name = $model->showName;
                }

                $identify = '未提交资料';
                if (!empty($model->extension)) {
                    $identify = UserExtension::$identify_map[$model->extension['identify']];
                }

                $item = [
                    'user_id'       => $model->user_id,
                    'user_name'     => $user_name,
                    'nick_name'     => $model->nickname,
                    'mobile'        => $model->mobile_phone,
                    'company_name'  => $model->company_name,

                    'reg_time'      => DateTimeHelper::getFormatCNDateTime($model->reg_time),
                    'is_checked'    => Users::$is_checked_map[$model->is_checked],
                    'checked_note'  => $model->checked_note,
                    'is_identify'   => $identify,
                    'province'      => Region::getRegionName($model->province),
                    'city'          => Region::getRegionName($model->city),

                    'user_rank'     => Users::$user_rank_map[$model->user_rank],
                    'user_type'     => Users::$user_type_map[$model->user_type],
                    'last_login'    => DateTimeHelper::getFormatCNDateTime($model->last_login),
                    'visit_count'   => $model->visit_count,

                    'totalAmount'   => $model->totalAmount,
                    'payTimes'      => $model->payTimes,
                    'lastPayTime'   => $model->lastPayTime
                        ? DateTimeHelper::getFormatCNDateTime($model->lastPayTime)
                        : '没支付过',
                    'servicer_user_id' => $model->servicer_user_id,
                    'servicerUser' => $model['servicerUser']['user_name'],
                    'superServicerUser' => $model['servicerUser']['supserServicerUser']['user_name'],
                ];
                $data_array[] = array_values($item);
            }

        }

        $file_name = '用户列表'.date('YmdHis');
        Yii::trace('导出用户列表——调用导出');
        OfficeHelper::excelExport($file_name, $data_array);
    }
    /**
     * ajax拉取订单列表，给select2控件使用
     * @param null $q
     * @param null $id
     * @return array
     */
    public function actionUserList($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query();
            $query->select('user_id, user_name, mobile_phone')
                ->from(Users::tableName())
                ->where(['like', 'user_name', $q])
                ->orWhere(['like', 'mobile_phone', $q])
                ->orWhere(['like', 'nickname', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();

            $out['results'] = [];
            foreach ($data as $item) {
                $out['results'][] = [
                    'id' => $item['user_id'],
                    'text' => $item['user_name']. '('. $item['mobile_phone']. ')',
                ];
            }
        }
        elseif ($id > 0) {
            $user = Users::find($id);
            $out['results'] = ['id' => $id, 'text' => $user->showName. '('. $user->mobile_phone. ')'];
        }
        return $out;
    }

    public function actionSendCoupon($userId, $eventId) {
        $userModel = Users::findOne($userId);

        if (empty($userModel)) {
            throw new BadRequestHttpException('未找到用户', 1);
        }

        //查找这个优惠券活动
        $event = Event::find()->joinWith([
            'fullCutRule fullCutRule',
            'couponPkg couponPkg',
        ])->where([
            Event::tableName(). '.event_id' => $eventId
        ])->andWhere([
            'is_active' => 1,
        ])->andWhere([
            'event_type' => Event::EVENT_TYPE_COUPON,
        ])->andWhere([
            'couponPkg.enable' => 1,
        ])->one();

        if (empty($event)) {
            throw new BadRequestHttpException('缺少这个活动', 2);
        }
        else {
            Yii::trace('event = '. VarDumper::export($event), __METHOD__);
        }

        //查到这个用户参与这个活动的次数
        $eventUserCount = EventUserCount::find()->where([
            'event_id' => $eventId,
            'user_id' => $userModel->user_id,
        ])->one();

        //未参与过的用户就新建一个对象，为后面的入库做准备
        if (empty($eventUserCount)) {
            $eventUserCount = new EventUserCount();
            $eventUserCount->user_id = $userModel->user_id;
            $eventUserCount->event_id = $eventId;
            $eventUserCount->count = 0;
        }

        if ($eventUserCount->count >= $event->times_limit) {
            throw new BadRequestHttpException('用户已经领取过优惠券', 3);
        }

        $couponCanTake =[];

        foreach($event->fullCutRule as $rule) {
            $couponCanTake[] = CouponRecord::find()->where([
                'user_id' =>0,
                'rule_id'=>$rule->rule_id ,
            ])->one();
        }

        //领券，事务操作
        Event::getDb()->transaction(function ($db) use ($userModel, $couponCanTake, $eventUserCount) {
            foreach ($couponCanTake as $rule) {
                if (!empty($rule)) {
                    $rule->user_id = $userModel->user_id;
                    $rule->received_at = DateTimeHelper::gmtime();
                    $rule->save();
                }
                else {
                    throw new BadRequestHttpException('券已经被领完了', 4);
                }
            }
            ++$eventUserCount->count;
            $eventUserCount->save();
        });

        Yii::$app->session->setFlash('success', '优惠券已派发');
        return $this->redirect(['check', 'id' => $userId]);
    }

    public function actionRegion($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (empty($id)) {
            return [
                'code' => 1,
                'msg' => '缺少id',
            ];
        }
        $regionsList = Region::find()->where([
            'parent_id' => $id
        ])->indexBy('region_id')->all();

        $data = [];
        foreach ($regionsList as $region) {
            $item = [
                'region_id' => $region['region_id'],
                'region_name' => $region['region_name'],
            ];
            $data[] = $item;
        }

        return $data;
    }
}

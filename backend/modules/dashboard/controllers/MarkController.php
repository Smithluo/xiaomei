<?php

namespace backend\modules\dashboard\controllers;

use backend\models\Goods;
use backend\models\Users;
use backend\modules\dashboard\models\MarkCountForm;
use common\helper\DateTimeHelper;
use common\helper\OfficeHelper;
use Yii;
use backend\modules\dashboard\models\Mark;
use backend\modules\dashboard\models\MarkSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MarkController implements the CRUD actions for Mark model.
 */
class MarkController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Mark models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MarkSearch();

        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        $platFormMap = Mark::$platFormMap;

        //  设置默认查询时段
        $search_start = !empty($params['MarkSearch']['start_time'])
            ? $params['MarkSearch']['start_time']
            : date('Y-m-d', strtotime('-60 days'));
        $search_end = !empty($params['MarkSearch']['end_time'])
            ? $params['MarkSearch']['end_time']
            : date('Y-m-d');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'back_action' => 'index',
            'dataProvider' => $dataProvider,
            'search_start' => $search_start,
            'search_end' => $search_end,
            'platFormMap' => $platFormMap,
        ]);
    }

    /**
     * 用户行为数据统计
     *
     * 待扩展，手动选择排序字段
     */
    public function actionCount()
    {
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 30);
        $searchModel = new MarkSearch();
        $params = Yii::$app->request->queryParams;
        $params['page_size'] = 0;
        //  设置默认查询时段
        $search_start = !empty($params['MarkSearch']['start_time'])
            ? $params['MarkSearch']['start_time']
            : date('Y-m-d', strtotime('-60 days'));
        $search_end = !empty($params['MarkSearch']['end_time'])
            ? $params['MarkSearch']['end_time']
            : date('Y-m-d');

        $dataProvider = $searchModel->search($params);
        $markList = $dataProvider->getModels();

        $userIdList = [];   //  统计到的用户ID    用于获取用户名和手机号
        $data = []; //  结果集
        $dataMap = []; //  结果集
        if ($markList) {
            foreach ($markList as $mark) {
                //  如果没有创建数据则初始化
                if (isset($dataMap[$mark->user_id][$mark->plat_form])) {
                    $dataMap[$mark->user_id][$mark->plat_form]['login_times'] += $mark->login_times;
                    $dataMap[$mark->user_id][$mark->plat_form]['click_times'] += $mark->click_times;
                    $dataMap[$mark->user_id][$mark->plat_form]['order_count'] += $mark->order_count;
                    $dataMap[$mark->user_id][$mark->plat_form]['pay_count']   += $mark->pay_count;
                } else {
                    $userIdList[] = $mark->user_id;
                    $dataMap[$mark->user_id][$mark->plat_form]['plat_form']   = $mark->plat_form;
                    $dataMap[$mark->user_id][$mark->plat_form]['login_times'] = $mark->login_times;
                    $dataMap[$mark->user_id][$mark->plat_form]['click_times'] = $mark->click_times;
                    $dataMap[$mark->user_id][$mark->plat_form]['order_count'] = $mark->order_count;
                    $dataMap[$mark->user_id][$mark->plat_form]['pay_count']   = $mark->pay_count;
                    $dataMap[$mark->user_id][$mark->plat_form]['login_days']  = [];
                    $dataMap[$mark->user_id][$mark->plat_form]['order_times'] = 0;
                    $dataMap[$mark->user_id][$mark->plat_form]['pay_times']   = 0;
                }

                $dataMap[$mark->user_id][$mark->plat_form]['user_id'] = $mark->user_id;
                $dataMap[$mark->user_id][$mark->plat_form]['login_days'][] = $mark->date;
                if ($mark->order_count > 0) {
                    $dataMap[$mark->user_id][$mark->plat_form]['order_times']++;
                }
                if ($mark->pay_count > 0) {
                    $dataMap[$mark->user_id][$mark->plat_form]['pay_times']++;
                }
            }

            //  为方便排序，输出遍历  把三维数组转成二维数组
            foreach ($dataMap as $arr) {
                foreach ($arr as $row) {
                    $data[] = $row;
                }
            }

            //  获取活跃用户的信息
            $userInfoMap = Users::find()->select(['user_id', 'user_name', 'mobile_phone', 'reg_time'])
                ->where([
                    'user_id' => $userIdList
                ])->indexBy('user_id')
                ->asArray()
                ->all();
            $gmt_search_start = DateTimeHelper::getFormatGMTTimesTimestamp($search_start);
            $gmt_search_end = DateTimeHelper::getFormatGMTTimesTimestamp($search_end) + 86400;

            //  格式化数据
            $total = [
                'click_times' => 0,     //  点击总次数
//              'login_days_max' => 0,  //  登录天数最高
                'order_times_max' => 0, //  下单次数最多
                'pay_times_max' => 0,   //  支付次数最多
                'login_days' => 0,      //  登录天数总计
                'login_times' => 0,     //  登录总次数
                'order_times' => 0,     //  下单总次数
                'order_count' => 0,     //  下单总数
                'pay_times' => 0,       //  支付总次数
                'pay_count' => 0,       //  支付总单数
                'repeat_pay' => 0,      //  支付总单数
                'pay_user' => 0,        //  有支付的用户数量
                'new_user' => 0,        //  新用户数量
                'change_user' => 0,     //  转化用户数量

            ];
            foreach ($data as &$item) {
                if ($item['login_days']) {
                    $item['login_days'] = count(array_unique($item['login_days']));
                }

                $ignore_mobile_list = Yii::$app->params['employee_mobile'];
                //  只统计有效信息
                if (empty($userInfoMap[$item['user_id']])) {
                    //  如果用户信息不存在，极有可能是测试用户，已删除
                    $item = [];
                    continue;
                } elseif (in_array($userInfoMap[$item['user_id']]['mobile_phone'], $ignore_mobile_list)) {
                    //  内部用户不纳入统计
                    $item = [];
                    continue;
                } else {
                    $item['user_name'] = $userInfoMap[$item['user_id']]['user_name'];
                    $item['mobile_phone'] = $userInfoMap[$item['user_id']]['mobile_phone'];

                    //  是否新用户
                    if ($gmt_search_start < $userInfoMap[$item['user_id']]['reg_time'] &&
                        $userInfoMap[$item['user_id']]['reg_time'] < $gmt_search_end
                    ) {
                        $total['new_user']++;
                        //  新转化用户
                        if ($total['pay_count'] > 0) {
                            $total['change_user']++;
                        }
                    }

                }

                $total['click_times'] += $item['click_times'];
                $total['login_days'] += $item['login_days'];
                $total['login_times'] += $item['login_times'];
                $total['order_times'] += $item['order_times'];
                $total['order_count'] += $item['order_count'];
                $total['pay_times'] += $item['pay_times'];
                $total['pay_count'] += $item['pay_count'];

                /*$total['login_days_max'] = $total['login_days_max'] > $item['login_days']
                    ? $total['login_days_max']
                    : $item['login_days'] ;*/
                $total['order_times_max'] = $total['order_times_max'] > $item['order_times']
                    ? $total['order_times_max']
                    : $item['order_times'] ;
                $total['pay_times_max'] = $total['pay_times_max'] > $item['pay_times']
                    ? $total['pay_times_max']
                    : $item['pay_times'] ;
                //  复购
                if ($item['pay_times'] > 1) {
                    $total['repeat_pay']++;
                }

                //  有支付的用户
                if ($total['pay_count'] > 0) {
                    $total['pay_user']++;
                }

            }

        }
        $data = array_filter($data);    //  判空， 如果没有对应的用户信息，则数据不显示

        $validUserIdList = array_column($data, 'user_id');
        $validUserIdList = array_unique($validUserIdList);
        $count = count($validUserIdList);
        $period = round( (strtotime($search_end) - strtotime($search_start) ) / 86400 ) + 1;
        //  分母可能为0
        $repeat_percent = $total['pay_user']
            ? number_format(($total['repeat_pay'] / $total['pay_user']) * 100, 2)
            : 0;
        $repeat_percent .= ' % ('.$total['repeat_pay'].' / '.$total['pay_user'].')';
        $change_percent = $total['new_user']
            ? number_format(($total['change_user'] / $total['new_user']) * 100, 2)
            : 0;
        $change_percent .= ' % ('.$total['change_user'].' / '.$total['new_user'].')';

        $platFormMap = Mark::$platFormMap;

        return $this->render('count', [
            'searchModel' => $searchModel,
            'platFormMap' => $platFormMap,
            'search_start' => $search_start,
            'search_end' => $search_end,
            'data' => $data,
            'count' => $count,
            'period' => $period,
            'total' => $total,
            'repeat_percent' => $repeat_percent,
            'change_percent' => $change_percent,
            'back_action' => 'count',
        ]);
    }

    public function actionExportCount() {
        ini_set('memory_limit', '1G');
        ini_set('max_execution_time', 120);

        $searchModel = new MarkSearch();
        $params = Yii::$app->request->queryParams;
        $params['page_size'] = 0;
        //  设置默认查询时段
        $search_start = !empty($params['MarkSearch']['start_time'])
            ? $params['MarkSearch']['start_time']
            : date('Y-m-d', strtotime('-60 days'));
        $search_end = !empty($params['MarkSearch']['end_time'])
            ? $params['MarkSearch']['end_time']
            : date('Y-m-d');

        $dataProvider = $searchModel->search($params);
        $markList = $dataProvider->getModels();

        $userIdList = [];   //  统计到的用户ID    用于获取用户名和手机号
        $data = []; //  结果集
        $dataMap = []; //  结果集
        if ($markList) {
            foreach ($markList as $mark) {
                //  如果没有创建数据则初始化
                if (isset($dataMap[$mark->user_id][$mark->plat_form])) {
                    $dataMap[$mark->user_id][$mark->plat_form]['login_times'] += $mark->login_times;
                    $dataMap[$mark->user_id][$mark->plat_form]['click_times'] += $mark->click_times;
                    $dataMap[$mark->user_id][$mark->plat_form]['order_count'] += $mark->order_count;
                    $dataMap[$mark->user_id][$mark->plat_form]['pay_count']   += $mark->pay_count;
                } else {
                    $userIdList[] = $mark->user_id;
                    $dataMap[$mark->user_id][$mark->plat_form]['plat_form']   = $mark->plat_form;
                    $dataMap[$mark->user_id][$mark->plat_form]['login_times'] = $mark->login_times;
                    $dataMap[$mark->user_id][$mark->plat_form]['click_times'] = $mark->click_times;
                    $dataMap[$mark->user_id][$mark->plat_form]['order_count'] = $mark->order_count;
                    $dataMap[$mark->user_id][$mark->plat_form]['pay_count']   = $mark->pay_count;
                    $dataMap[$mark->user_id][$mark->plat_form]['login_days']  = [];
                    $dataMap[$mark->user_id][$mark->plat_form]['order_times'] = 0;
                    $dataMap[$mark->user_id][$mark->plat_form]['pay_times']   = 0;
                }

                $dataMap[$mark->user_id][$mark->plat_form]['user_id'] = $mark->user_id;
                $dataMap[$mark->user_id][$mark->plat_form]['login_days'][] = $mark->date;
                if ($mark->order_count > 0) {
                    $dataMap[$mark->user_id][$mark->plat_form]['order_times']++;
                }
                if ($mark->pay_count > 0) {
                    $dataMap[$mark->user_id][$mark->plat_form]['pay_times']++;
                }
            }

            //  为方便排序，输出遍历  把三维数组转成二维数组
            foreach ($dataMap as $arr) {
                foreach ($arr as $row) {
                    $data[] = $row;
                }
            }

            //  获取活跃用户的信息
            $userInfoMap = Users::find()->select(['user_id', 'user_name', 'mobile_phone', 'reg_time'])
                ->where([
                    'user_id' => $userIdList
                ])->indexBy('user_id')
                ->asArray()
                ->all();
            $gmt_search_start = DateTimeHelper::getFormatGMTTimesTimestamp($search_start);
            $gmt_search_end = DateTimeHelper::getFormatGMTTimesTimestamp($search_end) + 86400;

            //  格式化数据
            $total = [
                'click_times' => 0,     //  点击总次数
//              'login_days_max' => 0,  //  登录天数最高
                'order_times_max' => 0, //  下单次数最多
                'pay_times_max' => 0,   //  支付次数最多
                'login_days' => 0,      //  登录天数总计
                'login_times' => 0,     //  登录总次数
                'order_times' => 0,     //  下单总次数
                'order_count' => 0,     //  下单总数
                'pay_times' => 0,       //  支付总次数
                'pay_count' => 0,       //  支付总单数
                'repeat_pay' => 0,      //  支付总单数
                'pay_user' => 0,        //  有支付的用户数量
                'new_user' => 0,        //  新用户数量
                'change_user' => 0,     //  转化用户数量

            ];
            foreach ($data as &$item) {
                if ($item['login_days']) {
                    $item['login_days'] = count(array_unique($item['login_days']));
                }

                $ignore_mobile_list = Yii::$app->params['employee_mobile'];
                //  只统计有效信息
                if (empty($userInfoMap[$item['user_id']])) {
                    //  如果用户信息不存在，极有可能是测试用户，已删除
                    $item = [];
                    continue;
                } elseif (in_array($userInfoMap[$item['user_id']]['mobile_phone'], $ignore_mobile_list)) {
                    //  内部用户不纳入统计
                    $item = [];
                    continue;
                } else {
                    $item['user_name'] = $userInfoMap[$item['user_id']]['user_name'];
                    $item['mobile_phone'] = $userInfoMap[$item['user_id']]['mobile_phone'];

                    //  是否新用户
                    if ($gmt_search_start < $userInfoMap[$item['user_id']]['reg_time'] &&
                        $userInfoMap[$item['user_id']]['reg_time'] < $gmt_search_end
                    ) {
                        $total['new_user']++;
                        //  新转化用户
                        if ($total['pay_count'] > 0) {
                            $total['change_user']++;
                        }
                    }

                }

                $total['click_times'] += $item['click_times'];
                $total['login_days'] += $item['login_days'];
                $total['login_times'] += $item['login_times'];
                $total['order_times'] += $item['order_times'];
                $total['order_count'] += $item['order_count'];
                $total['pay_times'] += $item['pay_times'];
                $total['pay_count'] += $item['pay_count'];

                /*$total['login_days_max'] = $total['login_days_max'] > $item['login_days']
                    ? $total['login_days_max']
                    : $item['login_days'] ;*/
                $total['order_times_max'] = $total['order_times_max'] > $item['order_times']
                    ? $total['order_times_max']
                    : $item['order_times'] ;
                $total['pay_times_max'] = $total['pay_times_max'] > $item['pay_times']
                    ? $total['pay_times_max']
                    : $item['pay_times'] ;
                //  复购
                if ($item['pay_times'] > 1) {
                    $total['repeat_pay']++;
                }

                //  有支付的用户
                if ($total['pay_count'] > 0) {
                    $total['pay_user']++;
                }

            }

        }
        $data = array_filter($data);    //  判空， 如果没有对应的用户信息，则数据不显示

        $data_header = [
            //  order_info
            'plat_form' => '客户归属人',
            'login_times' => '登录次数',
            'click_times' => '点击量',
            'order_count' => '下单总数',
            'pay_count' => '支付单数',
            'login_days' => '登录天数',
            'order_times' => '下单次数',
            'pay_times' => '支付次数',
            'user_id' => '用户ID',
            'user_name' => '用户名',
            'mobile_phone' => '手机号码',
        ];
        $data_array[] = array_values($data_header);

        $data = ArrayHelper::merge($data_array, $data);

        $file_name = '用户行为分析'.date('YmdHis');

        OfficeHelper::excelExport($file_name, $data);

    }

    /**
     * Displays a single Mark model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Mark model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Mark();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Mark model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Mark model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Mark model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Mark the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Mark::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

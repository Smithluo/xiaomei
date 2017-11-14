<?php

namespace backend\controllers;

use backend\models\CouponRecord;
use backend\models\Event;
use backend\models\FullCutRule;
use backend\models\FullCutRuleSearch;
use backend\models\CouponRecordIssueForm;
use common\helper\DateTimeHelper;
use common\helper\TextHelper;
use common\controllers\Controller;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FullCutRuleController implements the CRUD actions for FullCutRule model.
 */
class FullCutRuleController extends Controller
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
     * Lists all FullCutRule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FullCutRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $params = $this->getBasicParams();
        $params['searchModel'] = $searchModel;
        $params['dataProvider'] = $dataProvider;
        $params['isActiveMap'] = Event::$is_active_map;

        return $this->render('index', $params);
    }

    /**
     * Displays a single FullCutRule model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $params = $this->getBasicParams();
        $model = $this->findModel($id);
        $params['model'] = $model;

        //  获取活动类型
        $event = $model->event;
        $eventType = $event->event_type;
        $params['eventType'] = $eventType;

        $couponRecordIssueForm = [];
        $coupon = [];
        //  优惠券活动获取发行Model
        if ($eventType == Event::EVENT_TYPE_COUPON) {
            $couponInfo = FullCutRule::getCouponInfo($model->event_id, $model->rule_id, $event->event_name);

            if (!empty($couponInfo['couponRecordIssueForm'])) {
                $couponRecordIssueForm = $couponInfo['couponRecordIssueForm'];
            }

            if (!empty($couponInfo['coupon'])) {
                $coupon = $couponInfo['coupon'];
            }
        }

        $params['couponRecordIssueForm'] = $couponRecordIssueForm;
        $params['coupon'] = $coupon;

        return $this->render('view', $params);
    }

    /**
     * Creates a new FullCutRule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        $model = new FullCutRule();

        $post = Yii::$app->request->post();
        if (!empty($post)) {
            $event = Event::find()
                ->select(['event_type'])
                ->where(['event_id' => $post['FullCutRule']['event_id']])
                ->one();
            if (!empty($event)) {
                //  仅当活动类型为优惠券时判定当前用户是否有权限操作
                if (
                    $event->event_type != Event::EVENT_TYPE_COUPON
                    || Yii::$app->user->can('/event/create', ['event_type' => $event->event_type])
                ) {
                    if ($model->load($post) && $model->save()) {
                        return $this->redirect(['view', 'id' => $model->rule_id]);
                    }
                } else {
                    throw new NotFoundHttpException('你无权操作优惠券');
                }
            }

        } else {
            $params = $this->getBasicParams();
            $params['model'] = $model;
            return $this->render('create', $params);
        }
    }

    /**
     * Updates an existing FullCutRule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $post = Yii::$app->request->post();
        if (!empty($post)) {
            $event = Event::find()
                ->select(['event_type'])
                ->where(['event_id' => $model->event_id])
                ->one();
            if (!empty($event)) {
                //  仅当活动类型为优惠券时判定当前用户是否有权限操作
                if (
                    $event->event_type != Event::EVENT_TYPE_COUPON
                    || Yii::$app->user->can('/event/update', ['event_type' => $event->event_type])
                ) {
                    if ($model->load($post) && $model->save()) {
                        return $this->redirect(['view', 'id' => $model->rule_id]);
                    }
                } else {
                    throw new NotFoundHttpException('你无权操作优惠券');
                }
            }

        } else {
            $params = $this->getBasicParams();
            $params['model'] = $model;

            //  获取活动类型
            $event = $model->event;
            $eventType = $event->event_type;
            $params['eventType'] = $eventType;

            $couponRecordIssueForm = [];
            $coupon = [];
            //  优惠券活动获取发行Model
            if ($eventType == Event::EVENT_TYPE_COUPON) {
                $couponInfo = FullCutRule::getCouponInfo($model->event_id, $model->rule_id, $event->event_name);

                if (!empty($couponInfo['couponRecordIssueForm'])) {
                    $couponRecordIssueForm = $couponInfo['couponRecordIssueForm'];
                }

                if (!empty($couponInfo['coupon'])) {
                    $coupon = $couponInfo['coupon'];
                }
            }

            $params['couponRecordIssueForm'] = $couponRecordIssueForm;
            $params['coupon'] = $coupon;

            return $this->render('update', $params);
        }
    }

    /**
     * Deletes an existing FullCutRule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = FullCutRule::find()
            ->joinWith('event')
            ->where([FullCutRule::tableName().'.rule_id' => $id])
            ->one();

        if (
            $model->event->event_type != Event::EVENT_TYPE_COUPON
            || Yii::$app->user->can('/event/create', ['event_type' => $model->event->event_type])
        ) {
            $model->delete();

            return $this->redirect(['index']);
        } else {
            throw new NotFoundHttpException('你无权操作优惠券');
        }
    }

    /**
     * 发行优惠券
     */
    public function actionIssue()
    {
        //  判定当前用户是否有权限操作
        if (Yii::$app->user->can('/event/update', ['event_type' => Event::EVENT_TYPE_COUPON])) {
            $params = Yii::$app->request->get('CouponRecordIssueForm');
            $eventId = $params['event_id'];
            $ruleId = $params['rule_id'];

            if ($params['number'] < 1) {
                Yii::$app->session->setFlash('error', '发行量应改是正整数');
            } else {
                $event = Event::find()->where(['event_id' => $eventId])->one();
                $rule = FullCutRule::find()->where(['rule_id' => $ruleId])->one();

                if ($event->is_active && $rule->status) {
                    $number = $params['number'];

                    $model = new CouponRecord();
                    $model->event_id = $eventId;

                    $model->rule_id = $ruleId;
                    $model->received_at = DateTimeHelper::getFormatGMTTimesTimestamp();
                    $model->created_by = Yii::$app->user->identity->id;

                    $successNum = 0;
                    $errorStr = '';
                    for ($i = 1; $i <= $number; $i++) {
                        $couponModel = clone $model;
                        $couponModel->coupon_sn = CouponRecordIssueForm::getNewCouponSn(10);
                        if ($couponModel->save()) {
                            $successNum++;
                        } else {
                            $errorStr .= '第'.$i.'张优惠券发行失败'.PHP_EOL;

                            if ($model->errors) {
                                $errorStr .= TextHelper::getErrorsMsg($model->errors).' -| '.PHP_EOL;
                            }
                        }
                    }


                    //  ；用时 '.$useTime.'s   $endTime = DateTimeHelper::getMicroTime();
                    if ($errorStr) {
                        Yii::$app->session->setFlash('error', '成功发行'.$successNum.'张优惠券'.PHP_EOL.$errorStr);
                    } else {
                        $this->flashSuccess('成功发行'.$successNum.'张优惠券。'.PHP_EOL);
                    }
                } else {
                    Yii::$app->session->setFlash('error', '活动 和 活动规则同时生效才能发行优惠券');
                }
            }

            $this->redirect(['update', 'id' => $ruleId]);
        } else {
            throw new NotFoundHttpException('你无权操作优惠券');
        }
    }

    /**
     * Finds the FullCutRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FullCutRule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FullCutRule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function getBasicParams()
    {
        $eventList = Event::getEventNameMap([Event::EVENT_TYPE_FULL_CUT, Event::EVENT_TYPE_COUPON]);

        return [
            'eventList' => $eventList,
        ];
    }
}

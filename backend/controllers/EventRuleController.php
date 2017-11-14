<?php

namespace backend\controllers;

use backend\models\Event;
use Yii;
use backend\models\EventRule;
use common\models\EventRuleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventRuleController implements the CRUD actions for EventRule model.
 */
class EventRuleController extends Controller
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
     * Lists all EventRule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'giftEventMap' => Event::giftEventMap([Event::EVENT_TYPE_FULL_GIFT, Event::EVENT_TYPE_WULIAO]),
        ]);
    }

    /**
     * Displays a single EventRule model.
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
     * Creates a new EventRule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EventRule();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->rule_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'giftEventMap' => Event::giftEventMap([Event::EVENT_TYPE_FULL_GIFT, Event::EVENT_TYPE_WULIAO]),
            ]);
        }
    }

    /**
     * Updates an existing EventRule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->rule_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'giftEventMap' => Event::giftEventMap([Event::EVENT_TYPE_FULL_GIFT, Event::EVENT_TYPE_WULIAO]),
            ]);
        }
    }

    /**
     * Deletes an existing EventRule model.
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
     * Finds the EventRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventRule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventRule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

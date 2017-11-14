<?php

namespace backend\controllers;

use backend\models\Gallery;
use backend\models\MoreGalleryImg;
use Yii;
use backend\models\GalleryImg;
use backend\models\GalleryImgSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GalleryImgController implements the CRUD actions for GalleryImg model.
 */
class GalleryImgController extends Controller
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
     * Lists all GalleryImg models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GalleryImgSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $galleryMap = Gallery::getGalleryList();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'galleryMap' => $galleryMap,
        ]);
    }

    /**
     * Displays a single GalleryImg model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = GalleryImg::find()
            ->joinWith('gallery')
            ->where(['img_id' => $id])
            ->one();
        $galleryMap = [$model->gallery->gallery_id => $model->gallery->gallery_name];

        return $this->render('view', [
            'model' => $model,
            'galleryMap' => $galleryMap,
        ]);
    }

    /**
     * Creates a new GalleryImg model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GalleryImg();
        $model->setScenario('insert');

        $post = Yii::$app->request->post();
        if (!empty($post)) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->img_id]);
            }
        }

        $galleryMap = Gallery::getGalleryList();
        return $this->render('create', [
            'model' => $model,
            'galleryMap' => $galleryMap,
        ]);
    }

    /**
     * Updates an existing GalleryImg model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->img_id]);
        } else {
            $galleryMap = Gallery::getGalleryList();

            return $this->render('update', [
                'model' => $model,
                'galleryMap' => $galleryMap,
            ]);
        }
    }

    /**
     * Deletes an existing GalleryImg model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the GalleryImg model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GalleryImg the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GalleryImg::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

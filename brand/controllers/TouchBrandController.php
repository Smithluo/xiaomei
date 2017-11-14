<?php

namespace brand\controllers;

use common\helper\DateTimeHelper;
use Yii;
use brand\models\TouchBrand;
use common\models\TouchBrandSearch;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TouchBrandController implements the CRUD actions for TouchBrand model.
 */
class TouchBrandController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update'],
                'rules' => [
                    [
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
     * Lists all TouchBrand models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TouchBrandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TouchBrand model.
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
     * Creates a new TouchBrand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TouchBrand();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->brand_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TouchBrand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $model = $this->findModel(Yii::$app->request->post('brand_id'));
        $brand_qualification = Yii::$app->request->post('brand_qualification');
        preg_match_all("/(src)\=\"(.*?)\"/i", $brand_qualification, $match);
        $replace = [];

        if (is_array($match['2'])) {
            foreach ($match['2'] as $picture) {
                //  "src="data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAA...."
                list($type, $data) = explode(',', $picture);
                if(strstr($type,'image/jpeg')!==''){
                    $ext = '.jpg';
                }elseif(strstr($type,'image/gif')!==''){
                    $ext = '.gif';
                }elseif(strstr($type,'image/png')!==''){
                    $ext = '.png';
                }

                $picture_name = DateTimeHelper::getFormatMtime();
                $img_base_url = \Yii::$app->params['shop_config']['img_base_url'];
                $relative_path = '/data/attached/image/';
                $date_now = DateTimeHelper::getFormatDateNow();

                $picture_path = $img_base_url.$relative_path.$date_now.'/';

                if (!is_dir($picture_path)) {
                    mkdir($picture_path, '0777');
                }
                file_put_contents($picture_path.$picture_name.$ext, base64_decode($data), true);
                $replace[] = $relative_path.$date_now.'/'.$picture_name.$ext;
            }
            $brand_qualification =str_replace($match[2], $replace, $brand_qualification);
        }
        $model->brand_qualification = addslashes(htmlspecialchars($brand_qualification));
        if ($model->save()) {
            echo json_encode([
                'code' => 0,
                'msg' => '品牌资质更新成功',
            ]);
        } else {
            echo json_encode([
                'code' => 1,
                'msg' => '品牌资质更新失败，请重试',
            ]);
        }
    }

    /**
     * Deletes an existing TouchBrand model.
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
     * Finds the TouchBrand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TouchBrand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TouchBrand::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

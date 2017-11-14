<?php

namespace brand\controllers;


use common\helper\TextHelper;
use common\models\TouchBrand;
use Yii;
use common\models\Brand;
use common\models\BrandQuery;
use common\models\BrandUser;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\helper\ImageHelper;

/**
 * BrandController implements the CRUD actions for Brand model.
 */
class BrandController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'update'],
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
     * Lists all Brand models.
     * @return mixed
     */
    public function actionIndex()
    {
        $brand_list_id_array = BrandUser::getBrandIdList();   //  当前用户旗下的品牌id数组
        $rs = Brand::find()->select('brand_id, brand_name, brand_depot_area, brand_logo_two, brand_desc,
                short_brand_desc, brand_desc_long')
            ->where(['brand_id' => $brand_list_id_array])
            ->all();

        $brand_list = [];
        foreach ($rs as $brand) {
            $touch_brand = TouchBrand::find()
                ->select('brand_qualification, brand_content')
                ->where(['brand_id' => $brand->brand_id])
                ->asArray()
                ->one();

            $brand_list[$brand->brand_id] = [
                'brand_id'          => $brand->brand_id,
                'brand_name'        => $brand->brand_name,
                'brand_depot_area'  => $brand->brand_depot_area,
                'brand_logo_two'    => ImageHelper::get_image_path($brand->brand_logo_two),
                'brand_desc'        => $brand->brand_desc,
                'short_brand_desc'  => $brand->short_brand_desc,
                'brand_desc_long'   => $brand->brand_desc_long,
                'brand_qualification'     => TextHelper::formatRichText($touch_brand['brand_qualification']),
                'brand_content'     => TextHelper::formatRichText($touch_brand['brand_content']),
            ];
        }

        return $this->render('index', [
            'brand_list' => $brand_list,
            'r_version' => $r_version = \Yii::$app->params['r_version'],
        ]);
    }

    /**
     * Displays a single Brand model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->brand_logo_two = ImageHelper::get_image_path($model->brand_logo_two);
        $model->brand_policy = ImageHelper::get_image_path($model->brand_policy);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Brand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Brand();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->brand_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Brand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->brand_id]);
        } else {
            $brand_logo_two_url = ImageHelper::get_image_path($model->brand_logo_two);
            $brand_policy_url = ImageHelper::get_image_path($model->brand_policy);
            return $this->render('update', [
                'model' => $model,
                'brand_logo_two_url' => $brand_logo_two_url,
                'brand_policy_url' => $brand_policy_url,
            ]);
        }
    }

    /**
     * Deletes an existing Brand model.
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
     * Finds the Brand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Brand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Brand::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

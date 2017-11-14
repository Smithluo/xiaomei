<?php

namespace backend\controllers;

use backend\models\Brand;
use common\helper\DateTimeHelper;
use Yii;
use common\models\BrandUser;
use common\models\BankInfo;
use common\models\BrandAdmin;
use backend\models\BrandUsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * BrandUserController implements the CRUD actions for BrandUser model.
 */
class BrandUserController extends Controller
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
                        'actions' => ['index', 'update', 'create', 'view', 'delete'],
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
     * Lists all BrandUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BrandUsersSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search(array_merge($params, ['is_brand_user' => true]));

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BrandUser model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $bind_brand_map = Brand::getBindBrandMap($model->user_id);
        $brand_name_list = '';
        if ($bind_brand_map) {
            foreach ($bind_brand_map as $brand_id => $brand_name) {
                $brand_name_list .= $brand_id.' : '.$brand_name.'<br />';
            }
        }

        return $this->render('view', [
            'model' => $model,
            'brand_name_list' => $brand_name_list,
        ]);
    }

    /**
     * Creates a new BrandUser model.
     * 添加品牌商，同时创建品牌商的联系人信息
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BrandUser();
        $bankModel = new BankInfo();
        $brandAdminModel = new BrandAdmin();

        $post = Yii::$app->request->post();

        if (isset($post['BrandUser']['brand_id_list'])) {
            $brand_id_list = $post['BrandUser']['brand_id_list'];
            unset($post['BrandUser']['brand_id_list']);
            $model->brand_id_list = implode(',', $brand_id_list);
        }

        $un_bind_brand_map = Brand::getUnBindBrandMap();

        if ($model->load($post) && $bankModel->load($post) && $brandAdminModel->load($post)
        ) {
            $model->setPassword($model->password);
            $model->ec_salt = null;

            if ($brandAdminModel->save()) {
                $model->brand_admin_id = $brandAdminModel->id;
            } else {
                die([
                    'code' => 1,
                    'msg' => '品牌联系人信息保存失败'
                ]);
            }

            if ($bankModel->save()) {
                $model->bank_info_id = $bankModel->id;
            } else {
                die([
                    'code' => 2,
                    'msg' => '品牌商银行账户信息保存失败'
                ]);
            }

            $model->reg_time = DateTimeHelper::getFormatGMTTimesTimestamp(time());
            $model->birthday = '0000-01-01';
            $model->last_time = '1970-01-01 00:00:00';
            if($model->save()) {

                $result = Brand::setSupplier($brand_id_list, $model->user_id);
                if ($result['code']) {
                    die(json_encode($result));
                }
                return $this->redirect(['view', 'id' => $model->user_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'bankModel' => $bankModel,
            'brandAdminModel' => $brandAdminModel,
            'brand_map' => $un_bind_brand_map,
        ]);
    }

    /**
     * Updates an existing BrandUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $bankModel = BankInfo::findOne($model->bank_info_id);
        $brandAdminModel = BrandAdmin::findOne($model->brand_admin_id);

        if ($post = Yii::$app->request->post()) {
            if (isset($post['BrandUser']['brand_id_list'])) {
                $brand_id_list = $post['BrandUser']['brand_id_list'];
                unset($post['BrandUser']['brand_id_list']);
                $model->brand_id_list = implode(',', $brand_id_list);
            }

            if ($bankModel->load($post) && $bankModel->save() &&
                $brandAdminModel->load($post) && $brandAdminModel->save() &&
                $model->load($post) && $model->save()
            ) {
                $result = Brand::setSupplier($brand_id_list, $model->user_id);
                if ($result['code']) {
                    die(json_encode($result));
                }

                return $this->redirect(['view', 'id' => $model->user_id]);
            }
        } else {
            $bind_brand_map = Brand::getBindBrandMap($model->user_id);
            $brand_map = Brand::getBrandMaShow($model->user_id);
            $model->brand_id_list = array_keys($bind_brand_map);

            return $this->render('update', [
                'model' => $model,
                'bankModel' => $bankModel,
                'brandAdminModel' => $brandAdminModel,
                'brand_map' => $brand_map,
                'bind_brand_map' => $bind_brand_map,
            ]);
        }
    }

    /**
     * Deletes an existing BrandUser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model) {
            Brand::updateAll(['supplier_user_id' => 0], ['supplier_user_id' => $model->user_id]);
            $model->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the BrandUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return BrandUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BrandUser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

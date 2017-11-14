<?php

namespace backend\controllers;

use backend\models\CategoryUploadForm;
use backend\models\Goods;
use backend\models\GoodsCat;
use common\helper\CacheHelper;
use common\helper\CategoryHelper;
use common\models\WechatAlbum;
use yii\web\UploadedFile;
use Yii;
use backend\models\Category;
use backend\models\CategorySearch;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
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
                        'actions' => ['index', 'update', 'create', 'view', 'delete' , 'export', 'import'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['POST'],
//                ],
//            ],
        ];
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $categories = Category::find()->joinWith('children children')->where([Category::tableName().'.parent_id' => 0])->all();

        $query = new Query();
        $goodsCounts = $query->select(['cat_id', 'count(*) as goodsCount'])->from(Goods::tableName())->groupBy('cat_id')->all();

        $query = new Query();
        $goodsCatCounts = $query->select(['cat_id', 'count(*) as goodsCount'])->from(GoodsCat::tableName())->groupBy('cat_id')->all();

        foreach($categories as $category) {
            $this->loopCategorys($category, $goodsCounts, $goodsCatCounts);
        }

        $level = 0;
        $this->generateCatTreeName($categories, $level);

        $importModel = new CategoryUploadForm();

        return $this->render('index', [
            'categories' => $categories,
            'importModel' => $importModel,
        ]);
    }

    /**
     * 生成分级的名字，就是加前缀便于区分级别
     * @param $categories
     * @param $level
     */
    private function generateCatTreeName($categories, &$level) {
        foreach($categories as $category) {
            $catPre = '';
            for($i = 0; $i < $level; ++$i) {
                $catPre .= '|----';
            }
            $category->cat_name = $catPre.$category->cat_name;

            if(count($category->children) > 0) {
                ++$level;
                $this->generateCatTreeName($category->children, $level);
                --$level;
            }
        }
    }


    /**
     * 遍历所有分类，计算商品数量
     * @param $category
     * @param $goodsCounts
     * @param $goodsCatCounts
     */
    private function loopCategorys(&$category, &$goodsCounts, &$goodsCatCounts) {
        foreach($goodsCounts as $goodsCount) {
            if($category->cat_id == $goodsCount['cat_id']) {
                $category->goodsCount += $goodsCount['goodsCount'];
                unset($goodsCount['cat_id']);
                break;
            }
        }

        foreach($goodsCatCounts as $goodsCatCount) {
            if($category->cat_id == $goodsCatCount['cat_id']) {
                $category->goodsCount += $goodsCatCount['goodsCount'];
                unset($goodsCatCount['cat_id']);
                break;
            }
        }

        if(count($category->children) > 0) {
            foreach($category->children as $child) {
                $this->loopCategorys($child, $goodsCounts, $goodsCatCounts);
            }
        }
    }

    /**
     * Displays a single Category model.
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
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();


        $allCategories = Category::getCategoryTree(0);

        $albums = WechatAlbum::find()->asArray()->indexBy('album_id')->all();
        $albums = array_column($albums, 'album_name', 'album_id');
        $albums[0] = '请选择';

        //  价格分级 grade 不允许为空，给默认值 0
        $model->grade = 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cat_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'allCategories' => $allCategories,
                'albums' => $albums,
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $categories = Category::find()->with([
            'children',
            'children.children',
            'children.children.children',
            'children.children.children.children',
            'children.children.children.children.children',
        ])->where([Category::tableName().'.parent_id' => 0])->all();
        $allCategories = [];
        $level = 0;
        Category::generateCatTree($categories, $allCategories, $level);

        $albums = WechatAlbum::find()->asArray()->indexBy('album_id')->all();
        $albums = array_column($albums, 'album_name', 'album_id');
        $albums[0] = '请选择';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cat_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'allCategories' => $allCategories,
                'albums' => $albums,
            ]);
        }
    }

    /**
     * 删除指定分类及其所有子分类
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $category = CacheHelper::getCategoryCache();
        CacheHelper::getCategoryChildrenIds($ids , $id, $category );

        $model = Category::findOne($id);
        if ($model->parent_id == 0) {
            Yii::$app->session->setFlash('error', '根目录不可删除');
        } else {
            Category::deleteAll(['cat_id' => $ids]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionImport() {
        $discountImportModel = new CategoryUploadForm();

        if (Yii::$app->request->isPost) {
            $discountImportModel->file = UploadedFile::getInstance($discountImportModel, 'file');
            $discountImportModel->import();
        }

        return $this->redirect(['index']);
    }

    public function actionExport()
    {
        $categoryList = Category::find()->select([
            'cat_id',
            'cat_name',
            'cat_desc',
            'parent_id',
            'sort_order',
            'is_show',
            'album_id',
            'brand_list'
        ])->all();

        \moonland\phpexcel\Excel::export([
            'format' => 'Excel5',
            'fileName' => '分类列表',
            'models' => $categoryList,
            'columns' => [
                'cat_id',
                'cat_name',
                'parent_id',
            ],
        ]);

    }
}

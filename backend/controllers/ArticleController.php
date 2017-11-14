<?php

namespace backend\controllers;

use backend\models\Gallery;
use backend\models\Region;
use backend\models\ResourceSite;
use backend\models\TouchArticle;
use common\helper\DateTimeHelper;
use common\models\ArticleCat;
use common\models\Category;
use kartik\grid\EditableColumnAction;
use Yii;
use common\models\Article;
use common\models\ArticleSearch;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
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
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'add_time',
                ],
                'value' => function() {return date('U');}
            ]
        ];
    }

    //加入图片上传能力
    public function actions()
    {
        $actionValue = [
            'class' => EditableColumnAction::className(),
            'modelClass' => Article::className(),
            'outputValue' => function($model, $attribute, $key, $index) {
                if ($attribute == 'cat_id') {
                    if (empty(ArticleCat::getCatMap()[$model->$attribute])) {
                        return null;
                    }
                    return ArticleCat::getCatMap()[$model->$attribute];
                } elseif ($attribute == 'is_open') {
                    return $model->$attribute > 0 ? '显示' : '不显示';
                } elseif ($attribute == 'resource_site_id') {
                    return empty(ResourceSite::getResourceSiteMap()[$model->$attribute]) ? '': ResourceSite::getResourceSiteMap()[$model->$attribute];
                } elseif ($attribute == 'country') {
                    return empty(Region::getCountryMap()[$model->$attribute]) ? '': Region::getCountryMap()[$model->$attribute];
                } elseif ($attribute == 'scene') {
                    return empty(Article::$sceneMap[$model->$attribute]) ? '': Article::$sceneMap[$model->$attribute];
                } elseif ($attribute == 'link_cat') {
                    return empty(Category::getCategoryTree(299)[$model->$attribute]) ? '': Category::getCategoryTree(299)[$model->$attribute];
                }
                return $model->$attribute;
            },
            'outputMessage' => function($model, $attribute, $key, $index) {
                if ($model->hasErrors()) {
                    $errors = $model->getFirstError($attribute);
                    return $errors;
                }
                return '';
            },
        ];
        return ArrayHelper::merge(parent::actions(), [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    'imageRoot' => Yii::getAlias('@mRoot').'/data/attached',
                    'imagePathFormat' => '/image/{yyyy}{mm}{dd}/{time}{rand:6}',
                    'imageUrlPrefix' => 'http://img.xiaomei360.com',
                    'videoRoot' => Yii::getAlias('@mRoot'). '/data/attached',
                    'videoPathFormat' => '/video/{yyyy}{mm}{dd}/{time}{rand:6}',
                    'videoUrlPrefix' => 'http://img.xiaomei360.com',
                    'fileRoot' => Yii::getAlias('@mRoot/data/attached'),
                    'filePathFormat' => '/file/{yyyy}{mm}{dd}/{time}{rand:6}',
                    'fileUrlPrefix' => 'http://img.xiaomei360.com',
                ],
            ],
            'edit-value' => $actionValue,
        ]);
    }

    /**
     * Lists all Article models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sceneMap' => Article::$sceneMap,
            'categoryTree' => Category::getCategoryTree(299),   //  按品类
            'countryMap' => Region::getCountryMap(),
            'resourceTypeMap' => TouchArticle::$resourceTypeMap,
            'resourceSiteMap' => ResourceSite::getResourceSiteMap(),
            'galleryMap' => Gallery::getGalleryList(),
        ]);
    }

    /**
     * Displays a single Article model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'sceneMap' => Article::$sceneMap,
            'categoryTree' => Category::getCategoryTree(299),   //  按品类
            'countryMap' => Region::getCountryMap(),
            'resourceTypeMap' => TouchArticle::$resourceTypeMap,
            'resourceSiteMap' => ResourceSite::getResourceSiteMap(),
            'galleryMap' => Gallery::getGalleryList(),
        ]);
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Article();
        $model->setScenario('insert');

        if ($model->load(Yii::$app->request->post())) {
            $model->add_time = DateTimeHelper::gmtime();
            if (empty($model->content)) {
                $model->open_type = 1;
            }
            else {
                $model->open_type = 0;
            }
            $model->click = rand(50, 200);
            if ($model->save()) {
                return $this->redirect(['update', 'id' => $model->article_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'sceneMap' => Article::$sceneMap,
            'categoryTree' => Category::getCategoryTree(299),   //  按品类
            'countryMap' => Region::getCountryMap(),
            'resourceTypeMap' => TouchArticle::$resourceTypeMap,
            'resourceSiteMap' => ResourceSite::getResourceSiteMap(),
            'galleryMap' => Gallery::getGalleryList(),
        ]);
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');

        if (!empty($model->content)) {
            $model->content = htmlspecialchars_decode(stripslashes($model->content));
        }

        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $model->add_time = DateTimeHelper::gmtime();
            if (empty($model->content)) {
                $model->open_type = 1;
            }
            else {
                $model->open_type = 0;
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->article_id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'sceneMap' => Article::$sceneMap,
            'categoryTree' => Category::getCategoryTree(299),   //  按品类
            'countryMap' => Region::getCountryMap(),
            'resourceTypeMap' => TouchArticle::$resourceTypeMap,
            'resourceSiteMap' => ResourceSite::getResourceSiteMap(),
            'galleryMap' => Gallery::getGalleryList(),
        ]);
    }

    /**
     * Deletes an existing Article model.
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
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

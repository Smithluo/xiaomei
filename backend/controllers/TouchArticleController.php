<?php

namespace backend\controllers;

use backend\models\ArticleImageUploader;
use backend\models\Gallery;
use backend\models\GalleryImg;
use backend\models\ResourceSite;
use backend\models\TouchArticleCat;
use common\helper\DateTimeHelper;
use common\helper\TextHelper;
use common\models\Article;
use kartik\editable\Editable;
use kartik\grid\EditableColumnAction;
use moonland\phpexcel\Excel;
use Yii;
use backend\models\TouchArticle;
use common\models\TouchArticleSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * TouchArticleController implements the CRUD actions for TouchArticle model.
 */
class TouchArticleController extends Controller
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

    //加入图片上传能力
    public function actions()
    {
        $actionValue = [
            'class' => EditableColumnAction::className(),
            'modelClass' => TouchArticle::className(),
            'outputValue' => function($model, $attribute, $key, $index) {
                if (empty($model->add_time)) {
                    $model->add_time = DateTimeHelper::getFormatCNDateTime(DateTimeHelper::gmtime());
                }
                if ($attribute == 'cat_id') {
                    if (empty(TouchArticleCat::getTouchCatMap()[$model->$attribute])) {
                        return null;
                    }
                    return TouchArticleCat::getTouchCatMap()[$model->$attribute];
                }
                return ''.$model->$attribute;
            },
            'outputMessage' => function($model, $attribute, $key, $index) {
                if ($model->hasErrors()) {
                    $errors = $model->getFirstError($attribute);
                    return $errors;
                }
                return '';
            },
            'showModelErrors' => true,
            'errorOptions' => ['header' => '222'],
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

                    'fileRoot' => Yii::getAlias('@mRoot').'/data/attached',
                    'filePathFormat' => '/download/{yyyy}{mm}{dd}/{time}{rand:6}',
                    'fileUrlPrefix' => 'http://img.xiaomei360.com',
                ],
            ],
            'edit-value' => $actionValue,
        ]);
    }

    private function get_cats_list(&$categories, $cat) {
        $categories[] = $cat['cat_id'];
        if (!empty($cat['children'])) {
            foreach ($cat['children'] as $child) {
                $this->get_cats_list($categories, $child);
            }
        }
    }

    /**
     * Lists all TouchArticle models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TouchArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $resourceTypeMap = TouchArticle::$resourceTypeMap;

        $galleryMap = Gallery::getGalleryList();
        $resourceSiteMap = ResourceSite::getResourceSiteMap();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'resourceTypeMap' => $resourceTypeMap,
            'resourceSiteMap' => $resourceSiteMap,
            'galleryMap' => $galleryMap,
        ]);
    }

    /**
     * Displays a single TouchArticle model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => TouchArticle::find()
                ->joinWith([
                    'resourceSite',
                    'gallery'
                ])
                ->where(['article_id' => $id])
                ->one(),
            'resourceTypeMap' => TouchArticle::$resourceTypeMap,
        ]);
    }

    /**
     * Creates a new TouchArticle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TouchArticle();
        $model->setScenario('insert');

        if ($model->load(Yii::$app->request->post())) {
            if (empty($model->content)) {
                $model->open_type = 1;
            }
            else {
                $model->open_type = 0;
            }
            $model->click = rand(100, 300);
//            $model->add_time = DateTimeHelper::getFormatCNDateTime(DateTimeHelper::gmtime());
            $model->add_time = DateTimeHelper::gmtime();
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->article_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'resourceTypeMap' => TouchArticle::$resourceTypeMap,
            'resourceSiteMap' => ResourceSite::getResourceSiteMap(),
            'galleryMap' => Gallery::getGalleryList(),
        ]);
    }

    public function actionCopy($id)
    {
        $touchArticle = TouchArticle::find()
            ->joinWith([
                'touchArticleCat',
                'touchArticleCat.articleCat',
            ])->where(['article_id' => $id])
            ->one();
        if (!$touchArticle) {
            Yii::$app->session->setFlash('error', 'ID对应的文章不存在，请刷新重试');
            return $this->redirect('/touch-article/index');
        } elseif (
            empty($touchArticle->touchArticleCat) ||
            empty($touchArticle->touchArticleCat->articleCat) ||
            empty($touchArticle->touchArticleCat->articleCat->cat_id)
        ) {
            Yii::$app->session->setFlash('error', '您要复制的文章对应的文章分类在PC文章分类中不存在，请先创建PC文章分类');
            return $this->redirect('/touch-article/index');
        } else {
            $article = new Article();
            $article->setScenario('insert');
            $article->cat_id = $touchArticle->touchArticleCat->articleCat->cat_id;
            $article->title = $touchArticle->title;
            $article->brand_id = $touchArticle->brand_id;
            $article->content = htmlspecialchars_decode(stripslashes($touchArticle->content));
            $article->author = $touchArticle->author;
            $article->author_email = $touchArticle->author_email;
            $article->keywords = $touchArticle->keywords;
            $article->is_open = $touchArticle->is_open;
            $article->add_time = DateTimeHelper::gmtime();
            $article->file_url = $touchArticle->file_url;
            $article->open_type = $touchArticle->open_type;
            $article->link = $touchArticle->link;
            $article->description = $touchArticle->description;
            $article->tag = $touchArticle->tag;
            $article->sort_order = 128;
            $article->resource_type = $touchArticle->resource_type;
            $article->gallery_id = $touchArticle->gallery_id;
            $article->resource_site_id = $touchArticle->resource_site_id;
            $article->country = '';

            if ($article->save()) {
                Yii::$app->session->setFlash('success', '复制文章成功。 文章封面图片请重新上传 285 * 210px');
                return $this->redirect('/article/update?id='.$article->article_id);
            } else {
                $errorMsg = '';
                if (!empty($article->errors)) {
                    $errorMsg = TextHelper::getErrorsMsg($article->errors);
                }

                Yii::$app->session->setFlash('error', '复制文章失败。 错误原因：'.$errorMsg);
                return $this->redirect('/touch-article/index?id=');
            }
        }
    }

    /**
     * Updates an existing TouchArticle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');

        if ($model->load(Yii::$app->request->post())) {
            if (empty($model->content)) {
                $model->open_type = 1;
            }
            else {
                $model->open_type = 0;
            }

            if (empty($model->add_time)) {
//                $model->add_time = DateTimeHelper::getFormatCNDateTime(DateTimeHelper::gmtime());
                $model->add_time = DateTimeHelper::gmtime();
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->article_id]);
            }
        }

        if (!empty($model->content)) {
            $model->content = htmlspecialchars_decode(stripslashes($model->content));
        }

        $galleryMap = Gallery::getGalleryList();
        $resourceSiteMap = ResourceSite::getResourceSiteMap();
        return $this->render('update', [
            'model' => $model,
            'resourceTypeMap' => TouchArticle::$resourceTypeMap,
            'resourceSiteMap' => $resourceSiteMap,
            'galleryMap' => $galleryMap,
        ]);
    }

    /**
     * Deletes an existing TouchArticle model.
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
     * Finds the TouchArticle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TouchArticle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TouchArticle::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionExport() {
        $searchModel = new TouchArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        Excel::export([
            'format' => 'Excel5',
            'fileName' => '微信文章列表'. DateTimeHelper::getFormatCNDateTime(time()),
            'models' => $dataProvider->getModels(),
            'columns' => [
                'article_id',
                'title',
                'click',
                [
                    'attribute' => 'resource_type',
                    'value' => function($model) {
                        return empty(TouchArticle::$resourceTypeMap[$model->resource_type]) ? null : TouchArticle::$resourceTypeMap[$model->resource_type];
                    },
                ],
            ], //without header working, because the header will be get label from attribute label.
            'headers' => [
                'article_id' => '文章ID',
                'title' => '标题',
                'click' => '浏览量',
                'resource_type' => '类型',
            ],
        ]);
    }
}

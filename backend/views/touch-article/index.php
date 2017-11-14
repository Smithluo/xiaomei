<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TouchArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '微信文章列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="touch-article-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel, 'resourceTypeMap' => $resourceTypeMap]); ?>

    <p>
        <?= Html::a('新建微信文章', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    $url = str_replace(
        '/touch-article/index',
        '/touch-article/export',
        urldecode(Yii::$app->request->url)
    );

    echo Html::a('导出微信文章',
        $url,
        ['class' => 'btn btn-default']);
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            'article_id',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'cat_id',
                'value' => function ($model) {
                    if (empty(\common\models\TouchArticleCat::getTouchCatMap()[$model->cat_id])) {
                        return null;
                    }
                    return \common\models\TouchArticleCat::getTouchCatMap()[$model->cat_id];
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '分类',
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'options' => [
                            'data' => \common\models\TouchArticleCat::getTouchCatMap(),
                        ],
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/touch-article/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'attribute' => 'resource_type',
                'value' => function ($model) use ($resourceTypeMap) {
                    return isset($resourceTypeMap[$model->resource_type])
                        ? $resourceTypeMap[$model->resource_type]
                        : '';
                }
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'title',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '标题',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/touch-article/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
//            'author',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'sort_order',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '排序值，大的在前面显示',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/touch-article/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            // 'author_email:email',
            // 'keywords',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_open',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否显示',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/touch-article/edit-value'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                    ];
                },
                'pageSummary' => true,
            ],
            // 'add_time:datetime',
            // 'file_url:url',
            'open_type',
            'link',
            'click',
            [
                'attribute' => 'resource_type',
                'value' => function ($model) {
                    return isset($resourceTypeMap[$model->resource_type])
                        ? $resourceTypeMap[$model->resource_type]
                        : '';
                },
            ],
            [
                'attribute' => 'gallery_id',
                'value' => function ($model) {
                    return isset($galleryMap[$model->gallery_id])
                        ? $galleryMap[$model->gallery_id]
                        : '';
                },
            ],
            [
                'attribute' => 'resource_site_id',
                'value' => function ($model) {
                    return isset($resourceSiteMap[$model->resource_site_id])
                        ? $resourceSiteMap[$model->resource_site_id]
                        : '';
                },
            ],
            // 'description',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {update} {copy}',

                'buttons' => [
                    'copy' => function ($url, $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-adjust"></span>',
                            '/touch-article/copy?id='.$model->article_id,
                            ['title' => '复制为PC文章']
                        );
                    },
                ]
            ],
        ],
    ]); ?>
</div>

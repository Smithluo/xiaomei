<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '文章列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render(
            '_search',
            [
                'model' => $searchModel,
                'sceneMap' => $sceneMap,
                'categoryTree' => $categoryTree,
                'countryMap' => $countryMap,
                'resourceTypeMap' => $resourceTypeMap,
                'resourceSiteMap' => $resourceSiteMap,
                'galleryMap' => $galleryMap,
            ]
    ); ?>

    <p>
        <?= Html::a('新建文章', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            'article_id',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'cat_id',
                'value' => function ($model) {
                    if (empty(\common\models\ArticleCat::getCatMap()[$model->cat_id])) {
                        return null;
                    }
                    return \common\models\ArticleCat::getCatMap()[$model->cat_id];
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '分类',
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'options' => [
                            'data' => \common\models\ArticleCat::getCatMap(),
                        ],
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/article/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'title',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '标题',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/article/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            'keywords',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_open',
                'value' => function ($model) {
                    return (isset($model->is_open) && $model->is_open == 1) ? '显示' : '不显示';
                },
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否显示',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/article/edit-value'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                        'pluginOptions' => [
                            'onText' => '显示',
                            'offText' => '不显示',
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'attribute' => 'add_time',
                'value' => function ($model) {
                    return \common\helper\DateTimeHelper::getFormatCNDateTime($model->add_time);
                }
            ],
            // 'file_url:url',
            'open_type',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'link',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '外链链接',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/article/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'sort_order',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '排序值',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/article/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            'complex_order',
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'click',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '点击次数',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/article/edit-value'],
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
                },
            ],
            [
                'attribute' => 'gallery_id',
                'value' => function ($model) use ($galleryMap) {
                    return isset($galleryMap[$model->gallery_id])
                        ? $galleryMap[$model->gallery_id]
                        : '';
                },
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'resource_site_id',
                'value' => function ($model) use ($resourceSiteMap) {
                    return isset($resourceSiteMap[$model->resource_site_id])
                        ? $resourceSiteMap[$model->resource_site_id]
                        : '';
                },
                'editableOptions' => function($model, $key, $index) use ($resourceSiteMap) {
                    return [
                        'header' => '来源站点',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/article/edit-value'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'options' => [
                            'data' => $resourceSiteMap,
                            'options' => [
                                'placeholder' => '选择来源站点'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'country',
                'value' => function ($model) use ($countryMap) {
                    return isset($countryMap[$model->country])
                        ? $countryMap[$model->country]
                        : '';
                },
                'editableOptions' => function($model, $key, $index) use ($countryMap) {
                    return [
                        'header' => '来源站点',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/article/edit-value'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'options' => [
                            'data' => $countryMap,
                            'options' => [
                                'placeholder' => '选择区域维度'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'link_cat',
                'value' => function ($model) use ($categoryTree) {
                    return isset($categoryTree[$model->link_cat])
                        ? $categoryTree[$model->link_cat]
                        : '';
                },
                'editableOptions' => function($model, $key, $index) use ($categoryTree) {
                    return [
                        'header' => '品类维度',
                        'size' => 'md',
                        'formOptions' => [
                            'action' => ['/article/edit-value'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'options' => [
                            'data' => $categoryTree,
                            'options' => [
                                'placeholder' => '选择品类维度'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'scene',
                'value' => function ($model) use ($sceneMap) {
                    return isset($sceneMap[$model->scene])
                        ? $sceneMap[$model->scene]
                        : '';
                },
                'editableOptions' => function($model, $key, $index) use ($sceneMap) {
                    return [
                        'header' => '来源站点',
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/article/edit-value'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                        'options' => [
                            'data' => $sceneMap,
                            'options' => [
                                'placeholder' => '选择应用场景'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ],
                    ];
                },
                'options' => [
                    'style' => 'width: 300px',
                ],
                'pageSummary' => true,
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
//                'template' => '{view} {update}'
                'buttons' => [
                    'view'=>function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url);
                    },
                    'update'=>function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                    },
//                    'delete'=> function ($url, $model) {
//                        if ($model->cat_id < 0) {
//                            return '';
//                        }
//                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url);
//                    }
                ],
            ],
        ],
    ]); ?>
</div>

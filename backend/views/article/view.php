<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->article_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->article_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'article_id',
            'cat_id',
            'title',
            'content:ntext',
            'author',
            'author_email:email',
            'keywords',
            'article_type',
            'is_open',
            'add_time:datetime',
            'file_url:url',
            'open_type',
            'link',
            'description',
            'sort_order',
            'complex_order',
            'click',
            [
                'attribute' => 'resource_type',
                'value' => isset($resourceTypeMap[$model->resource_type])
                    ? $resourceTypeMap[$model->resource_type]
                    : '',
            ],
            [
                'attribute' => 'gallery_id',
                'value' => isset($model->gallery)
                    ? $model->gallery->gallery_name
                    : '',
            ],
            [
                'attribute' => 'resource_site_id',
                'value' => !empty($model->resource_site_id) && !empty($model->resource_site_id)
                    ? $model->resourceSite->site_name
                    : '',
            ],
            [
                'attribute' => 'country',
                'value' => isset($countryMap[$model->country])
                    ? $countryMap[$model->country]
                    : '',
            ],
            [
                'attribute' => 'link_cat',
                'value' => isset($categoryTree[$model->link_cat])
                    ? $categoryTree[$model->link_cat]
                    : '',
            ],
            [
                'attribute' => 'scene',
                'value' => isset($sceneMap[$model->scene])
                    ? $sceneMap[$model->scene]
                    : '',
            ],
        ],
    ]) ?>

</div>

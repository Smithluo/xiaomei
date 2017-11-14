<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helper\DateTimeHelper;

/* @var $this yii\web\View */
/* @var $model common\models\TouchArticle */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '微信文章列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="touch-article-view">

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
            'is_open',
            [
                'attribute' => 'add_time',
                'value' => !empty($model->add_time)
                    ? DateTimeHelper::getFormatCNDateTime($model->add_time)
                    : '未设置',
            ],
            'file_url:url',
            'open_type',
            'link',
            'description',
            'click',
            'brand_id',
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
                'value' => !empty($model->resource_site_id) && !empty($model->resourceSite)
                    ? $model->resourceSite->site_name
                    : '',
            ],
        ],
    ]) ?>

</div>

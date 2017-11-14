<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TouchArticleCatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '微信文章分类列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="touch-article-cat-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新建微信文章分类', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'cat_id',
            'cat_name',
            'keywords',
            'cat_desc',
            'sort_order',
            [
                'attribute' => 'show_in_news',
                'value' => function($model) {
                    if ($model->show_in_news) {
                        return '是';
                    }
                    return '否';
                }
            ],
            [
                'attribute' => 'parent_id',
                'value' => function ($model) {
                    if (empty($model->parent_id)) {
                        return '顶级分类';
                    }
                    return $model->parent->cat_name;
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {update}'
            ],
        ],
    ]); ?>
</div>

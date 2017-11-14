<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ArticleCatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Article Cats';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-cat-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Article Cat', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'cat_id',
            'cat_name',
            'keywords',
            'cat_desc',
            'sort_order',
            'show_in_nav',
            [
                'attribute' => 'parent_id',
                'value' => function ($model) {
                    if (!empty($model->parent)) {
                        return $model->parent->cat_name;
                    }
                    return '顶级分类';
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

<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TagsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '标签';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tags-index">

    <h1><?= Html::encode($this->title) ?> </h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Tags', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'type',
                'value' => function($model) {
                    return \common\models\Tags::tagTypeName($model->type);
                }
            ],
            'name',
            'desc',
            'sort',
            [
                'attribute' => 'enabled',
                'value' => function($model) {
                    return $model->enabled ? '显示' : '不显示';
                }
            ],
            'code:ntext',
            'mCode:ntext',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

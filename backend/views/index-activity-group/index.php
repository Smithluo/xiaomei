<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\IndexActivityGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Index Activity Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-activity-group-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Index Activity Group', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    return \common\models\IndexActivityGroup::$typeMap[$model['type']];
                }
            ],
            'title',
            'desc',
            'sort_order',
            'is_show',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

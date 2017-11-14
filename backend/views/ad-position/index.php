<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AdPositionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ad Positions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ad-position-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Ad Position', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'position_id',
            'position_name',
            'position_desc',
             'position_style:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} | {update} | {list} | {delete}',
                'buttons' => [
                    'list' => function ($url, $model, $key) {
                        $jump = '/ad/index?AdSearch%5Bposition_id%5D='. $model->position_id;
                        return
                            Html::a(
                                '<span class="glyphicon glyphicon-question-sign"></span>',
                                $jump,
                                [
                                    'title' => '列表',
                                ]
                            );
                    },
                ],
            ],
        ],
    ]); ?>
</div>

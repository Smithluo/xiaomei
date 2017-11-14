<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\FeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '意见反馈';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feedback-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'msg_id',
            [
                'attribute' => 'user_id',
                'value' => function ($model) {
                    return isset($model['users']['showName']) ? $model['users']['showName'] . '('. $model['users']['mobile_phone']. ')' : '';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \backend\models\Users::getAllUsers(),
                    'options' => ['placeholder' => '用户'],
                    'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                ],
            ],
             'msg_title',
            [
                'attribute' => 'msg_type',
                'value' => function ($model) {
                    return \common\models\Feedback::$msg_type_map[$model->msg_type];
                },
            ],
            'user_phone',
            [
                'attribute' => 'msg_time',
                'value' => function ($model) {
                    return \common\helper\DateTimeHelper::getFormatCNDateTime($model->msg_time);
                },
            ],
            // 'order_id',
            // 'msg_area',
            [
                    'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>
</div>

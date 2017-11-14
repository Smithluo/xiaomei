<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ArrivalReminderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '到货提醒列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="arrival-reminder-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'user_id',
                'value' => function ($model) {
                    return $model['user']['showName']. '('. $model['user']['mobile_phone']. ')';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \backend\models\Users::getAllUsers(),
                    'options' => ['placeholder' => '用户'],
                    'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                ],
            ],
            [
                'attribute' => 'goods_id',
                'value' => function ($model) {
                    if (empty($model['goods'])) {
                        return null;
                    }
                    return $model['goods']->getBackendName();
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \backend\models\Goods::getGoodsMap(),
                    'options' => ['placeholder' => '用户'],
                    'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                ],
            ],
            [
                'attribute' => 'add_time',
                'value' => function ($model) {
                    return \common\helper\DateTimeHelper::getFormatCNDateTime($model->add_time);
                },
                'filter' => false,
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return \common\models\ArrivalReminder::$arrival_map[$model->status];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \common\models\ArrivalReminder::$arrival_map,
                    'options' => ['placeholder' => '状态'],
                    'pluginOptions' => ['allowClear'=>true, 'width'=>'100%'],
                ],
            ],
        ],
    ]); ?>
</div>

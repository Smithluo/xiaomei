<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WishListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户采购心愿';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wish-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '用户',
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
                'label' => '状态',
                'attribute' => 'state',
                'value' => function($model) {
                    return \common\models\WishList::$stateMap[$model->state];
                }
            ],
            'content:ntext',
            'created_at',
            'updated_at',

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

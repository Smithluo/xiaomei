<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PaidCouponSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Paid Coupons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="paid-coupon-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Paid Coupon', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'amount',
            [
                'attribute' => 'event_id',
                'value' => function ($model) {
                    if (empty($model->event)) {
                        return null;
                    }
                    return '('. $model['event']['event_id']. ')'. $model['event']['event_name'];
                }
            ],
            [
                'attribute' => 'rule_id',
                'value' => function ($model) {
                    if (empty($model->rule)) {
                        return null;
                    }
                    return '('. $model['event']['event_id']. ')'. '('. $model['rule']['rule_id']. ')'. $model['rule']['rule_name'];
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

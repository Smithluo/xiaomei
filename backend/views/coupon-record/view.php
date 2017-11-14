<?php

use common\helper\DateTimeHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\CouponRecord */

$this->title = $model->coupon_id;
$this->params['breadcrumbs'][] = ['label' => '优惠券流水', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-record-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->coupon_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <div class="col-lg-3">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'coupon_id',
            'event_id',
            'rule_id',
            'coupon_sn',
            'user_id',
            [
                'attribute' => 'received_at',
                'value' => $model->received_at ? DateTimeHelper::getFormatCNDateTime($model->received_at) : '错误数据'
            ],
            [
                'attribute' => 'used_at',
                'value' => $model->used_at ? DateTimeHelper::getFormatCNDateTime($model->used_at) : '未使用'
            ],
            'group_id',
            'created_by',
            [
                'attribute' => 'status',
                'value' => $couponStatusMap[$model->status]
            ],
            'start_time',
            'end_time',
        ],
    ]) ?>
    </div>
</div>

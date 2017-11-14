<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helper\DateTimeHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Integral */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '积分列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="integral-view col-lg-4" >

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'integral',
            'user_id',
            [
                'attribute' => 'pay_code',
                'value' => $payCodeMap[$model->pay_code]
            ],
            'out_trade_no',
            'note',
            [
                'attribute' => 'created_at',
                'value' => DateTimeHelper::getFormatCNDateTime($model->created_at)
            ],
            [
                'attribute' => 'updated_at',
                'value' => DateTimeHelper::getFormatCNDateTime($model->updated_at)
            ],
            [
                'attribute' => 'status',
                'value' => $statusMap[$model->status]
            ],
        ],
    ]) ?>

</div>

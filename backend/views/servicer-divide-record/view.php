<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ServicerDivideRecord */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Servicer Divide Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicer-divide-record-view">

    <?php
    $attributes = [
        [
            'group' => true,
            'label' => '订单信息',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
        [
            'columns' => [
                [
                    'label' => '订单号',
                    'value' => isset($model->orderInfo) ? $model->orderInfo->order_sn : null,
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'label' => '零售店',
                    'value' => isset($model->user) ? ($model->user->showName. '('. $model->user->mobile_phone. ')') : null,
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
                [
                    'attribute' => 'amount',
                    'label' => '货款',
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 20%',
                    ],
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'label' => '业务员',
                    'value' => isset($model->servicer) ? ($model->servicer->nickname. '('. $model->servicer->mobile_phone. ')') : null,
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 17%',
                    ],
                ],
                [
                    'attribute' => 'divide_amount',
                    'label' => '业务员分成',
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 17%',
                    ],
                ],
                [
                    'label' => '服务商',
                    'value' => isset($model->parentServicer) ? ($model->parentServicer->company_name. '('. $model->parentServicer->mobile_phone. ')') : null,
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 17%',
                    ],
                ],
                [
                    'attribute' => 'parent_divide_amount',
                    'label' => '服务商分成',
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 17%',
                    ],
                ],
            ],
        ],
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        'buttons1' => '{update}',
        'deleteOptions'=>[ // your ajax delete parameters
            'params' => ['id' => $model->id, 'custom_param' => true],
        ],
        'panel'=>[
            'heading'=>'服务商分成：' . isset($model->orderInfo)? $model->orderInfo->order_sn: '',
            'type'=>DetailView::TYPE_PRIMARY,
        ],
    ]) ?>

</div>

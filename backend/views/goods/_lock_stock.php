<?php

use kartik\detail\DetailView;

$lockAttributes = [
    [
        'group' => true,
        'label' => '当前除锁定库存之外的库存：'. $model->goods_number,
        'rowOptions' => [
            'class' => 'info',
        ],
    ],
];

$lockStockColumns = [];
$lockStockList = $model['goodsLockStockList'];
if (!empty($lockStockList)) {
    foreach ($lockStockList as $lockStock) {
        $lockStockColumns[]['columns'] = [
            [
                'viewModel' => $lockStock,
                'editModel' => $lockStock,
                'attribute' => 'lock_num',
                'labelColOptions' => [
                    'style' => 'width: 5%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 10%',
                ],
                'displayOnly' => true,
            ],
            [
                'viewModel' => $lockStock,
                'editModel' => $lockStock,
                'attribute' => 'user_id',
                'value' => empty($lockStock['user']) ? '用户不存在': $lockStock['user']->getShowName(),
                'labelColOptions' => [
                    'style' => 'width: 5%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 10%',
                ],
                'displayOnly' => true,
            ],
            [
                'viewModel' => $lockStock,
                'editModel' => $lockStock,
                'attribute' => 'lock_time',
                'value' => \common\helper\DateTimeHelper::getFormatCNDateTime($lockStock->lock_time),
                'labelColOptions' => [
                    'style' => 'width: 5%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 10%',
                ],
                'displayOnly' => true,
            ],
            [
                'viewModel' => $lockStock,
                'editModel' => $lockStock,
                'attribute' => 'note',
                'labelColOptions' => [
                    'style' => 'width: 5%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 10%',
                ],
                'displayOnly' => true,
            ],
            [
                'viewModel' => $lockStock,
                'editModel' => $lockStock,
                'format' => 'raw',
                'label' => '解锁',
                'value' => \yii\helpers\Html::a('点击解锁库存', \yii\helpers\Url::to(['/goods/release-lock-stock', 'id' => $lockStock->id])),
                'labelColOptions' => [
                    'style' => 'width: 5%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 10%',
                ],
                'displayOnly' => true,
            ],
        ];
    }
}

if (!empty($lockStockColumns)) {
    $lockAttributes = \yii\helpers\ArrayHelper::merge($lockAttributes, $lockStockColumns);
}

if (!empty($newStockLock)) {
    $lockAttributes = \yii\helpers\ArrayHelper::merge($lockAttributes, [
        [
            'columns' => [
                [
                    'viewModel' => $newStockLock,
                    'editModel' => $newStockLock,
                    'attribute' => 'lock_num',
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 10%',
                    ],
                ],
                [
                    'viewModel' => $newStockLock,
                    'editModel' => $newStockLock,
                    'attribute' => 'lock_time',
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'displayOnly' => true,
                ],
                [
                    'viewModel' => $newStockLock,
                    'editModel' => $newStockLock,
                    'attribute' => 'note',
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 10%',
                    ],
                ],
            ],
        ],
    ]);
}

echo DetailView::widget([
    'model' => $model,
    'attributes' => $lockAttributes,
    'mode' => Yii::$app->controller->action->id != 'view' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
    'enableEditMode' => true,
    'deleteOptions'=>[ // your ajax delete parameters
        'params' => ['id' => $model->goods_id, 'custom_param' => true],
    ],
    'panel'=>[
        'heading' => '锁定库存：' . $model->goods_name,
        'type' => DetailView::TYPE_WARNING,
    ],
    'formOptions' => [
        'action' => \yii\helpers\Url::to(['lock-stock', 'id' => $model->goods_id]),
    ],

//            'buttons1' => $model->isNewRecord ? '{create}' : '{update}',
//            'buttons1' => '{update}',
    //  Yii::$app->controller->action->id == 'update' ? '{update}' : '',
]);
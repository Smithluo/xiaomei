<?php

use kartik\detail\DetailView;
use backend\models\Goods;

$attributes = [
    [
        'columns' => [
            [
                'attribute' => 'goods_id',
                'value' => $model->isNewRecord ? '' : $goodsName,
                'type' => DetailView::INPUT_SELECT2,
                'showToggleAll' => false,
                'widgetOptions' => [
                    'data' => Goods::getUnDeleteGoodsMap(),
                    'options' => [
                        'placeholder' => '选择首页显示的商品',
                        'multiple' => false,
                    ],
                    'pluginOptions' => [
                        'allowClear'=>true,
                        'width'=>'100%',
                    ],
                ],
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'sort_order',
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'title',
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'sub_title',
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ],
];
?>


<div class="col-lg-4">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        'mode' => Yii::$app->controller->action->id != 'view' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
        'deleteOptions'=>[ // your ajax delete parameters
            'params' => ['id' => $model->id, 'custom_param' => true],
        ],
        'panel'=>[
            'heading'=>'配置首页显示',
            'type'=> DetailView::TYPE_PRIMARY,
        ],

        'formOptions' => [
            'action' => $model->isNewRecord
                ? \yii\helpers\Url::to(['create'])
                : \yii\helpers\Url::to(['update', 'id' => $model->id]),
        ],

        'buttons1' => $model->isNewRecord ? '{create}' : '{update}',
    ]);
    ?>
</div>

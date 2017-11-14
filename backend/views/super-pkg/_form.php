<?php

use yii\helpers\Html;
use backend\models\Event;
use common\helper\DateTimeHelper;
use kartik\detail\DetailView;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $model common\models\SuperPkg */
/* @var $form yii\widgets\ActiveForm */
?>



<?php

$attributes = [
    [
        'group' => true,
        'label' => '基本信息',
        'rowOptions' => [
            'class' => 'info',
        ],
    ],
    [
        'columns' => [
            [
                'attribute' => 'pag_name',
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
                'attribute' => 'pag_desc',
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
                'attribute' => 'gift_pkg_id',
                'value' => !empty($giftPkgList[$model->gift_pkg_id]) ? $giftPkgList[$model->gift_pkg_id] : '' ,
                'labelColOptions' => [
                    'style' => 'width: 5%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 20%',
                ],
                'type' => DetailView::INPUT_SELECT2,
                'widgetOptions' => [
                    'data' => $giftPkgList,
                    'options' => ['placeholder' => '请选择商品'],
                    'pluginOptions' => [
                        'allowClear'=>true,
                        'width'=>'90%'
                    ],
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
                'attribute' => 'start_time',
                'value' => $model->start_time,
                'type' => Editable::INPUT_DATE,
                'options' => ['placeholder' => date('Y-m-d', time())],
                'convertFormat' => true,
                'widgetOptions' => [
                    'pluginOptions' => [
                        'singleDatePicker'=>true,
                        'showDropdowns'=>true,
                        'value' => $model->start_time,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'autoclose' => true,
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
                'attribute' => 'end_time',
                'value' => $model->end_time,
                'type' => Editable::INPUT_DATE,
                'options' => ['placeholder' => date('Y-m-d', time())],
                'convertFormat' => true,
                'widgetOptions' => [
                    'pluginOptions' => [
                        'singleDatePicker'=>true,
                        'showDropdowns'=>true,
                        'value' => $model->end_time,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'autoclose' => true,
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
];
?>

<div class="col-lg-5">
    <?php
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        'mode' => Yii::$app->controller->action->id != 'view' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
        'deleteOptions'=>[ // your ajax delete parameters
                           'params' => ['id' => $model->id, 'custom_param' => true],
        ],
        'panel'=>[
            'heading'=>'超值礼包：',
            'type'=>DetailView::TYPE_PRIMARY,
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
<div class="col-lg-7">

</div>



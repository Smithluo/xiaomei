<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-09-23
 * Time: 15:44
 */

use kartik\dynagrid\DynaGrid;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>

<div style="border: 1px solid #6e4f1c; margin-top: 5px">
    <?php $form = ActiveForm::begin([
        'action' => [
            '/brand-spec-goods-cat/update',
            'id' => $specCat['id'],
        ]
    ]); ?>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($specCat, 'title') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($specCat, 'sort_order') ?>
        </div>
        <div class="col-lg-3">
            <?= Html::submitButton('修改', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <?php

    echo \yii\helpers\Html::a('新增商品', \yii\helpers\Url::to([
        '/brand-spec-goods/create',
        'catId' => $specCat['id'],
    ]), [
        'class' => 'btn btn-success',
    ]);

    $dataProvider = $specCat->getSpecGoodsProvider();

    $columns = [
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'goods_id',
            'value' => function($model){
                if (empty($model['goods'])) {
                    return null;
                }
                return $model['goods']['goods_name'];
            },
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '商品',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/brand-spec-goods/edit-value'],
                    ],
                    'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                    'options' => [
                        'data' => \backend\models\Goods::getGoodsMap(),
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'sort_order',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '排序值',
                    'size' => 'sm',
                    'formOptions' => [
                        'action' => ['/brand-spec-goods/edit-value'],
                    ],
                ];
            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{delete}',
            'buttons' => [
                'delete'=> function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', \yii\helpers\Url::to([
                        '/brand-spec-goods/delete',
                        'id' => $model['id'],
                    ]));
                }
            ],
        ],
    ];

    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'panel' => [
                'heading' => '<h3 class="panel-title">'. $specCat['title']. '</h3>',
            ],
            'toolbar' =>  [
                ['content'=>
                    \yii\helpers\Html::a('<i class="glyphicon glyphicon-repeat"></i>',
                        ['index'],
                        ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],
                ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                '{toggleData}',
            ]
        ],
        'options' => [
            'id' => 'brand-spec-'. $specCat['id'],
        ],
    ]);
    ?>
</div>






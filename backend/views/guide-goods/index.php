<?php

use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
use common\models\GuideGoods;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel common\models\IndexActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '选品指南商品配置';
$this->params['breadcrumbs'][] = $this->title;

$url = \yii\helpers\Url::to(['/goods/goods-list']);

?>
<div class="season-goods-index">

    <?= $this->render('_search', [
        'model' => $searchModel,
    ]) ?>
    <p>
        <?= Html::a('选品指南商品配置', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php


    $columns =[
        [
            'attribute' => 'id',
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'type',
            'value' => function($model) {
                return $model->guideType['title'];
            },
            'editableOptions' => function ($model, $key, $index) {
                return [
                    'header' => '选择选品分类',
                    'size' => 'md',
                    'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                    'data' => GuideGoods::TypeMap(),
                    'formOptions' => [
                        'method' => 'post',
                        'action' => \yii\helpers\Url::to('/guide-goods/editType'),
                    ],
                ];
            },
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'goods_id',
            'value' => function($model) {
                if (empty($model->goods)) {
                    return null;
                }
                return $model->goods['goods_name'];
            },
            'editableOptions' => function ($model, $key, $index) use($url) {
                return [
                    'header' => '选择所选商品',
                    'size' => 'md',
                    //找了尼玛半个小时 要用select2 得配置widgetClass options ['data']
                    'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                    'widgetClass'=> 'kartik\editable\Select2',
                    'options' => [
                        'initValueText' => $model->goods['goods_name'],
                        'pluginOptions' => [
                            'allowClear' => false,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return '正在查询...'; }"),
                            ],
                            'ajax' => [
                                'url' => $url,
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(order) { return order.text; }'),
                            'templateSelection' => new JsExpression('function (order) { return order.text; }'),
                        ],
                    ],
                    'formOptions' => [
                        'method' => 'post',
                        'action' => \yii\helpers\Url::to('/guide-goods/editGoodsID'),
                    ],

                ];
            },
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'sort_order',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '排序值',
                    'size' => 'md',
                    'formOptions' => [
                        'action' => ['/guide-goods/editSortOrder'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{update} {delete}',

        ],
    ];

    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            'panel' => [
                'heading' => '<h3 class="panel-title">选品指南分类配置</h3>',
            ],
            'toolbar' =>  [
                ['content'=>
                     Html::a('<i class="glyphicon glyphicon-repeat"></i>',
                         ['index'],
                         ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],
                ['content'=>'{dynagridFilter}{dynagridSort}{dynagrid}'],
                '{toggleData}',
            ]
        ],
        'options' => [
            'id' => 'dynagrid-guide-goods',
        ],
    ]);
    ?>
</div>

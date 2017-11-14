<?php

use kartik\grid\GridView;

?>

<p>↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓关键词列表↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓</p>

<?php

echo GridView::widget([
    'dataProvider' => $itemDataProvider,
    'filterModel' => $itemSearchModel,
    'columns' => [
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'title',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '标题',
                    'size' => 'sm',
                    'formOptions' => [
                        'action' => ['/index-keywords-group/edit-keywords-value'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'ext',
            'value' => function ($model) {
                return \common\models\IndexKeywords::$extMap[$model->ext];
            },
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '扩展场景',
                    'size' => 'sm',
                    'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                    'options' => [
                        'data' => \common\models\IndexKeywords::$extMap,
                    ],
                    'formOptions' => [
                        'action' => ['/index-keywords-group/edit-keywords-value'],
                    ],
                ];
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'data' => \common\models\IndexKeywords::$extMap,
                'options' => [
                    'placeholder' => '选择扩展场景',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'width' => '100%',
                ],
            ],
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'is_show',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '是否显示',
                    'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                    'size' => 'sm',
                    'formOptions' => [
                        'action' => ['/index-keywords-group/edit-keywords-value'],
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
                        'action' => ['/index-keywords-group/edit-keywords-value'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'url',
            'editableOptions' => function($model, $key, $index) {
                return [
                    'header' => '跳转链接',
                    'size' => 'sm',
                    'formOptions' => [
                        'action' => ['/index-keywords-group/edit-keywords-value'],
                    ],
                ];
            },
            'pageSummary' => true,
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    return
                        \yii\helpers\Html::a(
                            '<span class="glyphicon glyphicon glyphicon-trash"></span>',
                            \yii\helpers\Url::to(['/index-keywords-group/delete-keywords', 'id' => $model->id]),
                            [
                                'title' => '删除',
                                'data-method' => 'post',
                                'data-confirm' => '确定删除此项吗？',
                            ]
                        );
                },
            ],
        ],
    ],
]);
?>



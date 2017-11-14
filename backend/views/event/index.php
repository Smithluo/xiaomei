<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\models\Event;
use common\helper\DateTimeHelper;
use kartik\dynagrid\DynaGrid;


/* @var $this yii\web\View */
/* @var $searchModel common\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '活动管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <div class="row">
        <div class="col-lg-3">
            <?= Html::a(
                '创建满增活动',
                ['create', 'event_type' => Event::EVENT_TYPE_FULL_GIFT],
                ['class' => 'btn btn-primary']
            ) ?>

            <?= Html::a(
                '创建满减活动',
                ['create', 'event_type' => Event::EVENT_TYPE_FULL_CUT],
                ['class' => 'btn btn-success']
            ) ?>

            <?= Html::a(
                '创建优惠券活动',
                ['create', 'event_type' => Event::EVENT_TYPE_COUPON],
                ['class' => 'btn btn-warning']
            ) ?>

            <?= Html::a(
                '物料配比规则',
                ['create', 'event_type' => Event::EVENT_TYPE_WULIAO],
                ['class' => 'btn btn-default']
            ) ?>
        </div>
        <div class="col-lg-9">
            <?= $this->render(
                '_search',
                [
                    'model' => $searchModel,
                    'eventTypeMap' => $eventTypeMap,
                    'eventNameMap' => $eventNameMap,
                    'eventDescMap' => $eventDescMap,
                    'is_active_map' => $is_active_map,
                ]
            ); ?>
        </div>
    </div>
    <p>

    </p>
    <?= DynaGrid::widget([
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            'panel' => [
                'heading' => '<h3 class="panel-title">活动列表</h3>',
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
            'id' => 'dynagrid-event-index',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'event_id',
            [
                'attribute' => 'event_type',
                'value' => function($model) use ($eventTypeMap){
                    return $eventTypeMap[$model->event_type];
                }
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'sub_type',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '子类型',
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/event/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'event_name',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '活动名',
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/event/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'event_desc',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '活动规则',
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/event/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'bgcolor',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '背景色值',
                        'inputType' => \kartik\editable\Editable::INPUT_COLOR,
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/event/edit-value'],
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
                            'action' => ['/event/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'pre_time',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '预热时间',
                        'inputType' => \kartik\editable\Editable::INPUT_DATETIME,
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/event/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'start_time',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '开始时间',
                        'inputType' => \kartik\editable\Editable::INPUT_DATETIME,
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/event/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'end_time',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '结束时间',
                        'inputType' => \kartik\editable\Editable::INPUT_DATETIME,
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/event/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($model){
                    return DateTimeHelper::getFormatCNDateTime($model->updated_at);
                }
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'is_active',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否有效',
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/event/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'times_limit',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '限制参与次数',
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/event/edit-value'],
                        ],
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'hot',
                'editableOptions' => function($model, $key, $index) {
                    return [
                        'header' => '是否热门',
                        'size' => 'sm',
                        'formOptions' => [
                            'action' => ['/event/edit-value'],
                        ],
                        'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
                    ];
                },
                'pageSummary' => true,
            ],
            [
                'attribute' => 'effective_scope_type',
                'value' => function($model){
                    $map = Event::$effectiveScopeTypeMap;
                    if (!empty($map[$model->effective_scope_type])) {
                        return $map[$model->effective_scope_type];
                    } else {
                        return '【错误】未知类型';
                    }
                }
            ],
            [
                'attribute' => 'auto_destroy',
                'value' => function($model){
                    $map = Event::$autoDestroyMap;
                    if (!empty($map[$model->auto_destroy])) {
                        return $map[$model->auto_destroy];
                    } else {
                        return '【错误】未知类型';
                    }
                }
            ],
            [
                'attribute' => 'receive_type',
                'value' => function($model) use ($receiveTypeMap){
                    if (!empty($receiveTypeMap[$model->receive_type])) {
                        return $receiveTypeMap[$model->receive_type];
                    } else {
                        return $receiveTypeMap[Event::RECEIVE_TYPE_AUTO];
                    }
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} | {update} | {toogle}',
                'buttons' => [
                    'toogle' => function ($url, $model, $key) {
                        return
                            Html::a(
                                '<span class="glyphicon glyphicon-question-sign"></span>',
                                $url,
                                [
                                    'title' => $model->is_active ? '置为失效' : '置为生效',
                                ]
                            );
                    },
                ],
            ],
        ],
    ]); ?>
</div>

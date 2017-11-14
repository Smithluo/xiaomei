<?php

use common\helper\DateTimeHelper;
use kartik\dynagrid\DynaGrid;
use kartik\editable\Editable;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CouponRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '优惠券记录';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="coupon-record-index">
    <div class="row">
        <?php echo $this->render('_search', [
                'model' => $searchModel,
                'usersMap' => $usersMap,
                'couponStatusMap' => $couponStatusMap,
                'couponEventMap' => $couponEventMap,
                'couponEventRuleMap' => $couponEventRuleMap,
        ]); ?>
    </div>

    <?php
    $url = str_replace(
        '/coupon-record/index',
        '/coupon-record/export',
        urldecode(Yii::$app->request->url)
    );
    echo Html::a('导出', $url, ['class' => 'btn btn-default']);
    ?>

    <?php
    $columns = [
        'coupon_id',
        [
            'attribute' => 'event_id',
            'value' => function ($model) use ($couponEventMap) {
                if (isset($couponEventMap[$model->event_id])) {
                    return '[ID：'.$model->event_id.']'.$couponEventMap[$model->event_id];
                } else {
                    return '错误数据';
                }
            }
        ],
        [
            'attribute' => 'rule_id',
            'value' => function ($model) use ($couponEventRuleMap) {
                if (isset($couponEventRuleMap[$model->rule_id])) {
                    return '[ID：'.$model->rule_id.']'.$couponEventRuleMap[$model->rule_id];
                } else {
                    return '错误数据';
                }
            }
        ],
        'coupon_sn',
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'user_id',
            'format' => 'raw',
            'editableOptions' => function ($model, $key, $index) use ($usersMap){
                return [
                    'size' => 'md',
                    'header' => '请填写用户ID',
                    'inputType' => Editable::INPUT_TEXT,
                    'asPopover' => true,
//                    'inputType' => Editable::INPUT_SELECT2,
//                    'afterInput'=>function ($form, $widget) use ($model, $index, $usersMap) {
//                        return $form->field($model, "user_id")->widget(\kartik\widgets\Select2::classname(), [
//                            'data' => $usersMap,
//                            'options' => ['placeholder' => '请选择'],
//                            'pluginOptions' => [
//                                'allowClear' => true
//                            ],
//                        ]);
//                    },

                    'formOptions' => [
                        'method' => 'post',
                        'action' => Url::to('/coupon-record/bind'),
                    ],
                ];
            },
            'value' => function($model){
                if (empty($model->user_id)) {
                    return '未绑定';
                } else {
                    if (!empty($model->user)) {
                        $user = $model->user;
                        return $user->showName;
                    } else {
                        return '【坑】用户被删除';
                    }
                }
            }
        ],
        [
//            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'status',
            'value' => function ($model) use ($couponStatusMap) {
                return $couponStatusMap[$model->status];
            },
//
//            'editableOptions' => function ($model, $key, $index) use ($couponStatusMap){
//                return [
//                    'header' => '选择状态',
//                    'size' => 'md',
//                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
//
//                    'data' => $couponStatusMap,
//
//                    'formOptions' => [
//                        'method' => 'post',
//                        'action' => Url::to('/coupon-record/editStatus'),
//                    ],
//                ];
//            },
//            'pageSummary' => true,
        ],
        [
            'attribute' => 'received_at',
            'value' => function ($model) {
                if ($model->received_at > 0) {
                    return DateTimeHelper::getFormatCNDateTime($model->received_at);
                } else {
                    return '未绑定';
                }
            }
        ],
        [
            'attribute' => 'used_at',
            'value' => function ($model) {
                if ($model->used_at > 0) {
                    return DateTimeHelper::getFormatCNDateTime($model->used_at);
                } else {
                    return '未使用';
                }
            }
        ],
        [
            'attribute' => 'group_id',
            'format' => 'raw',
            'value' => function ($model) {
                if (empty($model->orderGroup)) {
                    return null;
                }
                return Html::a($model->group_id, Url::to(['/order-group/view', 'id' => $model->orderGroup->id]), [
                    'target' => '_blank',
                ]);
            }
        ],
        [
            'attribute' => 'created_by',
            'value' => function ($model) {
                if (empty($model->creater)) {
                    return null;
                }
                return $model->creater->showName.' [ID：'.$model->created_by.']';
            }
        ],
        'start_time',
        'end_time',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{view} {update}',
        ]
    ];

    echo DynaGrid::widget([

        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-primary',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            'panel' => [
                'heading' => '<h3 class="panel-title">商品列表</h3>',
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
            'id' => 'dynagrid-coupon-index',
        ],
    ]);
    ?>
</div>

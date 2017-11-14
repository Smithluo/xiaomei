<?php

use yii\helpers\Html;
use backend\models\Event;
use backend\models\AdminUser;
use common\helper\DateTimeHelper;
use common\helper\ImageHelper;
use kartik\detail\DetailView;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $model common\models\Event */
/* @var $form yii\widgets\ActiveForm */

echo '<p style="color: red">活动的结束时间只需要配置到天，默认结束时间为当天的23:59:59，00:00:00 修改过期优惠券状态为已过期</p>';
echo '<p style="color: red">子类型，当前用于填写优惠券的 新人券、全局券、品牌券等</p>';
if ($model->event_type == Event::EVENT_TYPE_FULL_CUT) {
    echo '<p style="color: red">创建【满减、优惠券】活动规则时必须有满减活动ID, 所以在创建活动时可不选择规则，先保存；然后创建活动规则；再修改活动生效状态</p><p style="color: red">满减和优惠券同时满足条件时，如果满减幅度 > 优惠券的最大幅度，则不显示优惠券，否则显示可用优惠券列表供用户选择</p>';
}
?>
<?php
    //  满赠、满减、优惠券、物料配比 都可以对应对跳规则， rule_id 不做设置， 用于显示规则名称列表
    $ruleIdColumn = [
        'columns' => [
            [
                'attribute' => 'rule_id',
                'label' => '策略 '.Html::a(
                        '活动策略列表',
                        $ruleLink,
                        ['class' => 'btn btn-primary', 'target' => '_blank']
                    ),
                'format' => 'raw',
                'displayOnly' => true,
                'value' => !empty($ruleMap) ? implode('<br />', $ruleMap) : '活动不关联规则，在活动规则中关联活动',
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ];

?>
<br />
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
                    'attribute' => 'event_type',
                    'value' => $eventTypeMap[$model->event_type],
                    'displayOnly' => true,
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
                    'attribute' => 'sub_type',
                    'value' => $model->sub_type,
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

    if ($model->event_type != Event::EVENT_TYPE_FULL_GIFT) {
        $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'effective_scope_type',
                    'type' => DetailView::INPUT_DROPDOWN_LIST,
                    'items' => $effectiveScopeTypeMap,
                    'value' => !empty($model->effective_scope_type)
                        ? $effectiveScopeTypeMap[$model->effective_scope_type]
                        : '',
                    'labelColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 80%',
                    ],
                ],
            ],
        ];
    }

    $attributes[] = [
        //  领取类型
        'columns' => [
            [
                'attribute' => 'receive_type',
                'type' => DetailView::INPUT_DROPDOWN_LIST,
                'items' => $receiveTypeMap,
                'value' => !empty($receiveTypeMap[$model->receive_type])
                    ? $receiveTypeMap[$model->receive_type]
                    : $receiveTypeMap[Event::RECEIVE_TYPE_AUTO],
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
                'displayOnly' => $model->event_type != Event::EVENT_TYPE_COUPON ? true : false,
            ],
        ],
    ];
    $attributes[] = [
        'columns' => [
            [
                'attribute' => 'event_name',
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ];
    $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'event_desc',
                    'labelColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 80%',
                    ],
                ],
            ],
        ];
    if ($model->event_type == Event::EVENT_TYPE_COUPON) {
        $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'auto_destroy',
                    'format' => 'raw',
                    'value' => $model->auto_destroy
                        ? '<span class="glyphicon glyphicon-ok">'.$autoDestroyMap[Event::AUTO_DESTROY_YES].'</span>'
                        : '<span class="glyphicon glyphicon-remove">'.$autoDestroyMap[Event::AUTO_DESTROY_NO].'</span>',
                    'type' => DetailView::INPUT_SWITCH,
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'onText' => $autoDestroyMap[Event::AUTO_DESTROY_YES],
                            'offText' => $autoDestroyMap[Event::AUTO_DESTROY_NO],
                        ]
                    ],
                    'labelColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 80%',
                    ],
                ],
            ],
        ];
    }

    $attributes[] = $ruleIdColumn;
    $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'is_active',
                    'format' => 'raw',
                    'value' => $model->is_active ? '有效' : '无效',
                    'type' => DetailView::INPUT_SWITCH,
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'onText' => '有效',
                            'offText' => '无效',
                        ]
                    ],
                    'labelColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 80%',
                    ],
                ],
            ],
        ];

    if ($model->event_type == Event::EVENT_TYPE_COUPON) {
        $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'pkgEnable',
                    'value' => $model->pkgEnable ? '是' : '否',
                    'type' => DetailView::INPUT_SWITCH,
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'onText' => '是',
                            'offText' => '否',
                        ]
                    ],
                    'labelColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 80%',
                    ],
                ],
            ],
        ];
    }

    $attributes[] = [
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
            ];
    $attributes[] = [
                'columns' => [
                    [
                        'attribute' => 'banner',
                        'format' => 'raw',
                        'value' => Html::img(ImageHelper::get_image_path($model->banner), ['style' => 'height:200px']),
                        'type' => DetailView::INPUT_FILE,
                        'labelColOptions' => [
                            'style' => 'width: 20%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 80%',
                        ],
                    ],
                ],
            ];
    $attributes[] = [
                'columns' => [
                    [
                        'attribute' => 'url',
                        'labelColOptions' => [
                            'style' => 'width: 20%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 80%',
                        ],
                    ],
                ],
            ];
    $attributes[] = [
                'columns' => [
                    [
                        'attribute' => 'bgcolor',
                        'type' => DetailView::INPUT_COLOR,
                        'labelColOptions' => [
                            'style' => 'width: 20%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 80%',
                        ],
                    ],
                ],
            ];
    $attributes[] = [
                'columns' => [
                    [
                        'attribute' => 'times_limit',
                        'value' => $model->times_limit ?: '无限制',
                        'labelColOptions' => [
                            'style' => 'width: 20%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 80%',
                    ],
                ],
            ],
        ];
    $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'pre_time',
                    'value' => $model->pre_time ?: '未设置',
                    'type' => DetailView::INPUT_DATETIME,
                    'format' => 'raw',
                    'options' => ['placeholder' => date('Y-m-d H:i:s', time())],
                    'convertFormat' => true,
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'singleDatePicker'=>true,
                            'showDropdowns'=>true,
                            'value' => $model->pre_time,
                            'format' => 'yyyy-mm-dd hh:ii:ss',
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
        ];
    $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'start_time',
                    'value' => $model->start_time,
                    'type' => Editable::INPUT_DATETIME,
                    'options' => ['placeholder' => date('Y-m-d H:i:s', time())],
                    'convertFormat' => true,
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'singleDatePicker'=>true,
                            'showDropdowns'=>true,
                            'value' => $model->start_time,
                            'format' => 'yyyy-mm-dd hh:ii:ss',
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
        ];
    $attributes[] = [
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
        ];

    $attributes[] = [
        'columns' => [
            [
                'attribute' => 'hot',
                'value' => $model->hot ? '是': '否',
                'type' => DetailView::INPUT_SWITCH,
                'widgetOptions' => [
                    'pluginOptions' => [
                        'onText' => '是',
                        'offText' => '否',
                    ]
                ],
                'labelColOptions' => [
                    'style' => 'width: 20%',
                ],
                'valueColOptions' => [
                    'style' => 'width: 80%',
                ],
            ],
        ],
    ];

    $attributes[] = [
            'group' => true,
            'label' => '参与活动的商品列表 <span style="color: red">tips: 不配置 参与活动的商品 等价于 活动无效 </span>',
            'rowOptions' => [
                'class' => 'info',
            ],
        ];
    $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'eventToBrandList',
                    'label' => '参与活动的品牌',
                    'format' => 'raw',
                    'value' => !empty($selectedBrandList) ? implode('<br />', $selectedBrandList) : '',
                    'type' => DetailView::INPUT_SELECT2,
                    'showToggleAll' => false,
                    'widgetOptions' => [
                        'data' => $goodsBrandList,
                        'options' => [
                            'placeholder' => '请选择参与活动的品牌',
                            'multiple' => true
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
        ];
    $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'eventToGoodsList',
                    'label' => '参与活动的商品',
                    'format' => 'raw',
                    'value' => implode('<br />', $goodsNameList),
                    'type' => DetailView::INPUT_SELECT2,
                    'showToggleAll' => false,
                    'widgetOptions' => [
                        'data' => $goodsList,
                        'options' => [
                            'placeholder' => '请选择参与活动的商品',
                            'multiple' => true
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
        ];
    $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'eventFilterGoodsList',
                    'label' => '活动过滤的商品',
                    'format' => 'raw',
                    'value' => '参与活动的方式是 商品 => 活动，不存储过滤条件',
                    'type' => DetailView::INPUT_SELECT2,
                    'showToggleAll' => false,
                    'widgetOptions' => [
                        'data' => $goodsList,
                        'options' => [
                            'placeholder' => '请选择参与活动的商品',
                            'multiple' => true
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
        ];
    $attributes[] = [
            'group' => true,
            'label' => '操作者信息',
            'rowOptions' => [
                'class' => 'info',
            ],
        ];
    $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'updated_by',
//                    'value' => Yii::$app->user->identity->id,
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 80%',
                    ],
                ],
            ],
        ];
    $attributes[] = [
            'columns' => [
                [
                    'attribute' => 'updated_at',
                    'value' => DateTimeHelper::getFormatCNDateTime($model->updated_at),
                    'displayOnly' => true,
                    'labelColOptions' => [
                        'style' => 'width: 20%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 80%',
                    ],
                ],
            ],
        ];

?>
<div class="col-lg-5">
    <?php
        if ($model->event_type == Event::EVENT_TYPE_FULL_CUT) {
            echo '<p style="color:red">tips:满减活动当前只支持一个，活动结束，要把所有的规则置为失效，活动关联的商品取消</p>';
        }

        echo DetailView::widget([
            'model' => $model,
            'attributes' => $attributes,
            'mode' => Yii::$app->controller->action->id != 'view' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'deleteOptions'=>[ // your ajax delete parameters
                'params' => ['id' => $model->event_id, 'custom_param' => true],
            ],
            'panel'=>[
                'heading'=>'活动详情：' . $model->event_name,
                'type'=>DetailView::TYPE_PRIMARY,
            ],

            'formOptions' => [
                'action' => $model->isNewRecord
                    ? \yii\helpers\Url::to(['create', 'event_type' => $model->event_type])
                    : \yii\helpers\Url::to(['update', 'id' => $model->event_id]),
            ],

            'buttons1' => $model->isNewRecord ? '{create}' : '{update}',
        ]);
    ?>

</div>
<div class="col-lg-7">

</div>


<?php // echo $form->field($model, 'pkg_id')->dropDownList($pkg_list_map, ['prompt' => '请选择商品包']) ?>
,<?php

use dee\adminlte\Html;
use common\models\Users;
use common\models\UserExtension;
use common\helper\DateTimeHelper;
use common\helper\CacheHelper;
use kartik\detail\DetailView;
use common\helper\ImageHelper;
/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $form yii\widgets\ActiveForm */

$this->title = '用户审核: ' . $model->showName;
$this->params['breadcrumbs'][] = ['label' => '用户审核', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->showName, 'url' => ['view', 'id' => $model->user_id]];

?>
<?php
    $attributes = [
        [
            'group' => true,
            'label' => '基础信息',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'user_name',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'displayOnly' => true,
                ],
                [
                    'attribute' => 'nickname',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                ],
                [
                    'attribute' => 'localPlace',
                    'label' => '所在地',
                    'value' => $area,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'displayOnly' => true,
                ],
                [
                    'attribute' => 'mobile_phone',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'displayOnly' => true,
                ],
            ],
        ],
        [
            'columns' => [

                [
                    'attribute' => 'last_login',
                    'value' => DateTimeHelper::getFormatCNDateTime($model->last_login),
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'displayOnly' => true,
                ],
                [
                    'attribute'=>'reg_time',
                    'value' => DateTimeHelper::getFormatCNDateTime($model->reg_time),
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 15%'
                    ],
                    'displayOnly' => true,
                ],
                [
                    'attribute' => 'last_time',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'displayOnly' => true,
                ],
                [
                    'attribute' => 'last_time',
                    'label' => '',
                    'value' => '',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'displayOnly' => true,
                ],
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' => 'user_rank',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'value' => function () use ($model){
                        $rankMap = Users::$user_rank_map;
                        return $rankMap[$model->user_rank];
                    },
                    'type' => 'dropDownList',
                    'items' => Users::$user_rank_map
                ],
                [
                    'attribute' => 'user_type',
                    'value' => Users::$user_type_map[$model->user_type],
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 15%',
                    ],
                    'type' => 'dropDownList',
                    'items' => Users::$user_type_map

                ],

                [
                    'attribute' => 'channel',
                    'value' => function () use ($model){
                        if(in_array($model->channel,[1, 2, 3, 4, 5, 6] )) {
                            return Users::$channel_map[$model->channel];
                        } else {
                            return $model->channel;
                        }
                    },
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 15%'
                    ],
                    'type' => 'dropDownList',
                    'items' => Users::$channel_map
                ],
                [
                    'attribute'=>'company_name',
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions'=>[
                        'style' => 'width: 15%'
                    ],
                ],

            ],
        ],
        [
            'columns' =>[
                [
                    'attribute' => 'shippingPlace',
                    'label' => '收货地址',
                    'value' => $defult_address,
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 40%',
                    ],
                    'displayOnly' => true,
                ],
                [
                    'attribute' => 'servicer_user_id',
                    'value' => $model->servicer_user_id ==0 ? '尚未绑定': Users::getServicerUserMap()[$model->servicer_user_id],
                    'labelColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 40%',
                    ],
                    'type' => DetailView::INPUT_SELECT2,
                    'widgetOptions' => [
                        'data' => Users::getServicerUserMap(),
                        'options' => [
                            'placeholder' => '绑定服务商',
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                            'width'=>'100%',
                        ],
                    ],
                ],

            ]
        ],

    ];

    if(!empty($model->extension)) {
       $extension =  [
            [
            'group' => true,
            'label' => '扩展信息',
            'rowOptions' => [
                'class' => 'info',
                ],
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'month_sale_count',
                        'label' => '月销量',
                        'value' => UserExtension::$sale_count_map[$model->extension['month_sale_count']],
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                        'viewModel' => $model->extension,
                        'editModel' => $model->extension,
                        'type' => 'dropDownList',
                        'items' => UserExtension::$sale_count_map
                    ],
                    [
                        'attribute' => 'imports_per',
                        'label' => '进口品占比',
                        'value' => UserExtension::$import_map[$model->extension['imports_per']],
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                        'viewModel' => $model->extension,
                        'editModel' => $model->extension,
                        'type' => 'dropDownList',
                        'items' => UserExtension::$import_map
                    ],
                    [
                        'attribute'=>'duty',
                        'label' => '职务',
                        'value' => UserExtension::$duty_map[$model->extension['duty']],
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions'=>[
                            'style' => 'width: 15%'
                        ],
                        'viewModel' => $model->extension,
                        'editModel' => $model->extension,
                        'type' => 'dropDownList',
                        'items' => UserExtension::$duty_map
                    ],
                    [
                        'attribute' => 'store_number',
                        'label' => '店铺数量',
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 15%',
                        ],
                        'viewModel' => $model->extension,
                        'editModel' => $model->extension,
                    ],
                ],
            ],

            [
                'columns' => [
                    [
                        'attribute' => 'shopfront_pic',
                        'value' =>
                        \dee\adminlte\Html::img(ImageHelper::get_image_path($model->shopfront_pic), ['width' => '50%'])
                        ,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 40%',
                        ],
                        'format' => 'raw',
                        'displayOnly' => true,
                    ],
                    [
                        'attribute' => 'biz_license_pic',
                        'value' =>
                            \dee\adminlte\Html::img(ImageHelper::get_image_path($model->biz_license_pic), ['width' => '50%'])
                        ,
                        'labelColOptions' => [
                            'style' => 'width: 10%',
                        ],
                        'valueColOptions' => [
                            'style' => 'width: 40%',
                        ],
                        'format' => 'raw',
                        'displayOnly' => true,
                    ]
                ],
            ],
        ];
       $attributes = array_merge($attributes, $extension);
    }

    $checkInfo = [
        [
            'group' => true,
            'label' => '审核意见',
            'rowOptions' => [
                'class' => 'info',
            ],
        ],
        [
            'columns' => [
                [
                    'attribute' =>  'send_sms',
                    'label' => '发送短信',
                    'value' => '发送短信提醒',
                    'type' => DetailView::INPUT_SWITCH,
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'offText' => '不发送',
                            'onText' => '发送',
                        ]
                    ],
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 5%',
                    ],
                ],
                [
                    'attribute' => 'is_checked',
                    'value' => Users::$is_checked_map[$model->is_checked],
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'type' => 'dropDownList',
                    'items' => Users::$is_checked_map
                ],
                [
                    'attribute' => 'checked_note',
                    'value' => function() use ($model) {
                        return $model->checked_note;
                    },
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 60%',
                    ],
                    'type' => 'textArea',
                    'options' => [
                        'placeholder' => $model->checked_note,
                    ],
                    'editModel' => function() use ($model) {
                        $model->checked_note = '';
                        return $model;
                    },
                ],
                [
                    'attribute' => 'user_check_note',
                    'value' => function() use ($model){
                        return (empty($model->user_check_note) || empty(Users::$notice_map["$model->user_check_note"])) ? '无' :  Users::$notice_map["$model->user_check_note"];
                    },
                    'labelColOptions' => [
                        'style' => 'width: 5%',
                    ],
                    'valueColOptions' => [
                        'style' => 'width: 10%',
                    ],
                    'type' => 'dropDownList',
                    'items' => Users::$notice_map,
                ],
            ],

        ],
    ];



    $attributes = array_merge($attributes, $checkInfo);
    echo DetailView::widget([
    'model' => $model,
    'attributes' => $attributes,
    'mode' => DetailView::MODE_VIEW,
    'deleteOptions'=>[ // your ajax delete parameters
                       'params' => ['id' => $model->user_id, 'delete' => true],
    ],
    'panel'=>[
        'heading'=>'审核用户：' . $model->user_id,
        'type'=>DetailView::TYPE_PRIMARY,
    ],
    'buttons1' => '{update}',
    'formOptions' => [
        'action' => \yii\helpers\Url::to(['check', 'id' => $model->user_id]),
    ],
    'fadeDelay' => 100,
    ]);


?>
<?php
echo
Html::a('派券', \yii\helpers\Url::to([
    '/sc-user/send-coupon',
    'userId' => $model->user_id,
    'eventId' => 148
]), ['class' => 'btn btn-primary']);
?>

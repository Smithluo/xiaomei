<?php

use backend\assets\AppAsset;
use backend\assets\DatepickerAsset;
use common\models\OrderInfo;
use common\helper\DateTimeHelper;
use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\OrderInfoSearch */
/* @var $form yii\widgets\ActiveForm */

$extensionCodeMap = OrderInfo::$extensionCodeMap;

$provinces = \common\helper\CacheHelper::getRegionCache([
    'type' => 'tree',
    'ids' => [],
    'deepth' => 1
]);

?>

<div class="order-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
//        'fieldConfig' => [
//            'template' => "{label}\n<div class=\"col-sm-2\">{input}</div>",
//            'labelOptions' => ['class' => 'col-lg-1 control-label text-right'],
//        ],

    ]); ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="form-group col-lg-6">
                <label>订单创建时间</label>
                <?= DatePicker::widget([
                    'name' => 'OrderInfoSearch[add_time_start]',
                    'name2' => 'OrderInfoSearch[add_time_end]',
                    'value' => $model->add_time_start,
                    'value2' => $model->add_time_end,
                    'type' => DatePicker::TYPE_RANGE,

                    'convertFormat' => true,
                    'pluginOptions' => [
                        'format' => 'yyyy-MM-dd',
                        'todayHighlight' => true,
                        'autoclose' => true,
                    ]
                ]);?>
            </div>
            <div class="col-lg-6">
                <?php
                $users = \common\models\Users::find()->asArray()->all();
                $data = [];
                foreach ($users as $user) {
                    $data[$user['user_id']] = $user['user_name']. '('. $user['mobile_phone']. ')';
                }
                echo $form->field($model, 'user_id')->widget(kartik\select2\Select2::className(), [
                    'data' => $data,
                    'options' => ['placeholder' => '选择下单用户'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label('搜索下单人用户名或者手机号码')
                ?>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="col-lg-3">
                <?= $form->field($model, 'group_id') ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'order_sn') ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'order_id') ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'pay_id')->dropDownList($paymentMap, ['prompt' => '请选择']) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="col-lg-6">
                <?php
                $brands = \backend\models\Brand::find()->asArray()->all();  //  品牌下架，也应该支持搜索
                $data = array_column($brands, 'brand_name', 'brand_id');
                echo $form->field($model, 'brand_id')->widget(\kartik\widgets\Select2::className(), [
                    'data' => $data,
                    'options' => ['placeholder' => '选择品牌'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])
                ?>
            </div>
            <div class="col-lg-6">
                <?php  echo $form->field($model, 'consignee') ?>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="col-lg-3">
                <?php  echo $form->field($model, 'order_status')->dropDownList(OrderInfo::$order_status_map, ['prompt' => '请选择']) ?>
            </div>
            <div class="col-lg-3">
                <?php  echo $form->field($model, 'pay_status')->dropDownList(OrderInfo::$pay_status_map, ['prompt' => '请选择']) ?>
            </div>
            <div class="col-lg-3">
                <?php  echo $form->field($model, 'shipping_status')->dropDownList(OrderInfo::$shipping_status_map, ['prompt' => '请选择']) ?>
            </div>

            <div class="col-lg-3">
                <?php  echo $form->field($model, 'extension_code')->dropDownList($extensionCodeMap, ['prompt' => '请选择']) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2">
            <?=
            $form->field($model, 'province')->widget(kartik\widgets\Select2::className(), [
                'data' => $provinces,
                'options' => ['placeholder' => '选择收货地址所在省'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'user_province')->widget(kartik\widgets\Select2::className(), [
                'data' => $provinces,
                'options' => [
                    'placeholder' => '选择用户注册时填写的省',
                    'multiple' => true,
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>
        </div>
        <div class="col-lg-4">
            <div class="form-group col-lg-6">
                <label>订单支付时间</label>
                <?= DatePicker::widget([
                    'name' => 'OrderInfoSearch[pay_time_start]',
                    'name2' => 'OrderInfoSearch[pay_time_end]',
                    'value' => $model->pay_time_start,
                    'value2' => $model->pay_time_end,
                    'type' => DatePicker::TYPE_RANGE,

                    'convertFormat' => true,
                    'pluginOptions' => [
                        'format' => 'yyyy-MM-dd',
                        'todayHighlight' => true,
                        'autoclose' => true,
                    ]
                ]);?>
            </div>
        </div>
    </div>

    <div class="form-group col-lg-3">
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<br />
<?php /* echo $form->field($model, 'os_status')->dropDownList(
    OrderInfo::$order_cs_status_map_no_style,
    ['prompt' => '请选择']
)->label('综合状态') */?>
<?php // echo $form->field($model, 'country') ?>

<?php // echo $form->field($model, 'province') ?>

<?php // echo $form->field($model, 'city') ?>

<?php // echo $form->field($model, 'district') ?>

<?php // echo $form->field($model, 'address') ?>

<?php // echo $form->field($model, 'zipcode') ?>

<?php // echo $form->field($model, 'tel') ?>

<?php // echo $form->field($model, 'email') ?>

<?php // echo $form->field($model, 'best_time') ?>

<?php // echo $form->field($model, 'sign_building') ?>

<?php // echo $form->field($model, 'postscript') ?>

<?php // echo $form->field($model, 'shipping_id') ?>

<?php // echo $form->field($model, 'shipping_name') ?>

<?php // echo $form->field($model, 'pay_id') ?>

<?php // echo $form->field($model, 'pay_name') ?>

<?php // echo $form->field($model, 'how_oos') ?>

<?php // echo $form->field($model, 'how_surplus') ?>

<?php // echo $form->field($model, 'pack_name') ?>

<?php // echo $form->field($model, 'card_name') ?>

<?php // echo $form->field($model, 'card_message') ?>

<?php // echo $form->field($model, 'inv_payee') ?>

<?php // echo $form->field($model, 'inv_content') ?>

<?php // echo $form->field($model, 'goods_amount') ?>

<?php // echo $form->field($model, 'shipping_fee') ?>

<?php // echo $form->field($model, 'insure_fee') ?>

<?php // echo $form->field($model, 'pay_fee') ?>

<?php // echo $form->field($model, 'pack_fee') ?>

<?php // echo $form->field($model, 'card_fee') ?>

<?php // echo $form->field($model, 'money_paid') ?>

<?php // echo $form->field($model, 'surplus') ?>

<?php // echo $form->field($model, 'integral') ?>

<?php // echo $form->field($model, 'integral_money') ?>

<?php // echo $form->field($model, 'bonus') ?>

<?php // echo $form->field($model, 'order_amount') ?>

<?php // echo $form->field($model, 'from_ad') ?>

<?php // echo $form->field($model, 'referer') ?>

<?php // echo $form->field($model, 'add_time') ?>

<?php // echo $form->field($model, 'confirm_time') ?>

<?php // echo $form->field($model, 'pay_time') ?>

<?php // echo $form->field($model, 'shipping_time') ?>

<?php // echo $form->field($model, 'recv_time') ?>

<?php // echo $form->field($model, 'pack_id') ?>

<?php // echo $form->field($model, 'card_id') ?>

<?php // echo $form->field($model, 'bonus_id') ?>

<?php // echo $form->field($model, 'invoice_no') ?>

<?php // echo $form->field($model, 'extension_code') ?>

<?php // echo $form->field($model, 'extension_id') ?>

<?php // echo $form->field($model, 'to_buyer') ?>

<?php // echo $form->field($model, 'pay_note') ?>

<?php // echo $form->field($model, 'agency_id') ?>

<?php // echo $form->field($model, 'inv_type') ?>

<?php // echo $form->field($model, 'tax') ?>

<?php // echo $form->field($model, 'is_separate') ?>

<?php // echo $form->field($model, 'parent_id') ?>

<?php // echo $form->field($model, 'discount') ?>

<?php // echo $form->field($model, 'mobile_pay') ?>

<?php // echo $form->field($model, 'mobile_order') ?>
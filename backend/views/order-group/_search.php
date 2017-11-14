<?php

use backend\assets\AppAsset;
use backend\assets\DatepickerAsset;
use backend\models\OrderGroup;
use common\helper\DateTimeHelper;
use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Region;

/* @var $this yii\web\View */
/* @var $model common\models\OrderGroupSearch */
/* @var $form yii\widgets\ActiveForm */

$provinceMap = Region::getProvinceMap();
$cityMap = [];
foreach ($provinceMap as $provinceId => $provinceName) {
    $cities = Region::getCityMap($provinceId);
    foreach ($cities as $cityId => $cityName) {
        $cityMap[$cityId] = '('. $provinceName. ')'. $cityName;
    }
}

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
        <div class="col-lg-6">
            <div class="form-group col-lg-6">
                <label>订单创建时间</label>
                <?= DatePicker::widget([
                    'name' => 'OrderGroupSearch[add_time_start]',
                    'name2' => 'OrderGroupSearch[add_time_end]',
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
        <div class="col-lg-3">
            <?= $form->field($model, 'group_id') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'order_sn') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?php  echo $form->field($model, 'consignee') ?>
        </div>
        <div class="col-lg-3">
            <?php  echo $form->field($model, 'group_status')->dropDownList(OrderGroup::$order_group_status, ['prompt' => '请选择']) ?>
        </div>
        <div class="col-lg-3">
            <?=
            $form->field($model, 'province')->widget(kartik\widgets\Select2::className(), [
                'data' => $provinceMap,
                'options' => ['placeholder' => '选择收货地址所在省'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'user_province')->widget(kartik\widgets\Select2::className(), [
                'data' => $provinceMap,
                'options' => [
                    'placeholder' => '选择用户注册时填写的省',
                    'multiple' => true,
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-3">
            <?=
            $form->field($model, 'city')->widget(kartik\widgets\Select2::className(), [
                'data' => $cityMap,
                'options' => ['placeholder' => '选择收货地址所在市'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'payNote') ?>
        </div>
    </div>

    <div class="form-group col-lg-3">
        <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>

    </div>
    <?php ActiveForm::end(); ?>
</div>
<br />
<br />
<br />
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
        'action' => ['list'],
        'method' => 'get',
//        'fieldConfig' => [
//            'template' => "{label}\n<div class=\"col-sm-2\">{input}</div>",
//            'labelOptions' => ['class' => 'col-lg-1 control-label text-right'],
//        ],

    ]); ?>

    <div class="row">
        <div class="col-lg-2">
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
        <div class="col-lg-2">
            <?= $form->field($model, 'group_status')->dropDownList(OrderGroup::$order_group_status, ['prompt' => '请选择']) ?>
        </div>
        <div class="col-lg-3">
            <div class="col-lg-6">
                <?= $form->field($model, 'mobile') ?>
            </div>

            <div class="col-lg-6">
                <?= $form->field($model, 'consignee') ?>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="col-lg-6">
                <?= $form->field($model, 'group_id') ?>
            </div>

            <div class="col-lg-6">
                <?= $form->field($model, 'order_sn') ?>
            </div>
        </div>

        <div class="form-group col-lg-2">
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<br />
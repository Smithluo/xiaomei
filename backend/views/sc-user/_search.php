<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Users;
use common\models\Region;
use common\helper\DateTimeHelper;
use kartik\widgets\DatePicker;
use common\models\UserExtension;
/* @var $this yii\web\View */
/* @var $model backend\models\ScUsersSearch */
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

<div class="users-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-sm-2\">{input}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label text-right'],
        ],
        'fieldClass' => 'common\widgets\ActiveField',
    ]); ?>
    <div class="col-lg-2">
        <label>注册时间</label>
        <?= DatePicker::widget([
            'name' => 'ScUsersSearch[reg_time_start]',
            'name2' => 'ScUsersSearch[reg_time_end]',
            'value' => !empty($model->reg_time_start)
                ? DateTimeHelper::getFormatCNDate($model->reg_time_start)
                : '',
            'value2' => DateTimeHelper::getFormatCNDate($model->reg_time_end),
            'type' => DatePicker::TYPE_RANGE,

            'convertFormat' => true,
            'pluginOptions' => [
                'format' => 'yyyy-MM-dd',
                'todayHighlight' => true,
                'autoclose' => true,
            ]
        ]);?>

        <label>最近登录时间</label>
        <?= DatePicker::widget([
            'name' => 'ScUsersSearch[last_login_start]',
            'name2' => 'ScUsersSearch[last_login_end]',
            'value' => !empty($model->last_login_start)
                ? DateTimeHelper::getFormatCNDate($model->last_login_start)
                : '',
            'value2' => DateTimeHelper::getFormatCNDate($model->last_login_end),
            'type' => DatePicker::TYPE_RANGE,

            'convertFormat' => true,
            'pluginOptions' => [
                'format' => 'yyyy-MM-dd',
                'todayHighlight' => true,
                'autoclose' => true,
            ]
        ]);?>
    </div>

    <div class="col-lg-10">
        <?= $form->field($model, 'user_id') ?>

        <?= $form->field($model, 'user_type')->dropDownList(Users::$user_type_map, ['prompt' => '请选择']) ?>

        <?= $form->field($model, 'province')->widget(kartik\select2\Select2::className(), [
            'data' => $provinceMap,
            'options' => [
                'placeholder' => '选择注册时填写的省',
            ],
            'pluginOptions' => [
                'allowClear'=>true,
                'width'=>'100%',
            ],
        ]) ?>

        <?= $form->field($model, 'company_name') ?>

        <?= $form->field($model, 'mobile_phone') ?>

        <?= $form->field($model, 'user_rank')->dropDownList(Users::$user_rank_map, ['prompt' => '请选择']) ?>

        <?= $form->field($model, 'city')->widget(kartik\select2\Select2::className(), [
            'data' => $cityMap,
            'options' => [
                'placeholder' => '选择注册时填写的城市',
            ],
            'pluginOptions' => [
                'allowClear'=>true,
                'width'=>'100%',
            ],
        ]) ?>

        <?= $form->field($model, 'nickname') ?>

        <?= $form->field($model, 'user_name') ?>

        <?= $form->field($model, 'is_checked')->dropDownList(Users::$is_checked_map, ['prompt' => '请选择']) ?>

        <?= $form->field($model, 'checked_note') ?>

        <?= $form->field($model, 'qq') ?>

        <?= $form->field($model, 'doneAmount') ->label('成单金额 >')?>

        <?= $form->field($model, 'int_balance')->label('可用积分 >') ?>

        <?= $form->field($model, 'is_identify')->dropDownList(UserExtension::$identify_map , ['prompt' => '请选择'])->label('是否认证') ?>

        <?= $form->field($model, 'duty')->dropDownList(UserExtension::$duty_map, ['prompt' => '请选择'])->label('职务') ?>

        <?= $form->field($model, 'channel')->dropDownList(Users::$channel_map, ['prompt' => '请选择'])->label('了解小美诚品的渠道') ?>

        <?= $form->field($model, 'sale_count')->dropDownList(UserExtension::$sale_count_map, ['prompt' => '请选择'])->label('月销量') ?>

        <?= $form->field($model, 'imports')->dropDownList(UserExtension::$import_map, ['prompt' => '请选择'])->label('进口品占比') ?>

        <?= $form->field($model, 'store_number')->label('店铺数量') ?>



        <div class="form-group text-center">
            <?= Html::submitButton('筛选', ['class' => 'btn btn-primary']) ?>
            <?php // echo Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        </div>
   </div>
    <?php ActiveForm::end(); ?>
</div>
<br />

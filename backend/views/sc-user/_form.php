<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Users;
use common\helper\CacheHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $form yii\widgets\ActiveForm */

$province_list = CacheHelper::getRegionCache([
    'type' => 'tree',
    'deepth' => 1,
]);

$servicer_list = CacheHelper::getServicerCache();
$servicer_map = [];
foreach ($servicer_list as $servicer) {
    if ($servicer['nickname']) {
        $servicer_map[$servicer['user_id']] = $servicer['nickname'].' | '.$servicer['user_name'];
    } else {
        $servicer_map[$servicer['user_id']] = $servicer['user_name'];
    }
}
//  暂时放开服务商的选择，改版后，服务商管辖区域的用户显示对应的服务商列表，不显示全部服务商
$servicer_show = true;

//  如果用户来自特殊渠道，则暂时显示绑定服务商
/*$servicer_show = false;
$spec_channel = Yii::$app->params['spec_channel'];
if ($model->is_checked == Users::IS_CHECKED_STATUS_PASSED && (
        !empty($model->channel) || !in_array($model->channel, $spec_channel)
    )
) {
    $servicer_show = true;
}*/
?>

<div class="users-form">
    <p style="color: red">
        <strong>
            注意：编辑用户的 绑定服务商 要谨慎，如果是测试，尽量不要绑定真实的服务商，service是测试的，如果一定要临时绑定真实服务商，务必在验证后解除绑定，否则夜里系统自动计算服务商分成会多给服务商分钱
        </strong>
    </p>
    <p style="color: red">
        <strong>
            天津服务商 服务区域：天津市、秦皇岛市、唐山市、廊坊市，这四个城市的 已审核过的会员没有绑定服务商的，【确定要转移给天津服务商的时候】手动绑定到天津服务商名下，省市信息不要修改
        </strong>
    </p>
    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "<div class='row'>
                {label}\n
                <div class=\"col-lg-8\">{input}</div>\n
                <div class=\"col-lg-4\"></div>
                <div class=\"col-lg-8\">{error}</div>
            </div>",
            'labelOptions' => ['class' => 'col-lg-4 control-label text-right'],
        ],
    ]); ?>
    <div class="col-lg-4">
    <?= '微信头像：'.Html::img($model->headimgurl, ['width' => '80px', 'height' => '80px']) ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <?php
    if ($model->isNewRecord) {
        echo $form->field($model, 'mobile_phone')->textInput(['maxlength' => true]);
    }
    ?>

    <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>

    <?=
        $form->field($model, 'province')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $province_list,
            'options' => ['placeholder' => '请选择所属省份'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])
    ?>

    <?= $form->field($model, 'user_rank')->dropDownList(Users::$user_rank_map) ?>

    <?= $form->field($model, 'is_checked')->dropDownList(Users::$is_checked_map) ?>

    <?= $form->field($model, 'checked_note')->textarea(['maxlength' => 255, 'rows' => 3, 'placeholder' => '请前往审核']) ?>

    <?php //echo $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-lg-4">
    <?php if ($servicer_show) : ?>
    <?= $form->field($model, 'servicer_user_id')->dropDownList($servicer_map, ['prompt' => '请选择服务商']) ?>
    <?php // echo $form->field($model, 'servicer_super_id')->dropDownList($servicer_map, ['prompt' => '请选择服务商']) ?>
    <?php endif; ?>

    <?= $form->field($model, 'last_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'visit_count')->textInput() ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'office_phone')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'sex')->textInput() ?>

    <?= $form->field($model, 'is_special')->textInput() ?>

    <?= $form->field($model, 'is_validated')->textInput() ?>

    <?= $form->field($model, 'channel')->textInput(['maxlength' => true]) ?>


    <?php echo $form->field($model, 'regionList')->widget(kartik\widgets\Select2::className(), [
        'data' => \backend\models\Region::getRegionData($model->user_id),
        'options' => [
            'multiple' => true,
        ],
    ])->label('服务区域') ?>

    </div>

    <div class="form-group col-lg-12">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>


    <?php //echo  $form->field($model, 'openid')->textInput(['maxlength' => true]) ?>

    <?php //echo  $form->field($model, 'unionid')->textInput(['maxlength' => true]) ?>

    <?php //echo  $form->field($model, 'wx_pc_openid')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'qq_open_id')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //echo $form->field($model, 'user_money')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //echo $form->field($model, 'frozen_money')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //echo $form->field($model, 'pay_points')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //echo $form->field($model, 'rank_points')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //echo $form->field($model, 'credit_line')->textInput(['maxlength' => true]) ?>

    <!--    --><?php // $form->field($model, 'birthday')->textInput() ?>

    <!--    --><?php // $form->field($model, 'parent_id')->textInput() ?>

    <!--    --><?php // $form->field($model, 'flag')->textInput() ?>

    <!--    --><?php // $form->field($model, 'zone_id')->textInput(['maxlength' => true]) ?>

    <?php
    /*$form->field($model, 'reg_time')->textInput([
        'value' => DateTimeHelper::getFormatCNDateTime($model->reg_time),
        'readonly' => true
    ])*/
    ?>

    <?php // $form->field($model, 'ec_salt')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'salt')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'brand_id_list')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'licence_image')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'access_token')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'question')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'answer')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'servicer_info_id')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'aite_id')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'home_phone')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'passwd_question')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'passwd_answer')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'qq')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'msn')->textInput(['maxlength' => true]) ?>
</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Users;
use common\helper\CacheHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Users */
/* @var $form yii\widgets\ActiveForm */

$provinces = CacheHelper::getRegionCache([
    'type' => 'tree',
    'ids' => [],
    'deepth' => 0
]);

$regionData = [];
foreach ($provinces as $province) {
    CacheHelper::getRegionIdArrayFromTree($regionData, $province);
}

$userRegions = \backend\models\UserRegion::find()->where([
    'not',
    [
        'user_id' => $model->user_id,
    ]
])->asArray()->all();

$userRegions = array_column($userRegions, 'region_id');

foreach ($regionData as $regionId => $regionName) {
    //已经被分配给其他服务商了
    if (in_array($regionId, $userRegions)) {
        $regionData[$regionId] = $regionName. '(已分配给其他用户了)';
    }
}

?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-3">
        <?php echo $isCreate ?
            $form->field($model, 'user_name')->textInput(['maxlength' => true]) : $form->field($model, 'user_name')->textInput(['maxlength' => true, 'readonly'=>'readonly'])
        ?>
        </div>
        <?php if ($isCreate) {
            ?>
        <div class="col-lg-3">
            <?php echo $form->field($model, 'password')->textInput(['maxlength' => true]) ?>
        </div>
        <?php } ?>

        <div class="col-lg-3">
        <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'mobile_phone')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'office_phone')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?php echo $form->field($model, 'regionList')->widget(kartik\widgets\Select2::className(), [
                'data' => $regionData,
                'options' => [
                    'multiple' => true,
                ],
            ])->label('服务区域') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'user_rank')->hiddenInput(['value' => Users::USER_RANK_MEMBER])->label('') ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'user_type')->hiddenInput(['value' => Users::USER_TYPE_SERVICER])->label('') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
        <?= $form->field($model, 'is_checked')->hiddenInput(['value' => Users::IS_CHECKED_STATUS_PASSED])->label('') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
        <?= $form->field($bankModel, 'user_name')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($bankModel, 'id_card_no')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($bankModel, 'bank_name')->textInput(['maxLength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
        <?= $form->field($bankModel, 'bank_card_no')->textInput(['maxLength' => true]) ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($bankModel, 'bank_address')->textInput(['maxLength' => true]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

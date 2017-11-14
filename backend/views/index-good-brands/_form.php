<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\IndexGoodBrands;
/* @var $this yii\web\View */
/* @var $model common\models\IndexGoodBrands */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="season-goods-form">

    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-lg-3">
                <?php echo $form->field($model, "brand_id")->widget(kartik\widgets\Select2::className(), [
                    'data' => IndexGoodBrands::Brands(),
                    'options' => [
                        'multiple' => false,
                        'prompt' => '请选择品牌'
                    ],
                ]) ?>
            </div>
            <div class="col-lg-2">
                <?= $form->field($model, "title")->textInput() ?>
            </div>

            <div class="col-lg-2">
                <?= $form->field($model, "sort_order")->textInput() ?>
            </div>

            <div class="col-lg-2">
                <?= $form->field($model, "index_logo")->fileInput() ?>
            </div>
            <?php if(!$model->isNewRecord):?>
            <div class="clo-lg-2">
                <img src="<?= $model->getUploadUrl('index_logo') ?>" alt="">
            </div>
            <?php endif;?>
        </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
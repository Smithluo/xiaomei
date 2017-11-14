<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\IndexGroupBuy;
/* @var $this yii\web\View */
/* @var $model common\models\IndexGroupBuy */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="season-goods-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php if($model->isNewRecord) :?>
        <?php for($i=0; $i < 5 ;$i++ ):?>
            <div class="row">
                <div class="col-lg-2">
                    <?= $form->field($model, "[$i]title")->textInput() ?>
                </div>
                <div class="col-lg-3">
                    <?php echo $form->field($model, "[$i]activity_id")->widget(kartik\widgets\Select2::className(), [
                        'data' => IndexGroupBuy::GroupBuy(),
                        'options' => [
                            'multiple' => false,
                            'prompt' => '请选择团采活动'
                        ],
                    ]) ?>
                </div>

                <div class="col-lg-2">
                    <?= $form->field($model, "[$i]sort_order")->textInput() ?>
                </div>
            </div>
        <?php endfor;?>
    <?php else:?>
        <div class="row">
            <div class="col-lg-2">
                <?= $form->field($model, "title")->textInput() ?>
            </div>
            <div class="col-lg-3">
                <?php echo $form->field($model, "activity_id")->widget(kartik\widgets\Select2::className(), [
                    'data' => IndexGroupBuy::GroupBuy(),
                    'options' => [
                        'multiple' => false,
                        'prompt' => '请选择团采活动'
                    ],
                ]) ?>
            </div>

            <div class="col-lg-2">
                <?= $form->field($model, "sort_order")->textInput() ?>
            </div>
        </div>
    <?php endif;?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\GuideGoods;
/* @var $this yii\web\View */
/* @var $model common\models\GuideGoods */
/* @var $form yii\widgets\ActiveForm */
$typeList = GuideGoods::TypeMap();
$goodsList =GuideGoods::Goods();
?>

<div class="guide-goods-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php if($model->isNewRecord):?>
    <?php for($i =0; $i < 6; $i++):?>
    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, "[$i]type")->dropDownList($typeList, ['prompt' => '请选择类别']) ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, "[$i]goods_id")->widget(\kartik\widgets\Select2::className(),[
                'data' => $goodsList,
                'options' => [
                    'multiple' => false,
                    'prompt' => '请选择商品'
                ],
            ]) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, "[$i]sort_order")->textInput()?>
        </div>
    </div>
    <?php endfor;?>
    <?php else:?>
        <div class="row">
            <div class="col-lg-2">
                <?= $form->field($model, "type")->dropDownList($typeList, ['prompt' => '请选择类别']) ?>
            </div>

            <div class="col-lg-3">
                <?= $form->field($model, "goods_id")->widget(\kartik\widgets\Select2::className(),[
                    'data' => $goodsList,
                    'options' => [
                        'multiple' => false,
                        'prompt' => '请选择商品'
                    ],
                ]) ?>
            </div>
            <div class="col-lg-2">
                <?= $form->field($model, "sort_order")->textInput()?>
            </div>
        </div>
    <?php endif;?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

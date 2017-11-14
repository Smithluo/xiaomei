<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-09-25
 * Time: 10:40
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>

<p>特殊商品列表</p>

<div class="brand-spec-goods-cat-form" style="border: 1px solid #00a0e9;">

    <?php $form = ActiveForm::begin([
        'action' => [
            '/brand-spec-goods-cat/create',
            'brandId' => $brandId,
        ],
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'sort_order')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= Html::submitButton('新建商品类别', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\SeasonGoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="season-goods-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'type')->dropDownList(\common\models\SeasonGoods::Type()) ?>
        </div>
    </div>




    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

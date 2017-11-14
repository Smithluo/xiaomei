<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helper\DateTimeHelper;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsPkg */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-pkg-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "<div class='row'>
                {label}\n
                <div class=\"col-lg-4\">{input}</div>\n
                <div class=\"col-lg-3\">{error}</div>
            </div>",
            'labelOptions' => ['class' => 'col-lg-2 control-label text-right'],
        ],
    ]); ?>
    
<div class="col-lg-9">
    <?= $form->field($model, 'pkg_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'allow_goods_list')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'deny_goods_list')->textarea(['rows' => 6]) ?>
</div>
<div class="col-lg-3">
    <p>
        时间充足的时候会在这里补充商品搜索功能 id_list
    </p>
    <?= $form->field($model, 'updated_at')->hiddenInput(['value' => DateTimeHelper::getFormatGMTTimesTimestamp(time())])->label('') ?>
</div>
    <div class="form-group col-lg-12">

        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helper\DateTimeHelper;
use backend\models\UserRank;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */
/* @var $form yii\widgets\ActiveForm */

$is_or_not_map = Yii::$app->params['is_or_not_map'];
?>

<div class="goods-form">
    <h3>tips:参与团采活动的商品可以编辑库存</h3>
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

    <div class="row">
        <div class="col-lg-5">
            <?= $form->field($model, 'tagIds')->checkboxList($allTagIds) ?>

            <?= $form->field($model, 'is_hot')->dropDownList($is_or_not_map) ?>

            <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'goods_brief')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'sort_order')->textInput() ?>

            <?php
                if (!$model->is_on_sale) {
                    echo $form->field($model, 'goods_number')->textInput();
                }
            ?>

            <?= $form->field($model, 'seller_note')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'need_rank')->dropDownList(UserRank::$user_rank_map)?>

            <div class="col-lg-6">
                <?= $form->field($model, 'last_update')->textInput([
                    'vlaue' => DateTimeHelper::getFormatGMTTimesTimestamp(time())
                ])->hiddenInput()->label('') ?>
            </div>

            <?php //echo $form->field($model, 'is_best')->dropDownList($is_or_not_map) ?>

            <?php //echo $form->field($model, 'is_new')->dropDownList($is_or_not_map) ?>

            <?php //echo $form->field($model, 'is_spec')->dropDownList($is_or_not_map) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php //echo $form->field($model, 'goods_desc')->textarea(['rows' => 6]) ?>

<?php //echo $form->field($model, 'goods_name_style')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'click_count')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'brand_id')->textInput() ?>

<?php //echo $form->field($model, 'provider_name')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'measure_unit')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'number_per_box')->textInput() ?>

<?php //echo $form->field($model, 'goods_weight')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'market_price')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'shop_price')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'min_price')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'promote_price')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'promote_start_date')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'promote_end_date')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'warn_number')->textInput() ?>

<?php //echo $form->field($model, 'goods_thumb')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'goods_img')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'original_img')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'is_real')->textInput() ?>

<?php //echo $form->field($model, 'extension_code')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'is_on_sale')->textInput() ?>

<?php //echo $form->field($model, 'is_alone_sale')->textInput() ?>

<?php //echo $form->field($model, 'is_shipping')->textInput() ?>

<?php //echo $form->field($model, 'integral')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'add_time')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'is_promote')->textInput() ?>

<?php //echo $form->field($model, 'bonus_type_id')->textInput() ?>

<?php //echo $form->field($model, 'goods_type')->textInput() ?>

<?php //echo $form->field($model, 'give_integral')->textInput() ?>

<?php //echo $form->field($model, 'rank_integral')->textInput() ?>

<?php //echo $form->field($model, 'suppliers_id')->textInput() ?>

<?php //echo $form->field($model, 'is_check')->textInput() ?>

<?php //echo $form->field($model, 'children')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'shelf_life')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'start_num')->textInput() ?>

<?php //echo $form->field($model, 'discount_disable')->textInput() ?>

<?php //echo $form->field($model, 'cat_id')->textInput() ?>

<?php //echo $form->field($model, 'goods_sn')->textInput(['maxlength' => true]) ?>

<?php //echo $form->field($model, 'goods_name')->textInput(['maxlength' => true]) ?>

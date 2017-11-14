<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(
        [
            'fieldConfig' => [
                'template' => "<div class='row'>{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-lg-3\"></div><div class=\"col-lg-6\">{error}</div></div>",
                'labelOptions' => ['class' => 'col-lg-3 control-label text-right'],
            ],
        ]
    ); ?>

    <div class="col-lg-3">
        <?=
        $form->field($model, 'parent_id')->widget(\kartik\widgets\Select2::classname(), [
            'data' => $allCategories,
            'options' => ['placeholder' => '选择父分类'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])
        ?>

        <?= $form->field($model, 'cat_name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-3">
        <?= $form->field($model, 'sort_order')->textInput() ?>

        <?= $form->field($model, 'keywords')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-lg-3">
        <?= $form->field($model, 'is_show')->dropDownList([
            '不显示',
            '显示'
        ]) ?>

        <?= $form->field($model, 'show_in_nav')->textInput()->dropDownList([
            '不显示',
            '显示'
        ]) ?>
    </div>

    <div class="col-lg-3">

        <?= $form->field($model, 'album_id')->dropDownList($albums) ?>

        <?= $form->field($model, 'brand_list')->textInput(['maxlength' => true, 'placeholder' => '显示在首页分类树的推荐品牌，一般是4个']) ?>
    </div>

    <div class="col-lg-12">
    <?= $form->field($model, 'cat_desc', [
        'labelOptions' => ['class' => 'col-lg-1 control-label text-right'],
        'template' => '<div class=\'row\'>{label}<div class="col-lg-11">{input}</div><div class="col-lg-1"></div><div class="col-lg-11">{error}</div></div>',
    ])->textarea(['maxlength' => true]) ?>
    </div>

    <div class="form-group col-lg-12">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php //echo $form->field($model, 'grade')->textInput() ?>
<?php //echo $form->field($model, 'template_file')->textInput(['maxlength' => true]) ?>
<?php //echo $form->field($model, 'style')->textInput(['maxlength' => true]) ?>
<?php //echo $form->field($model, 'filter_attr')->textInput(['maxlength' => true]) ?>

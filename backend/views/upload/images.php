<?php
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-5\">{input}</div>\n<div class=\"col-lg-3\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-2 control-label'],  //修改label的样式
    ],

]);

//it is necessary to see all the errors for all the files.
if ($model->hasErrors()) {
    echo '<pre>';
    print_r($model->getErrors());
    echo '</pre>';
}
?>

<?= $form->field($model, 'file[]')->fileInput() ?>

<?= $form->field($model, 'file[]')->fileInput() ?>

<?= $form->field($model, 'file[]')->fileInput() ?>

<?= $form->field($model, 'file[]')->fileInput() ?>

<?= $form->field($model, 'file[]')->fileInput() ?>

    <button>Submit</button>

<?php ActiveForm::end() ?>

<?php
    if ($result && is_array($result)) {
        foreach ($result as $item) {
            echo $item.'<br />';
        }
    }
?>

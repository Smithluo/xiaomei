<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}
?>
-use yii\helpers\Html<?= "\n" ?>
-use yii\widgets\ActiveForm<?= "\n" ?>
.<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form

  -$form = ActiveForm::begin();

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "  !=" . $generator->generateActiveField($attribute) . "\n";
    }
} ?>
  .form-group
    <?= "!=" ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])

  -ActiveForm::end()

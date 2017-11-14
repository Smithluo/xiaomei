<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */
?>
-use yii\helpers\Html<?= "\n" ?>
-use yii\widgets\ActiveForm<?= "\n" ?>
.<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search
  -$form = ActiveForm::begin(['action' => ['index'],'method' => 'get',])
<?php
$count = 0;
foreach ($generator->getColumnNames() as $attribute) {
    if (++$count < 6) {
        echo "  !=" . $generator->generateActiveSearchField($attribute) . "\n";
    } else {
        echo "  //-!=" . $generator->generateActiveSearchField($attribute) . "\n";
    }
}
?>
  .form-group
    <?= "!=" ?>Html::submitButton(<?= $generator->generateString('Search') ?>, ['class' => 'btn btn-primary'])
    <?= "!=" ?>Html::resetButton(<?= $generator->generateString('Reset') ?>, ['class' => 'btn btn-default'])

  -ActiveForm::end()

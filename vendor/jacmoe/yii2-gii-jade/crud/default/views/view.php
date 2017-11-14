<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

?>
-use yii\helpers\Html<?= "\n" ?>
-use yii\widgets\DetailView<?= "\n" ?>
-$view->title = $model-><?= $generator->getNameAttribute() ?><?= "\n" ?>
-$view->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']]<?= "\n" ?>
-$view->params['breadcrumbs'][] = $view->title<?= "\n" ?>
.<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view
  h1
    !=Html::encode($view->title)
  p
    !=Html::a(<?= $generator->generateString('Update') ?>, ['update', <?= $urlParams ?>], ['class' => 'btn btn-primary'])
    !=Html::a(<?= $generator->generateString('Delete') ?>, ['delete', <?= $urlParams ?>], ['class' => 'btn btn-danger','data' => ['confirm' => <?= $generator->generateString('Are you sure you want to delete this item?') ?>,'method' => 'post',],])

  -
    $attributes = [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "    '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        echo "    '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
    }
}?><?="    ]"?>

  !=DetailView::widget(['model' => $model,'attributes' => $attributes ])

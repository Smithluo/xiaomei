<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */
?>
-use yii\helpers\Html<?= "\n" ?>
-$view->title = <?= $generator->generateString('Create ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?><?= "\n" ?>
-$view->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']]<?= "\n" ?>
-$view->params['breadcrumbs'][] = $view->title<?= "\n" ?>
.<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create
  h1
    <?= "!=" ?>Html::encode($view->title)

  <?= "!=" ?>$view->render('_form', ['model' => $model,])

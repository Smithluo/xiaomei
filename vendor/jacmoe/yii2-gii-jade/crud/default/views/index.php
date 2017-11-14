<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
?>
-use yii\helpers\Html<?= "\n" ?>
-use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?><?= "\n" ?>
-$view->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?><?= "\n" ?>
-$view->params['breadcrumbs'][] = $view->title<?= "\n" ?>
.<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index
  h1
    <?= "!=" ?>Html::encode($view->title)
<?php if(!empty($generator->searchModelClass)): ?>
<?= "  ". ($generator->indexWidgetType === 'grid' ? "//-!= " : "!=") ?>$view->render('_search', ['model' => $searchModel]) ?>
<?php endif; ?>

  p
    <?= "!=" ?>Html::a(<?= $generator->generateString('Create ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>, ['create'], ['class' => 'btn btn-success'])

<?php if ($generator->indexWidgetType === 'grid'): ?>

  -
    $columns = [
    ['class' => 'yii\grid\SerialColumn'],
<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "    '" . $name . "',\n";
        } else {
            echo "    //'" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "    '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "    //'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>
    ['class' => 'yii\grid\ActionColumn'],
    ]

  <?= "!=" ?>GridView::widget(['dataProvider' => $dataProvider, <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel," : ""?> 'columns' => $columns ])

<?php else: ?>
  <?= "!=" ?>ListView::widget(['dataProvider' => $dataProvider,'itemOptions' => ['class' => 'item'],'itemView' => '_view'])
<?php endif; ?>

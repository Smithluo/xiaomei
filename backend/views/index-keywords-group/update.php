<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexKeywordsGroup */

$this->title = 'Update Index Keywords Group: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Index Keywords Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="index-keywords-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?= $this->render('_keywords_list', [
        'model' => $model,
        'itemSearchModel' => $itemSearchModel,
        'itemDataProvider' => $itemDataProvider,
    ])
    ?>

    <?= $this->render('_create_keywords', [
        'model' => $model,
        'newKeywords' => $newKeywords,
    ])
    ?>
</div>

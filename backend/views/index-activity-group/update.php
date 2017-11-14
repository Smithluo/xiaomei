<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexActivityGroup */

$this->title = 'Update Index Activity Group: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Index Activity Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="index-activity-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

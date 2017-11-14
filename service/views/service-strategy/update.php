<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model service\models\ServiceStrategy */

$this->title = 'Update Service Strategy: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Service Strategies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="service-strategy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ServicerDivideRecord */

$this->title = 'Update Servicer Divide Record: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Servicer Divide Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="servicer-divide-record-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

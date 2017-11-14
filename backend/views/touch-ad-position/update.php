<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TouchAdPosition */

$this->title = 'Update Touche-Ad-Position: ' . $model->position_id;
$this->params['breadcrumbs'][] = ['label' => 'Touche-Ad-Positions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->position_id, 'url' => ['view', 'id' => $model->position_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ad-position-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

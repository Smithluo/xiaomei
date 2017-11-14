<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AdPosition */

$this->title = 'Update Ad Position: ' . $model->position_id;
$this->params['breadcrumbs'][] = ['label' => 'Ad Positions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->position_id, 'url' => ['view', 'id' => $model->position_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ad-position-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

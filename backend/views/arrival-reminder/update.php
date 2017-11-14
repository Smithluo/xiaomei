<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ArrivalReminder */

$this->title = 'Update Arrival Reminder: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Arrival Reminders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="arrival-reminder-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

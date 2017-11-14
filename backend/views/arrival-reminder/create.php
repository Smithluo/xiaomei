<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ArrivalReminder */

$this->title = 'Create Arrival Reminder';
$this->params['breadcrumbs'][] = ['label' => 'Arrival Reminders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="arrival-reminder-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Feedback */

$this->title = 'Update Feedback: ' . $model->msg_id;
$this->params['breadcrumbs'][] = ['label' => 'Feedbacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->msg_id, 'url' => ['view', 'id' => $model->msg_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="feedback-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

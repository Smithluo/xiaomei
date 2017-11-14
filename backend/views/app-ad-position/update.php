<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AppAdPosition */

$this->title = 'Update App Ad Position: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'App Ad Positions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="app-ad-position-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

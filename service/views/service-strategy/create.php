<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model service\models\ServiceStrategy */

$this->title = 'Create Service Strategy';
$this->params['breadcrumbs'][] = ['label' => 'Service Strategies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-strategy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

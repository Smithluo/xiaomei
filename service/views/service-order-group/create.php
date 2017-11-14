<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OrderGroup */

$this->title = 'Create Order Group';
$this->params['breadcrumbs'][] = ['label' => 'Order Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

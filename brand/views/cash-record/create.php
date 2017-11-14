<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model brand\models\CashRecord */

$this->title = 'Create Cash Record';
$this->params['breadcrumbs'][] = ['label' => 'Cash Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

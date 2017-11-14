<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ServicerDivideRecord */

$this->title = 'Create Servicer Divide Record';
$this->params['breadcrumbs'][] = ['label' => 'Servicer Divide Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicer-divide-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

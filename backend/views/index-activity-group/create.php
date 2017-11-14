<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexActivityGroup */

$this->title = 'Create Index Activity Group';
$this->params['breadcrumbs'][] = ['label' => 'Index Activity Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-activity-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

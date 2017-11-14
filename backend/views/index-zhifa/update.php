<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexZhifa */

$this->title = '编辑小美直发: ' ;
$this->params['breadcrumbs'][] = ['label' => '小美直发', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="index-zhifa-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

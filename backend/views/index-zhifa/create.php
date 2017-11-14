<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexZhifa */

$this->title = '小美直发配置';
$this->params['breadcrumbs'][] = ['label' => '小美直发', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-zhifa-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

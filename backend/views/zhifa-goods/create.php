<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ZhifaGoods */

$this->title = 'Create Zhifa Goods';
$this->params['breadcrumbs'][] = ['label' => 'Zhifa Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zhifa-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

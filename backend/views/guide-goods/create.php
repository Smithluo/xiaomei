<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GuideGoods */

$this->title = '添加选品指南商品';
$this->params['breadcrumbs'][] = ['label' => '选品指南', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="guide-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

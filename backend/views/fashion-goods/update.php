<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\FashionGoods */

$this->title = '编辑潮流爆款: '.$model->goods['goods_name'];
$this->params['breadcrumbs'][] = ['label' => '潮流爆款', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="fashion-goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

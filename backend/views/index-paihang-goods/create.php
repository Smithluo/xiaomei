<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexPaihangGoods */

$this->title = '新建排行商品';
$this->params['breadcrumbs'][] = ['label' => '排行商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-paihang-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

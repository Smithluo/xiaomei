<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GoodsPkg */

$this->title = '创建商品包';
$this->params['breadcrumbs'][] = ['label' => '商品包', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-pkg-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\GoodsPkg */

$this->title = 'Update 商品包: ' . $model->pkg_id;
$this->params['breadcrumbs'][] = ['label' => '商品包', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pkg_id, 'url' => ['view', 'id' => $model->pkg_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="goods-pkg-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

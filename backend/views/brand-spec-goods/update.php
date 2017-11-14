<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BrandSpecGoods */

$this->title = 'Update Brand Spec Goods: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Brand Spec Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="brand-spec-goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

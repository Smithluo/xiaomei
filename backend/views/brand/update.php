<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Brand */

$this->title = '编辑品牌信息: ' . $model->brand_name;
$this->params['breadcrumbs'][] = ['label' => '编辑品牌信息', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->brand_name, 'url' => ['view', 'id' => $model->brand_id]];
?>
<div class="brand-update">

    <?= $this->render('_form', [
        'model' => $model,
        'shippingList' => $shippingList,
        'suppliers' => $suppliers,
    ]) ?>

</div>

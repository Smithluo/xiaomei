<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Brand */

$this->title = 'Create Brand';
$this->params['breadcrumbs'][] = ['label' => 'Brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-create">

    <p style="color: red">
        <strong>注意：新创建的品牌默认不显示，避免信息不完整的品牌直接显示在商城上。如果需要显示，请手动修改。 | 分成比例可以不设置，默认为0，如果有分成比例，请手动修改</strong>
    </p>

    <?= $this->render('_form', [
        'model' => $model,
        'shippingList' => $shippingList,
        'suppliers' => $suppliers,
        'touchBrand' => $touchBrand,
        'servicerStrategy' => $servicerStrategy,
    ]) ?>

</div>

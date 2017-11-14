<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BrandUser */

$this->title = '更新品牌商信息: ' . $model->user_id;
$this->params['breadcrumbs'][] = ['label' => 'Brand Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_id, 'url' => ['view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="brand-user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'bankModel' => $bankModel,
        'brandAdminModel' => $brandAdminModel,
        'brand_map' => $brand_map,
        'bind_brand_map' => $bind_brand_map,
    ]) ?>

</div>

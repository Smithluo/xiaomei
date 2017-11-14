<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ShopConfig */

$this->title = 'Update Shop Config: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shop Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shop-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

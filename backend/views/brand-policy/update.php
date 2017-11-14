<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BrandPolicy */

$this->title = 'Update 品牌增值政策: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '品牌增值政策', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="brand-policy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'brands' => $brands,
    ]) ?>

</div>

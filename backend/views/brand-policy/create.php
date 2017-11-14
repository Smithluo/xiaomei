<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\BrandPolicy */

$this->title = '新增品牌增值政策';
$this->params['breadcrumbs'][] = ['label' => '品牌增值政策', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-policy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'brands' => $brands,
        'isCreate' => true,
    ]) ?>

</div>

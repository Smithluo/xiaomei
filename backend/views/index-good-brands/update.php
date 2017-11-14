<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexGoodBrands */

$this->title = '编辑优选品牌: ' . $model->brand['brand_name'];
$this->params['breadcrumbs'][] = ['label' => '优选品牌', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="index-good-brands-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

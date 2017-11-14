<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\BrandSpecGoods */

$this->title = 'Create Brand Spec Goods';
$this->params['breadcrumbs'][] = ['label' => 'Brand Spec Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-spec-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\BrandSpecGoodsCat */

$this->title = 'Create Brand Spec Goods Cat';
$this->params['breadcrumbs'][] = ['label' => 'Brand Spec Goods Cats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-spec-goods-cat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

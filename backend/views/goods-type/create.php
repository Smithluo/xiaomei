<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GoodsType */

$this->title = 'Create Goods Type';
$this->params['breadcrumbs'][] = ['label' => 'Goods Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

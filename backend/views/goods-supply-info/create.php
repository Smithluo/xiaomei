<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GoodsSupplyInfo */

$this->title = 'Create Goods Supply Info';
$this->params['breadcrumbs'][] = ['label' => 'Goods Supply Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-supply-info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

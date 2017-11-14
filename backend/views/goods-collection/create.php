<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GoodsCollection */

$this->title = 'Create Goods Collection';
$this->params['breadcrumbs'][] = ['label' => 'Goods Collections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-collection-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

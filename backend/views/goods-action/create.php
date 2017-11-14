<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GoodsAction */

$this->title = 'Create Goods Action';
$this->params['breadcrumbs'][] = ['label' => 'Goods Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-action-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\FashionGoods */

$this->title = '创建潮流爆款';
$this->params['breadcrumbs'][] = ['label' => '潮流爆款', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fashion-goods-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

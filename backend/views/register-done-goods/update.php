<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RegisterDoneGoods */

$this->title = '更新宣导页商品';
$this->params['breadcrumbs'][] = ['label' => '宣导页商品', 'url' => ['index']];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="register-done-goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'goodsList' => $goodsList,
    ]) ?>

</div>

<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RegisterDoneGoods */

$this->title = '创建宣导页商品';
$this->params['breadcrumbs'][] = ['label' => '宣导页', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="register-done-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'goodsList' => $goodsList,
    ]) ?>

</div>

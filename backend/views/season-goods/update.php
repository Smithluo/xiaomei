<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SeasonGoods */

$this->title = '编辑应季好货: ' ;
$this->params['breadcrumbs'][] = ['label' => '应季好货', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="season-goods-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

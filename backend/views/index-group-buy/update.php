<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\IndexGroupBuy */

$this->title = '编辑团采: ' ;
$this->params['breadcrumbs'][] = ['label' => '团采', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="index-group-buy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

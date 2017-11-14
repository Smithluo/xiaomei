<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexGroupBuy */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '团采', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-group-buy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

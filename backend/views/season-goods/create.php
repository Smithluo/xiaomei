<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SeasonGoods */

$this->title = '创建应季好货';
$this->params['breadcrumbs'][] = ['label' => '应季好货', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-goods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

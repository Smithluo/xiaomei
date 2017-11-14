<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexPaihangFloor */

$this->title = '新增排行品类';
$this->params['breadcrumbs'][] = ['label' => '排行品类列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-paihang-floor-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SuperPkg */

$this->title = '更新超值礼包: ' . $model->pag_name;
$this->params['breadcrumbs'][] = ['label' => '超值礼包', 'url' => ['index']];
$this->params['breadcrumbs'][] = '更新';
$this->params['breadcrumbs'][] = ['label' => $model->pag_name, 'url' => ['view', 'id' => $model->id]];

?>
<div class="super-pkg-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'giftPkgList' => $giftPkgList,
    ]) ?>

</div>

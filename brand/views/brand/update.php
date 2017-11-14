<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Brand */

$this->title = '编辑品牌信息: ' . $model->brand_name;
$this->params['breadcrumbs'][] = ['label' => '旗下品牌', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->brand_name, 'url' => ['view', 'id' => $model->brand_name]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="brand-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

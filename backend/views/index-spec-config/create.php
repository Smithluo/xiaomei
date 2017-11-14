<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexSpecConfig */

$this->title = '新建首页活动专区配置：';
$this->params['breadcrumbs'][] = ['label' => '活动专区配置列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-spec-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'allGoods' => $allGoods,
    ]) ?>

</div>

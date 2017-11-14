<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\GuideType */

$this->title = '创建选品指南类别';
$this->params['breadcrumbs'][] = ['label' => '选品指南', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="guide-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

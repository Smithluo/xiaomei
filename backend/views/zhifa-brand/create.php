<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ZhifaBrand */

$this->title = 'Create Zhifa Brand';
$this->params['breadcrumbs'][] = ['label' => 'Zhifa Brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zhifa-brand-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

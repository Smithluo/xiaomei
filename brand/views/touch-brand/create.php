<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model brand\models\TouchBrand */

$this->title = 'Create Touch Brand';
$this->params['breadcrumbs'][] = ['label' => 'Touch Brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="touch-brand-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

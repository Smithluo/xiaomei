<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model brand\models\BrandDivideRecord */

$this->title = 'Create Brand Divide Record';
$this->params['breadcrumbs'][] = ['label' => 'Brand Divide Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-divide-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

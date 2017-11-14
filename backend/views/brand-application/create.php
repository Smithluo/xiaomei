<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model home\models\BrandApplication */

$this->title = 'Create Brand Application';
$this->params['breadcrumbs'][] = ['label' => 'Brand Applications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-application-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

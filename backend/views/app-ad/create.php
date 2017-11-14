<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AppAd */

$this->title = 'Create App Ad';
$this->params['breadcrumbs'][] = ['label' => 'App Ads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-ad-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

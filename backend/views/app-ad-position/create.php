<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AppAdPosition */

$this->title = 'Create App Ad Position';
$this->params['breadcrumbs'][] = ['label' => 'App Ad Positions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-ad-position-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

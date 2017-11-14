<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Ad */

$this->title = 'Update TouchAd: ' . $model->ad_id;
$this->params['breadcrumbs'][] = ['label' => 'Touch-Ads', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ad_id, 'url' => ['view', 'id' => $model->ad_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ad">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'touchAdPositions' => $touchAdPositions,
    ]) ?>

</div>

<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Ad */

$this->title = 'Create Ad';
$this->params['breadcrumbs'][] = ['label' => 'Ads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ad-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'adPositions' => $adPositions,
        'isCreate' => true,
    ]) ?>

</div>

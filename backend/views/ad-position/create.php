<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AdPosition */

$this->title = 'Create Ad Position';
$this->params['breadcrumbs'][] = ['label' => 'Ad Positions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ad-position-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

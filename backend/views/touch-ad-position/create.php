<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TouchAdPosition */

$this->title = 'Create Touch-Ad-Position';
$this->params['breadcrumbs'][] = ['label' => 'Touch-Ad-Positions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ad-position-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

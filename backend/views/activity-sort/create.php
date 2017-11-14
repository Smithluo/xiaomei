<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ActivitySort */

$this->title = 'Create Activity Sort';
$this->params['breadcrumbs'][] = ['label' => 'Activity Sorts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-sort-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'isShowMap' => $isShowMap,
    ]) ?>

</div>

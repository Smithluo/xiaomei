<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EventRule */

$this->title = '创建活动策略';
$this->params['breadcrumbs'][] = ['label' => '活动策略列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'giftEventMap' => $giftEventMap,
    ]) ?>

</div>

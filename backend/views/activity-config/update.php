<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ActivityConfig */

$this->title = '编辑活动配置: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '活动配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑 - '.$model->title;
?>
<div class="activity-config-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = '编辑用户信息: ' . $model->showName;
$this->params['breadcrumbs'][] = ['label' => '用户', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->showName, 'url' => ['view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="users-update">
    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

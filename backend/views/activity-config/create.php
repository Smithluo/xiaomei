<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ActivityConfig */

$this->title = '创建活动配置';
$this->params['breadcrumbs'][] = ['label' => '活动配置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

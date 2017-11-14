<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ActivityManzeng */

$this->title = '新建活动满赠商品';
$this->params['breadcrumbs'][] = ['label' => '活动满赠商品列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-manzeng-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

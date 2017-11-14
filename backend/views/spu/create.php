<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Spu */

$this->title = '新建SPU';
$this->params['breadcrumbs'][] = ['label' => 'SPU列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="spu-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

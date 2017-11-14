<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\KnowledgeShowBrand */

$this->title = 'Update Knowledge Show Brand: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '推荐品牌', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="knowledge-show-brand-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'brandMap' => $brandMap,
        'platformMap' => $platformMap
    ]) ?>

</div>

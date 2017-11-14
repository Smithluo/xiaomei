<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ResourceSite */

$this->title = '新建 资源站点';
$this->params['breadcrumbs'][] = ['label' => '资源站点', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resource-site-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

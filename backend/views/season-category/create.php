<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SeasonCategory */

$this->title = '创建应季配置';
$this->params['breadcrumbs'][] = ['label' => '应季分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

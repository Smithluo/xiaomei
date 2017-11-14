<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ArticleCat */

$this->title = '更新文章分类: ' . $model->cat_name;
$this->params['breadcrumbs'][] = ['label' => 'Article Cats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cat_name, 'url' => ['view', 'id' => $model->cat_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="article-cat-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

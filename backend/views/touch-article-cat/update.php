<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TouchArticleCat */

$this->title = '更新微信文章分类: ' . $model->cat_id;
$this->params['breadcrumbs'][] = ['label' => '微信文章分类列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cat_id, 'url' => ['view', 'id' => $model->cat_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="touch-article-cat-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

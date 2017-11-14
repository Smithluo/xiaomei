<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TouchArticleCat */

$this->title = '新建微信文章分类';
$this->params['breadcrumbs'][] = ['label' => '微信文章分类列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="touch-article-cat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

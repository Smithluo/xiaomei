<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TouchArticle */

$this->title = '更新微信文章: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '微信文章列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->article_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="touch-article-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'resourceTypeMap' => $resourceTypeMap,
        'resourceSiteMap' => $resourceSiteMap,
        'galleryMap' => $galleryMap,
    ]) ?>

</div>

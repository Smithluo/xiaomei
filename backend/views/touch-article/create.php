<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TouchArticle */

$this->title = '新建微信文章';
$this->params['breadcrumbs'][] = ['label' => '微信文章列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="touch-article-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'resourceTypeMap' => $resourceTypeMap,
        'resourceSiteMap' => $resourceSiteMap,
        'galleryMap' => $galleryMap,
    ]) ?>

</div>

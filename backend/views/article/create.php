<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = '创建文章';
$this->params['breadcrumbs'][] = ['label' => '文章列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="article-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'sceneMap' => $sceneMap,
        'categoryTree' => $categoryTree,
        'countryMap' => $countryMap,
        'resourceTypeMap' => $resourceTypeMap,
        'resourceSiteMap' => $resourceSiteMap,
        'galleryMap' => $galleryMap,
    ]) ?>

</div>

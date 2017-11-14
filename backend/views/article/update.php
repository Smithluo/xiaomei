<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Article */

$this->title = '更新文章: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '文章列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->article_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="article-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
        if ($model->article_id == 20) {
            echo '<p style="color: red"><strong>会员服务的内容编辑无效，找技术处理; 会员服务的属性编辑有效。</strong></p>';
        }
    ?>

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

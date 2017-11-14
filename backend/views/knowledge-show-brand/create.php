<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\KnowledgeShowBrand */

$this->title = '新增 推荐品牌';
$this->params['breadcrumbs'][] = ['label' => '推荐品牌', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="knowledge-show-brand-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'brandMap' => $brandMap,
        'platformMap' => $platformMap,
    ]) ?>

</div>

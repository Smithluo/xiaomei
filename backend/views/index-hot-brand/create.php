<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexHotBrand */

$this->title = '新建热门品牌';
$this->params['breadcrumbs'][] = ['label' => '热门品牌列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-hot-brand-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

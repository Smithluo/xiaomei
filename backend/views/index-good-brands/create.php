<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexGoodBrands */

$this->title = '创建优选品牌';
$this->params['breadcrumbs'][] = ['label' => '优选品牌', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-good-brands-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

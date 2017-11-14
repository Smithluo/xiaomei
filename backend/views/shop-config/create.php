<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ShopConfig */

$this->title = 'Create Shop Config';
$this->params['breadcrumbs'][] = ['label' => 'Shop Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

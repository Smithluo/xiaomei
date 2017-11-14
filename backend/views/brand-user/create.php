<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\BrandUser */

$this->title = '创建品牌商账号';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'bankModel' => $bankModel,
        'brandAdminModel' => $brandAdminModel,
        'brand_map' => $brand_map,
    ]) ?>

</div>

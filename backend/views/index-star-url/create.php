<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexStarUrl */

$this->title = '新建楼层链接(显示在PC站楼层左侧)';
$this->params['breadcrumbs'][] = ['label' => '楼层链接列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-star-url-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

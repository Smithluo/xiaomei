<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\IndexKeywordsGroup */

$this->title = 'Create Index Keywords Group';
$this->params['breadcrumbs'][] = ['label' => 'Index Keywords Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="index-keywords-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

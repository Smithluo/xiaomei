<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BrandApplicationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '品牌入驻申请';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-application-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'company_name',
            'company_address',
            'name',
            'position',
            'contact',
            'brands',
            'licence',
            'recorded',
            'registed',
            'taxed',
            'checked',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

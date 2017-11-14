<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel home\models\BrandApplicationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Brand Applications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="brand-application-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Brand Application', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'company_name',
            'company_address',
            'name',
            'position',
            // 'contact',
            // 'brands',
            // 'licence',
            // 'recorded',
            // 'registed',
            // 'taxed',
            // 'checked',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

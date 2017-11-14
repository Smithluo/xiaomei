<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


/* @var $this yii\web\View */
/* @var $model common\models\Brand */

$this->title = $model->brand_name;
$this->params['breadcrumbs'][] = ['label' => '旗下品牌', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="brand-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'brand_id',
            'brand_name',
            'brand_depot_area',
            'brand_logo',
            [
                'attribute'=>'brand_logo_two',
                'label' => '品牌Logo',
                'format' => 'html',
                'value'=> '<img src="'.$model->brand_logo_two.'">',
                'headerOptions'=>['width'=>'190px'],
            ],
//            'brand_logo_two',
//            'brand_bgcolor',
            [
                'attribute'=>'brand_policy',
                'label' => '品牌政策',
                'format' => 'html',
                'value'=> '<img src="'.$model-> brand_policy.'">',
                'headerOptions'=>['width'=>'190px'],
            ],
//            'brand_desc:ntext',
//            'brand_desc_long:ntext',
//            'short_brand_desc',
            'site_url:url',
//            'sort_order',
//            'is_show',
//            'album_id',
//            'brand_tag',
        ],


    ]) ?>

    <p>
<!--        --><?php //echo Html::a('编辑', ['update', 'id' => $model->brand_id], ['class' => 'btn btn-primary']) ?>
    </p>
</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\dashboard\MarkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户行为数据监测详情，始于2016年11月18日';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mark-index">

    <h2><?= Html::encode($this->title) ?></h2>
    <h3>Tips:本页显示的条数不等于用户数，每个用户在每个平台有操作都会有一条记录  (内部用户记录也显示)</h3>
    <?php  echo $this->render('_search', [
        'model' => $searchModel,
        'back_action' => $back_action,
        'search_start' => $search_start,
        'search_end' => $search_end,
        'platFormMap' => $platFormMap,
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'date',
            'user_id',
            [
                'attribute' =>'plat_form',
                'value' => function($model) use ($platFormMap){
                    return $platFormMap[$model->plat_form];
                },
            ],
            'login_times',
            'click_times',
            'order_count',
            'pay_count',
        ],
    ]); ?>
</div>

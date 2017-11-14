<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\helper\DateTimeHelper;
use backend\models\Goods;
use backend\models\EventRule;

/* @var $this yii\web\View */
/* @var $searchModel common\models\EventRuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '活动策略';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-rule-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加策略', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'rule_id',
            'rule_name',
            [
                'attribute' => 'match_type',
                'value' => function($model){
                    return EventRule::$match_type_map[$model->match_type];
                }
            ],
            'match_value',
            [
                'attribute' => 'match_effect',
                'value' => function($model){
                    return EventRule::$match_effect_map[$model->match_effect];
                }
            ],
            [
                'attribute' => 'gift_id',
                'value' => function($model){
                    if (empty($model->gift_id)) {
                        return '';
                    }
                    $result = '('.$model->gift_id.')';
                    $giftGoods = Goods::findOne($model->gift_id);
                    if ($giftGoods) {
                        $result .= $giftGoods->goods_name;
                    }
                    return $result;
                }
            ],
            'gift_num',
            'gift_show_peice',
            'gift_need_pay',
            [
                'attribute' => 'updated_at',
                'value' => function($model){
                    return DateTimeHelper::getFormatCNDateTime($model->updated_at);
                }
            ],
            [
                'attribute' => 'event_id',
                'value' => function($model) use ($giftEventMap){
                    if (isset($giftEventMap[$model->event_id])) {
                        return $giftEventMap[$model->event_id];
                    } else {
                        return '';
                    }
                }
            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} | {update}',
            ],
        ],
    ]); ?>
</div>

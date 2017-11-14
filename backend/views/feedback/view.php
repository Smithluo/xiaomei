<?php

use yii\helpers\Html;
//use kartik\detail\DetailView;
use  yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Feedback */

$this->title = $model->msg_id;
$this->params['breadcrumbs'][] = ['label' => '意见反馈', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feedback-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//                ['columns' =>
            'msg_id',
            [
                'attribute' => 'user_id',
                'value' => function ($model) {
                    return empty($model->users) ? '' : $model->users->user_name;
                }
            ],
            'msg_title',
            [
                'attribute' => 'msg_type',
                'value' => function ($model) {
                    return \common\models\Feedback::$msg_type_map[$model->msg_type];
                }
            ],
//                ],
//                ['columns' =>
            'user_phone',
            [
                'attribute' => 'msg_content',
                'format' => 'raw',
            ],
        ],
//            ],
    ]) ?>


</div>

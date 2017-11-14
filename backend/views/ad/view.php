<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Ad */

$this->title = $model->ad_id;
$this->params['breadcrumbs'][] = ['label' => 'Ads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ad-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ad_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ad_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ad_id',
            [
                'attribute' => 'position_id',
                'value' => function($model) {
                    return $model->adPosition->position_name;
                }
            ],
            [
                'attribute' => 'media_type',
                'value' => function($model) {
                    return \common\models\Ad::$mediaTypeMap[$model->media_type];
                }
            ],
            'ad_name',
            [
                'attribute' => 'ad_link',
                'format' => 'url',
            ],

            [
                'attribute' => 'start_time',
                'value' => function($model) {
                    return \common\helper\DateTimeHelper::getFormatCNDate($model->start_time);
                }
            ],
            [
                'attribute' => 'end_time',
                'value' => function($model) {
                    return \common\helper\DateTimeHelper::getFormatCNDate($model->end_time);
                }
            ],
            'click_count',
            [
                'attribute' => 'enabled',
                'value' => function($model) {
                    return \common\models\Ad::$enableMap[$model->enabled];
                }
            ],
            [
                'attribute' => 'ad_code',
                'format' => 'raw',
                'value' => function($model){
                    if (strstr($model->ad_code, 'http://') || strstr($model->ad_code, 'https://')) {
                        return Html::img($model->ad_code);
                    }else {
                        return Html::img($model->getUploadUrl('ad_code'));
                    }
                }
            ],
        ],
    ]) ?>

</div>

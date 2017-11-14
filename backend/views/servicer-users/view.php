<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use common\helper\CacheHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = $model->user_id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$province = CacheHelper::getRegionCache([
    'type' => 'name',
    'ids' => [$model->province]
]);

?>
<div class="users-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->user_id], [
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
            'user_name',
            [
                'label' => '服务区域',
                'value' => function() use ($model) {
                    $result = '';
                    if (!empty($model->regions)) {
                        foreach ($model->regions as $region) {
                            $result .= $region['region_name']. '  ';
                        }
                    }
                    return $result;
                },
            ],
            //'reg_time',
            /**2017/07/24 HongXunPan
             * 修改两处时间显示为正常时间格式显示，原本为时间戳
             */
            [
                'attribute' => 'reg_time',
                'format' => ['datetime', 'php:Y-m-d H:i:s']
            ],
            [
                'attribute' => 'last_login',
                'format' => ['datetime', 'php:Y-m-d H:i:s']
            ],
            'last_time',
            'last_ip',
            'mobile_phone',
            [
                'label' => '服务商业务码',
                'value' => $model->servicerUserInfo->servicer_code,
            ],
        ],
    ]) ?>

</div>

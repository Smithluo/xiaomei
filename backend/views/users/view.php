<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
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
                'attribute' => 'province',
                'value' => current($province) ?: '未设置'
            ],
            'reg_time',
            'last_login',
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

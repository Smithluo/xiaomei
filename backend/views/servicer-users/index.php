<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\ServicerUserInfo;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '服务商列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建服务商', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('把已有用户升级为服务商', ['upgrade'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'user_id',
            'user_name',
            [
                'label'=>'公司名称',
                'attribute'=>'company_name',
                'format'=>'raw',
                'value'=>function($model) {
                    return $model->company_name;
                },
            ],
             'mobile_phone',
             'int_balance',
            [
                'label' => '服务编码',
                'value' => function($model) {
                    if ($model->servicer_info_id) {
                        $service_user_info = ServicerUserInfo::find()->where(['id' => $model->servicer_info_id])->one();
                        if ($service_user_info) {
                            return $service_user_info->id.' : '.$service_user_info->servicer_code;
                        }
                    }
                    return '';
                }
            ],
            [
                'label' => '上级服务商',
                'value' => function($model) {
                    if ($model->supserServicerUser) {
                        return $model->supserServicerUser->showName. ' | '. $model->supserServicerUser->mobile_phone. ' | '. $model->supserServicerUser->company_name;
                    }
                    return '';
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

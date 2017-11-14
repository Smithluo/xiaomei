<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ResourceSiteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '资源站点';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resource-site-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新建 资源站点', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'site_name',
            [
                'attribute' => 'site_logo',
                'format' => 'html',
                'value' => function($model){
                    return Html::img($model->getUploadUrl('site_logo'), ['height' => '50']);
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {update} {delete}',
            ]
        ],
    ]); ?>
</div>

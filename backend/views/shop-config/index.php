<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ShopConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商店设置';
$this->params['breadcrumbs'][] = $this->title;
?>


<?= \yii\bootstrap\Tabs::widget([
    'items' => [
        [
            'label' => '采购流程',
            'content' => $this->render('_form_liucheng', ['model' => $model]),
            'active' => true
        ],
    ]]);
?>

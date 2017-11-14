<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use common\models\Users;
use common\helper\DateTimeHelper;
use common\models\UserExtension;
use backend\models\Region;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;

/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = $model->user_id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-view">

    <?php
    echo $this->render('_user-detail', [
        'model' => $model,
    ]);

    echo $this->render('_user_address',[
        'model' => $userAddress,
        'provinceMap' => $provinceMap,
    ]);

    echo $this->render('_user-top', [
        'topGoods' => $topGoods,
        'totalNum' => $totalNum,
    ]);

    echo $this->render('_user-order-group', [
        'provider' => $provider,
        'searchModel' => $searchModel,
    ]);
    ?>
</div>

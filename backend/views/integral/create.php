<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Integral */

$this->title = '手动 赠送/扣除 积分';
$this->params['breadcrumbs'][] = ['label' => 'Integrals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="integral-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>tips:手动处理积分 支付方式默认为 后台手动</p>

    <?php echo $this->render('_form', [
        'model' => $model,
        'statusMap' => $statusMap,
        'payCodeMap' => $payCodeMap,
    ]) ?>

</div>

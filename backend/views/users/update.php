<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = '编辑服务商: ' . $model->showName . ' | ' .$model->company_name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_id, 'url' => ['view', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="users-update">
    <pre class="col-lg-6"><h3>tips服务商账号给用户使用之前，要验证基本功能没有问题再给服务商用</h3><h4>
    ①要完善 移动电话、服务区域、银行信息等；
    ②编辑保存一次服务商信息；
    ③修改一次服务商密码；
    ④注册服务商服务区域的用户检查是否出现在服务商的待审核列表；
    ⑤老用户属于服务商服务员的绑定服务商总号检查是否出现在服务商门店列表<h4></pre>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'bankModel' => $bankModel,
        'isCreate' => false,
    ]) ?>

</div>

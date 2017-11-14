<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = '创建新服务商';
$this->params['breadcrumbs'][] = ['label' => '服务商列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-create">

    <p>
        Tips: 创建失败时，请重新修改密码，避免发给服务商的账号无法登录。
        创建服务商账号后，要验证服务商账号的登录和服务商区域内未审核和已拒绝用户登录后的显示。
        周知新开服务商所辖区域，显示加颜色标记，新开服务商区域绑定新的业务员
    </p>

    <?= $this->render('_form', [
        'model' => $model,
        'bankModel' => $bankModel,
        'isCreate' => true,
    ]) ?>

</div>

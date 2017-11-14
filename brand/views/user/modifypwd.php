<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = '修改密码';
$this->params['breadcrumbs'][] = $this->title;

$this->params['ext_css'] =

$this->params['ext_js'] = 'http://adminjs.xiaomei360.com/lib/lib_base.js?version='.$r_version;
?>
<div class="users-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
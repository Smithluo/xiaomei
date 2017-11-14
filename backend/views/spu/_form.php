<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Spu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="spu-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? '创建' : '更新',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div>
    <?php if (!empty($skuGoodsList)) : ?>
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">已关联的商品</div>

            <!-- Table -->
            <table class="table">
                <table class="table">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>商品ID</th>
                        <th>商品名称</th>
                        <th>是否上架</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($skuGoodsList as $goods) :?>
                    <tr>
                        <th scope="row"><?=$goods->goods_id ?></th>
                        <td><?=$goods->goods_name?></td>
                        <td><?=$isOnSaleMap[$goods->is_on_sale]?></td>
                        <td><?=$goods->sku_size?></td>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </table>
        </div>
    <?php else : ?>
        当前SPU未关联商品
    <?php endif; ?>
</div>

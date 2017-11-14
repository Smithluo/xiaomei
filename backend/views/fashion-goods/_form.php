<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FashionGoods */
/* @var $form yii\widgets\ActiveForm */
$goodsList = \common\models\Goods::find()
    ->where([
        'is_on_sale' => 1,
        'is_delete' => 0,
    ])->asArray()->all();

$dropGoodsList = array_column($goodsList, 'goods_name', 'goods_id');
?>

<div class="fashion-goods-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php if($model->isNewRecord) :?>
        <?php for($i=0; $i < 10 ;$i++ ):?>
            <div class="row">
                <div class="col-lg-2">
                    <?= $form->field($model, "[$i]name")->textInput() ?>
                </div>

                <div class="col-lg-2">
                    <?= $form->field($model, "[$i]desc")->textInput() ?>
                </div>

                <div class="col-lg-3">
                    <?php echo $form->field($model, "[$i]goods_id")->widget(kartik\widgets\Select2::className(), [
                        'data' => $dropGoodsList,
                        'options' => [
                            'multiple' => false,
                            'prompt' => '请选择商品'
                        ],
                    ]) ?>
                </div>

                <div class="col-lg-2">
                    <?= $form->field($model, "[$i]sort_order")->textInput() ?>
                </div>
            </div>
        <?php endfor;?>
    <?php else:?>
        <div class="row">

            <div class="col-lg-2">
                <?= $form->field($model, "name")->textInput() ?>
            </div>

            <div class="col-lg-2">
                <?= $form->field($model, "desc")->textInput() ?>
            </div>

            <div class="col-lg-3">
                <?php echo $form->field($model, "goods_id")->widget(kartik\widgets\Select2::className(), [
                    'data' => $dropGoodsList,
                    'options' => [
                        'multiple' => false,
                        'prompt' => '请选择商品'
                    ],
                ]) ?>
            </div>

            <div class="col-lg-2">
                <?= $form->field($model, "sort_order")->textInput() ?>
            </div>
        </div>
    <?php endif;?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

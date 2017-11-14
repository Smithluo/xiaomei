<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-08-02
 * Time: 17:48
 */
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>
↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓新增专辑商品↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    <?php $form = ActiveForm::begin([
        'action' => ['create-item', 'id' => $model->id],
    ]); ?>


    <div class="row">
        <?php foreach ($newItemList as $index => $item): ?>
            <div class="col-lg-2">
                <?= $form->field($item, "[$index]goods_id")->widget(kartik\select2\Select2::className(), [
                    'data' => \backend\models\Goods::getGoodsMap(),
                    'options' => [
                        'placeholder' => '请选择商品',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]) ?>
            </div>
            <div class="col-lg-2">
                <?= $form->field($item, "[$index]sort_order")->textInput(['maxlength' => true]) ?>
            </div>
        <?php endforeach; ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('增加商品', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>


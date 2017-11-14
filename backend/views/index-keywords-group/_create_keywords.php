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
↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓新增关键词↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    <?php $form = ActiveForm::begin([
        'action' => ['create-keywords', 'id' => $model->id],
    ]); ?>



    <?php foreach ($newKeywords as $index => $keyword): ?>
        <div class="row">
            <div class="col-lg-2">
                <?= $form->field($keyword, "[$index]title")->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-2">
                <?= $form->field($keyword, "[$index]sort_order")->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-2">
                <?= $form->field($keyword, "[$index]ext")->dropDownList(\common\models\IndexKeywords::$extMap) ?>
            </div>
            <div class="col-lg-2">
                <?= $form->field($keyword, "[$index]is_show")->dropDownList([
                    1 => '是',
                    0 => '否'
                ]) ?>
            </div>
            <div class="col-lg-2">
                <?= $form->field($keyword, "[$index]url")->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    <?php endforeach; ?>



    <div class="form-group">
        <?= Html::submitButton('增加关键词', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>


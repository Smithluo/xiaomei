<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ShopConfig */
/* @var $form yii\widgets\ActiveForm */
?>
<hr/>
<div class="row">

<div class="shop-config-form col-lg-12">
    <?php $form = ActiveForm::begin();?>
    <?php $num =0;?>
    <?php foreach($model as $index=>$config):?>
        <!-- parent=4 表示是属于采购流程的 -->

        <?php if($config['parent_id'] == 4 )
        {   //流程分支开始
            if($num%4==4)
            {
                echo '<div class="row" style="height: 10%">';
            }
           switch($config['type'])
           {
               case 'select':

                   if($index== 401 ) {
                      //SwitchInput     开关样式的 单选按钮
                       echo '<div class="col-lg-3">';
                       echo  $form->field($config,"[$index]value")->widget(\kartik\widgets\SwitchInput::className(),[
                           'options'=>Yii::$app->params['CAN_OR_NOT'],
                           'pluginOptions' => [
                               'onText' => "能",
                               'offText' => "不能",
                           ]
                       ])->label('<h4>'.Yii::$app->params['FLOW_CONTENT']["$index"].'</h4>');
                       echo '<p> </p></div>';
                   }
                   elseif (in_array($index,Yii::$app->params['USE_EMAIL']))
                   {
                       echo '<div class="col-lg-3">';
                       echo  $form->field($config,"[$index]value")->widget(\kartik\widgets\SwitchInput::className(),[
                           'options'=>Yii::$app->params['SEND_EMAIL_OR_NOT'],
                               'pluginOptions' => [
                                   'onText' => "发送邮件",
                                   'offText' => "不发送邮件",

                           ],
                       ])->label('<h4>'.Yii::$app->params['FLOW_CONTENT']["$index"].'</h4>');
                       echo '<p></p></div>';
                   }
                   elseif(in_array($index,Yii::$app->params['USE_NOTE']))
                   {
                       echo '<div class="col-lg-3">';
                       echo  $form->field($config,"[$index]value")->widget(\kartik\widgets\SwitchInput::className(),[
                           'options'=>Yii::$app->params['WRITE_NOTE_OR_NOT'],
                           'pluginOptions' => [
                                'onText' => '必须填写备注',
                                'offText' => '无需填写备注邮件',
                           ],
                       ])->label('<h4>'.Yii::$app->params['FLOW_CONTENT']["$index"].'</h4>');
                       echo '<p></p></div>';
                   }
                   elseif(in_array($index,Yii::$app->params['USE_UONT']))
                   {
                       echo '<div class="col-lg-3">';
                       echo  $form->field($config,"[$index]value")->widget(\kartik\widgets\SwitchInput::className(),[
                           'options'=>Yii::$app->params['USE_OR_NOT'],'pluginOptions' => [
                               'onText' => '使用',
                               'offText' => '不使用',
                           ],
                       ])->label('<h4>'.Yii::$app->params['FLOW_CONTENT']["$index"].'</h4>');
                       echo '<p></p></div>';
                   }
                   elseif(in_array($index,Yii::$app->params['USE_YON']))
                   {
                       echo '<div class="col-lg-3">';
                       echo  $form->field($config,"[$index]value")->widget(\kartik\widgets\SwitchInput::className(),[
                           'options'=>Yii::$app->params['YES_OR_NO'],'pluginOptions' => [
                               'onText' => '是',
                               'offText' => '否',
                           ],
                       ])->label('<h4>'.Yii::$app->params['FLOW_CONTENT']["$index"].'</h4>');
                       echo '<p></p></div>';
                   }
                   elseif($index == 419)
                   {
                       echo '<div class="col-lg-3">';
                       echo  $form->field($config,"[$index]value")->widget(\kartik\widgets\SwitchInput::className(),[
                           'options'=>[1=>'允许',0=>'不允许'],
                           'pluginOptions' => [
                               'onText' => '允许',
                               'offText' => '不允许',
                           ],
                       ])->label('<h4>'.Yii::$app->params['FLOW_CONTENT']["$index"].'</h4>');
                       echo '</div>';
                   }
                   elseif($index == 423)
                   {
                       echo '<div class="col-lg-3">';
                       echo $form->field($config,"[$index]value")->dropDownList(['支付后','下订单时','发货时'])->label('<h4>'.Yii::$app->params['FLOW_CONTENT']["$index"].'</h4>');
                       echo '<p></p></div>';
                   }
                   elseif($index == 426)
                   {
                       echo '<div class="col-lg-3">';
                       echo $form->field($config,"[$index]value")->dropDownList([1=>'只显示文字',2=>'只显示图片',3=>'显示文字与图片'])->label('<h4>'.Yii::$app->params['FLOW_CONTENT']["$index"].'</h4>');
                       echo '<p></p></div>';
                   }
                   break;
               case 'textarea':
                   echo '<div class="col-lg-3">';
                   echo $form->field($config,"[$index]value")->textarea()->label('<h4>'.Yii::$app->params['FLOW_CONTENT']["$index"].'</h4>');
                   echo '<small>客户要求开发票时可以选择的内容。例如：办公用品。每一行代表一个选项。</small>';
                   echo '</div>';
                   break;
               case 'manual':
                   break;
               case 'options' :
                   echo '<div class="col-lg-3">';
                   echo $form->field($config,"[$index]value")->dropDownList(Yii::$app->params['OPTIONS'])->label('<h4>'.Yii::$app->params['FLOW_CONTENT']["$index"].'</h4>');
                   echo '<p></p></div>';
                   break;
               case 'text':
                   echo '<div class="col-lg-3">';
                   echo $form->field($config,"[$index]value")->input('text')->label('<h4>'.Yii::$app->params['FLOW_CONTENT']["$index"].'</h4>');
                   if($index==420)
                   {
                       echo '<small>达到此采购金额，才能提交订单。</small>';
                   }

                   echo '</div>';
                   break;

           }
            if($num%5==5 && $num!=0)
            {
                echo '</div>';
            }
        }?>

        <?php $num++;?>
    <?php endforeach;?>


    <div class="form-group">

        <?= Html::submitButton( '确定', ['class' =>  'btn btn-success']) ?>
        <?= Html::resetButton( '重置', ['class' =>  'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end();?>
</div>
</div>




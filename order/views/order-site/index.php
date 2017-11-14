<?php
use yii\widgets\ActiveForm;
?>

<div class="row animated fadeInRight">
    <?php if (Yii::$app->user->can('/order-site/import')) : ?>
        <div class="col-lg-2"></div>
        <div class="col-lg-3">
            <?php
            $form = ActiveForm::begin([
                'action' => ['import'],
                'method' => 'post',
                'options' => ['enctype' => 'multipart/form-data'
                ]]);
            echo $form->field($importForm, 'file')->fileInput();
            echo '<button>提交</button>';

            ActiveForm::end();
            ?>
        </div>

        <div class="col-lg-5">
            <h3>导入模板说明</h3>
            <pre>
    每个订单的第一行必填信息：
        consignee: 收件人姓名
        address: 收件人手机号
        mobile: 收件人的收货地址（要填写完整的省、市、县/区、具体地址）

    每行必填信息：
        goods_sn: 购买商品的条码
        goods_number:条码对应的购买数量

    模板导入时
        整个excel只保留一个sheet（看excel表的左下角，只保留一个选项卡）
        只保留有英文表头的前五列，
        删除有效信息之间的空行

    如果您认为导入的数据是正确的，但是系统提示数据有错误，请下载最新的导入模板，重新填写数据后导入新的模板
    <a href="http://img.xiaomei360.com/import_tpl/order-site导入订单模板.xlsx" download=""  class="nor_load">下载 订单导入模板</a>


    如果导入模板有错误数据，则模板中的所有订单都不会导入
    导入时提示 库存不足、已下架等错误的商品
</pre>
        </div>
        <div class="col-lg-1"></div>
    <?php endif;?>
</div>

<?php

use brand\models\VolumePrice;
use common\helper\NumberHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\GoodsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品列表';
$this->params['breadcrumbs'][] = $this->title;

$this->params['ext_css'] = '<link href="http://adminjs.xiaomei360.com/components/supplier/goodsMange/goodsMange.css?version='.$r_version.'" type="text/css" rel="stylesheet">';

$this->params['ext_js'] = '<script src="http://adminjs.xiaomei360.com/lib/lib_base.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/lib/grid.js?version='.$r_version.'"></script>
<script src="http://adminjs.xiaomei360.com/app/supplier/goodsMange.js?version='.$r_version.'"></script>
<script>steel.boot(\'app/supplier/goodsMange\');</script>';

$model_list = $dataProvider->getModels();
?>
<div class="wrapper wrapper-content animated fadeInRight ecommerce">

    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <div class="radio radio-success radio-inline" xm-data="/index.php?r=goods" >
                <input type="radio" id="inlineRadio0" value="option0" name="radioInline"
                    <?php if (!isset($_GET['brand_id']) || !$_GET['brand_id']) : ?> checked="checked"<?php endif; ?>>
                <label for="inlineRadio1"> 全部商品 </label>
            </div>
            <?php foreach($brand_map as $brand_id => $brand_name) : ?>
            <div class="radio radio-success radio-inline" xm-data="/index.php?r=goods&brand_id=<?=$brand_id?>">
                <input type="radio" id="inlineRadio<?=$brand_id?>" value="option<?=$brand_id?>" name="radioInline" <?php if (isset($_GET['brand_id']) && $_GET['brand_id'] == $brand_id) : ?> checked="checked"<?php endif; ?>>
                <label for="inlineRadio<?=$brand_id?>"> <?=$brand_name?> </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
                        <thead>
                        <tr>
                            <th data-toggle="true">商品ID</th>
                            <th data-toggle="true">商品名称</th>
                            <th data-hide="phone" data-sort-ignore="true">条形码</th>
                            <th data-hide="all" data-sort-ignore="true">商品卖点</th>
                            <th data-hide="all">梯度价格</th>
                            <th data-hide="all">装箱数</th>
                            <th data-hide="all">保质期</th>
                            <th data-hide="all">起售数量</th>
                            <th data-hide="phone">上架状态</th>
                            <th data-hide="phone,tablet" >库存</th>

                            <th class="text-right" data-sort-ignore="true">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ($model_list as $model) :
                        ?>
                                <tr>
                                    <td>
                                        <?=$model->goods_id?>
                                    </td>
                                    <td>
                                        <?=$model->goods_name?>
                                    </td>
                                    <td>
                                        <?=$model->goods_sn?>
                                    </td>
                                    <td>
                                        <?=$model->goods_brief?>
                                    </td>
                                    <td>
                                        <?php
                                            $volume_price_map = VolumePrice::get_volume_price_list($model->goods_id);
                                            $discount = 1; //   后台不显示折扣，按原始梯度价格显示
                                            $volume_price_format = VolumePrice::volume_price_list_format($volume_price_map, $model->shop_price, $discount, $model->start_num, $model->goods_number);
                                            foreach ($volume_price_format as $key => $item) :
                                        ?>
                                        <span class="<?php
                                            switch ($key % 3) {
                                                case 0:
                                                    echo 'product-price-tb';
                                                    break;
                                                case 1:
                                                    echo 'product-price-tb ppt-2';
                                                    break;
                                                case 2:
                                                    echo 'product-price-tb ppt-3';
                                                    break;
                                                default :
                                                    echo 'product-price-tb';
                                                    break;
                                            }
                                        ?>">
                                            <?=$item['range'].$model->measure_unit.'：'.NumberHelper::format_as_money($item['price'])?>
                                        </span>
                                        <?php
                                            endforeach;
                                        ?>
                                    </td>
                                    <td>
                                        <?=$model->number_per_box.$model->measure_unit?>
                                    </td>
                                    <td>
                                        <?=$model->shelf_life?>
                                    </td>
                                    <td>
                                        <?=$model->start_num?>
                                    </td>
                                    <td>
                                        <?php if ($model->is_on_sale) : ?>
                                            <button class="btn btn-info btn-circle" type="button"><i class="fa fa-check"></i></button>
                                        <?php else : ?>
                                            <button class="btn btn-warning btn-circle" type="button"><i class="fa fa-times"></i></button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" xm-action="goodsNum" data-toggle="popover" data-placement="left" data-content="库存数字只能为整数" placeholder="<?=$model->goods_number.$model->measure_unit?>">
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            <button class="btn btn-outline btn-primary" xm-data="id=<?=$model->goods_id?>" xm-action="modifyNum">确认修改库存</button>
                                        </div>
                                    </td>
                                </tr>
                        <?php
                            endforeach;
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="9">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
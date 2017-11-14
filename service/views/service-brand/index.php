<?php

use yii\helpers\Html;
use common\widgets\GridView;

\service\assets\BrandAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel service\models\BrandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '分成管理';
$this->params['breadcrumbs'][] = $this->title;
$this->params['steel_boot'] = 'app/service/sharing';
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox-content m-b-sm border-bottom">
        <div class="row">
            <form action="<?= \yii\helpers\Url::to(['/service-brand/change-all-percent']) ?>" method="post">
                <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label" for="product_name">设置全局提成(为所有品牌设置统一的业务员提成)</label>
                        <input type="text" id="product_name" name="percent" value="" placeholder="输入业务员提成百分比，请输入整数" class="form-control">
                    </div>
                </div>
                <button type="submit" style="margin-top: 22px;" class="btn btn-w-m btn-pink ">确定</button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-5">
            <div class="ibox">
                    <?= GridView::widget([
                        'showFooter' => true,                    //使用前端分页 shiningxiao
                        'dataProvider' => $dataProvider,
                        'dataColumnClass' => \common\widgets\DataColumn::className(),
                        'tableOptions' => [
                            'id' => 'brandList',
                        ],
                        'columns' => [
                            [
                                'label'=>'品牌名称',
                                'encodeLabel' => false,
                                'attribute'=>'brand_name',
                                'format'=>'raw',
                                'value'=>function($model) {
                                    return $model->brand_name;
                                },
                                'filter'=>Html::activeTextInput($searchModel, 'brand_name', ['class'=>'form-control']),
                                'footer' => '
                                    <td colspan="11">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                ',          //前端分页
                                'enableSorting' => false, //客户端分页
                            ],
                            [
                                'label'=>'业务员分成(百分比%)',
                                'encodeLabel' => false,
                                'format'=>'raw',
                                'value'=>function($model) {
                                    return Yii::$app->user->identity['divide_percent'];
                                },
                                'filter'=>Html::activeTextInput($searchModel, 'percent_level_2', ['class'=>'form-control']),
//                                'headerOptions'=>['class'=>'footable-visible footable-sortable footable-first-column'],
//                                'contentOptions'=>['class'=>'footable-visible footable-first-column'],
                                'enableSorting' => false, //客户端分页
                            ],
                        ],
                    ]); ?>
            </div>
        </div>
        <div class="col-lg-7 animated fadeInRight">
            <div class="ibox">
                <div class="ibox-content">
                    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
                        <thead>
                        <tr>
                            <th >商品名称</th>
                            <th data-hide="all">商品价格</th>
                            <th>品牌名</th>
                            <th>返利金额</th>
                            <!--<th class="text-right">操作</th>-->
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        foreach($goodsList as $goods) {
                            $measure_unit = $goods->measure_unit;
                            if(empty($measure_unit)) {
                                $measure_unit = '件';
                            }
                            $discount = 1; //   后台不显示折扣，按原始梯度价格显示
                            $price_list = \brand\models\VolumePrice::volume_price_list_format(\brand\models\VolumePrice::get_volume_price_list($goods->goods_id), $goods->shop_price, $discount, $goods->start_num, $goods->goods_number);
                            $dest_price_list = [];
                            foreach($price_list as $price_group) {
                                $dest_price_list[] = $price_group['range'].$measure_unit.':￥'.$price_group['price'];
                            }

                            echo '<tr>
                            <td>
                                '. $goods->goods_name. '
                            </td>
                            <td>';
                                foreach($dest_price_list as $key => $price) {
                                    switch($key) {
                                        case 0:
                                            echo '<span class="product-price-tb">'.$price.'</span>';
                                            break;
                                        case 1:
                                            echo '<span class="product-price-tb ppt-2">'.$price.'</span>';
                                            break;
                                        case 2:
                                            echo '<span class="product-price-tb ppt-3">'.$price.'</span>';
                                            break;
                                    }
                                }
                            echo '</td>
                            <td>
                                '.$goods->brand->brand_name.'
                            </td>
                            <td>
                                '. \common\helper\NumberHelper::price_format($goods->getProfit() * $percent / 100.0) .'
                            </td>
                        </tr>';
                        } ?>

                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="11">
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

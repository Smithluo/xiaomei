<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/24
 * Time: 10:15
 */

namespace api\modules\v2\models;

use common\helper\ImageHelper;
use common\helper\NumberHelper;

class GoodsActivity extends \common\models\GoodsActivity
{
    /**
     * 格式化输出数据格式
     * @return array
     */
    public function fields()
    {
        return [
            'act_id' => function($model){
                return (int)$model->act_id;
            },  //  '活动ID',
            'act_name' => function($model){
                return (string)$model->act_name;
            },  //  '活动名称',
            'act_desc' => function($model){
                return (string)$model->act_desc;
            },  //  '活动描述',
            'act_type' => function($model){
                return (int)$model->act_type;
            },  //  '活动类型',
            'goods_id' => function($model){
                return (int)$model->goods_id;
            },  //  '商品ID',
            'start_num' => function($model){
                return (int)$model->start_num;
            },  //  '起售数量',
            'limit_num' => function($model){
                return (int)$model->limit_num;
            },  //  '每单限购数量',
            'match_num' => function($model){
                return (int)$model->match_num;
            },  //  '成团数量',

            'old_price' => function($model){
                return NumberHelper::price_format($model->old_price);
            },  //  '原价',
            'act_price' => function($model){
                return NumberHelper::price_format($model->act_price);
            },  //  '团采价',
            'production_date' => function($model){
                return substr($model->production_date, 0, 10);
            },  //  '商品有效期',
            'show_banner' => function($model){
                return ImageHelper::get_image_path($model->show_banner);
            },  //  '展示图',
            'qr_code' => function($model){
                return ImageHelper::get_image_path($model->qr_code);
            },  //  '二维码',
            'product_id' => function($model){
                return (int)$model->product_id;
            },  //  'Product ID',
            'goods_name' => function($model){
                return (string)$model->goods_name;
            },  //  '商品名称',
            'goods_list' => function($model){
                return ImageHelper::get_image_path($model->goods_list);
            },  //  '商品列表(图)',
            'start_time' => function($model){
                return (int)$model->start_time;
            },  //  '开始时间',
            'end_time' => function($model){
                return (int)$model->end_time;
            },  //  '结束时间',
            'is_hot' => function($model){
                return (int)$model->is_hot;
            },  //  '热门推荐',
            'is_finished' => function($model){
                return (int)$model->is_finished;
            },  //  '状态',
            'ext_info' => function($model){
                return (string)$model->ext_info;
            },  //  '扩展信息',

        ];
    }
    
}
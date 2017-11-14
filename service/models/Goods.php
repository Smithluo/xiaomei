<?php

namespace service\models;

use common\models\Category;
use common\models\VolumePrice;
use Yii;
use common\models\ServicerSpecStrategy;
use common\models\ServicerStrategy;

/**
 * This is the model class for table "o_goods".
 *
 * @property string $goods_id
 * @property integer $cat_id
 * @property string $goods_sn
 * @property string $goods_name
 * @property string $goods_name_style
 * @property string $click_count
 * @property integer $brand_id
 * @property string $provider_name
 * @property integer $goods_number
 * @property string $measure_unit
 * @property integer $number_per_box
 * @property string $goods_weight
 * @property string $market_price
 * @property string $shop_price
 * @property string $min_price
 * @property string $promote_price
 * @property string $promote_start_date
 * @property string $promote_end_date
 * @property integer $warn_number
 * @property string $keywords
 * @property string $goods_brief
 * @property string $goods_desc
 * @property string $goods_thumb
 * @property string $goods_img
 * @property string $original_img
 * @property integer $is_real
 * @property string $extension_code
 * @property integer $is_on_sale
 * @property integer $is_alone_sale
 * @property integer $is_shipping
 * @property string $integral
 * @property string $add_time
 * @property integer $sort_order
 * @property integer $is_delete
 * @property integer $is_best
 * @property integer $is_new
 * @property integer $is_hot
 * @property integer $is_spec
 * @property integer $is_promote
 * @property integer $bonus_type_id
 * @property string $last_update
 * @property integer $goods_type
 * @property string $seller_note
 * @property integer $give_integral
 * @property integer $rank_integral
 * @property integer $suppliers_id
 * @property integer $is_check
 * @property string $children
 * @property string $shelf_life
 * @property string $servicer_strategy_id
 */
class Goods extends \common\models\Goods
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'o_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'click_count', 'brand_id', 'goods_number', 'number_per_box', 'promote_start_date', 'promote_end_date', 'warn_number', 'is_real', 'is_on_sale', 'is_alone_sale', 'is_shipping', 'integral', 'add_time', 'sort_order', 'is_delete', 'is_best', 'is_new', 'is_hot', 'is_spec', 'is_promote', 'bonus_type_id', 'last_update', 'goods_type', 'give_integral', 'rank_integral', 'suppliers_id', 'is_check', 'servicer_strategy_id'], 'integer'],
            [['measure_unit', 'number_per_box', 'goods_desc', 'is_spec', 'start_num'], 'required'],
            [['goods_weight', 'market_price', 'shop_price', 'min_price', 'promote_price'], 'number'],
            [['goods_desc'], 'string'],
            [['goods_sn', 'goods_name_style'], 'string', 'max' => 60],
            [['goods_name'], 'string', 'max' => 120],
            [['provider_name'], 'string', 'max' => 100],
            [['measure_unit'], 'string', 'max' => 20],
            [['keywords', 'goods_brief', 'goods_thumb', 'goods_img', 'original_img', 'seller_note', 'children'], 'string', 'max' => 255],
            [['extension_code'], 'string', 'max' => 30],
            [['shelf_life'], 'string', 'max' => 10],
            ['start_num', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品ID',
            'cat_id' => '商品分类',
            'goods_sn' => '商品的唯一货号',
            'goods_name' => '商品名称',
            'goods_name_style' => '商品名称的样式',
            'click_count' => '浏览量',
            'brand_id' => '所属品牌',
            'provider_name' => '供应商',
            'goods_number' => '库存',
            'measure_unit' => '商品计件单位',
            'number_per_box' => '装箱数',
            'goods_weight' => '商品重量',
            'market_price' => '市场价',
            'shop_price' => '本店售价',
            'min_price' => '最低售价',
            'promote_price' => '促销价',
            'promote_start_date' => '活动开始时间',
            'promote_end_date' => '活动结束时间',
            'warn_number' => '库存报警数量',
            'keywords' => '关键词',
            'goods_brief' => '商品的简短描述',
            'goods_desc' => '商品的详细描述',
            'goods_thumb' => '商品缩略图',
            'goods_img' => '商品大图',
            'original_img' => '商品原图',
            'is_real' => '	是否是实物', //  1，是；0，否；比如虚拟卡就为0，不是实物
            'extension_code' => '商品的扩展属性',
            'is_on_sale' => '是否上架',
            'is_alone_sale' => '是否能单独销售',   //  1，是；0，否；如果不能单独销售，则只能作为某商品的配件或者赠品销售
            'is_shipping' => '是否已发货',
            'integral' => '可用积分',   //  购买该商品可以使用的积分数量，估计应该是用积分代替金额消费；但程序好像还没有实现该功能
            'add_time' => '添加时间',
            'sort_order' => '排序权重',
            'is_delete' => '已经删除',  //  0，否；1，已删除
            'is_best' => '是否精品',   //  0，否；1，是
            'is_new' => '是否新品',
            'is_hot' => '是否热销',
            'is_spec' => '每周特供',
            'is_promote' => '是否促销',
            'bonus_type_id' => '赠送的红包', //  购买该商品所能领到的红包类型
            'last_update' => '最近更新',
            'goods_type' => '商品类型',   //  商品所属类型id，取值表goods_type的cat_id
            'seller_note' => '商家备注', //  商品的商家备注，仅商家可见
            'give_integral' => '赠送积分', //  购买该商品时每笔成功交易赠送的积分数量
            'rank_integral' => '等级积分',
            'suppliers_id' => '供应商ID',
            'is_check' => '是否审核通过',
            'children' => '套装商品',   // 字符串，goods_id => goods_num
            'shelf_life' => '保质期',
            'servicer_strategy_id' => '应用分成策略id',
            'start_num' => '起售数量',   // 最低销售数量
        ];
    }

    /**
     * @inheritdoc
     * @return GoodsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GoodsQuery(get_called_class());
    }

    public function getServicerStrategy() {
        return $this->hasOne(ServicerStrategy::className(), ['id' => 'servicer_strategy_id']);
    }

    public function getSpecServicerStrategy() {
        return $this->hasOne(ServicerSpecStrategy::className(), ['brand_id' => 'brand_id']);
    }

    public function getBrand() {
        return $this->hasOne(Brand::className(), ['brand_id' => 'brand_id']);
    }

    public function getCategory() {
        return $this->hasOne(Category::className(), ['cat_id' => 'cat_id']);
    }
}

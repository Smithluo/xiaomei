<?php
use common\models\Shipping;

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* 配送方式插件的代码必须和文件名保持一致 */
    $modules[$i]['code']    = 'free';

    $modules[$i]['version'] = '1.0.0';

    /* 配送方式的描述 */
    $modules[$i]['desc']    = 'free_desc';

    /* 配送方式是否支持货到付款 */
    $modules[$i]['cod']     = false;

    /* 插件的作者 */
    $modules[$i]['author']  = '小美诚品';

    /* 插件作者的官方网站 */
    $modules[$i]['website'] = 'http://www.xiaomei360.com';

    /* 配送接口需要的参数 */
    $modules[$i]['configure'] = [
        [
            'name' => 'backup_shipping_code',
            'value' => 'fpd',
        ]
    ];

    /* 模式编辑器 */
    $modules[$i]['print_model'] = 2;

    /* 打印单背景 */
    $modules[$i]['print_bg'] = '';

   /* 打印快递单标签位置信息 */
    $modules[$i]['config_lable'] = '';

    return;
}

class free
{
    /**
     * 配置信息
     */
    var $configure;

    /**
     * 构造函数
     *
     * @param: $configure[array]    配送方式的参数的数组
     *
     * @return null
     */
    public function free($cfg=array())
    {
    }

    /**
     * 计算订单的配送费用的函数
     *
     * @param   float   $goods_weight   商品重量
     * @param   float   $goods_amount   商品金额
     * @return  decimal
     */
    public function calculate($goods_weight, $goods_amount, $goods_number)
    {
        return 0;
    }

    /**
     * 计算订单的配送费用 和 真实的配送方式
     *
     * @param float $goods_weight 商品重量
     * @param float $goods_amount 商品总价
     * @param int $goods_number 商品数量
     * @param int $prepay       是否现付运费
     *
     * @return array    返回 shipping_fee 和 shipping_code
     */
    public function calculateAndModify($goods_weight, $goods_amount, $goods_number, $prepay = 0)
    {
        return [
            'shipping_fee' => 0,
            'shipping_code' => 'free',
            'shipping_id' => Shipping::getShippingIdByCode('free'),
        ];
    }

    /**
     * 查询发货状态
     * 该配送方式不支持查询发货状态
     *
     * @access  public
     * @param   string  $invoice_sn     发货单号
     * @return  string
     */
    public function query($invoice_sn)
    {
        return $invoice_sn;
    }

}

?>
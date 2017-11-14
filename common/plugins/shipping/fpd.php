<?php
use common\models\Shipping;

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* 配送方式插件的代码必须和文件名保持一致 */
    $modules[$i]['code']    = 'fpd';

    $modules[$i]['version'] = '1.0.0';

    /* 配送方式的描述 */
    $modules[$i]['desc']    = 'fpd_desc';

    /* 配送方式是否支持货到付款 */
    $modules[$i]['cod']     = false;

    /* 插件的作者 */
    $modules[$i]['author']  = '小美诚品';

    /* 插件作者的官方网站 */
    $modules[$i]['website'] = 'http://www.xiaomei360.com';

    /* 配送接口需要的参数 */
    $modules[$i]['configure'] = array();

    /* 模式编辑器 */
    $modules[$i]['print_model'] = 2;

    /* 打印单背景 */
    $modules[$i]['print_bg'] = '';

   /* 打印快递单标签位置信息 */
    $modules[$i]['config_lable'] = '';

    return;
}

class fpd
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
    public function fpd($cfg=array())
    {
    }

    /**
     * 计算订单的配送费用的函数
     *
     * @param   float   $goods_weight   商品重量
     * @param   float   $goods_amount   商品金额
     * @param   int     $goods_number   商品数量
     * @return  string
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
        Yii::warning(' fpd 计算运费开始  $goods_amount = '.$goods_amount, __METHOD__);
        return [
            'shipping_fee' => 0,
            'shipping_code' => 'fpd',
            'shipping_id' => Shipping::getShippingIdByCode('fpd'),
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
    
    /**
     * 返回快递100查询链接 by wang 
     * URL：https://code.google.com/p/kuaidi-api/wiki/Open_API_Chaxun_URL
     */
    public function kuaidi100($invoice_sn){
        $url = 'http://m.kuaidi100.com/query?type=ems&id=1&postid=' .$invoice_sn. '&temp='.time();
        return $invoice_sn;
    }
}

?>
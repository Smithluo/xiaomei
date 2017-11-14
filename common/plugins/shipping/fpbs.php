<?php
/**
 * 预付运费插件 fpbs(Freight paid before shipment)
 */

use common\models\Shipping;

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* 配送方式插件的代码必须和文件名保持一致 */
    $modules[$i]['code']    = 'fpbs';

    $modules[$i]['version'] = '1.0.0';

    /* 配送方式的描述 */
    $modules[$i]['desc']    = 'fpbs_desc';

    /* 配送方式是否支持货到付款 */
    $modules[$i]['cod']     = false;

    /* 插件的作者 */
    $modules[$i]['author']  = '小美诚品';

    /* 插件作者的官方网站 */
    $modules[$i]['website'] = 'http://www.xiaomei360.com';

    //  配送接口需要的参数   ——【小坑】name值使用驼峰 在m站后台配置看不到字段名
    $modules[$i]['configure'] = [

        ['name' => 'tpl1_min_fee'],
        [
            'name' => 'tpl1_base_fee',
            'value' => 5,
        ],
        [
            'name' => 'tpl1_rate',
            'value' => 5,
        ],
        [
            'name' => 'tpl1_discount',
            'value' => 0.8,
        ],

        [
            'name' => 'weight_point_1',
            'value' => 20,
        ],

        [
            'name' => 'tpl2_min_fee',
            'value' => 72,
        ],
        [
            'name' => 'tpl2_base_fee',
            'value' => 27,
        ],
        [
            'name' => 'tpl2_rate',
            'value' => 2.5,
        ],
        [
            'name' => 'tpl2_discount',
            'value' => 0.8,
        ],

        ['name' => 'weight_point_2'],

        ['name' => 'tpl3_min_fee'],
        ['name' => 'tpl3_base_fee'],
        ['name' => 'tpl3_rate'],
        ['name' => 'tpl3_discount'],
    ];

    /* 模式编辑器 */
    $modules[$i]['print_model'] = 2;

    /* 打印单背景 */
    $modules[$i]['print_bg'] = '';

   /* 打印快递单标签位置信息 */
    $modules[$i]['config_lable'] = '';

    return;
}

class fpbs
{
    public $configure;  //  配置信息

    //  构造函数 ——分配数据
    public function fpbs($cfg=array())
    {
        foreach ($cfg AS $key=>$val)
        {
            $this->configure[$val['name']] = $val['value'];
        }
    }

    /**
     * 计算订单的配送费用的函数
     *
     * @param $goods_weight 商品重量
     * @param $goods_amount 商品总价
     * @param $goods_number 商品数量
     * @return float
     */
    function calculate($goods_weight, $goods_amount, $goods_number)
    {
        ToLog(5,  __FILE__.' | '.__METHOD__. ' fpbs 计算运费开始  $goods_weight = '.$goods_weight);
        //  发货重量 按 商品累计重量的 1倍计算
        $shippingWeight = $goods_weight * 1;
        $shippingFee = 0;
        if ($this->configure['weight_point_1'] > 0) {
            if ($shippingWeight < $this->configure['weight_point_1']) {
                //  20kg以内德邦快递 向上取整打八折
                $shippingFee = ceil(($this->configure['tpl1_base_fee'] + ceil($goods_weight) * $this->configure['tpl1_rate']));
                $shippingFee = (int)ceil($shippingFee * $this->configure['tpl1_discount']);
                ToLog(5, __METHOD__. ' fpbs 计算运费  $shippingFee = '.$shippingFee);

                //  10kg以下特例3kg 以下要计算起步价
                $baseFee = (int)$this->configure['tpl1_min_fee'];
                if ($shippingFee < $baseFee) {
                    $shippingFee = $baseFee;
                }
                ToLog(5, __METHOD__. ' fpbs 修正运费起步价  $shippingFee = '.$shippingFee);
            }
            else {
                //  有第二个重量分界点即为三段运费规则 —— 当前没用到，用时需要验证规则，暂时按天地华宇的规则计算
                if ($this->configure['weight_point_2'] > 0 && $shippingWeight >= $this->configure['weight_point_2']) {
                    $shippingFee = ceil($goods_weight) * $this->configure['tpl3_rate'] * $this->configure['tpl13_discount'];
                    $shippingFee = ceil($shippingFee  + $this->configure['tpl3_base_fee']);
                    ToLog(5, __METHOD__. ' fpbs 计算运费  $shippingFee = '.$shippingFee);

                    $baseFee = (int)$this->configure['tpl3_min_fee'];
                    if ($shippingFee < $baseFee) {
                        $shippingFee = $baseFee;
                    }
                    ToLog(5, __METHOD__. ' fpbs 计算运费  $shippingFee = '.$shippingFee);
                } else {
                    //  第二段(20kg以上) 天地华宇 打八折
                    $shippingFee = ceil($goods_weight) * $this->configure['tpl2_rate'] * $this->configure['tpl2_discount'];
                    $shippingFee = ceil($shippingFee  + $this->configure['tpl2_base_fee']);
                    ToLog(5, __METHOD__. ' fpbs 计算运费  $shippingFee = '.$shippingFee);

                    $baseFee = (int)$this->configure['tpl2_min_fee'];
                    if ($shippingFee < $baseFee) {
                        $shippingFee = $baseFee;
                    }
                    ToLog(5, __METHOD__. ' fpbs 计算运费  $shippingFee = '.$shippingFee);
                }

            }
        }

        //  若有运费计算结果向上取整
        $shippingFee = ceil($shippingFee);
        $shippingFee = (int)$shippingFee;
        ToLog(5, __METHOD__. ' fpbs 运费取整  $shippingFee = '.$shippingFee);
        return $shippingFee;
    }

    /**
     * 计算订单的配送费用 和 真实的配送方式
     *
     * 用户没有选择现付也需要计算现付的运费金额供用户选择，
     * 用户最终没有选择现付，则入库的运费金额要修正 为 0
     *
     * @param float $goods_weight 商品重量
     * @param float $goods_amount 商品总价
     * @param int $goods_number 商品数量
     * @param int $prepay       是否现付运费
     *
     * @return array    返回 shipping_fee 和 shipping_code
     */
    function calculateAndModify($goods_weight, $goods_amount, $goods_number, $prepay = 0)
    {
        ToLog(5,  __FILE__.' | '.__METHOD__. ' fpbs 计算运费开始  $goods_weight = '.$goods_weight);
        //  发货重量 按 商品累计重量的 1倍计算
        $shippingWeight = $goods_weight * 1;
        $shippingFee = 0;
        $shippingCode = 'fpd';

        if ($this->configure['weight_point_1'] > 0) {
            if ($shippingWeight < $this->configure['weight_point_1']) {
                //  20kg以内德邦快递 向上取整打八折
                $shippingFee = ceil(($this->configure['tpl1_base_fee'] + ceil($goods_weight) * $this->configure['tpl1_rate']));
                $shippingFee = (int)ceil($shippingFee * $this->configure['tpl1_discount']);
                ToLog(5, __METHOD__ . ' fpbs 计算运费  $shippingFee = ' . $shippingFee);

                //  10kg以下特例3kg 以下要计算起步价
                $baseFee = (int)$this->configure['tpl1_min_fee'];
                if ($shippingFee < $baseFee) {
                    $shippingFee = $baseFee;
                }
                ToLog(5, __METHOD__ . ' fpbs 修正运费起步价  $shippingFee = ' . $shippingFee);
            } else {
                //  有第二个重量分界点即为三段运费规则 —— 当前没用到，用时需要验证规则，暂时按天地华宇的规则计算
                if ($this->configure['weight_point_2'] > 0 && $shippingWeight >= $this->configure['weight_point_2']) {
                    $shippingFee = ceil($goods_weight) * $this->configure['tpl3_rate'] * $this->configure['tpl13_discount'];
                    $shippingFee = ceil($shippingFee + $this->configure['tpl3_base_fee']);
                    ToLog(5, __METHOD__ . ' fpbs 计算运费  $shippingFee = ' . $shippingFee);

                    $baseFee = (int)$this->configure['tpl3_min_fee'];
                    if ($shippingFee < $baseFee) {
                        $shippingFee = $baseFee;
                    }
                    ToLog(5, __METHOD__ . ' fpbs 计算运费  $shippingFee = ' . $shippingFee);
                } else {
                    //  第二段(20kg以上) 天地华宇 打八折
                    $shippingFee = ceil($goods_weight) * $this->configure['tpl2_rate'] * $this->configure['tpl2_discount'];
                    $shippingFee = ceil($shippingFee + $this->configure['tpl2_base_fee']);
                    ToLog(5, __METHOD__ . ' fpbs 计算运费  $shippingFee = ' . $shippingFee);

                    $baseFee = (int)$this->configure['tpl2_min_fee'];
                    if ($shippingFee < $baseFee) {
                        $shippingFee = $baseFee;
                    }
                    ToLog(5, __METHOD__ . ' fpbs 计算运费  $shippingFee = ' . $shippingFee);
                }
            }

            if (!empty($prepay)) {
                $shippingCode = 'fpbs';
            }
        }
        $shippingId = Shipping::getShippingIdByCode($shippingCode);

        ToLog(5, __METHOD__. ' guba 运费 $shippingFee = '.$shippingFee);
        return [
            'shipping_fee' => $shippingFee,
            'shipping_code' => $shippingCode,
            'shipping_id' => $shippingId,
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
    function query($invoice_sn)
    {
        return $invoice_sn;
    }
    
    /**
     * 返回快递100查询链接 by wang 
     * URL：https://code.google.com/p/kuaidi-api/wiki/Open_API_Chaxun_URL
     */
    function kuaidi100($invoice_sn){
        $url = 'http://m.kuaidi100.com/query?type=ems&id=1&postid=' .$invoice_sn. '&temp='.time();
        return $invoice_sn;
    }
}

?>
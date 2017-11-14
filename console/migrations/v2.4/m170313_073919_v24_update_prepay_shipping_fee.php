<?php

use yii\db\Migration;
use common\models\Shipping;
use common\models\ShippingArea;
use common\models\AreaRegion;

class m170313_073919_v24_update_prepay_shipping_fee extends Migration
{
    /**
     * 更新运费现付
     */
    public function safeUp()
    {
        //  先删除运费现付模板，然后改写自增ID为4，保证新安装的现付运费模板还是4
        $this->delete(Shipping::tableName(), ['shipping_id' => 4]);
        $this->execute('ALTER TABLE `o_shipping` auto_increment = 4');

        //  同理删除现付运费的区域，并改写自增ID
        $this->delete(ShippingArea::tableName(), ['shipping_id' => 4]);
        $this->execute('ALTER TABLE `o_shipping_area` auto_increment = 6');

        //  插入新的运费区域配置
        $shippingAreaSql = <<<STR
INSERT INTO `o_shipping_area` (`shipping_area_id`, `shipping_area_name`, `shipping_id`, `configure`, `overweight`) VALUES
(6, '新疆', 4, 'a:16:{i:0;a:2:{s:4:"name";s:12:"tpl1_min_fee";s:5:"value";s:2:"53";}i:1;a:2:{s:4:"name";s:13:"tpl1_base_fee";s:5:"value";s:1:"5";}i:2;a:2:{s:4:"name";s:9:"tpl1_rate";s:5:"value";s:2:"16";}i:3;a:2:{s:4:"name";s:13:"tpl1_discount";s:5:"value";s:3:"0.8";}i:4;a:2:{s:4:"name";s:14:"weight_point_1";s:5:"value";s:2:"20";}i:5;a:2:{s:4:"name";s:12:"tpl2_min_fee";s:5:"value";s:2:"72";}i:6;a:2:{s:4:"name";s:13:"tpl2_base_fee";s:5:"value";s:2:"27";}i:7;a:2:{s:4:"name";s:9:"tpl2_rate";s:5:"value";s:1:"4";}i:8;a:2:{s:4:"name";s:13:"tpl2_discount";s:5:"value";s:3:"0.8";}i:9;a:2:{s:4:"name";s:14:"weight_point_2";s:5:"value";s:0:"";}i:10;a:2:{s:4:"name";s:12:"tpl3_min_fee";s:5:"value";s:0:"";}i:11;a:2:{s:4:"name";s:13:"tpl3_base_fee";s:5:"value";s:0:"";}i:12;a:2:{s:4:"name";s:9:"tpl3_rate";s:5:"value";s:0:"";}i:13;a:2:{s:4:"name";s:13:"tpl3_discount";s:5:"value";s:0:"";}i:14;a:2:{s:4:"name";s:10:"free_money";s:5:"value";s:0:"";}i:15;a:2:{s:4:"name";s:16:"fee_compute_mode";s:5:"value";s:0:"";}}', '0.000'),
(7, '河南-湖北', 4, 'a:16:{i:0;a:2:{s:4:"name";s:12:"tpl1_min_fee";s:5:"value";s:2:"20";}i:1;a:2:{s:4:"name";s:13:"tpl1_base_fee";s:5:"value";s:1:"5";}i:2;a:2:{s:4:"name";s:9:"tpl1_rate";s:5:"value";s:1:"5";}i:3;a:2:{s:4:"name";s:13:"tpl1_discount";s:5:"value";s:3:"0.8";}i:4;a:2:{s:4:"name";s:14:"weight_point_1";s:5:"value";s:2:"20";}i:5;a:2:{s:4:"name";s:12:"tpl2_min_fee";s:5:"value";s:2:"72";}i:6;a:2:{s:4:"name";s:13:"tpl2_base_fee";s:5:"value";s:2:"27";}i:7;a:2:{s:4:"name";s:9:"tpl2_rate";s:5:"value";s:1:"2";}i:8;a:2:{s:4:"name";s:13:"tpl2_discount";s:5:"value";s:3:"0.8";}i:9;a:2:{s:4:"name";s:14:"weight_point_2";s:5:"value";s:0:"";}i:10;a:2:{s:4:"name";s:12:"tpl3_min_fee";s:5:"value";s:0:"";}i:11;a:2:{s:4:"name";s:13:"tpl3_base_fee";s:5:"value";s:0:"";}i:12;a:2:{s:4:"name";s:9:"tpl3_rate";s:5:"value";s:0:"";}i:13;a:2:{s:4:"name";s:13:"tpl3_discount";s:5:"value";s:0:"";}i:14;a:2:{s:4:"name";s:10:"free_money";s:5:"value";s:0:"";}i:15;a:2:{s:4:"name";s:16:"fee_compute_mode";s:5:"value";s:0:"";}}', '0.000'),
(8, '山西-河北', 4, 'a:16:{i:0;a:2:{s:4:"name";s:12:"tpl1_min_fee";s:5:"value";s:2:"25";}i:1;a:2:{s:4:"name";s:13:"tpl1_base_fee";s:5:"value";s:2:"10";}i:2;a:2:{s:4:"name";s:9:"tpl1_rate";s:5:"value";s:1:"5";}i:3;a:2:{s:4:"name";s:13:"tpl1_discount";s:5:"value";s:3:"0.8";}i:4;a:2:{s:4:"name";s:14:"weight_point_1";s:5:"value";s:2:"20";}i:5;a:2:{s:4:"name";s:12:"tpl2_min_fee";s:5:"value";s:2:"72";}i:6;a:2:{s:4:"name";s:13:"tpl2_base_fee";s:5:"value";s:2:"27";}i:7;a:2:{s:4:"name";s:9:"tpl2_rate";s:5:"value";s:3:"2.5";}i:8;a:2:{s:4:"name";s:13:"tpl2_discount";s:5:"value";s:3:"0.8";}i:9;a:2:{s:4:"name";s:14:"weight_point_2";s:5:"value";s:0:"";}i:10;a:2:{s:4:"name";s:12:"tpl3_min_fee";s:5:"value";s:0:"";}i:11;a:2:{s:4:"name";s:13:"tpl3_base_fee";s:5:"value";s:0:"";}i:12;a:2:{s:4:"name";s:9:"tpl3_rate";s:5:"value";s:0:"";}i:13;a:2:{s:4:"name";s:13:"tpl3_discount";s:5:"value";s:0:"";}i:14;a:2:{s:4:"name";s:10:"free_money";s:5:"value";s:0:"";}i:15;a:2:{s:4:"name";s:16:"fee_compute_mode";s:5:"value";s:0:"";}}', '0.000'),
(9, '黑龙江', 4, 'a:16:{i:0;a:2:{s:4:"name";s:12:"tpl1_min_fee";s:5:"value";s:2:"27";}i:1;a:2:{s:4:"name";s:13:"tpl1_base_fee";s:5:"value";s:2:"10";}i:2;a:2:{s:4:"name";s:9:"tpl1_rate";s:5:"value";s:1:"6";}i:3;a:2:{s:4:"name";s:13:"tpl1_discount";s:5:"value";s:3:"0.8";}i:4;a:2:{s:4:"name";s:14:"weight_point_1";s:5:"value";s:2:"20";}i:5;a:2:{s:4:"name";s:12:"tpl2_min_fee";s:5:"value";s:2:"72";}i:6;a:2:{s:4:"name";s:13:"tpl2_base_fee";s:5:"value";s:2:"27";}i:7;a:2:{s:4:"name";s:9:"tpl2_rate";s:5:"value";s:3:"2.8";}i:8;a:2:{s:4:"name";s:13:"tpl2_discount";s:5:"value";s:3:"0.8";}i:9;a:2:{s:4:"name";s:14:"weight_point_2";s:5:"value";s:0:"";}i:10;a:2:{s:4:"name";s:12:"tpl3_min_fee";s:5:"value";s:0:"";}i:11;a:2:{s:4:"name";s:13:"tpl3_base_fee";s:5:"value";s:0:"";}i:12;a:2:{s:4:"name";s:9:"tpl3_rate";s:5:"value";s:0:"";}i:13;a:2:{s:4:"name";s:13:"tpl3_discount";s:5:"value";s:0:"";}i:14;a:2:{s:4:"name";s:10:"free_money";s:5:"value";s:0:"";}i:15;a:2:{s:4:"name";s:16:"fee_compute_mode";s:5:"value";s:0:"";}}', '0.000'),
(10, '辽宁', 4, 'a:16:{i:0;a:2:{s:4:"name";s:12:"tpl1_min_fee";s:5:"value";s:2:"28";}i:1;a:2:{s:4:"name";s:13:"tpl1_base_fee";s:5:"value";s:1:"6";}i:2;a:2:{s:4:"name";s:9:"tpl1_rate";s:5:"value";s:1:"7";}i:3;a:2:{s:4:"name";s:13:"tpl1_discount";s:5:"value";s:3:"0.8";}i:4;a:2:{s:4:"name";s:14:"weight_point_1";s:5:"value";s:2:"20";}i:5;a:2:{s:4:"name";s:12:"tpl2_min_fee";s:5:"value";s:2:"72";}i:6;a:2:{s:4:"name";s:13:"tpl2_base_fee";s:5:"value";s:2:"27";}i:7;a:2:{s:4:"name";s:9:"tpl2_rate";s:5:"value";s:3:"2.5";}i:8;a:2:{s:4:"name";s:13:"tpl2_discount";s:5:"value";s:3:"0.8";}i:9;a:2:{s:4:"name";s:14:"weight_point_2";s:5:"value";s:0:"";}i:10;a:2:{s:4:"name";s:12:"tpl3_min_fee";s:5:"value";s:0:"";}i:11;a:2:{s:4:"name";s:13:"tpl3_base_fee";s:5:"value";s:0:"";}i:12;a:2:{s:4:"name";s:9:"tpl3_rate";s:5:"value";s:0:"";}i:13;a:2:{s:4:"name";s:13:"tpl3_discount";s:5:"value";s:0:"";}i:14;a:2:{s:4:"name";s:10:"free_money";s:5:"value";s:0:"";}i:15;a:2:{s:4:"name";s:16:"fee_compute_mode";s:5:"value";s:0:"";}}', '0.000'),
(11, '重庆', 4, 'a:16:{i:0;a:2:{s:4:"name";s:12:"tpl1_min_fee";s:5:"value";s:2:"20";}i:1;a:2:{s:4:"name";s:13:"tpl1_base_fee";s:5:"value";s:1:"5";}i:2;a:2:{s:4:"name";s:9:"tpl1_rate";s:5:"value";s:1:"5";}i:3;a:2:{s:4:"name";s:13:"tpl1_discount";s:5:"value";s:3:"0.8";}i:4;a:2:{s:4:"name";s:14:"weight_point_1";s:5:"value";s:2:"20";}i:5;a:2:{s:4:"name";s:12:"tpl2_min_fee";s:5:"value";s:2:"72";}i:6;a:2:{s:4:"name";s:13:"tpl2_base_fee";s:5:"value";s:2:"27";}i:7;a:2:{s:4:"name";s:9:"tpl2_rate";s:5:"value";s:3:"2.5";}i:8;a:2:{s:4:"name";s:13:"tpl2_discount";s:5:"value";s:3:"0.8";}i:9;a:2:{s:4:"name";s:14:"weight_point_2";s:5:"value";s:0:"";}i:10;a:2:{s:4:"name";s:12:"tpl3_min_fee";s:5:"value";s:0:"";}i:11;a:2:{s:4:"name";s:13:"tpl3_base_fee";s:5:"value";s:0:"";}i:12;a:2:{s:4:"name";s:9:"tpl3_rate";s:5:"value";s:0:"";}i:13;a:2:{s:4:"name";s:13:"tpl3_discount";s:5:"value";s:0:"";}i:14;a:2:{s:4:"name";s:10:"free_money";s:5:"value";s:0:"";}i:15;a:2:{s:4:"name";s:16:"fee_compute_mode";s:5:"value";s:0:"";}}', '0.000'),
(12, '天津', 4, 'a:16:{i:0;a:2:{s:4:"name";s:12:"tpl1_min_fee";s:5:"value";s:2:"22";}i:1;a:2:{s:4:"name";s:13:"tpl1_base_fee";s:5:"value";s:1:"4";}i:2;a:2:{s:4:"name";s:9:"tpl1_rate";s:5:"value";s:1:"6";}i:3;a:2:{s:4:"name";s:13:"tpl1_discount";s:5:"value";s:3:"0.8";}i:4;a:2:{s:4:"name";s:14:"weight_point_1";s:5:"value";s:2:"20";}i:5;a:2:{s:4:"name";s:12:"tpl2_min_fee";s:5:"value";s:2:"72";}i:6;a:2:{s:4:"name";s:13:"tpl2_base_fee";s:5:"value";s:2:"27";}i:7;a:2:{s:4:"name";s:9:"tpl2_rate";s:5:"value";s:3:"2.5";}i:8;a:2:{s:4:"name";s:13:"tpl2_discount";s:5:"value";s:3:"0.8";}i:9;a:2:{s:4:"name";s:14:"weight_point_2";s:5:"value";s:0:"";}i:10;a:2:{s:4:"name";s:12:"tpl3_min_fee";s:5:"value";s:0:"";}i:11;a:2:{s:4:"name";s:13:"tpl3_base_fee";s:5:"value";s:0:"";}i:12;a:2:{s:4:"name";s:9:"tpl3_rate";s:5:"value";s:0:"";}i:13;a:2:{s:4:"name";s:13:"tpl3_discount";s:5:"value";s:0:"";}i:14;a:2:{s:4:"name";s:10:"free_money";s:5:"value";s:0:"";}i:15;a:2:{s:4:"name";s:16:"fee_compute_mode";s:5:"value";s:0:"";}}', '0.000');
STR;

        $this->db->createCommand($shippingAreaSql)->execute();

        //  插入配送区域与区域id的对应关系
        $this->delete(AreaRegion::tableName(), ' shipping_area_id BETWEEN 6 AND 12');
        $areaRegionSql = <<<STR
INSERT INTO `o_area_region` (`shipping_area_id`, `region_id`) VALUES
(6, 3391),
(7, 1662),
(7, 1857),
(8, 65),
(8, 81),
(8, 209),
(8, 234),
(9, 706),
(10, 499),
(11, 2466),
(12, 20);
STR;
        $this->db->createCommand($areaRegionSql)->execute();
    }

    public function safeDown()
    {
        return true;
    }

}

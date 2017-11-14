<?php

use yii\db\Migration;
use common\models\Shipping;
use common\models\ShippingArea;
use common\models\AreaRegion;

class m170313_085821_add_shipping_tpl_fgaf extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->delete(AreaRegion::tableName(), ' shipping_area_id BETWEEN 13 AND 15');
        $this->execute('ALTER TABLE `o_shipping_area` auto_increment = 13');
        //  插入新的运费区域配置
        $shippingAreaSql = <<<STR
INSERT INTO `o_shipping_area` (`shipping_area_id`, `shipping_area_name`, `shipping_id`, `configure`, `overweight`) VALUES
(13, '1000Km以内', 5, 'a:5:{i:0;a:2:{s:4:"name";s:12:"amount_above";s:5:"value";s:4:"2999";}i:1;a:2:{s:4:"name";s:12:"shipping_fee";s:5:"value";s:2:"20";}i:2;a:2:{s:4:"name";s:13:"free_brand_id";s:5:"value";s:0:"";}i:3;a:2:{s:4:"name";s:10:"free_money";s:5:"value";s:0:"";}i:4;a:2:{s:4:"name";s:16:"fee_compute_mode";s:5:"value";s:0:"";}}', '0.000'),
(14, '1000——2000Km', 5, 'a:5:{i:0;a:2:{s:4:"name";s:12:"amount_above";s:5:"value";s:4:"3999";}i:1;a:2:{s:4:"name";s:12:"shipping_fee";s:5:"value";s:2:"30";}i:2;a:2:{s:4:"name";s:13:"free_brand_id";s:5:"value";s:0:"";}i:3;a:2:{s:4:"name";s:10:"free_money";s:5:"value";s:0:"";}i:4;a:2:{s:4:"name";s:16:"fee_compute_mode";s:5:"value";s:0:"";}}', '0.000'),
(15, '偏远地区', 5, 'a:5:{i:0;a:2:{s:4:"name";s:12:"amount_above";s:5:"value";s:4:"4999";}i:1;a:2:{s:4:"name";s:12:"shipping_fee";s:5:"value";s:2:"50";}i:2;a:2:{s:4:"name";s:13:"free_brand_id";s:5:"value";s:0:"";}i:3;a:2:{s:4:"name";s:10:"free_money";s:5:"value";s:0:"";}i:4;a:2:{s:4:"name";s:16:"fee_compute_mode";s:5:"value";s:0:"";}}', '0.000');
STR;
        $this->db->createCommand($shippingAreaSql)->execute();

        //  插入配送区域与区域id的对应关系
        $areaRegionSql = <<<STR
INSERT INTO `o_area_region` (`shipping_area_id`, `region_id`) VALUES
(13, 860),
(13, 1010),
(13, 1261),
(13, 1365),
(13, 1857),
(13, 1986),
(13, 2136),
(13, 2298),
(13, 2728),
(14, 2),
(14, 20),
(14, 39),
(14, 234),
(14, 880),
(14, 1123),
(14, 1487),
(14, 1662),
(14, 2436),
(14, 2466),
(14, 2507),
(14, 2829),
(14, 3064),
(14, 3358),
(15, 376),
(15, 499),
(15, 628),
(15, 706),
(15, 2982),
(15, 3192),
(15, 3305),
(15, 3391);
STR;
        $this->db->createCommand($areaRegionSql)->execute();
    }

    public function safeDown()
    {

        $this->delete(ShippingArea::tableName(), ['shipping_id' => 5]);
        $this->execute('ALTER TABLE `o_shipping_area` auto_increment = 13');
    }

}

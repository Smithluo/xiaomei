<?php

use yii\db\Migration;
use common\models\SuperPkg;

class m170704_070112_modify_super_pkg extends Migration
{
    public function safeUp()
    {
        SuperPkg::deleteAll(['>', 'id', 0]);
        $this->dropColumn('o_super_pkg', 'goods_id');
        $this->addColumn('o_super_pkg', 'gift_pkg_id', " INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '礼包ID' ");
    }

    public function safeDown()
    {
        $this->dropColumn('o_super_pkg', 'gift_pkg_id');
        $this->addColumn('o_super_pkg', 'gift_pkg_id', " `goods_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID' ");;
    }
}

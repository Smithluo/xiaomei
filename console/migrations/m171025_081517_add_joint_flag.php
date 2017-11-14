<?php

use yii\db\Migration;

class m171025_081517_add_joint_flag extends Migration
{
    private $tableBrand = 'o_brand';
    private $tableGoods = 'o_goods';
    private $tableEvent = 'o_event';
    private $tableGoodsActivity = 'o_goods_activity';
    private $tableShopConfig = 'o_shop_config';

    public function safeUp()
    {
        try {
            $this->addColumn($this->tableBrand, 'biz_type', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "业务类型"');
            $this->addColumn($this->tableGoods, 'biz_type', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "业务类型"');
            $this->addColumn($this->tableGoodsActivity, 'biz_type', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "业务类型"');
            $this->addColumn($this->tableEvent, 'biz_type', 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT "业务类型"');
            $this->insert($this->tableShopConfig, [
                'id' => 338,
                'parent_id' => 3,
                'code' => 'search_keywords_joint',
                'type' => 'text',
                'store_range' => '',
                'store_dir' => '',
                'value' => '',
                'sort_order' => 1,
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function safeDown()
    {
        try {
            $this->dropColumn($this->tableBrand, 'biz_type');
            $this->dropColumn($this->tableGoods, 'biz_type');
            $this->dropColumn($this->tableGoodsActivity, 'biz_type');
            $this->dropColumn($this->tableEvent, 'biz_type');
            $this->delete($this->tableShopConfig, [
                'id' => 338,
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        } catch (Throwable $e) {
            return false;
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171025_081517_add_joint_flag cannot be reverted.\n";

        return false;
    }
    */
}

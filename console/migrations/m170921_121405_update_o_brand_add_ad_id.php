<?php

use yii\db\Migration;

class m170921_121405_update_o_brand_add_ad_id extends Migration
{
    private $tableName = 'o_brand';

    public function safeUp()
    {
        try {
            $this->addColumn($this->tableName, 'top_touch_ad_position_id', 'MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT "微信端顶部banner广告位"');
            $this->createIndex('top_touch_ad_position_id', $this->tableName, 'top_touch_ad_position_id');

            $this->addColumn($this->tableName, 'center_touch_ad_position_id', 'MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT "微信端中间广告位"');
            $this->createIndex('center_touch_ad_position_id', $this->tableName, 'center_touch_ad_position_id');
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
            $this->dropColumn($this->tableName, 'top_touch_ad_position_id');
            $this->dropColumn($this->tableName, 'center_touch_ad_position_id');
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
        echo "m170921_121405_update_o_brand_add_ad_id cannot be reverted.\n";

        return false;
    }
    */
}

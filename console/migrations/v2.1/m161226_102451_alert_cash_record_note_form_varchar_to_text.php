<?php

use yii\db\Migration;
use common\models\CashRecord;

class m161226_102451_alert_cash_record_note_form_varchar_to_text extends Migration
{
    public function up()
    {
        $this->alterColumn(
            CashRecord::tableName(),
            'note',
            " TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL  COMMENT '备注';");
    }

    public function down()
    {
        $this->alterColumn(
            CashRecord::tableName(),
            'note',
            " VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '备注';");
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}

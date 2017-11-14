<?php

use yii\db\Migration;

class m170725_102530_modify_scenario_to_scene extends Migration
{
    public $tableName = 'o_article';
    public function safeUp()
    {
        $this->dropIndex('scenario', $this->tableName);
        $this->dropColumn($this->tableName, 'scenario');
        $this->addColumn(
            $this->tableName,
            'scene',
            " VARCHAR(10) NULL DEFAULT '' COMMENT '应用场景' "
        );
        $this->createIndex('scene', $this->tableName, 'scene');
    }

    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170725_102530_modify_scenario_to_sence cannot be reverted.\n";

        return false;
    }
    */
}

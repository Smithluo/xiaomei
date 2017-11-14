<?php

use yii\db\Migration;

class m170315_075849_o_super_pag extends Migration
{
    private  $tableName = 'o_super_pkg';
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        $this -> createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'pag_name' => $this->string(16)->notNull()->defaultValue('')->comment('礼包名称'),
                'pag_desc' => $this->string(64)->notNull()->defaultValue('')->comment('礼包描述'),
                'goods_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('商品ID'),
                'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
                'start_time' =>  $this->dateTime()->notNull()->defaultValue(0)->comment('开始时间戳'),
                'end_time' =>   $this->dateTime()->notNull()->defaultValue(0)->comment('结束时间戳'),
            ],$tableOptions
        );

    }

    public function down()
    {
        //echo "m170315_075849_o_super_pag cannot be reverted.\n";
        $this-> dropTable($this->tableName);
        return true;
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

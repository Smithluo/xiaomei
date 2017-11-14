<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    private $table_name = 'o_brand_admin';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
    }

    public function down()
    {

    }
}
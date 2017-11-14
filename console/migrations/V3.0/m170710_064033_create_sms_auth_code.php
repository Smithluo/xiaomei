<?php

use yii\db\Migration;

/**
 * Class m170710_064033_create_sms_auth_code
 * 短信验证码入库
 */
class m170710_064033_create_sms_auth_code extends Migration
{
    public $tbName = 'o_sms_auth_code';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $this->tbName,
            [
                'mobile' => $this->string(11)->notNull()->comment('手机号'),
                'code' => $this->string(20)->notNull()->comment('验证码'),
                'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
                'expired' => $this->integer()->unsigned()->notNull()->comment('有效期'),
                'PRIMARY KEY (mobile)',
            ],
            $tableOptions
        );
    }

    public function safeDown()
    {
        $this->dropTable($this->tbName);
    }

}

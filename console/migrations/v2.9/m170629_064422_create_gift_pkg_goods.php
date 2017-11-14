<?php

use yii\db\Migration;

class m170629_064422_create_gift_pkg_goods extends Migration
{
    const TB_NAME = 'o_gift_pkg_goods';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            self::TB_NAME,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'gift_pkg_id' => $this->integer()->notNull()->comment('礼包活动'),
                'goods_id' => $this->integer()->notNull()->comment('商品ID'),
                'goods_num' => $this->integer()->notNull()->defaultValue(1)->comment('商品数量'),
            ],
            $tableOptions
        );

        $this->createIndex('gift_pkg_id', self::TB_NAME, 'gift_pkg_id');
        $this->createIndex('goods_id', self::TB_NAME, 'goods_id');
    }

    public function safeDown()
    {
        $this->dropTable(self::TB_NAME);
    }

}

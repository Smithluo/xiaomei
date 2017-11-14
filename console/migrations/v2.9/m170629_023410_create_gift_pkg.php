<?php

use yii\db\Migration;

/**
 * Class m170629_023410_create_gift_pkg
 * 创建礼包活动表 不设活动时段，售完即止，库存同步
 */
class m170629_023410_create_gift_pkg extends Migration
{
    const TB_NAME = 'o_gift_pkg';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            self::TB_NAME,
            [
                'id' => $this->primaryKey()->comment('礼包活动ID'),
                'name' => $this->string(40)->notNull()->comment('礼包活动名称'),
                'img' => $this->string(255)->notNull()->comment('主显示图'),
                'original_img' => $this->string(255)->notNull()->comment('原图'),
                'thumb_img' => $this->string(255)->notNull()->comment('缩略图'),
                'price' => $this->decimal(10,2)->notNull()->comment('活动价'),
                'shipping_code' => $this->string(20)->notNull()->comment('配送政策'),
                'brief' => $this->string(255)->null()->defaultValue('')->comment('卖点(逗号分隔)'),
                'is_on_sale' => $this->boolean()->notNull()->defaultValue(false)->comment('是否上架'),
                'updated_at' => $this->dateTime()->notNull()->defaultValue('0000-00-00 00:00:00')->comment('更新时间'),
                'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('最后操作人'),
                'pkg_desc' => $this->text()->null()->comment('礼包活动详情')
            ],
            $tableOptions
        );

        $this->createIndex('shipping_code', self::TB_NAME, 'shipping_code');
        $this->createIndex('is_on_sale', self::TB_NAME, 'is_on_sale');
    }

    public function safeDown()
    {
        $this->dropTable(self::TB_NAME);
    }

}

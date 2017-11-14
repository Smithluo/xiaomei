<?php

use yii\db\Migration;

/**
 * 活动中心页面 导航的顺序配置
 * Class m170811_062121_create_table_activity_sort
 */
class m170811_062121_create_table_activity_sort extends Migration
{
    public $tbName = 'o_activity_sort';

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable(
            $this->tbName,
            [
                'id' => $this->primaryKey()->comment('ID'),
                'type' => $this->string(20)->notNull()->unique()->comment('活动类型'),
                'alias' => $this->string(10)->notNull()->comment('活动类型名称'),
                'link' => $this->string(255)->notNull()->comment('链接页面'),
                'sort_order' => $this->smallInteger()->unsigned()->notNull()->defaultValue(30000)->comment('排序值'),
                'is_show' => $this->smallInteger()->notNull()->defaultValue(0)->comment('是否显示'),
                'show_limit' => $this->smallInteger()->unsigned()->notNull()->defaultValue(20)->comment('首页显示数量上限'),
            ],
            $tableOptions
        );

        //  插入默认数据
        $feedSql = "INSERT INTO `o_activity_sort` (`id`, `type`, `alias`, `link`, `sort_order`, `is_show`, `show_limit`) VALUES
(1, 'group_buy', '团采聚惠', '/activity.php?type=group_buy', 20000, 1, 20),
(2, 'flash_sale', '限量秒杀', '/activity.php?type=flash_sale', 35000, 1, 20),
(3, 'full_gift', '超值满赠', '/activity.php?type=full_gift', 34000, 1, 20),
(4, 'full_cut', '惊喜满减', '/activity.php?type=full_cut', 33000, 1, 20),
(5, 'gift_pkg', '精选套餐', '/activity.php?type=gift_pkg', 32000, 1, 20);";

        Yii::$app->db->createCommand($feedSql)->execute();
    }

    public function safeDown()
    {
        $this->dropTable($this->tbName);
    }

}

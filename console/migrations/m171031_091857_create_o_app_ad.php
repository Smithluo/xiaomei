<?php

use yii\db\Migration;

class m171031_091857_create_o_app_ad extends Migration
{
    private $tableNameAdPosition = 'o_app_ad_position';
    private $tableNameAd = 'o_app_ad';

    public function safeUp()
    {
        try {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
            $this->createTable(
                $this->tableNameAdPosition,
                [
                    'id' => $this->primaryKey()->comment('ID'),
                    'title' => $this->string(60)->notNull()->defaultValue('')->comment('标题'),
                ],
                $tableOptions
            );

            $this->createTable(
                $this->tableNameAd,
                [
                    'id' => $this->primaryKey()->comment('ID'),
                    'position_id' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('广告位'),
                    'title' => $this->string(60)->notNull()->defaultValue('')->comment('标题'),
                    'desc' => $this->string(80)->notNull()->defaultValue('')->comment('描述'),
                    'start_time' => $this->dateTime()->notNull()->defaultValue(0)->comment('开始时间'),
                    'end_time' => $this->dateTime()->notNull()->defaultValue(0)->comment('结束时间'),
                    'enable' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('是否显示'),
                    'image' => $this->string(255)->notNull()->defaultValue('')->comment('图片'),
                    'route' => $this->string(255)->notNull()->defaultValue('')->comment('app页面路由'),
                    'params' => $this->text()->comment('参数'),
                    'sort_order' => $this->smallInteger(5)->unsigned()->notNull()->defaultValue(0)->comment('排序值'),
                ],
                $tableOptions
            );

            $this->createIndex('enable', $this->tableNameAd, [
                'enable',
                'start_time',
                'end_time',
            ]);

            $this->createIndex('sort_order', $this->tableNameAd, 'sort_order');

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
            $this->dropTable($this->tableNameAdPosition);
            $this->dropTable($this->tableNameAd);
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
        echo "m171031_091857_create_o_app_ad cannot be reverted.\n";

        return false;
    }
    */
}

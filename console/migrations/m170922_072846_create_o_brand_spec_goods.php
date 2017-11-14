<?php

use yii\db\Migration;

class m170922_072846_create_o_brand_spec_goods extends Migration
{
    private $tableBrandSpecGoodsCat = 'o_brand_spec_goods_cat';
    private $tableBrandSpecGoods = 'o_brand_spec_goods';

    public function safeUp()
    {
        try {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

            $this->createTable(
                $this->tableBrandSpecGoodsCat,
                [
                    'id' => $this->primaryKey(),
                    'brand_id' => $this->smallInteger()->unsigned()->notNull()->defaultValue(0)->comment('品牌'),
                    'title' => $this->string(56)->unsigned()->notNull()->defaultValue('')->comment('标题'),
                    'sort_order' => $this->smallInteger()->unsigned()->notNull()->defaultValue(100)->comment('排序值'),
                ],
                $tableOptions
            );
            $this->createIndex('brand_id', $this->tableBrandSpecGoodsCat, 'brand_id');
            $this->createIndex('sort_order', $this->tableBrandSpecGoodsCat, 'sort_order');

            $this->createTable(
                $this->tableBrandSpecGoods,
                [
                    'id' => $this->primaryKey(),
                    'spec_goods_cat_id' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('分类'),
                    'goods_id' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('商品'),
                    'sort_order' => $this->smallInteger()->unsigned()->notNull()->defaultValue(100)->comment('排序值'),
                ],
                $tableOptions
            );
            $this->createIndex('spec_goods_cat_id', $this->tableBrandSpecGoods, 'spec_goods_cat_id');
            $this->createIndex('goods_id', $this->tableBrandSpecGoods, 'goods_id');
            $this->createIndex('sort_order', $this->tableBrandSpecGoods, 'sort_order');

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
            $this->dropTable($this->tableBrandSpecGoodsCat);
            $this->dropTable($this->tableBrandSpecGoods);
            return true;
        } catch (Exception $e) {
            return false;
        } catch (Throwable $e) {
            return false;
        }
    }
}

<?php

use yii\db\Migration;

/**
 * Handles the creation for table `brand_application`.
 */
class m160624_024640_create_brand_application extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_brand_application', [
            'id' => $this->primaryKey(),
            'company_name' => $this->string(255)->notNull()->defaultValue('')->comment('店铺名称'),
            'company_address' => $this->string(255)->notNull()->defaultValue('')->comment('公司地址'),
            'name' => $this->string(20)->notNull()->defaultValue('')->comment('姓名'),
            'position' => $this->string(50)->notNull()->defaultValue('')->comment('职位'),
            'contact' => $this->string(50)->notNull()->defaultValue('')->comment('联系方式'),
            'brands' => $this->string(255)->notNull()->defaultValue('')->comment('品牌名称'),
            'licence' => $this->string(4)->defaultValue(0)->comment('是否有授权'),
            'recorded' => $this->string(4)->defaultValue(0)->comment('是否有备案'),
            'registed' => $this->string(4)->defaultValue(0)->comment('能否提供商标注册证明'),
            'taxed' => $this->string(4)->defaultValue(0)->comment('能否提供报关单及完税证明'),
            'checked' => $this->string(4)->defaultValue(0)->comment('能否提供检疫检验证明'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_brand_application');
    }
}

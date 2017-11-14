<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_servicer_spec_strategy`.
 */
class m160603_020131_create_o_servicer_spec_strategy extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_servicer_spec_strategy', [
            'id' => $this->primaryKey(),
            'brand_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('策略应用的品牌'),
            'goods_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('策略应用的商品'),
            'servicer_user_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('1级服务商id'),
            'strategy_id' => $this->integer(8)->unsigned()->notNull()->defaultValue(0)->comment('分成策略id，对应订单总价的百分比'),
            'percent_level_1' => $this->smallInteger(4)->unsigned()->notNull()->defaultValue(0)->comment('一级服务商分成百分比'),
            'percent_level_2' => $this->smallInteger(4)->unsigned()->notNull()->defaultValue(0)->comment('二级服务商分成百分比'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_servicer_spec_strategy');
    }
}

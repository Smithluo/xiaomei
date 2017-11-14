<?php

use yii\db\Migration;

/**
 * Handles the creation for table `o_servicer_strategy`.
 */
class m160603_015700_create_o_servicer_strategy extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('o_servicer_strategy', [
            'id' => $this->primaryKey(),
            'percent_total' => $this->double()->unsigned()->notNull()->defaultValue(0)->comment('总的分成比例'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('o_servicer_strategy');
    }
}

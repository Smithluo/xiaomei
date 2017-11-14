<?php

use yii\db\Migration;

/**
 * Handles adding channel to table `users`.
 */
class m161025_024013_add_channel_to_users extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('o_users', 'channel', " VARCHAR(60) NULL COMMENT '渠道' AFTER `biz_license_pic` ");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('o_users', 'channel');
    }
}

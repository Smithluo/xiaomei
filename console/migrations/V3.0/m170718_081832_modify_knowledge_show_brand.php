<?php

use yii\db\Migration;
use common\models\KnowledgeShowBrand;

/**
 * 美妆学院 推荐品牌 添加 平台字段 支持区分 微信、PC
 * Class m170718_081832_modify_knowledge_show_brand
 */
class m170718_081832_modify_knowledge_show_brand extends Migration
{
    public $tbName = 'o_knowledge_show_brand';
    public function safeUp()
    {
        $this->addColumn('o_knowledge_show_brand', 'platform', " VARCHAR(10) NULL COMMENT '平台' ");
        $this->createIndex('platform', 'o_knowledge_show_brand', 'platform');

        KnowledgeShowBrand::updateAll(['platform' => KnowledgeShowBrand::PLATFORM_M]);
    }

    public function safeDown()
    {
        $this->dropIndex('platform', 'o_knowledge_show_brand');
        $this->dropColumn('o_knowledge_show_brand', 'platform');
    }
}

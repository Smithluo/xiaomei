<?php

use yii\db\Migration;
use common\models\Event;
use common\models\EventRule;

/**
 * Class m170726_065359_event_rule_add_event_id
 * 满赠活动支持 活动 => 活动规则 1:n
 * o_event_rule 添加 event_id, 一个满赠活动支持多个赠品规则，解决商品属性中的物料配比不走库存的问题
 * event表中的 rule_id 弃用， 满赠、满减、优惠券 都通过event_id 去关联对应 rule ，不使用o_event.rule_id
 * o_event.event_type 添加 新的映射，区分 满赠、物料配比
 */
class m170726_065359_event_rule_add_event_id extends Migration
{
    private $tbName = 'o_event_rule';

    public function safeUp()
    {
        $this->addColumn($this->tbName,'event_id', " INT NOT NULL DEFAULT '0' COMMENT '关联活动' ");
        $this->createIndex('event_id', $this->tbName, 'event_id');

        $eventList = Event::find()
            ->select(['event_id', 'rule_id'])
            ->where(['>', 'rule_id', 0])
            ->all();
        foreach ($eventList as $event) {
            $eventRule = EventRule::find()->where(['rule_id' => $event->rule_id])->one();
            if (!empty($eventRule)) {
                $eventRule->event_id = $event->event_id;
                if (!$eventRule->save()) {
                    echo 'rule_id = '.$event->rule_id.' set event_id error'.PHP_EOL;
                }
            }
        }
    }

    public function safeDown()
    {
        $this->dropIndex('event_id', $this->tbName);
        $this->dropColumn($this->tbName, 'event_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170726_065359_event_rule_add_event_id cannot be reverted.\n";

        return false;
    }
    */
}

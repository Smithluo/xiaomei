<?php

use yii\db\Migration;

class m170812_042828_feed_ad_position_for_activity_center extends Migration
{
    public function safeUp()
    {
        $sql = "INSERT INTO `o_ad_position` (`position_name`, `ad_width`, `ad_height`, `position_desc`, `position_style`) VALUES
('activity_hot_banner', 640, 360, '活动首页轮播 配连接到活动详情页', '{foreach from=\$ads item=ad}<div style=\"background: url(&quot;{\$ad.src}&quot;) center center;\"> <a href=\"{\$ad.url}\" style=\"width: 100%;display: block;height: 360px;\"></a></div>{/foreach}'),
('activity_hot_right_first', 276, 178, '活动首页轮播右上广告位', '{foreach from=\$ads item=ad}<a href=\"{\$ad.url}\" target=\"_blank\"><img src=\"{\$ad.src}\"></a>{/foreach}'),
('activity_hot_right_second', 276, 178, '活动首页轮播右下广告位', '{foreach from=\$ads item=ad}<a href=\"{\$ad.url}\" target=\"_blank\"><img src=\"{\$ad.src}\"></a>{/foreach}'),
('activity_topic_group_buy', 1200, 360, '活动中心子类型页banner团采', '<div class=\"banner_other\">{foreach from=\$ads item=ad}<a href=\"{\$ad.url}\"><img src=\"{\$ad.src}\" class=\"banner_img\"></a>{/foreach}</div>'),
('activity_topic_flash_sale', 1200, 360, '活动中心子类型页banner秒杀', '<div class=\"banner_other\">{foreach from=\$ads item=ad}<a href=\"{\$ad.url}\"><img src=\"{\$ad.src}\" class=\"banner_img\"></a>{/foreach}</div>'),
('activity_topic_full_gift', 1200, 360, '活动中心子类型页banner满赠', '<div class=\"banner_other\">{foreach from=\$ads item=ad}<a href=\"{\$ad.url}\"><img src=\"{\$ad.src}\" class=\"banner_img\"></a>{/foreach}</div>'),
('activity_topic_full_cut', 1200, 360, '活动中心子类型页banner满减', '<div class=\"banner_other\">{foreach from=\$ads item=ad}<a href=\"{\$ad.url}\"><img src=\"{\$ad.src}\" class=\"banner_img\"></a>{/foreach}</div>'),
('activity_topic_gift_pkg', 1200, 360, '活动中心子类型页banner礼包', '<div class=\"banner_other\">{foreach from=\$ads item=ad}<a href=\"{\$ad.url}\"><img src=\"{\$ad.src}\" class=\"banner_img\"></a>{/foreach}</div>');";
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function safeDown()
    {
        $nameList = [
            'activity_hot_banner',
            'activity_hot_right_first',
            'activity_hot_right_second',
            'activity_topic_group_buy',
            'activity_topic_flash_sale',
            'activity_topic_full_gift',
            'activity_topic_full_cut',
            'activity_topic_full_cut',
            'activity_topic_gift_pkg',
        ];
        \common\models\AdPosition::deleteAll(['position_name' => $nameList]);
    }

}

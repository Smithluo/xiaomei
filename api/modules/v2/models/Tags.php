<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/3/16
 * Time: 10:49 AM
 */

namespace api\modules\v2\models;

class Tags extends \common\models\Tags
{
    public static $tag_name_map = [
        'new'       => 1,   //  新品
        'supply'    => 2,   //  直供
        'gift'      => 3,   //  满赠
        'mix_up'    => 4,   //  混批
        'star'      => 5,   //  明星单品
        'group'     => 6,   //  团采
        'full_cut'  => 7,   //  满减
        'coupon'    => 8,   //  优惠券
    ];

}
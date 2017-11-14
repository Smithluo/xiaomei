<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/3/16
 * Time: 10:49 AM
 */

namespace api\modules\v1\models;

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

    /**
     * 处理商品标签，返回要显示的标签
     * @param $tags array
     * @return array
     */
    public static function formartTags($tags)
    {
        $show_tag_array = [];
        $showTagMap = [];
        $tag_array = [];
        //  按标签类型分组
        foreach ($tags as $tag) {
            if ($tag['enabled'] == Tags::TAG_ENABLE) {
                $tagItem = [
                    'type' => $tag['type'],
                    'name' => $tag['name'],
                    'code' => $tag['code'],
                    'mCode' => $tag['mCode'],
                    'sort' => $tag['sort'],
                ];
                $tag_array['TYPE'.$tag['type']][$tag['sort']] = $tagItem;
                $showTagMap[$tag['sort']] = $tagItem;
            }
        }

        //  统一类型的标签按排序值排序，取显示的最大数量
        if ($tag_array) {
            $limit_map = Tags::$tag_show_limit_map;
            foreach ($limit_map as $type => $limit) {
                if (isset($tag_array['TYPE'.$type])) {
                    $show_tag_array['TYPE'.$type] = array_slice($tag_array['TYPE'.$type], 0, $limit);
                }
            }
        }

        return [
            'show_tag_array'    => $show_tag_array,
            'showTagMap'        => $showTagMap,
        ];
    }

}
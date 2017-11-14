<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/11/14
 * Time: 10:58
 */

namespace common\helper;


class SortHelper
{
    /**
     * usort 的回调函数， 按sort_order 逆序排列
     * @param $a
     * @param $b
     * @return int
     */
    public function usort_by_sortorder($a, $b) {
        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        } else {
            return $a['sort_order'] > $b['sort_order'] ? -1 : 1;
        }
    }
}
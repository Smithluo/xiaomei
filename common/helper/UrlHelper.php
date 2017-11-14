<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/10/28
 * Time: 16:10
 */

namespace common\helper;


class UrlHelper
{

    /**
     * unset掉键值对格式参数中的指定参数，并且重新组装参数
     * @param $paramArr  /键值对参数
     * @param $params     /指定参数名
     * @return string   以&开始的字符串
     */
    public static function clearUrlParam($paramArr, $params)
    {
        $url = '';
        if (is_array($params)) {
            foreach ($params as $param) {
                unset($paramArr[$param]);
            }
        } else {
            unset($paramArr[$params]);
        }

        foreach ($paramArr as $key => $value) {
            $url .= '&' . $key . '=' . $value;
        }
        return $url;
    }

    /**
     * 格式化当前链接中的参数
     * @return array 返回键值对格式的参数
     */
    public static function getLinksParam()
    {
        //拼接链接
        $urlParam = $_SERVER["QUERY_STRING"];
        $paramData = array_filter(explode('&', $urlParam));
        //转换为键值对
        $paramDataArr = [];
        foreach ($paramData as $paramDataStr) {
            $paramDataStrArr = explode('=', $paramDataStr);
            $paramDataArr[$paramDataStrArr[0]] = $paramDataStrArr[1];
        }
        return $paramDataArr;
    }

    /**
     * 格式化Yii AR 分页的参数
     * @param $links
     * @return array
     */
    public static function formatLinks($links)
    {
        $valid_params_lisy = ['brand_id', 'sub_cat_id', 'tag', 'region', 'effect', 'keywords', 'goods_sn', 'page'];

        foreach ($links as &$link) {
            $parse_arr = parse_url($link['href']);
            parse_str($parse_arr['query'], $gets);

            $valid_params = [];
            foreach ($gets as $key => $param) {
                if (in_array($key, $valid_params_lisy) && $param) {
                    $valid_params[$key] = $param;
                }
            }

            if ($valid_params) {
                $link['format_url'] = '?'.http_build_query($valid_params);
            }
        }

        return $links;
    }

    /**
     * 文章页的筛选url处理
     */
    public static function formatArticleSearch($uri)
    {
        //  配置所有参数，便于生成所有对应的拼接uri
        $basic_params = [
            'country' => 0,
            'link_cat' => 0,
            'scene' => '',
            'act' => 'all',    //  请求
            'catId' => 33,  //  文章的父级分类ID   33 => 小美学院
            'brandId' => 0,  //  文章的关联品牌ID
            'sort' => ['complex_order' => SORT_DESC],    //
            'page' => 1,
        ];

        $parse_arr = parse_url($uri);
        parse_str($parse_arr['query'], $params);
        $params = array_merge($basic_params, $params);

        if ($params['sort'] == 'new') {
            $params['sort'] = ['add_time' => SORT_DESC];
        }
        if ($params['sort'] == 'hot') {
            $params['sort'] = ['click' => SORT_DESC];
        }

        $mixUri = [];
        //  默认URI 添加第一页参数，其他的参数就都用&拼接 避免*.php& 错误
        $base_uri = $parse_arr['path'];
        foreach ($params as $key => $value) {
            $uri_params = array_diff_key($params, [$key => 1]); //  某一筛选项的href链接 应去掉自身的参数，然后再拼接遍历的选项对应的key=>value
            $uri_params = array_filter($uri_params);
            $uri_params['page'] = 1;    // 跳转后 页面page = 1
            $mixUri[$key] = $base_uri.'?'.http_build_query($uri_params);
        }

        return [
            'params' => $params,
            'mixUri' => $mixUri,
        ];
    }
}
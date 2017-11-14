<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/10/28
 * Time: 14:50
 */

namespace common\helper;

use common\models\Category;
use common\models\Goods;
use common\models\GoodsTag;
use common\models\Region;
use common\models\ServicerUserInfo;
use common\models\ShopConfig;
use common\models\Users;
use \Yii;
use common\models\UserRank;
use yii\db\Query;

class CacheHelper
{

    /**
     * 设置用户等级缓存
     */
    public static function setUserRankCache()
    {
        $cache_file_name = self::getCacheFileName('user_rank_map');
        $cache = '';

        $rs = UserRank::find()->asArray()->indexBy('rank_id')->all();
        if ($rs) {
            $cache = json_encode($rs);
        }

        file_put_contents($cache_file_name, $cache);
    }

    /**
     * 获取用户等级的缓存
     * @param int $key
     * @return mixed
     */
    public static function getUserRankCache($key = 0)
    {
        $cache = self::getCache('user_rank_map');

        if ($key && isset($cache[$key])) {
            return $cache[$key];
        } else {
            return $cache;
        }
    }

    /**
     * 配置商城全局配置参数
     */
    public static function setShopConfigParams()
    {
        $cache_file_name = self::getCacheFileName('shop_config_params');
        $cache = '';

        $rs = ShopConfig::find()
            ->asArray()
            ->indexBy('code')
            ->all();
        if ($rs) {
            $cache = json_encode($rs);
        }

        file_put_contents($cache_file_name, $cache);
    }

    /**
     * 获取商城全局配置参数
     *
     * 如果没有找到对应配置的值，则返回NULL，上层调用时用 !== NULL 做有效性验证
     * @param array $keys   可指定获取部分配置
     * @return array
     */
    public static function getShopConfigParams($keys = [])
    {
        $cache = self::getCache('shop_config_params');

        if ($keys && is_array($keys)) {
            foreach ($keys as $key) {
                $rs[$key] = isset($cache[$key]) ? $cache[$key] : NULL;
            }
        } elseif ($keys && is_string($keys)) {
            $rs = $cache[$keys];
        } else {
            $rs = $cache;
        }

        return $rs;
    }

    public static function getShopConfigValueByCode($code) {
        $config = self::getShopConfigParams([
            $code
        ]);
        if (empty($config)) {
            return null;
        }
        return $config[$code]['value'];
    }

    /**
     * 设置区域地址s数的缓存
     *
     * tree = [
     *      $province_id => [
     *          'region_name' => 省级行政单位名称,
     *          'region_id' => $region_id,
     *          'children' => [
     *              $city_id = [
     *                  'region_name' => '市级行政单位（含省直辖县）名称',
     *                  'region_id' => $region_id,
     *                  //  4个直辖市没有下级行政单位
     *              ],
     *              $city_id = [
     *                  'region_name' => '市级行政单位（含省直辖县）名称',
     *                  'region_id' => $region_id,
     *                  //  4个直辖市没有下级行政单位
     *                  'children' => [
     *                      $region_id => $region_name, //  区域ID => 县区级行政单位名称
     *                      $region_id => $region_name,
     *                      ...
     *                  ]
     *              ],
     *              ...
     *          ]
     *      ],
     *      ...
     * ]
     */
    public static function setRegionCache()
    {
        $cache_file_name = self::getCacheFileName('region_map');
        $cache = '';

        $province_tree = Region::find()->select([
                'region_name', 'region_id'
            ])->where([
                'parent_id' => 1
            ])->asArray()
            ->indexBy('region_id')
            ->all();

        $province_ids = array_keys($province_tree);
        $city_tree = Region::find()->select([
                'region_name', 'region_id', 'parent_id'
            ])->where([
                'parent_id' => $province_ids
            ])->asArray()
            ->indexBy('region_id')
            ->all();

        $city_ids = array_keys($city_tree);
        $district_tree = Region::find()->select([
                'region_name', 'region_id', 'parent_id'
            ])->where([
                'parent_id' => $city_ids
            ])->asArray()
            ->indexBy('region_id')
            ->all();

        foreach ($district_tree as $district_id => $district) {
            $city_tree[$district['parent_id']]['children'][$district_id] = [
                'region_name' => $district['region_name'],
                'region_id' => $district['region_id'],
            ];
        }

        foreach ($city_tree as $city_id => $city) {
            $format_city = [
                'region_name' => $city['region_name'],
                'region_id' => $city['region_id'],
            ];
            if (isset($city['children'])) {
                $format_city['children'] = $city['children'];
            }
            $province_tree[$city['parent_id']]['children'][$city_id] = $format_city;
        }

        if ($province_tree) {
            $cache = json_encode($province_tree);
        }

        file_put_contents($cache_file_name, $cache);
    }

    /**
     * 设置 IOS APP 使用的 地址缓存文件
     */
    public static function setRegionAppCache() {
        $cache_file_name = self::getCacheFileName('region_app_map');

        $regions = Region::find()->select([Region::tableName().'.region_id', Region::tableName().'.region_name', Region::tableName().'.parent_id'])
            ->joinWith([
                'children c1' => function($query) {
                    $query->select(['c1.region_id', 'c1.region_name', 'c1.parent_id'])->orderBy([
                        'c1.region_id' => SORT_ASC
                    ]);
                },
                'children.children c2' => function($query) {
                    $query->select(['c2.region_id', 'c2.region_name', 'c2.parent_id'])->orderBy([
                        'c2.region_id' => SORT_ASC
                    ]);
                }
            ])
            ->where([Region::tableName().'.parent_id' => 1])
            ->orderBy([
                Region::tableName().'.region_id' => SORT_ASC
            ])
            ->asArray()
            ->all();

//        if (count($regions) > 0) {
//            $regions = array_slice($regions, 1);
//        }

        $cache = json_encode([
            'regions' => $regions
        ]);

        file_put_contents($cache_file_name, $cache);
    }

    public static function getRegionAppCache() {
        $cache_file_name = self::getCacheFileName('region_app_map');
        $content = file_get_contents($cache_file_name);
        return json_decode($content);
    }

    /**
     * 设置 IOS APP 使用的 地址缓存文件
     */
    public static function setRegionWechatRegisterCache() {
        $cache_file_name = self::getCacheFileName('region_wechat_register_map');

        $regions = Region::find()->select([Region::tableName().'.region_id', Region::tableName().'.region_name', Region::tableName().'.parent_id'])
            ->joinWith([
                'children c1' => function($query) {
                    $query->select(['c1.region_id', 'c1.region_name', 'c1.parent_id'])->orderBy([
                        'c1.region_id' => SORT_ASC
                    ]);
                },
                'children.children c2' => function($query) {
                    $query->select(['c2.region_id', 'c2.region_name', 'c2.parent_id'])->orderBy([
                        'c2.region_id' => SORT_ASC
                    ]);
                }
            ])
            ->where([Region::tableName().'.parent_id' => 1])
            ->orderBy([
                Region::tableName().'.region_id' => SORT_ASC
            ])
            ->asArray()
            ->all();

        //  转换数据的 key
        $dataMap = [];
        if ($regions) {
            foreach ($regions as $item) {
                $data = [];
                $data['id']     = $item['region_id'];
                $data['name']   = $item['region_name'];

                if (!empty($item['children'])) {
                    $childrenMap = [];
                    foreach ($item['children'] as $child) {
                        $children = [];
                        $children['id']     = $child['region_id'];
                        $children['name']   = $child['region_name'];

                        //  处理第三级区域
                        if (!empty($child['children'])) {
                            foreach ($child['children'] as $grandson) {
                                $children['child'][] = [
                                    'id' => $grandson['region_id'],
                                    'name' => $grandson['region_name'],
                                ];
                            }
                        }

                        $childrenMap[] = $children;
                    }

                    $data['child'] = $childrenMap;
                }

                $dataMap[] = $data;
            }
        }

        $cache = json_encode([
            'data' => $dataMap
        ]);

        file_put_contents($cache_file_name, $cache);
//        $m_region_cache_file = Yii::$app->params['m_region_cache_file'];
//        file_put_contents($cache_file_name, $cache);
    }

    /**
     * 获取需要的区域名称或区域数
     *
     * @param array $params = [
     *      'type' => tree | name,  //  要获取地址树 或者 用户地址里的region_id 对应的具体地址
     *      'ids' => array,         //  按省、市、县区 的顺序填写，可以为空
     *      'deepth' => 0 | 1       //  只在type = tree时有效，获取全部、一层、两层数据
     *      tree 对应 id 的类型为 int，$id > 0 提供省级单位获取对应省份的地址树; $id = 0 提供完整的地址树
     *      name 对应 id 的类型为 array，按 省、市、县区获取地址名称
     * ]
     * @return array
     */
    public static function getRegionCache($params = [])
    {
        $cache = self::getCache('region_map');

        if (!isset($params['type'])) {
            //  $params = [] || $params = ['type' => 'tree']    获取完整树
            return $cache;
        }
        elseif ($params['type'] == 'tree') {

            if (empty($params['ids'])) {
                //  $params = ['type' => 'tree', 'ids' => []]
                if ($params['deepth'] == 1) {
                    //  只获取省份 region_id => region_name
                    return array_column($cache, 'region_name', 'region_id');
                } else {
                    return $cache;  //  获取完整树
                }
            } else {
                //  $params = ['type' => 'tree', 'id' => [$province_id, $city_id]]
                list($province_id, $city_id, $district_id) = array_pad($params['ids'], 3, 0);
                //  如果有省份ID，则获取该省份的树
                if (!empty($province_id)) {
                    $return = $cache[$province_id];

                    if ($return) {
                        //  如果有市ID，则获取该市的树 即区域map
                        if (!empty($city_id)) {
                            $return = $return['children'][$city_id];

                            return $return['children'] ?: false;
                        } else {
                            if ($params['deeps'] == 1) {
                                //  只获取市级别的 region_id => region_name
                                return array_column($return, 'region_name', 'region_id');
                            } else {
                                //  获取省下面的 完整树 即 每一个市 对应的 县区
                                return array_column($return['children'], 'region_name', 'region_id');
                            }
                        }
                    }

                } else {
                    //  $province_id == 0
                    return false;
                }
            }
        }
        elseif ($params['type'] == 'name') {
            //  获取省、市、县区 的完整名称， 参数必须按顺序写
            if (is_array($params['ids'])) {
                list($province_id, $city_id, $district_id) = array_pad($params['ids'], 3, 0);
                //  省级单位名称
                if (!empty($province_id)) {
                    $return[] = $cache[$province_id]['region_name'] ?: '';

                    //  市级单位名称
                    if (!empty($city_id)) {
                        $children = $cache[$province_id]['children'];
                        $return[] = $children[$city_id]['region_name'] ?: '';

                        //  县区级单位名称
                        if (!empty($district_id)) {
                            $grandson = $children[$city_id]['children'] ?: '';
                            if ($grandson) {
                                $return[] = $grandson[$city_id];
                            }
                        }
                    }

                    return $return;
                } else {
                    return false;
                }
            }
        }

        return [];
    }

    /**
     * 设置服务商列表缓存
     */
    public static function setServicerCache()
    {
        $cache_file_name = self::getCacheFileName('servicer_list');
        $cache = '';

        $service_user = Users::find()->select([
                'user_id', 'user_name', 'nickname',
                'servicer_super_id', 'servicer_user_id', 'servicer_info_id',
                'province', 'city', 'mobile_phone'
            ])->where([
                '!=', 'servicer_info_id', 0
            ])->asArray()
            ->all();
        if ($service_user) {
            $cache = json_encode($service_user);
        }

        file_put_contents($cache_file_name, $cache);
    }

    /**
     * 获取服务商列表缓存
     * @return mixed
     */
    public static function getServicerCache()
    {
        return self::getCache('servicer_list');
    }

    /**
     * 设置商品分类树缓存
     * @return mixed|string
     */
    public static function setGoodsCategoryCache()
    {
        $cache_file_name = self::getCacheFileName('goods_category');
        $cache = '';

        $goods_cat_tree_root = Yii::$app->params['goods_cat_tree_root'];
        $goods_category_list = self::getCatTree($goods_cat_tree_root);

        if ($goods_category_list) {
            $cache = json_encode($goods_category_list);
        }

        file_put_contents($cache_file_name, $cache);
    }

    /**
     * 获取商品分类树缓存
     * @param string $param
     * @return mixed
     */
    public static function getGoodsCategoryCache($param = '')
    {
        $cache = self::getCache('goods_category');

        if ($param == 'cat_map') {
            $cat_map = [];
            foreach ($cache as $item) {
                $cat_map[$item['cat_id']] = $item['cat_name'];
                if (!empty($item['children'])) {
                    foreach ($item['children'] as $child) {
                        $cat_map[$child['cat_id']] = $item['cat_name'].'—>'.$child['cat_name'];
                    }
                }
            }
            return $cat_map;
        }

        return $cache;
    }

    /**
     * 设置 品牌与分类的映射关系 的缓存
     *
     * 使用时要用usort 按品牌的排序值做排序，category表与brand表的关联方式不唯一
     */
    public static function setHotBrandCatCache() {
        $cache_file_name = self::getCacheFileName('hot_brand_cat_map');
        $cache = [];

        //  品牌-区域映射
        $area_cat_tree_root = Yii::$app->params['area_cat_tree_root'];
        $brandAreaMap = Category::find()->joinWith('children son')
            ->select(['o_category.cat_id', 'o_category.cat_name', 'o_category.sort_order', 'son.cat_desc'])
            ->where([
                'o_category.parent_id' => $area_cat_tree_root,
                'o_category.is_show' => Category::IS_SHOW,
                'son.is_show' => Category::IS_SHOW,
            ])->orderBy([
                'o_category.sort_order' => SORT_DESC
            ])->asArray()
            ->all();
        foreach ($brandAreaMap as $cat) {
            $cache['area'][$cat['cat_id']]['cat_id'] = $cat['cat_id'];
            $cache['area'][$cat['cat_id']]['cat_name'] = $cat['cat_name'];
            if (!empty($cat['children'])) {
                $brand_list = array_column($cat['children'], 'cat_desc');
            }
            $cache['area'][$cat['cat_id']]['brand_id_list'] = array_unique($brand_list);
            $cache['area'][$cat['cat_id']]['sort_order'] = $cat['sort_order'];
        }

        usort($cache['area'], function($a, $b){
            if ($a['sort_order'] == $b['sort_order']) {
                return 0;
            } else {
                return $a['sort_order'] > $b['sort_order'] ? -1 : 1;
            }
        });

        //  品牌-商品分类映射
        $goods_cat_tree_root = Yii::$app->params['goods_cat_tree_root'];
        $cache['cat'] = Category::find()->select(['cat_id', 'cat_name', 'brand_list'])
            ->where(['parent_id' => $goods_cat_tree_root])
            ->orderBy(['sort_order' => SORT_DESC])
            ->asArray()
            ->all();
        foreach ($cache['cat'] as &$item) {
            $item['brand_id_list'] = explode(',', $item['brand_list']);
        }

        //  热门品牌    配置在商品分类的 【品牌分类(101)】 的热门品牌    $area_cat_tree_root
        $hot_brand = Category::find()->select(['cat_id', 'brand_list'])
            ->where(['cat_id' => $area_cat_tree_root])
            ->asArray()
            ->one();
        $cache['hot'] = [
            'cat_id' => $hot_brand['cat_id'],
            'cat_name' => '热门品牌',
            'brand_id_list' => explode(',', $hot_brand['brand_list']),
        ];

        file_put_contents($cache_file_name, json_encode($cache));
    }

    /**
     * 获取品牌和 商品分类、区分的映射关系
     *
     * @param string $param ['area', 'cat', 'hot', ''] 分别对应 品牌-区域映射、品牌-商品分类映射、热门品牌、合集
     * @return mixed
     */
    public static function getHotBrandCatCache($param = '') {
        $cache = self::getCache('hot_brand_cat_map');

        switch ($param) {
            case 'area':
                $return = $cache['area'];
                break;
            case 'cat':
                $return = $cache['cat'];
                break;
            case 'hot':
                $return = $cache['hot'];
                break;
            default :
                $return = $cache;
                break;
        }

        return $return;
    }

    /**
     * 设置 品牌与分类的映射关系 的缓存
     *
     * 使用时要用usort 按品牌的排序值做排序，category表与brand表的关联方式不唯一
     */
    public static function setBrandCatCache() {
        $cache_file_name = self::getCacheFileName('all_brand_cat_map');
        $cache = [];

        //  品牌-区域映射
        $area_cat_tree_root = Yii::$app->params['area_cat_tree_root'];
        $brandAreaMap = Category::find()->joinWith('children son')
            ->select(['o_category.cat_id', 'o_category.cat_name', 'o_category.sort_order', 'son.cat_desc'])
            ->where([
                'o_category.parent_id' => $area_cat_tree_root,
                'o_category.is_show' => Category::IS_SHOW,
                'son.is_show' => Category::IS_SHOW,
            ])->orderBy([
                'o_category.sort_order' => SORT_DESC
            ])->asArray()
            ->all();
        foreach ($brandAreaMap as $cat) {
            $cache['area'][$cat['cat_id']]['cat_id'] = $cat['cat_id'];
            $cache['area'][$cat['cat_id']]['cat_name'] = $cat['cat_name'];
            if (!empty($cat['children'])) {
                $brand_list = array_column($cat['children'], 'cat_desc');
            }
            $cache['area'][$cat['cat_id']]['brand_id_list'] = array_unique($brand_list);
            $cache['area'][$cat['cat_id']]['sort_order'] = $cat['sort_order'];
        }

        usort($cache['area'], function($a, $b){
            if ($a['sort_order'] == $b['sort_order']) {
                return 0;
            } else {
                return $a['sort_order'] > $b['sort_order'] ? -1 : 1;
            }
        });

        //  品牌-商品分类映射
        $goods_cat_tree_root = Yii::$app->params['goods_cat_tree_root'];
        $cache['cat'] = Category::find()->select(['cat_id', 'cat_name', 'brand_list'])
            ->where(['parent_id' => $goods_cat_tree_root])
            ->orderBy(['sort_order' => SORT_DESC])
            ->asArray()
            ->all();
        foreach ($cache['cat'] as &$item) {
            $item['brand_id_list'] = explode(',', $item['brand_list']);
        }

        //  热门品牌    配置在商品分类的 【品牌分类(101)】 的热门品牌    $area_cat_tree_root
        $hot_brand = Category::find()->select(['cat_id', 'brand_list'])
            ->where(['cat_id' => $area_cat_tree_root])
            ->asArray()
            ->one();
        $cache['hot'] = [
            'cat_id' => $hot_brand['cat_id'],
            'cat_name' => '热门品牌',
            'brand_id_list' => explode(',', $hot_brand['brand_list']),
        ];

        file_put_contents($cache_file_name, json_encode($cache));
    }

    /**
     * 获取品牌和 商品分类、区分的映射关系
     *
     * @param string $param ['area', 'cat', 'hot', ''] 分别对应 品牌-区域映射、品牌-商品分类映射、热门品牌、合集
     * @return mixed
     */
    public static function getBrandCatCache($param = '') {
        $cache = self::getCache('all_brand_cat_map');

        switch ($param) {
            case 'area':
                $return = $cache['area'];
                break;
            case 'cat':
                $return = $cache['cat'];
                break;
            case 'hot':
                $return = $cache['hot'];
                break;
            default :
                $return = $cache;
                break;
        }

        return $return;
    }

    /**
     * 获取cache名称对应的cache路径
     * @param $cache_name
     * @return bool|string
     */
    public static function getCacheFileName($cache_name)
    {
        $params = Yii::$app->params;
        if (!empty($params['cache_file_name'][$cache_name])) {
            return $params['caches_base_dir'].$params['cache_file_name'][$cache_name];
        } else {
            return false;
        }
    }

    /**
     * 获取指定cache名称中的缓存内容
     *
     * 二维数组json_encode后 直接解码会被当做object，追加第二个参数true
     *
     * @param $cache_name
     * @return mixed
     */
    public static function getCache($cache_name)
    {
        $cache_file_name = self::getCacheFileName($cache_name);
        if ($cache_file_name) {
            $rs = file_get_contents($cache_file_name);
            if ($rs) {
                return json_decode($rs, true);
            }
        }

        return [];
    }

    public static function buildTree($category, $cat_id) {
        if ($category['cat_id'] == $cat_id) {
            return $category['children'];
        }

        if (!empty($category['children'])) {
            foreach ($category['children'] as $child) {
                return self::buildTree($child, $cat_id);
            }
        }
    }

    public static function getCatTree($root_id)
    {
        $categories = self::getCategoryCache();

        foreach ($categories as $category) {
            $cats = self::buildTree($category, $root_id);
        }

        return $cats;


//        $first_floor = Category::find()->select([
//            'cat_id', 'cat_name', 'cat_desc', 'sort_order', 'parent_id', 'is_show', 'album_id'
//        ])->where([
//            'parent_id' => $root_id
//        ])->orderBy([
//            'sort_order' => SORT_DESC
//        ])->indexBy('cat_id')
//        ->asArray()
//        ->all();
//
//        if ($first_floor) {
//            $parent_id_list = array_column($first_floor, 'cat_id');
//            if ($parent_id_list) {
//                $two_floor = Category::find()->select([
//                    'cat_id', 'cat_name', 'cat_desc', 'sort_order', 'parent_id', 'is_show', 'album_id'
//                ])->where([
//                    'parent_id' => $parent_id_list
//                ])->orderBy([
//                    'sort_order' => SORT_DESC
//                ])->asArray()
//                ->all();
//
//                if ($two_floor) {
//                    foreach ($two_floor as $cat) {
//                        $first_floor[$cat['parent_id']]['children'][] = $cat;
//                    }
//                }
//            }
//        }
//
//        return $first_floor;
    }


    /**
     * 取出所有树中的region_id
     * @param $result
     * @param $node
     */
    static public function getRegionIdArrayFromTree(&$result, $node, $level = 0) {
        $regionId = $node['region_id'];
        $regionName = '';
        for ($i = 0; $i < $level; ++$i) {
            $regionName .= '---|';
        }
        $regionName .= $node['region_name'];
        $result[$regionId] = $regionName;
        if (!empty($node['children'])) {
            ++$level;
            foreach ($node['children'] as $child) {
                self::getRegionIdArrayFromTree($result, $child, $level);
            }
        }
    }

    //根据 区域代码 获取 城市
     public static function getCityArrayFromTree(&$result, $nodeList, $regionList, $level=0) {

         foreach($nodeList as $node)
         {
             self::getCityIdArrayFromTree($result, $node, $regionList, $level);
         }

    }
    /*
         实例代码
        //拿到所有的区域
        $provinces = CacheHelper::getRegionCache([
            'type' => 'tree',
            'ids' => [],
            'deepth' => 0
        ]);
        //拿到服务商的区域
        $regionList = UserRegion::find()->select(['region_id'])->where(['user_id' => Yii::$app->user->identity['user_id']])->asArray()->all();

        $regionArray=[];

        foreach($regionList as $v)
        {
            $regionArray[]=$v['region_id'];
        }
        $res = [];
        CacheHelper::getCityArrayFromTree( $res, $provinces,$regionArray);
     * */

     public static function getCityIdArrayFromTree(&$result, $node, $regionList, $level = 0) {

        if(in_array($node['region_id'], $regionList)) {
            
            if($level == 0){
                foreach ($node['children'] as $child) {
                    $regionId   = $child['region_id'];
                    $regionName = $child['region_name'];
                    $result[$regionId] = $regionName;
                }
            }
            else {
                $regionId   = $node['region_id'];
                $regionName = $node['region_name'];
                $result[$regionId] = $regionName;
            }
        }

        if (!empty($node['children'])) {
            ++$level;
            if($level < 2)
            {
                foreach ($node['children'] as $child) {
                    self::getCityIdArrayFromTree($result, $child, $regionList, $level);
                }
            }
        }
    }

    /**
     * 缓存分类到 category_cache
     */
    public static function setCategoryCache()
    {
        $cache_file_name = self::getCacheFileName('category_cache');

        $categoryList = Category::find()
            ->asArray()
            ->indexBy('cat_id')
            ->orderBy(['sort_order' => SORT_DESC])->all();

        //统计SKU总数
        $query = new Query();
        $categoryGoodsCount = $query->select([
            'cat_id',
            'count(*) AS goodsCount'
        ])->from(Goods::tableName())->where([
            'is_on_sale' => 1,
            'is_delete' => 0,
        ])->indexBy('cat_id')->groupBy('cat_id')->all();

        foreach ($categoryList as $key => $category) {
            if (isset($categoryGoodsCount[$category['cat_id']])) {
                $categoryList[$key]['goods_count'] = $categoryGoodsCount[$category['cat_id']]['goodsCount'];
            }
            else {
                $categoryList[$key]['goods_count'] = 0;
            }
        }

        //统计直发SKU总数
        $zhifaCategoryGoodsCount = Goods::find()->select([
            Goods::tableName().'.goods_id',
            'cat_id',
            'count(*) AS goodsCount',
        ])->joinWith('goodsTag')->where([
            'is_on_sale' => 1,
            'is_delete' => 0,
        ])->andWhere([
            'tag_id' => 2,
        ])->indexBy('cat_id')->groupBy('cat_id')->asArray()->all();

        foreach ($categoryList as $key => $category) {
            if (isset($zhifaCategoryGoodsCount[$category['cat_id']])) {
                $categoryList[$key]['zhifa_goods_count'] = $zhifaCategoryGoodsCount[$category['cat_id']]['goodsCount'];
            }
            else {
                $categoryList[$key]['zhifa_goods_count'] = 0;
            }
        }

        $data = self::listTree($categoryList, $pid ='parent_id' , $child ='children' );

        self::updateCategoryGoodsCount($data);

        foreach ($data as &$categoryList) {
            self::updateDeep($categoryList);
        }

        file_put_contents($cache_file_name,json_encode($data));
    }

    /**
     * 更新深度
     * @param $node
     * @param int $deep
     */
    private static function updateDeep(&$node, $deep = 0) {
        $node['deep'] = $deep;
        if (!empty($node['children'])) {
            foreach ($node['children'] as &$child) {
                self::updateDeep($child, $deep + 1);
            }
        }
}

    /**
     * 获取分类缓存
     * @return mixed
     */
    public static function getCategoryCache()
    {
        return self::getCache('category_cache');
    }

    /**
     *  根据传入的类型id 返回改类型下的所有子节点
     * @param $catId
     * @param array $category
     * @return mixed
     */
    public static function getCategoryChildren($catId, $category = [] ,&$result)
    {
        foreach($category as $cate)
        {
            if($cate['cat_id'] == $catId)
            {
                return $result[] = $cate;
            }
            elseif (!empty($cate['children']))
            {
                self::getCategoryChildren($catId, $cate['children'], $result);
            }
        }
    }

    public static function getTopGoodsCategoryMap() {
        $categoryList = self::getTopGoodsCategory();
        $result = [];
        foreach ($categoryList as $category) {
            $result[$category['cat_id']] = $category['cat_name'];
        }
        return $result;
    }

    public static function getTopGoodsCategory() {
        static $result = [];
        if (empty($result)) {
            $goodsCategory = self::getGoodsCategoryCache();
            foreach ($goodsCategory as $category) {
                $result[] = [
                    'cat_id' => $category['cat_id'],
                    'cat_name' => $category['cat_name'],
                    'goods_count' => $category['goods_count'],
                    'zhifa_goods_count' => $category['zhifa_goods_count'],
                ];
            }
        }
        return $result;
    }

    /**
     * 获取所有1级分类和对应的最底层分类
     */
    public static function getAllCategoryLeaves() {
        static $all = [];
        if (empty($all)) {
            $goodsCategory = self::getGoodsCategoryCache();
            foreach ($goodsCategory as $category) {
                $result = [];
                self::getCategoryLeavesByCatId($category['cat_id'], $result);
                $all[] = [
                    'cat_id' => $category['cat_id'],
                    'cat_name' => $category['cat_name'],
                    'goods_count' => $category['goods_count'],
                    'zhifa_goods_count' => $category['zhifa_goods_count'],
                    'leaves' => $result,
                ];
            }
        }
        return $all;
    }


    /**
     * 获取一级分类，二级分类和叶子节点
     * @return mixed
     */
    public static function getAllCategoryLeavesWithLevel2($withSelf = true) {
        static $goodsCategory = [];

        if (empty($goodsCategory)) {
            $goodsCategory = self::getGoodsCategoryCache();
            //一级分类
            foreach ($goodsCategory as $i => $category) {
                if ($category['is_show'] && !empty($category['children'])) {
                    //二级分类
                    foreach ($category['children'] as $j => $subCategory) {
                        if (empty($subCategory['is_show'])) {
                            unset($goodsCategory[$i]['children'][$j]);
                            continue;
                        }
                        //叶子节点
                        $leaves = [];
                        if ($withSelf || !empty($subCategory['children'])) {
                            self::getCategoryLeavesByCatId($subCategory['cat_id'], $leaves);
                        }
                        $goodsCategory[$i]['children'][$j]['children'] = $leaves;
                    }
                }
            }
        }

        return $goodsCategory;
    }

    /**
     * 传入一个节点id 和整棵树 返回这个节点下的所有叶子节点的数组
     * @param $catId
     * @param $result
     * @param $withSelf 是否把自己放到叶子节点的数组中
     */
    public static function getCategoryLeavesByCatId($catId, &$result, $withSelf = true) {
        $category = self::getGoodsCategoryCache();
        self::getCategoryLeaves($catId, $category, $result);
    }

    /**
     * 传入一个节点id 和整棵树 返回这个节点下的所有叶子节点的数组
     * @param $catId
     * @param array $category
     * @param $result
     */
    public static function getCategoryLeaves ($catId, $category = [], &$result)
    {
        foreach($category as $cate) {
            //如果 cat_id == $catId
            if($cate['cat_id'] == $catId || $cate['parent_id'] == $catId)
            {   //是否有子集
                if(!empty($cate['children'])) {
                    //有子集 递归
                    self::getCategoryLeaves($cate['cat_id'], $cate['children'], $result);
                } else {
                    if ($cate['is_show']) {
                        $result[] = $cate;
                    }
                }
            }
            elseif (!empty($cate['children']))
            {
                self::getCategoryLeaves($catId, $cate['children'], $result);
            }
        }
    }

    /**
     * 传入一个含有parentId的数组 返回一棵树
     * @param $list
     * @param string $pid
     * @param string $child 子节点
     * @param int $root 根节点
     * @return array
     */
    private  static function listTree($list, $pid = 'parent_id', $child = 'children' , $root = 0  ) {
        // 创建Tree
        $tree = array();
        //如果是数组
        if(is_array($list)) {
            //遍历开始
            foreach ($list as $key => $data) {
                //拿到parentId
                $parentId =  $data[$pid];
                //根节点 ==  parentId
                if ($root == $parentId) {
                    $tree[] = & $list[$key];
                }else{
                    if (isset($list[$parentId])) {
                        $parent = &$list[$parentId];
                        $parent[$child][] = & $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    public static function updateCategoryGoodsCount(&$catList) {
        foreach ($catList as &$cat) {
            self::recursionCategoryGoodsCount($cat);
            self::recursionZhifaCategoryGoodsCount($cat);
        }
    }

    public static function recursionCategoryGoodsCount(&$category) {
        if (!empty($category['children'])) {
            $childGoodsCount = 0;
            foreach ($category['children'] as &$child) {
                $childGoodsCount += self::recursionCategoryGoodsCount($child);
            }
            $category['goods_count'] += $childGoodsCount;
            return $category['goods_count'];
        }
        else {
            return $category['goods_count'];
        }
    }

    public static function recursionZhifaCategoryGoodsCount(&$category) {
        if (!empty($category['children'])) {
            $childGoodsCount = 0;
            foreach ($category['children'] as &$child) {
                $childGoodsCount += self::recursionZhifaCategoryGoodsCount($child);
            }
            $category['zhifa_goods_count'] += $childGoodsCount;
            return $category['zhifa_goods_count'];
        }
        else {
            return $category['zhifa_goods_count'];
        }
    }

    /**
     * 传入一个子节点 返回这个节点下的所有子节点id
     *
     * @param $ids
     * @param $nodes
     */
    public static function getNodeIds(&$ids, $nodes)
    {
        if(!empty($nodes['children']))
        {
            foreach($nodes['children'] as $node)
            {
                if(!empty($node['children']))
                {
                    $ids[] = $node['cat_id'];
                    self::getNodeIds($ids, $node['children']);
                } else {
                    $ids[] = $node['cat_id'];
                }
            }
        } else {
            foreach( $nodes as $node) {
                $ids[] = $node['cat_id'];
            }
        }
    }

    /**
     * 传入一个子树  返回 id 和 name
     * @param $data
     * @param $result
     */
    public static function getCategoryMap($data, &$result)
    {
        foreach($data['children'] as $value)
        {
            if(!empty($value['children'])) {
                self::getCategoryMap($value, $result);
            } else {
                $result[] = [
                    'cat_id' => $value['cat_id'],
                    'cat_name' => $value['cat_name'],
                ];
            }
        }
    }

    public static function getCategoryChildrenIds(&$ids, $id, $category, $shouldAdd = false)
    {
        foreach($category as $cate) {
//            if ($cate['cat_id'] == $id) {
//                $shouldAdd = true;
//            }
//
//            if (!empty($cate['children'])) {
//                self::getCategoryChildrenIds($ids, $id, $cate['children'], $shouldAdd);
//            }
//
//            if ($shouldAdd) {
//                $ids[] = $cate['cat_id'];
//            }

            if($cate['cat_id'] == $id || $cate['parent_id'] == $id) {
                $ids[] = $cate['cat_id'];
                if(!empty($cate['children'])) {
                    self::getCategoryChildrenIds($ids, $cate['cat_id'], $cate['children']);
                }
            } else {
                if(!empty($cate['children'])) {
                    self::getCategoryChildrenIds($ids, $id, $cate['children']);
                }
            }
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2016/10/29
 * Time: 14:13
 */

namespace backend\models;

use common\helper\CacheHelper;

class Region extends \common\models\Region
{
    public static function getRegionData($userId) {
        $provinces = CacheHelper::getRegionCache([
            'type' => 'tree',
            'ids' => [],
            'deepth' => 0
        ]);

        $regionData = [];
        foreach ($provinces as $province) {
            CacheHelper::getRegionIdArrayFromTree($regionData, $province);
        }

        $userRegions = UserRegion::find()->where([
            'not',
            [
                'user_id' => $userId,
            ]
        ])->asArray()->all();

        $userRegions = array_column($userRegions, 'region_id');

        foreach ($regionData as $regionId => $regionName) {
            //已经被分配给其他服务商了
            if (in_array($regionId, $userRegions)) {
                $regionData[$regionId] = $regionName. '(已分配给其他用户了)';
            }
        }

        return $regionData;
    }
}
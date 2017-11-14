<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/27 0027
 * Time: 11:08
 */
use kartik\dynagrid\DynaGrid;

$province_has_server = Yii::$app->params['province_has_server'];
$city_has_server     = Yii::$app->params['city_has_server'];
$cityMap             = [];
foreach ($provinceMap as $provinceId => $provinceName) {
    $cities = \common\models\Region::getCityMap($provinceId);
    foreach ($cities as $cityId => $cityName) {
        $cityMap[$cityId] = '(' . $provinceName . ')' . $cityName;
        $districts        = \common\models\Region::getCityMap($cityId);
        foreach ($districts as $districtID => $districtName) {
            $districtMap[$districtID] = '(' . $provinceName . '-' . $cityName . ')' . $districtName;
        }
    }
}

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    [

        'attribute' => 'address_id',
        'options' => [
            'style' => 'width: 90px'
        ],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'label' => '省',
        'attribute' => 'province',
        'format' => 'raw',
        'value' => function ($model) use ($provinceMap) {
            return isset($provinceMap[$model->province]) ? $provinceMap[$model->province] : '未设置';
        },
        'editableOptions' => function ($model, $key, $index) use ($provinceMap) {
            return [
                'header' => '选择省份',
                'size' => 'md',
                //TODO 此处项需要一起配置，带有搜索下拉框选择
                'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                'widgetClass' => 'kartik\editable\Select2',
                'options' => [
                    'data' => $provinceMap,
                ],
                'formOptions' => [
                    'method' => 'post',
                    'action' => \yii\helpers\Url::to('/sc-user/editAddressProvinceCityDistrict'),
                ],
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'label' => '市',
        'attribute' => 'city',
        'value' => function ($model) {
            return \common\models\Region::getRegionName($model->city);
        },
        'editableOptions' => function ($model, $key, $index) use ($cityMap) {
            return [
                'header' => '选择市级',
                'size' => 'md',
                'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                'widgetClass' => 'kartik\editable\Select2',
                'options' => [
                    'data' => $cityMap,
                ],
                'formOptions' => [
                    'method' => 'post',
                    'action' => \yii\helpers\Url::to('/sc-user/editAddressProvinceCityDistrict'),
                ],
            ];
        },

    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'label' => '区/县',
        'attribute' => 'district',
        'value' => function ($model) {
            return \common\models\Region::getRegionName($model->district);
        },
        'editableOptions' => function ($model, $key, $index) use ($districtMap) {
            return [
                'header' => '选择区县',
                'size' => 'md',
                'inputType' => \kartik\editable\Editable::INPUT_SELECT2,
                'widgetClass' => 'kartik\editable\Select2',
                'options' => [
                    'data' => $districtMap,
                ],
                'formOptions' => [
                    'method' => 'post',
                    'action' => \yii\helpers\Url::to('/sc-user/editAddressProvinceCityDistrict'),
                ],
            ];
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'label' => '详细地址',
        'attribute' => 'address',
        'editableOptions' => function ($model, $key, $index) {
            return [
                'header' => '修改详细地址',
                'size' => 'md',
                'formOptions' => [
                    'action' => ['sc-user/editAddressText'],
                ],
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'label' => '收件人',
        'attribute' => 'consignee',
        'editableOptions' => function ($model, $key, $index) {
            return [
                'header' => '修改收件人',
                'size' => 'sm',
                'formOptions' => [
                    'action' => ['sc-user/editAddressText'],
                ],
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'label' => '收件人手机号码',
        'attribute' => 'mobile',
        'editableOptions' => function ($model, $key, $index) {
            return [
                'header' => '修改收件人号码',
                'size' => 'sm',
                'formOptions' => [
                    'action' => ['sc-user/editAddressText'],
                ],
            ];
        },
    ],
    [
//        'class' => 'kartik\grid\EditableColumn',
//        'options' => [
//            'style' => 'width: 220px'
//        ],
        'label' => '是否默认',
        'attribute' => 'is_default',
        'value' => function ($model) {
            return \common\models\UserAddress::$defaultMap[$model->is_default];
        },
//        'editableOptions' => function ($model, $key, $index) {
//            return [
//                'header' => '修改默认地址',
//                'size' => 'sm',
//                'data' => \common\models\UserAddress::$defaultMap,
//                'formOptions' => [
//                    'action' => ['sc-user/editAddressIsDefault'],
//                ],
//                'inputType' => \kartik\editable\Editable::INPUT_SWITCH,
//            ];
//        },
//        TODO 修改默认地址后，需要刷新页面，否则会存在多个默认地址，但实际上数据库表中只有一个默认收货地址
//        'refreshGrid' => true,
    ],
];

echo DynaGrid::widget([
    'columns' => $gridColumns,
    'theme' => 'panel-primary',
    'gridOptions' => [
        'dataProvider' => $model,
        'showPageSummary' => false,
        'panel' => [
            'heading' => '  收货地址',
        ],
    ],
    'options' => ['id' => 'dynagrid-1'],
]);

?>


<?php
/**
 * Created by PhpStorm.
 * User: HongXunPan
 * Date: 2017/8/25
 * Time: 10:00
 */

namespace data\controllers;

use common\models\Brand;
use common\models\Category;
use common\models\Goods;
use Yii;
use common\models\Users;

class DataCommonController extends DataBaseAuthController
{

    public $enableCsrfValidation = false;

    public function actionGetUserSuggests() {
        if(Yii::$app->request->isOptions) {
            return ['code' => 0];
        }
        $phone = Yii::$app->request->post('phone');
        if (!empty($phone)) {
            $userList = Users::find()->select([
                'user_id',
                'mobile_phone',
            ])->where([
                'like', 'mobile_phone', $phone
            ])->asArray()->all();
            $phoneList = array_column($userList, 'mobile_phone');
            return $this->throwError([
                'code' => 0,
                'msg' => '获取成功',
                'data' => $phoneList
            ]);
        } else {
            return $this->throwError(['code' => -1]);
        }
    }

    public function actionGetTypeSuggests() {
        if(Yii::$app->request->isOptions) {
            return ['code' => 0];
        }
        $key = Yii::$app->request->post('key');
        $value = Yii::$app->request->post('value');

        if (empty($key)) {
            return $this->throwError(['code' => 1, 'param' => 'key']);
        }
        if (empty($value)) {
            return $this->throwError(['code' => 1, 'param' => 'value']);
        }

        if (!in_array($key, ['brand_id', 'cat_id', 'goods_id'])) {
            return $this->throwError(['code' => 2, 'param' => 'key']);
        }
        $result = [];
        switch ($key) {
            case 'brand_id':
                $brand = Brand::find()->where([
                    'like', 'brand_name' , $value
                ])->asArray()->all();
                $result = array_column($brand, 'brand_name');
                break;
            case 'cat_id':
                $category = Category::find()->where([
                    'like', 'cat_name' , $value
                ])->asArray()->all();
                $result = array_column($category, 'cat_name');
                break;
            case 'goods_id' :
                $goods = Goods::find()->where([
                    'like', 'goods_name' , $value
                ])->asArray()->all();
                $result = array_column($goods, 'goods_name');
                break;
        }
        if (empty($result)) {
            return $this->throwError(['code' => -10]);
        } else {
            return $this->throwError(['code' => 0, 'data' => $result]);
        }
    }

    /**
     * 抛出错误
     */
    private function throwError($data)
    {
        $dataMap = array(
            '1' => '参数缺失',
            '2' => '参数错误',
            '0' => '请求成功',
            '-1' => '执行错误',
            '-10' => '无结果'
        );
        if (!isset($data['msg'])) {
            $data['msg'] = $dataMap[$data['code']];
        }
        return $data;
    }

}
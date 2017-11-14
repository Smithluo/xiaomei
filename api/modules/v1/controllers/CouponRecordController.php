<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/10 0010
 * Time: 10:09
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\CouponRecord;
use common\helper\DateTimeHelper;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;
use Yii;

/**
 * Class CartController
 *
 * cart/list 用户进入购物车时 修正商品的选中态(不可购买商品不可选中)、按箱购买商品修正数量、参与活动的状态
 * cart/num 修改商品数量要符合库存量和是否按箱购买的规则
 * 所有接口都需要计算已勾选商品参与活动的达成状态
 *
 * @package api\modules\v1\controllers
 */
class CouponRecordController extends BaseAuthActiveController
{
    public $modelClass = 'api\modules\v1\models\CouponRecord';

    /**
     * 购物车列表
     *
     * 修正商品的数量 和选中态，返回商品添加 不可购买的flag    考虑吧 OrderController 的 actionCheckout  代码复用
     * 当前只有ios在用，ios升级时要 调用 EventHelper::getValidEventList
     *
     * @return array
     */
    public function actionList() {
        $userModel = \Yii::$app->user->identity;
        $data = Yii::$app->request->post('data');

        if (empty($data['status'])) {
            $status = 0;
        }
        else {
            $status = intval($data['status'], 0);
        }

        $now = DateTimeHelper::getFormatCNDateTime(time());

        $query = CouponRecord::find()->joinWith([
            'event event',
            'fullCutRule fullCutRule',
        ])->where([
            CouponRecord::tableName().'.user_id' => $userModel['user_id'],
        ])->andWhere([
            CouponRecord::tableName().'.status' => $status,
        ]);

        if ($status == CouponRecord::COUPON_STATUS_UNUSED) {
            $query->andWhere([
                '<=',
                CouponRecord::tableName().'.start_time',
                $now,
            ])->andWhere([
                '>=',
                CouponRecord::tableName().'.end_time',
                $now,
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination = false;

        return $dataProvider;
    }

}
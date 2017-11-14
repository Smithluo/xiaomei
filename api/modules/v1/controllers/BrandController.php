<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/11 0011
 * Time: 9:26
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\Brand;
use api\modules\v1\models\Users;
use common\helper\DateTimeHelper;
use common\helper\ImageHelper;
use common\helper\TextHelper;
use common\models\GiftPkg;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class BrandController extends BaseActiveController
{
    public $modelClass = 'api\modules\v1\models\Brand';

    public function actionDetail() {
        $brandId = Yii::$app->request->post('brand_id');
        if (empty($brandId)) {
            Yii::error('缺少参数', __METHOD__);
            throw new BadRequestHttpException('缺少参数', 1);
        }

        $brand = Brand::find()->joinWith('touchBrand')->where([
            'is_show' => 1,
            Brand::tableName().'.brand_id' => $brandId,
        ])->asArray()->one();

        if (empty($brand)) {
            Yii::error('品牌不存在', __METHOD__);
            throw new BadRequestHttpException('品牌不存在', 1);
        }

        return [
            'brand_id' => $brand['brand_id'],
            'brand_name' => $brand['brand_name'],
            'brand_desc' => $brand['brand_desc'],
            'brand_desc_long' => $brand['brand_desc_long'],
            'brand_logo' => ImageHelper::get_image_path($brand['brand_logo']),
            'brand_content' => TextHelper::formatRichText($brand['touchBrand']['brand_content']),
            'brand_banner' => ImageHelper::get_image_path($brand['touchBrand']['brand_banner']),
            'brand_qualification' => TextHelper::formatRichText($brand['touchBrand']['brand_qualification']),
            'license' => TextHelper::formatRichText($brand['touchBrand']['license']),
        ];
    }

    /**
     * 品牌详情页
     * @param $brand_id
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionView($brand_id)
    {
        if (!isset(Yii::$app->user->identity)) {
            $token = Yii::$app->request->getAuthUser();
            if (!empty($token)) {
                $userModel = Users::findIdentityByAccessToken($token);
                Yii::$app->user->login($userModel);
            }
        }
        $userId = Yii::$app->user->getId() ?: 0;

        $brand = Brand::find()->joinWith([
            'touchBrand',
            'event',
            'event.fullCutRule',
            'shipping',
        ])->where([
            'is_show' => 1,
            Brand::tableName() . '.brand_id' => $brand_id,
        ])->one();

        if (!$brand) {
            Yii::error('品牌不存在', __METHOD__);
            throw new BadRequestHttpException('品牌不存在', 1);
        }

        //营业执照
        $license = '';
        $preg='/<img .*?src="(.*?)".*?>/is';
        $content = htmlspecialchars_decode(stripslashes($brand['touchBrand']['license']));
        $result = preg_match_all($preg, $content, $match);
        if ($result) {
            $license = $match[1];
        }

        $brandInfo = [
            'brand_banner' => $brand->getBrandBanner() == '' ?: ImageHelper::get_image_path($brand->getBrandBanner()),
            'brand_id' => $brand->brand_id,
            'brand_name' => $brand->brand_name,
            'brand_logo' => ImageHelper::get_image_path($brand->brand_logo_two),
            'brand_desc' => $brand->brand_desc,
            'brand_detail' => $brand->getBrandDetail(),
            'brand_license' => $license,
        ];

        $shipping['shipping_name'] = $brand['shipping']['shipping_name'];
        $shipping['shipping_desc'] = $brand['shipping']['shipping_desc'];

        $brandDetail = $brand->getBrandDetail();
        // 品牌的商品参与的套餐列表 --start
        $giftPkgList = GiftPkg::find()->alias('giftPkg')->joinWith([
            'giftPkgGoods giftPkgGoods',
            'giftPkgGoods.goods goods',
        ])->where([
            'giftPkg.is_on_sale' => 1,
        ])->andWhere([
            'goods.brand_id' => $brand_id,
        ])->all();

        $giftItemList = [];
        $giftPkgStr = '';
        foreach ($giftPkgList as $giftPkg) {
            foreach ($giftPkg['giftPkgGoods'] as $goodsItem) {
                $goods = $goodsItem['goods'];
                if ($goods['brand_id'] == $brand_id) {
                    $giftItemList[] = [
                        'id' => $giftPkg['id'],
                        'name' => $giftPkg['name'],
                        'img' => $giftPkg->getUploadUrl('thumb_img'),
                        'price' => $giftPkg['price'],
                    ];
                    $giftPkgStr .= $giftPkg['name'] . ',';
                    break;
                }
            }
        }
        if (!empty($giftPkgStr)) {
            $giftPkgStr = substr($giftPkgStr, 0, -1);
        }

        //品牌参与的优惠券活动，供用户领取优惠券
        $ruleList = [];
        if (!empty($brand['event']) && !empty($brand['event']['fullCutRule'])) {
            $event = $brand['event'];
            foreach ($event['fullCutRule'] as $rule) {
                $takenCount = $rule->getCouponCountTaken($userId);
                $limitCount = $event['times_limit'];
                $ruleList[] = [
                    'brand_id' => $brand_id,
                    'event_id' => $brand['event_id'],
                    'event_name' => $event['event_name'],
                    'event_desc' => $event['event_desc'],
                    'sub_type' => $event['sub_type'],
                    'rule_id' => $rule['rule_id'],
                    'rule_name' => $rule['rule_name'],
                    'above' => intval($rule['above']),
                    'cut' => intval($rule['cut']),
                    'start_time' => DateTimeHelper::getFormatCNDate($event['start_time']),
                    'end_time' => DateTimeHelper::getFormatCNDate($event['end_time']),
                    'color' => $event['bgcolor'],
                    'rest_count' => $limitCount - $takenCount,
                ];
            }
        }

//        return [
//            'brand_info' => $brand,
//            'brand_detail' => $brandDetail,
//            'gift_pkg' => [
//                'title' => $giftPkgStr,
//                'gift_list' => $giftItemList,
//            ],
//            'coupon_list' => $ruleList,
//            'shipping' => $shipping,
//        ];
        return [
            'brand_info' => $brandInfo,
            'gift_pkg' => [
                'title' => $giftPkgStr,
                'gift_list' => $giftItemList,
            ],
            'coupon_list' => $ruleList,
        ];
    }
}
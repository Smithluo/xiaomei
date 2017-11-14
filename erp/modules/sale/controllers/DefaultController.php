<?php

namespace erp\modules\sale\controllers;

use common\models\OrderInfo;
use yii\web\Controller;

/**
 * Default controller for the `sale` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $orderInfoList = OrderInfo::find()->where([
            'owner'
        ])->all();

        return $this->render('index');
    }
}

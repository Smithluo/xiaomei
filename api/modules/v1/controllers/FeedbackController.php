<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/30 0030
 * Time: 16:00
 */

namespace api\modules\v1\controllers;

use api\modules\v1\models\Feedback;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

class FeedbackController extends BaseActiveController
{
    public $modelClass = 'modules\v1\models\Feedback';

    public function actionAdd() {
        $data = Yii::$app->request->post('data');

        if (empty($data['content'])) {
            throw new BadRequestHttpException('请提交内容', 1);
        }

        $feedback = new Feedback();
        $feedback->msg_content = $data['content'];

        if ($feedback->save()) {
            return [
                'message' => '提交成功',
            ];
        }
        throw new ServerErrorHttpException('提交失败', 2);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: clark
 * Date: 2017/8/30
 * Time: 10:19
 */

namespace common\helper;

use \Yii;
use yii\base\Exception;
use yii\helpers\VarDumper;

class SwiftMailerHelper
{
    /**
     * 发送邮件的通用方法
     */
    public static function sendMail($setTo, $subject, $content = '', $data = [])
    {
        $from = Yii::$app->components['mailer']['transport']['username'];
        Yii::warning('入参： $setTo = '.json_encode($setTo).', $subject = '.$subject.', $content = '.$content.
            ', $data = '.json_encode($data).'。 发件人：'.$from, __METHOD__);
        $mail = Yii::$app->mailer->compose(); //  设置模板


        $mail->setFrom('from@domain.com')
            ->setFrom($from)
            ->setTo($setTo)
//            ->setReplyTo()    //  接收回复邮件的邮箱
//            ->setCc()         //  设置抄送邮件组
            ->setHtmlBody($content)
            ->setSubject($subject);

        try {
            $mail->send();
        } catch (Exception $e) {
            Yii::warning('邮件发送失败'.VarDumper::dumpAsString($e));
        }

        Yii::warning('邮件发送成功');
    }
}
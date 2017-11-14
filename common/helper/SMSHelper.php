<?php
namespace common\helper;

class SMSHelper
{
    //  限制60s内只能发送1条短信验证码
    const MIN_SEND_CHECKNO_DURATION = 60;

    public function encrypt_value($orig) {
        return md5($orig);
    }

    public function generateCode($length = 6) {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }

    public function sendCheckSms($mobile_no, $checkNo = null)
    {
        $cur_time = time();
        $last_time = $_SESSION['check_time'];
        $intval = $cur_time - $last_time;
        if(empty($mobile_no) || $intval < self::MIN_SEND_CHECKNO_DURATION) {
            return 1;
        }
        if($checkNo === null) {
            $checkNo = $this->generateCode();
        }
        $_SESSION['mobile_no'] = $this->encrypt_value(''.$mobile_no);
        $_SESSION['check_no'] = $this->encrypt_value(''.$checkNo);
        $_SESSION['check_time'] = time();
        $res = self::sendSms($mobile_no, "【小美诚品】您的验证码是".$checkNo);
        return $res;
    }

    public static function sendSms($mobile_no, $content)
    {
        if (empty($mobile_no) || empty($content)) {
            return 3;
        }
        $un = "N9058011";
        $pw = "2701dfc4";
        $da = $mobile_no;
        $content_gbk = iconv("UTF-8", "GB2312//IGNORE", $content);
        $sm = bin2hex($content_gbk);
        $dc = 15;

        $url = "http://222.73.117.138:7891/mt?un=".$un."&pw=".$pw."&da=".$da."&sm=".$sm."&dc=".$dc."&rf=2";
        $res = file_get_contents($url);
        if(!$res) {
            return 2;
        }
        $result = json_decode($res);
        //发送成功
        if($result->success) {
            return 0;
        }
        else {
            return 1;
        }
    }

    public function checkMobileResult($mobile_no, $check_no) {
        $mobile_no = $this->encrypt_value(''.$mobile_no);
        $check_no = $this->encrypt_value(''.$check_no);
        if(strlen($check_no) == 0 || strlen($_SESSION['check_no']) == 0 || strlen($mobile_no) == 0) {
            return false;
        }
        return $_SESSION['check_no'] === ''.$check_no && $_SESSION['mobile_no'] === ''.$mobile_no;
    }
}
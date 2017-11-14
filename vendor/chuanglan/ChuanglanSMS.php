<?php
/**
 * 创蓝短信接口
 */
class ChuanglanSMS{
	const SENDURL='http://222.73.117.138:7891/mt';
	const QUERYURL='http://222.73.117.138:7891/bi';
	const ISENDURL='http://222.73.117.140:8044/mt';
	const IQUERYURL='http://222.73.117.140:8044/bi';

	private $_sendUrl='';				// 发送短信接口url
	private $_queryBalanceUrl='';	// 查询余额接口url

	private $_un;			// 账号
	private $_pw;			// 密码

	/**
	 * 构造方法
	 * @param string $account  接口账号
	 * @param string $password 接口密码
	 */
	public function __construct($account,$password){
		$this->_un=$account;
		$this->_pw=$password;
	}

	/* ========== 业务模块 ========== */
	/**
	 * 短信发送
	 * @param string $phone   	手机号码
	 * @param string $content 	短信内容
	 * @param integer $isreport	是否需要状态报告
	 * @return void
	 */
	public function send($phone,$content,$isreport=0){
		$requestData=array(
			'un'=>$this->_un,
			'pw'=>$this->_pw,
			'sm'=>$content,
			'da'=>$phone,
			'rd'=>$isreport,
			'dc'=>15,
			'rf'=>2,
			'tf'=>3,
		);
		
		$url=ChuanglanSMS::SENDURL.'?'.http_build_query($requestData);
		return $this->_request($url);
	}

	/**
	 * 国际短信发送
	 * @param string $phone   	手机号码
	 * @param string $content 	短信内容
	 * @param integer $isreport	是否需要状态报告
	 * @return void
	 */
	public function sendInternational($phone,$content,$isreport=0){
		$requestData=array(
			'un'=>$this->_un,
			'pw'=>$this->_pw,
			'sm'=>$content,
			'da'=>$phone,
			'rd'=>$isreport,
			'rf'=>2,
			'tf'=>3,
		);
		
		$url=ChuanglanSMS::ISENDURL.'?'.http_build_query($requestData);
		return $this->_request($url);
	}

	/**
	 * 查询余额
	 * @return String 余额返回
	 */
	public function queryBalance(){
		$requestData=array(
			'un'=>$this->_un,
			'pw'=>$this->_pw,
			'rf'=>2
		);

		$url=ChuanglanSMS::QUERYURL.'?'.http_build_query($requestData);
		return $this->_request($url);
	}

	/**
	 * 查询余额
	 * @return String 余额返回
	 */
	public function queryBalanceInternational(){
		$requestData=array(
			'un'=>$this->_un,
			'pw'=>$this->_pw,
			'rf'=>2
		);

		$url=ChuanglanSMS::IQUERYURL.'?'.http_build_query($requestData);
		return $this->_request($url);
	}

	/* ========== 业务模块 ========== */

	/* ========== 功能模块 ========== */
	/**
	 * 请求发送
	 * @return string 返回状态报告
	 */
	private function _request($url){
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_URL,$url);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	/* ========== 功能模块 ========== */
}
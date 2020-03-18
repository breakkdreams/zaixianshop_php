<?php
/**
 * @Copyright 2019-2019 ali All Rights Reserved.
 * @Author:yyy
 * @Version 1.0 2019/10/31
 */
namespace wechatPay;
class Wechatpay {
	private $appid;
	private $mchid;
	private $p_mch_id;
	private $p_appid;
	private $key;
	private $appsecret;
	private $parameters;
	private $result;
	private $data;
	private $nativeurl = "https://api.mch.weixin.qq.com/pay/unifiedorder";
	private $orderqueryurl = "https://api.mch.weixin.qq.com/pay/orderquery";
	private $orderrefund = "https://api.mch.weixin.qq.com/secapi/pay/refund";
	private $transfers = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
	private $red = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";
	private $groupred ="https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack";
	private $getpublickey = "https://fraud.mch.weixin.qq.com/risk/getpublickey";
	private $pay_bank = "https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank";
	private $refundqueryurl = "https://api.mch.weixin.qq.com/pay/refundquery";
	private $query_bank = "https://api.mch.weixin.qq.com/mmpaysptrans/query_bank";
	public $CERT_PEM = null;
	public $KEY_PEM = null;
	public $PUB_PEM = null;
	/**
	 * 对像实例
	 * 
	 * @param unknown $appid
	 *        	公众号appid
	 * @param unknown $mchid
	 *        	商家支付编号
	 * @param unknown $key
	 *        	商家支付密匙
	 * @param unknown $appsecret
	 *        	公众号appsecret
	 */
	function __construct($appid, $mchid, $key, $appsecret,$p_mch_id='',$p_appid="",$pPartnerKey="") {
		$this->appid = $appid;
		$this->mchid = $mchid;
		$this->key = $key;
		$this->appsecret = $appsecret;
		$this->p_mch_id = $p_mch_id;
		$this->p_appid = $p_appid;
		if(!empty($pPartnerKey))
		{
			$this->key = $pPartnerKey;
		}
	}
	function trimString($value) {
		$ret = null;
		if (null != $value) {
			$ret = $value;
			if (strlen ( $ret ) == 0) {
				$ret = null;
			}
		}
		return $ret;
	}
	/**
	 * 设置参数
	 * 
	 * @param unknown $parameter
	 *        	参数名称
	 * @param unknown $parameterValue
	 *        	参数值
	 */
	function setParameter($parameter, $parameterValue) {
		$this->parameters [$this->trimString ( $parameter )] = $this->trimString ( $parameterValue );
	}
	/**
	 * 产生随机字符
	 * 
	 * @param number $length        	
	 * @return string
	 */
	public function createNoncestr($length = 32) {
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str = "";
		for($i = 0; $i < $length; $i ++) {
			$str .= substr ( $chars, mt_rand ( 0, strlen ( $chars ) - 1 ), 1 );
		}
		return $str;
	}
	/**
	 * 获取支付结果
	 * 
	 * @return Ambigous <boolean, mixed>
	 */
	public function getnativeurl() {	
		$this->parameters ["appid"] = $this->appid; // 公众账号ID
		$this->parameters ["mch_id"] = $this->mchid; // 商户号
		$this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
		$this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
		$xml = $this->arrayToXml ( $this->parameters );
		$result = $this->postXmlCurl ( $xml, $this->nativeurl );
		$this->result = $this->xmlToArray ( $result );
		return $result;
	}

    /**
     * 获取支付服务商结果
     * @return bool|mixed
     */
	public function getnativeurls()
    {
        $this->parameters ["appid"] =$this->p_appid;
        $this->parameters ["mch_id"] = $this->p_mch_id;
        $this->parameters ["sub_appid"] = $this->appid; // 公众账号ID
        $this->parameters ["sub_mch_id"] = $this->mchid; // 商户号
        $this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
        $this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
        $xml = $this->arrayToXml ( $this->parameters );
        $result = $this->postXmlCurl ( $xml, $this->nativeurl );
        $this->result = $this->xmlToArray ( $result );
        return $result;
    }
	/**
	 * 订单查询
	 * 
	 * @throws SDKRuntimeException
	 * @return string
	 */
	public function orderquery() {
		try {
			// 检测必填参数
			
			$this->parameters ["appid"] = $this->appid; // 公众账号ID
			$this->parameters ["mch_id"] = $this->mchid; // 商户号
			$this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
			$this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
			$xml = $this->arrayToXml ( $this->parameters );
			$result = $this->postXmlCurl ( $xml, $this->orderqueryurl );
			$this->result = $this->xmlToArray ( $result );
			return $result;
		} catch ( SDKRuntimeException $e ) {
			die ( $e->errorMessage () );
		}
	}
	/**
	 * 服务商订单查询
	 * 
	 * @throws SDKRuntimeException
	 * @return string
	 */
	public function orderquerys() {
		try {
			// 检测必填参数
		
			$this->parameters ["appid"] =$this->p_appid;
        	$this->parameters ["mch_id"] = $this->p_mch_id;
        	$this->parameters ["sub_appid"] = $this->appid; // 公众账号ID
        	$this->parameters ["sub_mch_id"] = $this->mchid; // 商户号
        	$this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
			$this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
			$xml = $this->arrayToXml ( $this->parameters );
			$result = $this->postXmlCurl ( $xml, $this->orderqueryurl );
			$this->result = $this->xmlToArray ( $result );
			return $result;
		} catch ( SDKRuntimeException $e ) {
			die ( $e->errorMessage () );
		}
	}

    /**
     * 商家退款查询
     * @return bool|mixed
     */
	public function refundquery()
    {
        $this->parameters ["appid"] = $this->appid; // 公众账号ID
        $this->parameters ["mch_id"] = $this->mchid; // 商户号
        $this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
        $this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
        $xml = $this->arrayToXml ( $this->parameters );
        $result = $this->postXmlCurl ( $xml, $this->refundqueryurl);
        $this->result = $this->xmlToArray ( $result );
        return $result;
    }
    /**
     * 服务商退款查询
     * @return bool|mixed
     */
    public function refundquerys()
    {
        $this->parameters ["appid"] =$this->p_appid;
        $this->parameters ["mch_id"] = $this->p_mch_id;
        $this->parameters ["sub_appid"] = $this->appid; // 公众账号ID
        $this->parameters ["sub_mch_id"] = $this->mchid; // 商户号
        $this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
        $this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
        $xml = $this->arrayToXml ( $this->parameters );
        $result = $this->postXmlCurl ( $xml, $this->refundqueryurl);
        $this->result = $this->xmlToArray ( $result );
        return $result;
    }
	/**
	 * 企业付款
	 * @return Ambigous <boolean, mixed>
	 */
	public function enterprisePay()
	{
		$this->parameters ["mch_appid"] = $this->appid; // 公众账号ID
		$this->parameters ["mchid"] = $this->mchid; // 商户号
		$this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
		$this->parameters ['spbill_create_ip'] = \think\Request::instance()->ip();//获取IP
		$this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
		$xml = $this->arrayToXml ( $this->parameters );		
		$result = $this->postXmlSSLCurl( $xml, $this->transfers );	
		$this->result = $this->xmlToArray ( $result );	
		$this->close();
		return $this->result;		
	}

	/**
	 * 获取publickey
	 * @return mixed
	 */
	public function getrsapem()
	{
		
		$this->parameters ["mch_id"] = $this->mchid; // 商户号
		$this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串	
		$this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名		
		$xml = $this->arrayToXml ( $this->parameters );
		$result = $this->postXmlSSLCurl( $xml, $this->getpublickey );
		$this->result = $this->xmlToArray ( $result );
		$this->close();
		return $this->result;
	}




	public function paybank()
	{
		$this->parameters ["mch_id"] = $this->mchid; // 商户号
		$this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
		$this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
		$xml = $this->arrayToXml ( $this->parameters );        
		$result = $this->postXmlSSLCurl( $xml, $this->pay_bank );             
		$this->result = $this->xmlToArray ( $result );
		$this->close();
		return $this->result;
	}

    /**
     * 查询分账
     */
    public function querybank()
    {
        $this->parameters ["mch_id"] = $this->mchid; // 商户号
        $this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
        $this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
        $xml = $this->arrayToXml ( $this->parameters );
        $result = $this->postXmlSSLCurl( $xml, $this->query_bank );
        $this->result = $this->xmlToArray ( $result );
        $this->close();
        return $this->result;
    }
	/**
	 * 获取公钥
	 * @param unknown $path
	 */
	public function loadpublic($path)
	{
		$pubKey = openssl_pkey_get_public(trim(file_get_contents($path)));
		$this->PUB_PEM = $pubKey;
	}
	/**
	 * RSA加密
	 * @param string $str 加密字符串
	 * @return string
	 */
	public function encrypt($str)
	{
		openssl_public_encrypt($str,$code,$this->PUB_PEM,OPENSSL_PKCS1_OAEP_PADDING);
		return base64_encode($code);
	}
	/**
	 * 现金红包
	 */
	public function redpack()
	{
		$this->parameters['wxappid'] = $this->appid;
		$this->parameters ["mch_id"] = $this->mchid; // 商户号
		$this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
		$this->parameters ['client_ip'] = \think\Request::instance()->ip();//获取IP
		$this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名	
		$xml = $this->arrayToXml ( $this->parameters );
		$result = $this->postXmlSSLCurl( $xml, $this->red );		
		$this->result = $this->xmlToArray ( $result );
		$this->close();
		return $this->result;
	}
    /**
     * 服务商现金红包
     */
    public function redpacks()
    {
        $this->parameters['wxappid'] = $this->p_appid;
        $this->parameters ["mch_id"] = $this->p_mch_id; // 商户号
        $this->parameters["sub_mch_id"]  =$this->mchid;
        $this->parameters["msgappid"]  =$this->appid;
        $this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
        $this->parameters ['client_ip'] = \think\Request::instance()->ip();//获取IP
        $this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
        $xml = $this->arrayToXml ( $this->parameters );
        $result = $this->postXmlSSLCurl( $xml, $this->red);
        $this->result = $this->xmlToArray ( $result );
        $this->close();
        return $this->result;
    }
	public function close()
	{
		unset($this->parameters);		
	}
	/**
	 * 退款
	 * @return mixed
	 */
	public function refund()
	{
		$this->parameters['appid'] = $this->appid;
		$this->parameters ["mch_id"] = $this->mchid; // 商户号
		$this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串		
		$this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
		$xml = $this->arrayToXml ( $this->parameters );	
		$result = $this->postXmlSSLCurl( $xml, $this->orderrefund);
		$this->result = $this->xmlToArray ( $result );
		$this->close();
		return $this->result;
	}
    /**
     * 服务商退款
     * @return mixed
     */
    public function refunds()
    {
        $this->parameters ["appid"] =$this->p_appid;
        $this->parameters ["mch_id"] = $this->p_mch_id;
        $this->parameters ["sub_appid"] = $this->appid; // 公众账号ID
        $this->parameters ["sub_mch_id"] = $this->mchid; // 商户号
        $this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
        $this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
        $xml = $this->arrayToXml ( $this->parameters );
        $result = $this->postXmlSSLCurl( $xml, $this->orderrefund);
        $this->result = $this->xmlToArray ( $result );
        $this->close();
        return $this->result;
    }
	/**
	 * 裂变红包
	 */
	public function groupredpack()
	{
		$this->parameters['wxappid'] = $this->appid;
		$this->parameters ["mch_id"] = $this->mchid; // 商户号
		$this->parameters ["nonce_str"] = $this->createNoncestr (); // 随机字符串
		$this->parameters ['client_ip'] = \think\Request::instance()->ip();//获取IP
		$this->parameters ["sign"] = $this->getSign ( $this->parameters ); // 签名
		$xml = $this->arrayToXml ( $this->parameters );
		$result = $this->postXmlSSLCurl( $xml, $this->groupred);	
		$this->result = $this->xmlToArray ( $result );
		$this->close();
		return $this->result;		
	}
	/**
	 * 获取支付二维码地址
	 */
	public function getnativecode_url() {
		return $this->data ['code_url'];
	}
	/**
	 * 获取jsapi参数
	 * 
	 * @return string
	 */
	public function getjsapiParameters() {
		$jsApiObj ["appId"] = $this->appid;
		$timeStamp = time ();
		$jsApiObj ["timeStamp"] = "$timeStamp";
		$jsApiObj ["nonceStr"] = $this->createNoncestr ();
		$jsApiObj ["package"] = "prepay_id=" . $this->result ['prepay_id'];
		$jsApiObj ["signType"] = "MD5";
		$jsApiObj ["paySign"] = $this->getSign ( $jsApiObj );
		return json_encode ( $jsApiObj );
	}
	/**
	 * 作用：array转xml
	 */
	function arrayToXml($arr) {
		$xml = "<xml>";
		foreach ( $arr as $key => $val ) {
			if (is_numeric ( $val )) {
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
			} else
				$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
		}
		$xml .= "</xml>";
		return $xml;
	}
	/**
	 * 作用：将xml转为array
	 */
	public function xmlToArray($xml) {
		// 将XML转为array
		$array_data = json_decode ( json_encode ( simplexml_load_string ( $xml, 'SimpleXMLElement', LIBXML_NOCDATA ) ), true );
		$this->data = $array_data;
		return $array_data;
	}
	/**
	 * 作用：以post方式提交xml到对应的接口url
	 */
	public function postXmlCurl($xml, $url, $second = 60) {
		// 初始化curl
		$ch = curl_init ();
		// 设置超时
		curl_setopt ( $ch, CURLOPT_TIMEOUT, $second );
		// 这里设置代理，如果有的话
		// curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
		// curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		// 设置header
		curl_setopt ( $ch, CURLOPT_HEADER, FALSE );
		// 要求结果为字符串且输出到屏幕上
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		// post提交方式
		curl_setopt ( $ch, CURLOPT_POST, TRUE );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $xml );
		// 运行curl
		$data = curl_exec ( $ch );	
		// 返回结果
		if ($data) {
			curl_close ( $ch );
			return $data;
		} else {
			$error = curl_errno ( $ch );
			echo "curl出错，错误码:$error" . "<br>";
			curl_close ( $ch );
			return false;
		}
	}
	/**
	 * 	作用：使用证书，以post方式提交xml到对应的接口url
	 */
	function postXmlSSLCurl($xml,$url,$second=30)
	{
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//设置header
		curl_setopt($ch,CURLOPT_HEADER,FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		//设置证书
		//使用证书：cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT,$this->CERT_PEM);
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY, $this->KEY_PEM);
		//post提交方式
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		}
		else {
			$error = curl_errno($ch);			
			curl_close($ch);
			return false;
		}
	}
	/**
	 * 作用：格式化参数，签名过程需要使用
	 */
	function formatBizQueryParaMap($paraMap, $urlencode) {
		$buff = "";
		ksort ( $paraMap );
		foreach ( $paraMap as $k => $v ) {
			if ($urlencode) {
				$v = urlencode ( $v );
			}
			// $buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen ( $buff ) > 0) {
			$reqPar = substr ( $buff, 0, strlen ( $buff ) - 1 );
		}
		return $reqPar;
	}
	
	/**
	 * 作用：生成签名
	 */
	public function getSign($Obj) {
		foreach ( $Obj as $k => $v ) {
			$Parameters [$k] = $v;
		}
		// 签名步骤一：按字典序排序参数
		ksort ( $Parameters );
		$String = $this->formatBizQueryParaMap ( $Parameters, false );
		// echo '【string1】'.$String.'</br>';
		// 签名步骤二：在string后加入KEY
		$String = $String . "&key=" . $this->key;
		// echo "【string2】".$String."</br>";
		// 签名步骤三：MD5加密
		$String = md5 ( $String );
		// echo "【string3】 ".$String."</br>";
		// 签名步骤四：所有字符转为大写
		$result_ = strtoupper ( $String );
		// echo "【result】 ".$result_."</br>";
		return $result_;
	}
}
class SDKRuntimeException extends \Exception {
	public function errorMessage() {
		return $this->getMessage ();
	}
}
<?php

//企业付款到微信零钱，PHP接口调用方法

/*
功能说明

1.企业付款至银行卡只支持新资金流类型账户
2.目前企业付款到银行卡支持17家银行，更多银行逐步开放中
3.付款到账实效为1-3日，最快次日到账
4.每笔按付款金额收取手续费，按金额0.1%收取，最低1元，最高25元,
如果商户开通了运营账户，手续费和付款的金额都从运营账户出。如果没有开通，则都从基本户出。
5.每个商户号每天可以出款10万元，单商户给同一银行卡付款每天限额2万元
6.发票：在账户中心-发票信息页面申请开票的商户会按月收到发票（已申请的无需重复申请）。
企业付款到银行卡发票与交易手续费发票为拆分单独开具。

*/

namespace wechatPay;

class wechatPaybank 
{
    private $appid;            //公众号APPID
    private $appsecret;        //公众号appsecret
    private $mchid;            //商户号
    private $key;              //支付密钥
    private $sslcert;         //证书保存的绝对路径
    private $sslkey;          //证书保存的绝对路径
    
    public function __construct($appid,$appsecret,$mchid,$key,$sslcert,$sslkey)
    {
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->mchid = $mchid;
        $this->key = $key;
        $this->sslcert = $sslcert;
        $this->sslkey = $sslkey;
    }
    /*
    *  企业付款到银行卡接口
     * @params string $out_trade_no : 商户订单号
     * @params int $amount : 付款金额，单位分
     * @params string $enc_bank_no : 收款方银行卡号
     * @params string $enc_true_name : 收款方用户名
     * @params string $bank_name : 收款方开户行,根据银行名称获取银行编号bank_code
     * @params string $desc : 付款备注
     * return string $payment_no ：支付成功的订单号
    */
    public function payForBank($out_trade_no,$amount,$enc_bank_no,$enc_true_name,$bank_name,$desc='企业付款到银行卡')
    {
        $data['amount'] = $amount;
        $data['bank_code'] = $this->getBankCode($bank_name);
        $data['desc'] = $desc;
        $data['enc_bank_no'] = $this->publicEncrypt($enc_bank_no);
        $data['enc_true_name'] = $this->publicEncrypt($enc_true_name);
        $data['mch_id'] = $this->mchid;
        $data['nonce_str'] = $this->createNoncestr(12);
        $data['partner_trade_no'] = $out_trade_no;
        $sign = $this->getParam($data);

        $dataXML="<xml>
        <amount>".$data['amount']."</amount>
        <bank_code>".$data['bank_code']."</bank_code>
        <desc>".$data['desc']."</desc>
        <enc_bank_no>".$data['enc_bank_no']."</enc_bank_no>
        <enc_true_name>".$data['enc_true_name']."</enc_true_name>
        <mch_id>".$data['mch_id']."</mch_id>
        <nonce_str>".$data['nonce_str']."</nonce_str>
        <partner_trade_no>".$data['partner_trade_no']."</partner_trade_no>
        <sign>".$sign."</sign>
        </xml>";

        $url = 'https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank';
        $ret =  $this->httpsPost($url,$dataXML,true);
        return $ret;
        /*if($ret['return_code'] == 'SUCCESS' && $ret['result_code'] == 'SUCCESS' && $ret['err_code'] == 'SUCCESS'){
            return $ret['payment_no'];
        }else{
            $this->errorLog('微信付款到银行卡失败，appid：'.$this->appid,$ret);
            return false;
        }*/
    }
    /*
     * 查询付款到银行卡状态
     * @params string $out_trade_no : 商户订单号
     * return array $ret：查询状态
     * */
    public function queryBank($out_trade_no)
    {
        $data['mch_id'] = $this->mchid;
        $data['nonce_str'] = $this->createNoncestr(12);
        $data['partner_trade_no'] = $out_trade_no;
        $sign = $this->getParam($data);
        $dataXML="<xml>
        <mch_id>".$data['mch_id']."</mch_id>
        <nonce_str>".$data['nonce_str']."</nonce_str>
        <partner_trade_no>".$data['partner_trade_no']."</partner_trade_no>
        <sign>".$sign."</sign>
        </xml>";
        $url = 'https://api.mch.weixin.qq.com/mmpaysptrans/query_bank';
        $ret = $this->httpsPost($url,$dataXML,true);
        if($ret['return_code'] == 'SUCCESS' && $ret['result_code'] == 'SUCCESS' && $ret['err_code'] == 'SUCCESS'){
            return $ret;
        }else{
            $this->errorLog('查询微信付款到银行卡失败，appid：'.$this->appid.'，订单号：'.$out_trade_no,$ret);
            return false;
        }
    }
    /*
     * 银行编号列表，详情参考：https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=24_4
     * @params string $bank_name : 银行名称，4个汉字
     * return int $bank_code : 银行编码
     * */
    private function getBankCode($bank_name)
    {
        $bank_code = 0;
        switch ($bank_name){
            case '工商银行':    $bank_code = 1002;  break;
            case '农业银行':    $bank_code = 1005;  break;
            case '中国银行':    $bank_code = 1026;  break;
            case '建设银行':    $bank_code = 1003;  break;
            case '招商银行':    $bank_code = 1001;  break;
            case '邮储银行':    $bank_code = 1066;  break;
            case '交通银行':    $bank_code = 1020;  break;
            case '浦发银行':    $bank_code = 1004;  break;
            case '民生银行':    $bank_code = 1006;  break;
            case '兴业银行':    $bank_code = 1009;  break;
            case '平安银行':    $bank_code = 1010;  break;
            case '中信银行':    $bank_code = 1021;  break;
            case '华夏银行':    $bank_code = 1025;  break;
            case '广发银行':    $bank_code = 1027;  break;
            case '光大银行':    $bank_code = 1022;  break;
            case '北京银行':    $bank_code = 1032;  break;
            case '宁波银行':    $bank_code = 1056;  break;
        }
        return $bank_code;
    }
    /**
     * 公钥加密，银行卡号和姓名需要RSA算法加密
     * @param string $data    需要加密的字符串，银行卡/姓名
     * @return null|string    加密后的字符串
     */
    private function publicEncrypt($data)
    {
        // 进行加密
        $pubkey = openssl_pkey_get_public(file_get_contents(dirname(__FILE__).'/cert/public_pkcs8.pem'));
        $encrypt_data = '';
        $encrypted = '';
        $r = openssl_public_encrypt($data,$encrypt_data,$pubkey,OPENSSL_PKCS1_OAEP_PADDING);
        if($r){//加密成功，返回base64编码的字符串
            return base64_encode($encrypted.$encrypt_data);
        }else{
            return false;
        }
    }
    /*
     * 获取公钥,格式为PKCS#1 转PKCS#8
     *  openssl rsa  -RSAPublicKey_in -in   <filename>  -out <out_put_filename>
     * */
    private function get_pub_key()
    {
        $rsafile = dirname(__FILE__).'/cert/'.$this->appid.'_publicrsa.pem';
        if(!is_file($rsafile)){
            $data['mch_id'] = $this->mchid;
            $data['nonce_str'] = $this->createNoncestr(12);
            $sign = $this->getParam($data);
            $dataXML="<xml>
            <mch_id>".$data['mch_id']."</mch_id>
            <nonce_str>".$data['nonce_str']."</nonce_str>
            <sign>".$sign."</sign>
            </xml>";
            $url = 'https://fraud.mch.weixin.qq.com/risk/getpublickey';
            $ret =  $this->httpsPost($url,$dataXML,true);
            if($ret['return_code'] == 'SUCCESS' && isset($ret['pub_key'])){
                file_put_contents($rsafile,$ret['pub_key']);
                return $ret['pub_key'];
            }else{
                return null;
            }
        }else{
            return file_get_contents($rsafile);
        }
    }
    /*
    * 发起POST网络请求
    * @params string $url : 请求的url链接地址
    * @params string $data : 数据包
    * @params bool $ssl : 是否加载证书
    * return array $result : 返回的数据结果 
    */
    private function httpsPost($url,$data,$ssl = false)
    {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        if($ssl) {
            curl_setopt ( $ch,CURLOPT_SSLCERT,$this->sslcert);
            curl_setopt ( $ch,CURLOPT_SSLKEY,$this->sslkey);
        }
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Errno: '.curl_error($ch);
        }
        curl_close($ch);
        return $this->xmlToArray($result);
    }
    //对参数排序，生成MD5加密签名
    private function getParam($paramArray, $isencode=false)
    {
        $paramStr = '';
        ksort($paramArray);
        $i = 0;
        foreach ($paramArray as $key => $value)
        {
            if ($key == 'Signature'){
                continue;
            }
            if ($i == 0){
                $paramStr .= '';
            }else{
                $paramStr .= '&';
            }
            $paramStr .= $key . '=' . ($isencode?urlencode($value):$value);
            ++$i;
        }
        $stringSignTemp=$paramStr."&key=".$this->key;
        $sign=strtoupper(md5($stringSignTemp));
        return $sign;
    }
    /*
    * 将xml转换成数组
    * @params xml $xml : xml数据
    * return array $data : 返回数组
    */
    private function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring),true);
        return $val;
    }


	/**
	 * [createNoncestr 生成随机字符串]
	 * @param  integer $length [长度]
	 * @return [type]          [字母大小写加数字]
	 */
	private function createNoncestr($length =32){
	    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYabcdefghijklmnopqrstuvwxyz0123456789";  
	    $str ="";

	    for($i=0;$i<$length;$i++){  
	        $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);   
	    }  
	    return $str;
	}


    /*
    * 日志记录
    * @params string $msg : 文字描述
    * @params array $ret : 调用接口返回的数组
    */
    private function errorLog($msg,$ret)
    {
        $path = dirname(__FILE__).'/logs/';
        if(!is_dir($path)) mkdir($path,0777);
        file_put_contents(dirname(__FILE__) . '/logs/wxpay.log', "[" . date('Y-m-d H:i:s') . "] ".$msg."," .json_encode($ret,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT).PHP_EOL, FILE_APPEND);
    }
}
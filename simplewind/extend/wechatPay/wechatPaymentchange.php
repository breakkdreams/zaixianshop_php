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


class wechatPaymentchange 
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




    /**
     * [xmltoarray xml格式转换为数组]
     * @param  [type] $xml [xml]
     * @return [type]      [xml 转化为array]
     */
    public function xmltoarray($xml) { 
         //禁止引用外部xml实体 
        libxml_disable_entity_loader(true); 
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); 
        $val = json_decode(json_encode($xmlstring),true); 
        return $val;
    }


    /**
     * [arraytoxml 将数组转换成xml格式（简单方法）:]
     * @param  [type] $data [数组]
     * @return [type]       [array 转 xml]
     */
    public function arraytoxml($data){
        $str='<xml>';
        foreach($data as $k=>$v) {
            $str.='<'.$k.'>'.$v.'</'.$k.'>';
        }
        $str.='</xml>';
        return $str;
    }

    /**
     * [createNoncestr 生成随机字符串]
     * @param  integer $length [长度]
     * @return [type]          [字母大小写加数字]
     */
    public function createNoncestr($length =32){
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYabcdefghijklmnopqrstuvwxyz0123456789";  
        $str ="";

        for($i=0;$i<$length;$i++){  
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);   
        }  
        return $str;
    }

    /**
     * [curl_post_ssl 发送curl_post数据]
     * @param  [type]  $url     [发送地址]
     * @param  [type]  $xmldata [发送文件格式]
     * @param  [type]  $second [设置执行最长秒数]
     * @param  [type]  $aHeader [设置头部]
     * @return [type]           [description]
     */
    function curl_post_ssl($url, $vars, $second = 30, $aHeader = array()){
        $isdir = dirname(__FILE__)."/cert/";//证书位置

        $ch = curl_init();//初始化curl

        curl_setopt($ch, CURLOPT_TIMEOUT, $second);//设置执行最长秒数
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');//证书类型
        curl_setopt($ch, CURLOPT_SSLCERT, $isdir . 'apiclient_cert.pem');//证书位置
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');//CURLOPT_SSLKEY中规定的私钥的加密类型
        curl_setopt($ch, CURLOPT_SSLKEY, $isdir . 'apiclient_key.pem');//证书位置
        curl_setopt($ch, CURLOPT_CAINFO, 'PEM');
        curl_setopt($ch, CURLOPT_CAINFO, $isdir . 'rootca.pem');
        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);//设置头部
        }
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);//全部数据使用HTTP协议中的"POST"操作来发送

        $data = curl_exec($ch);//执行回话
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }




    /**
     * [sendMoney 企业付款到零钱]
     * @param  [type] $amount     [发送的金额（分）目前发送金额不能少于1元]
     * @param  [type] $re_openid  [发送人的 openid]
     * @param  string $desc       [企业付款描述信息 (必填)]
     * @param  string $check_name [收款用户姓名 (选填)]
     * @return [type]             [description]
     */
    public function sendMoney($amount,$re_openid,$desc='测试',$check_name=''){

        
        $data=array(
            'mch_appid'=>$this->appid,//商户账号appid
            'mchid'=> $this->mchid,//商户号
            'nonce_str'=>$this->createNoncestr(),//随机字符串
            'partner_trade_no'=> date('YmdHis').rand(1000, 9999),//商户订单号
            'openid'=> $re_openid,//用户openid
            'check_name'=>'NO_CHECK',//校验用户姓名选项,
            're_user_name'=> $check_name,//收款用户姓名
            'amount'=>$amount,//金额
            'desc'=> $desc,//企业付款描述信息
            'spbill_create_ip'=> '47.96.24.12',//Ip地址
            // 'spbill_create_ip'=> $_SERVER['SERVER_ADDR'],//Ip地址
        );

        //生成签名算法
        $secrect_key=$this->key;///这个就是个API密码。MD5 32位。
        $data=array_filter($data);
        ksort($data);
        $str='';
        foreach($data as $k=>$v) {
           $str.=$k.'='.$v.'&';
        }
        $str.='key='.$secrect_key;
        $data['sign']=md5($str);
        //生成签名算法


        $xml=$this->arraytoxml($data);
      
        $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers'; //调用接口
        $res=$this->curl_post_ssl($url,$xml);
        $ret=$this->xmltoarray($res);
        return $ret;
        
     
        /*if($ret['return_code'] == 'SUCCESS' && $ret['result_code'] == 'SUCCESS'){
            return $ret['payment_no'];
        }else{
            $this->errorLog('微信付款到零钱失败，appid：'.$this->appid,$ret);
            return false;
        }*/


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
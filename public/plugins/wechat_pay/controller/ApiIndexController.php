<?php
namespace plugins\wechat_pay\controller;
/**
 * @Author: user
 * @Date:   2019-03-07 16:21:19
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-20 09:56:01
 */
use cmf\controller\PluginRestBaseController;//引用插件基类
use plugins\wechat_pay\Model\PluginApiIndexModel;
use think\Db;
use think\Loader;
use wechatPay\wechatPaybank;
use wechatPay\wechatPaymentchange;

Loader::import('wechatPay.example.log',EXTEND_PATH);
Loader::import('wechatPay.lib.WxPay',EXTEND_PATH,'.Api.php');
Loader::import('wechatPay.example.WxPay',EXTEND_PATH,'.JsApiPay.php');

/**
 * api控制器
 */
class ApiIndexController extends PluginRestBaseController
{
    public function index(){
		$order_sn  = "sdkphp".date("YmdHis");
    	//$aa = $this->wechat_jsapi(30,$order_sn,1,'这个是测试的');
    	
    	$orderinfo['module'] = 'fund';
		$mm = '\plugins\\'.$orderinfo['module'].'\controller\ApiIndexController';
        $obj = new $mm;
        $method = 'wechat_notify';
        $param = ['out_trade_no'=>'157197886227207','transaction_id'=>$order_sn,'type'=>1];
	    	$res = call_user_func_array( [ $obj , $method ] , $param );
	    	dump($res['status']);
	    	exit();
    }


    /**
     * [企业付款到银行卡_能用的银行卡]
     */
    public function wechatPaybank($isModule=false){
		$data = Db::name('wechat_pay_transfer_bank')->select();
		if($isModule==false){
			zy_json_echo(true,'操作成功！',$data,200);
		}
		
        return zy_array(true,'操作成功！',$data,200 ,$isModule);
    }


    /**
     * [企业付款到银行卡]
     * @param  string $out_trade_no  [退款订单号]
     * @param  string $money         [金额]
     * @param  string $enc_bank_no   [收款方银行卡号]
     * @param  string $enc_true_name [收款方用户名]
     * @param  string $bank_name     [收款方开户行]
     * @param  string $desc          [付款说明]
     * @return [type]                [description]
     */
    public function payForBank( $module=null,$out_trade_no='',$money='',$enc_bank_no='',$enc_true_name='',$bank_name='',$desc='',$isModule=false)
    {
		if($this->request->isPost()){
	        $data=$this->request->post();
	        //$data = zy_decodeData($data,$isModule);
	        $out_trade_no = $data['out_trade_no'];
	        $money = $data['money'];
	        $enc_bank_no = $data['enc_bank_no'];
	        $enc_true_name = $data['enc_true_name'];
	        $bank_name = $data['bank_name'];
	        $desc = $data['desc'];
		}

		if( $module!='fund' ){
			return zy_json_echo(false,'除资金外不可调用该方法',$result,-1);
		}

		if( empty($out_trade_no) || empty($money) || empty($enc_bank_no) || empty($enc_true_name)  || empty($bank_name) ){
	        return zy_json_echo(false,'参数不能为空',$result,-1);
		}

		if(empty(Db::name('wechat_pay_transfer_bank')->where('name',$bank_name)->find())){
	        return zy_json_echo(false,'操作失败:所提现的开户行不在微信银行列表当中！','',-4);
		}


		$config = new \WxPayConfig();
		$appid = $config->GetAppId();		//微信给的
		$mchid = $config->GetMerchantId();				//微信给的
		$key = $config->GetKey();	//自己设置的微信商家key
		$appsecret = $config->GetAppSecret();
        $sslcert = $config->sslCertPath();        
        $sslkey = $config->sslKeyPath();

        /*
        $out_trade_no =  date('Ymdhis', time()).substr(floor(microtime()*1000),0,1).rand(0,9);
        $money = 1;
        $enc_bank_no = '6212261207011575982';
        $enc_true_name = '叶洋洋';
        $bank_name = '工商银行';
        $desc = '企业付款到银行卡测试';
        */
        $money = 1;
        $wxapi = new wechatPaybank($appid,$appsecret,$mchid,$key,$sslcert,$sslkey);
        $payment_no = $wxapi->payForBank($out_trade_no,$money,$enc_bank_no,$enc_true_name,$bank_name,$desc);

        if($payment_no['return_code'] == 'SUCCESS' && $payment_no['result_code'] == 'SUCCESS' && $payment_no['err_code'] == 'SUCCESS'){
	        return zy_json_echo(true,'操作成功',$payment_no,200);
	    }else{
	        return zy_json_echo(false,'操作失败:'.$payment_no['err_code_des'],$payment_no,-2);
	    }
    }

    /**
     * [企业付款到零钱]
     * @param  [type] $amount     [发送的金额（分）目前发送金额不能少于1元]
     * @param  [type] $re_openid  [发送人的 openid]
     * @param  string $desc       [企业付款描述信息 (必填)]
     * @param  string $check_name [收款用户姓名 (选填)]
     * @return [type]                [description]
     */
    public function payForChange($module=null,$amount,$re_openid,$desc='测试',$check_name='',$isModule=false)
    {

		if($this->request->isPost()){
	        $data=$this->request->post();
	        //$data = zy_decodeData($data,$isModule);
	        $amount = $data['amount'];
	        $re_openid = $data['re_openid'];
	        $desc = $data['desc'];
	        $check_name = $data['check_name'];
		}

		if( $module!='fund' ){
			return zy_json_echo(false,'除资金外不可调用此方法',$result,-1);
		}

		if( empty($check_name) || empty($re_openid) || empty($desc) || empty($check_name) ){
	        return zy_json_echo(false,'参数不能为空',$result,-1);
		}



		$config = new \WxPayConfig();
		$appid = $config->GetAppId();		//微信给的
		$mchid = $config->GetMerchantId();				//微信给的
		$key = $config->GetKey();	//自己设置的微信商家key
		$appsecret = $config->GetAppSecret();
        $sslcert = $config->sslCertPath();        
        $sslkey = $config->sslKeyPath();

        $amount = 30;
	    $paymentchange = new wechatPaymentchange($appid,$appsecret,$mchid,$key,$sslcert,$sslkey);
	    $result = $paymentchange->sendMoney($amount,$re_openid,$desc,$check_name);
		if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
	        return zy_json_echo(true,'操作成功',$result,200);
        }else{
	        return zy_json_echo(false,'操作失败',$result,-2);
        }

    }



    /**
     * 微信支付-回调
     */
    public function wechat_notify()
    {
        //初始化日志
        $logHandler= new \CLogFileHandler(EXTEND_PATH.'wechatPay/logs/'.date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);

		//获取返回的xml
		$testxml  = file_get_contents("php://input");
		//将xml转化为json格式
		$jsonxml = json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA));
		//转成数组
		$result = json_decode($jsonxml, true);

		if($result){
			//如果成功返回了
			if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
				//收到微信支付结果通知后，请严格按照示例返回参数给微信支付：
				$notify_data = [
					'return_code'=>'SUCCESS',
					'return_msg'=>'OK'
				];
				$xmlData =$this->arraytoxml($notify_data);

				try {
					//调用表，利用订单号获取模块名称
					//返回成功给我，然后我返回给微信
					//更新这张表，条件是订单号，改成状态
					$orderinfo = Db::name('wechat_pay')->where('out_trade_no',$result['out_trade_no'])->find();

					$mm = '\plugins\\'.$orderinfo['module'].'\controller\ApiIndexController';
			        $obj = new $mm;
			        $method = 'wechat_notify';
			        $param = ['out_trade_no'=>$result['out_trade_no'],'transaction_id'=>$result['transaction_id'],'type'=>$orderinfo['type']];


					$res = call_user_func_array( [ $obj , $method ] , $param );

			        if($res['status']=='success'){
						Db::name('wechat_pay')
						    ->where('out_trade_no', $result['out_trade_no'])
						    ->update(['status'=>1,'transaction_id'=>$result['transaction_id'],'update_time'=>time(),'describe'=>$res['message']]);
			        }else{
						Db::name('wechat_pay')
						    ->where('out_trade_no', $result['out_trade_no'])
						    ->update(['transaction_id'=>$result['transaction_id'],'update_time'=>time(),'describe'=>$res['message']]);
			        }
 
	 				\Log::DEBUG("回调操作成功:" . $xmlData);
					echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
					
					return $xmlData;
				} catch ( \Exception $e) {
					\Log::DEBUG("回调操作成功:".$orderinfo['module']."回调接口错误");
					echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
					return $xmlData;
				}

 
				/*echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
				return $xmlData;*/

			}else{
				\Log::DEBUG("回调操作失败：" . $jsonxml);
			}
		}

    }

    /**
    * jsapi支付（公众号支付）
    */
    public function wechat_jsapi($uid=null,$order_sn=null,$money=null,$title=null,$isModule=false)
    {

        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);

        $uid = isset( $data['uid'] ) ? $data['uid'] : $uid ;
        $order_sn = isset( $data['order_sn'] ) ? $data['order_sn'] : $order_sn ;
        $money = isset( $data['money'] ) ? $data['money'] : $money ;
        $title = isset( $data['title'] ) ? $data['title'] : $title ;


    	if(empty($uid) || empty($order_sn) || empty($money) || empty($title)){
            return zy_array(false,'操作失败！','',-1 ,$isModule);
    	}
        //初始化日志
        $logHandler= new \CLogFileHandler(EXTEND_PATH.'wechatPay/logs/'.date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);

        try{
            $tools = new \JsApiPay();

            // $order_sn  = "sdkphp".date("YmdHis");
            // $money = 1;
            // $title = "和豆浆";
            // $openId = $tools->GetOpenid();
            $member_personalData = $this->member_personalData($uid,'wechatpe_openid');
            if($member_personalData['status']=='error'){
                return zy_array (false,$member_personalData['message'],'',-40,$isModule);
            }
            $openId = $member_personalData['data']['wechatpe_openid'];
            $money = $money*100;


            //②、统一下单
            $input = new \WxPayUnifiedOrder();
            $input->SetBody($title);
            $input->SetAttach("test");
            $input->SetOut_trade_no($order_sn);
            $input->SetTotal_fee($money);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");
            $input->SetNotify_url("http://tp.300c.cn/zy_137/subsystem20190906/public/plugin/wechat_pay/api_index/wechat_notify");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $config = new \WxPayConfig();
            $order = \WxPayApi::unifiedOrder($config, $input);
            //echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
            // var_dump($order);
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $jsApiParameters = json_decode($jsApiParameters,true);

            //获取共享收货地址js函数参数
            $editAddress = $tools->GetEditAddressParameters();
            return zy_array(true,'操作成功！',$jsApiParameters,200 ,$isModule);
        } catch(Exception $e) {
            \Log::ERROR(json_encode($e));
            return zy_array(false,'操作失败！','',-1 ,$isModule);
        }



    }

    /**
     * h5支付
     */
    public function wechat_h5($uid=null,$order_sn=null,$money=null,$title=null,$module=null,$type=null,$isModule=false)
    {

        $data=$this->request->post();
        // $data = zy_decodeData($data,$isModule);

        $uid = isset( $data['uid'] ) ? $data['uid'] : $uid ;
        $order_sn = isset( $data['order_sn'] ) ? $data['order_sn'] : $order_sn ;
        $money = isset( $data['money'] ) ? $data['money'] : $money ;
        $title = isset( $data['title'] ) ? $data['title'] : $title ;
        $module = isset( $data['module'] ) ? $data['module'] : $module ;
        $type = isset( $data['type'] ) ? $data['type'] : $type ;

    	if(empty($uid) || empty($order_sn) || empty($money) || empty($title) || empty($module)){
            return zy_array(false,'操作失败！','',-1 ,$isModule);
		}
		if( $module!='fund' ){
			return zy_array(false,'除资金外不可直接使用该方法',null,-1 ,$isModule);
		}

    	//获取站点配置
    	$siteconfiguration_c = $this->siteconfiguration_c();
    	if($siteconfiguration_c['status']=='error'){
            return zy_array(false,$siteconfiguration_c['message'],'',-100 ,$isModule);
    	}
		// ====验证这个模块下面的方法存不存在
		$module_config_c = new \plugins\module_config\common\Common();
		$methodExistsCheck = $module_config_c->methodExistsCheck('\plugins\\'.$module.'\controller\ApiIndexController','wechat_notify');
		if($methodExistsCheck==0){
            return zy_array(false,'模块方法不存在！','',-2 ,$isModule);
		}

        //初始化日志
        $logHandler= new \CLogFileHandler(EXTEND_PATH.'wechatPay/logs/'.date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);

		try {

            // $order_sn  = "sdkphp".date("YmdHis");
            // $money = 1;
            // $title = "和豆浆";
            $money = $money*100;

			$config = new \WxPayConfig();

			//定义
			$appid = $config->GetAppId();		//微信给的
			$mch_id = $config->GetMerchantId();				//微信给的
			$key = $config->GetKey();	//自己设置的微信商家key

			//xml数据
			$body = $title;		//内容
			$out_trade_no = $order_sn;		//平台内部订单号
			$nonce_str=MD5($out_trade_no);			//随机字符串
			$notify_url = $siteconfiguration_c['data']['basic']['site_domain'].'/plugin/wechat_pay/api_index/wechat_notify';//回调地址
			$total_fee = $money;		//支付金额
			$trade_type = 'MWEB';	//交易类型 具体看API 里面有详细介绍

			$spbill_create_ip = get_client_ip();//获得用户设备IP 自己网上百度去
			$scene_info = '{"h5_info":{"type":"Wap","wap_url":"http://js3.300c.cn","wap_name":"卓远网络"}}';//场景信息 必要参数

			//生成签名
			$stringA="appid=$appid&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";	//定义所有传递参数
			$strSignTmp = $stringA."&key=$key"; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面XML  是否正确
			$sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写
			//生成签名

			$xml = "<xml>
					<appid>$appid</appid>
					<body>$body</body>
					<mch_id>$mch_id</mch_id>
					<nonce_str>$nonce_str</nonce_str>
					<notify_url>$notify_url</notify_url>
					<out_trade_no>$out_trade_no</out_trade_no>
					<spbill_create_ip>$spbill_create_ip</spbill_create_ip>
					<total_fee>$total_fee</total_fee>
					<trade_type>$trade_type</trade_type>
					<scene_info>$scene_info</scene_info>
					<sign>$sign</sign>
					</xml>";
			$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
			$dataxml = $this->_crul_post($url,$xml);
			$objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组

			// ====如果成功，那么就加入到数据库
			if($objectxml['return_code']=='SUCCESS' && $objectxml['result_code']=='SUCCESS'){
				$wecaht_db = ['out_trade_no'=>$order_sn,'module'=>$module,'type'=>$type,'create_time'=>time()];
				Db::name('wechat_pay')->insert($wecaht_db);
			}

            return zy_array(true,'操作成功！',$objectxml,200 ,$isModule);
		} catch (Exception $e) {
            \Log::ERROR(json_encode($e));
            return zy_array(false,'操作失败！','',-1 ,$isModule);
		}

    }





    /**
     * 刷卡支付
     */
    public function wechat_micropay()
    {

    }


    /**
     * 扫码支付
     */
    public function wechat_native()
    {

    }


	/**
	* 订单查询
	*/
	public function wechat_orderquery($order_sn=null,$out_trade_no=null,$isModule=false)
	{
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);

        $transaction_id = isset( $data['order_sn'] ) ? $data['order_sn'] : $order_sn ;
        $out_trade_no = isset( $data['out_trade_no'] ) ? $data['out_trade_no'] : $out_trade_no ;


        $logHandler= new \CLogFileHandler(EXTEND_PATH.'wechatPay/logs/'.date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);


		if(isset($transaction_id) && $transaction_id != ""){
			try {
				$transaction_id = $transaction_id;
				$input = new \WxPayOrderQuery();
				$input->SetTransaction_id($transaction_id);
				$config = new \WxPayConfig();
				var_dump(\WxPayApi::orderQuery($config, $input));
			} catch(Exception $e) {
				\Log::ERROR(json_encode($e));
			}
			exit();
		}

		if(isset($out_trade_no) && $out_trade_no != ""){
			try{
				$out_trade_no = $out_trade_no;
				$input = new \WxPayOrderQuery();
				$input->SetOut_trade_no($out_trade_no);
				$config = new \WxPayConfig();
				var_dump(\WxPayApi::orderQuery($config, $input));
			} catch(Exception $e) {
				\Log::ERROR(json_encode($e));
			}
			exit();
		}

        return zy_array(false,'操作失败！','',-1 ,$isModule);

	}


	/**
	* 订单退款
	*/
	public function wechat_refund($transaction_id=null,$out_trade_no=null,$total_fee=0,$refund_fee=0,$isModule=false)
	{

		if($transaction_id || $out_trade_no){


	        $logHandler= new \CLogFileHandler(EXTEND_PATH.'wechatPay/logs/'.date('Y-m-d').'.log');
	        $log = \Log::Init($logHandler, 15);


			if(isset($transaction_id) && $transaction_id != ""){
				try{
					$transaction_id = $transaction_id;
					$total_fee = $total_fee*100;
					$refund_fee = $refund_fee*100;
					$input = new \WxPayRefund();
					$input->SetTransaction_id($transaction_id);
					$input->SetTotal_fee($total_fee);
					$input->SetRefund_fee($refund_fee);

					$config = new \WxPayConfig();
				    $input->SetOut_refund_no("sdkphp".date("YmdHis"));
				    $input->SetOp_user_id($config->GetMerchantId());

				    $result = \WxPayApi::refund($config, $input);

				    if($result['return_code']=="FAIL" ){
				    	return zy_array(false,'操作失败！',$result,-1 ,$isModule);
				    }

				    if($result['result_code']=="SUCCESS" && $result['return_code']=="SUCCESS" ){
						$wecaht_refund_db = ['transaction_id'=>$result['transaction_id'],'out_trade_no'=>$result['out_trade_no'],'out_refund_no'=>$result['out_refund_no'],'refund_id'=>$result['refund_id'],'total_fee'=>$result['total_fee']/100,'refund_fee'=>$result['refund_fee']/100,'create_time'=>time()];
						Db::name('wechat_pay_refund')->insert($wecaht_refund_db);

				    	return zy_array(true,'操作成功！',$result,200 ,$isModule);
				    }else{
				    	return zy_array(false,'操作失败！',$result,-1 ,$isModule);
				    }

				} catch(Exception $e) {
					\Log::ERROR(json_encode($e));
				}
				exit();
			}

			//$_REQUEST["out_trade_no"]= "122531270220150304194108";
			///$_REQUEST["total_fee"]= "1";
			//$_REQUEST["refund_fee"] = "1";
			if(isset($out_trade_no) && $out_trade_no != ""){
				try{
					$out_trade_no = $out_trade_no;
					$total_fee = $total_fee*100;
					$refund_fee = $refund_fee*100;
					$input = new \WxPayRefund();
					$input->SetOut_trade_no($out_trade_no);
					$input->SetTotal_fee($total_fee);
					$input->SetRefund_fee($refund_fee);

					$config = new \WxPayConfig();
				    $input->SetOut_refund_no("sdkphp".date("YmdHis"));
				    $input->SetOp_user_id($config->GetMerchantId());
					$result = \WxPayApi::refund($config, $input);

				    if($result['result_code']=="SUCCESS" && $result['return_code']=="SUCCESS" ){
						$wecaht_refund_db = ['transaction_id'=>$result['transaction_id'],'out_trade_no'=>$result['out_trade_no'],'out_refund_no'=>$result['out_refund_no'],'refund_id'=>$result['refund_id'],'total_fee'=>$result['total_fee']/100,'refund_fee'=>$result['refund_fee']/100,'create_time'=>time()];
						Db::name('wechat_pay_refund')->insert($wecaht_refund_db);
				    	
				    	return zy_array(true,'操作成功！',$result,200 ,$isModule);
				    }else{
				    	return zy_array(false,'操作失败！',$result,-1 ,$isModule);
				    }
				} catch(Exception $e) {
					\Log::ERROR(json_encode($e));
				}
				exit();
			}

		}else{

			return zy_array(false,'操作失败！','',-1 ,$isModule);
		}

	}

	/**
	* 例子页面_退款查询
	*/
	public function wechat_refundquery($order_sn=null,$out_trade_no=null,$out_refund_no=null,$refund_id=null,$isModule=false) 
	{

        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);

        $transaction_id = isset( $data['order_sn'] ) ? $data['order_sn'] : $order_sn ;
        $out_trade_no = isset( $data['out_trade_no'] ) ? $data['out_trade_no'] : $out_trade_no ;
        $out_refund_no = isset( $data['out_refund_no'] ) ? $data['out_refund_no'] : $out_refund_no ;
        $refund_id = isset( $data['refund_id'] ) ? $data['refund_id'] : $refund_id ;

        $logHandler= new \CLogFileHandler(EXTEND_PATH.'wechatPay/logs/'.date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);



		if(isset($transaction_id) && $transaction_id != ""){
			try{
				$transaction_id = $transaction_id;
				$input = new \WxPayRefundQuery();
				$input->SetTransaction_id($transaction_id);
				$config = new \WxPayConfig();
				var_dump(\WxPayApi::refundQuery($config, $input));
			} catch(Exception $e) {
				\Log::ERROR(json_encode($e));
			}
		}

		if(isset($out_trade_no) && $out_trade_no != ""){
			try{
				$out_trade_no = $out_trade_no;
				$input = new \WxPayRefundQuery();
				$input->SetOut_trade_no($out_trade_no);
				$config = new \WxPayConfig();
				var_dump(\WxPayApi::refundQuery($input));
			} catch(Exception $e) {
				\Log::ERROR(json_encode($e));
			}
			exit();
		}

		if(isset($out_refund_no) && $out_refund_no != ""){
			try{
				$out_refund_no = $out_refund_no;
				$input = new \WxPayRefundQuery();
				$input->SetOut_refund_no($out_refund_no);
				$config = new \WxPayConfig();
				var_dump(\WxPayApi::refundQuery($config, $input));
			} catch(Exception $e) {
				\Log::ERROR(json_encode($e));
			}
			exit();
		}

		if(isset($refund_id) && $refund_id != ""){
			try{
				$refund_id = $refund_id;
				$input = new \WxPayRefundQuery();
				$input->SetRefund_id($refund_id);
				$config = new \WxPayConfig();
				var_dump(\WxPayApi::refundQuery($config, $input));
			} catch(Exception $e) {
				\Log::ERROR(json_encode($e));
			}
			exit();
		}

        return zy_array(false,'操作失败！','',-1 ,$isModule);
	}


	/**
	* 下载订单
	*/
	public function wechat_download($bill_date=null,$bill_type=null,$isModule=false)
	{

        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);

        $bill_date = isset( $data['bill_date'] ) ? $data['bill_date'] : $bill_date ;
        $bill_type = isset( $data['bill_type'] ) ? $data['bill_type'] : $bill_type ;

        $logHandler= new \CLogFileHandler(EXTEND_PATH.'wechatPay/logs/'.date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);

		if(isset($bill_date) && $bill_date != ""){

			$bill_date = $bill_date;
		    $bill_type = $bill_type;
			$input = new \WxPayDownloadBill();
			$input->SetBill_date($bill_date);
			$input->SetBill_type($bill_type);
			$config = new \WxPayConfig();
			$file = \WxPayApi::downloadBill($config, $input);
			echo htmlspecialchars($file, ENT_QUOTES);
			//TODO 对账单文件处理
		    exit(0);
		}

        return zy_array(false,'操作失败！','',-1 ,$isModule);
	}

	/**
	 * CURL方式的POST传值
	 * @param  [type] $url  [POST传值的URL]
	 * @param  [type] $data [POST传值的参数]
	 * @return [type]       [description]
	 */
	public function _crul_post($url,$data){
	    //初始化curl		
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url); 
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    //post提交方式
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    //要求结果为字符串且输出到屏幕上
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    //运行curl
	    $result = curl_exec($curl);

	    //返回结果	    
	    if (curl_errno($curl)) {
	       return 'Errno'.curl_error($curl);
	    }
	    curl_close($curl);
	    return $result;
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



    // ====================== 调用其他模块操作

    /**
     * 会员模块-获取会员的微信信息
     * @param  [type]  $data     [description]
     */
    private function member_personalData($uid,$field){
        $symbol ='wechat_pay';
        $id = 'member_one';
        $param = ['uid'=>$uid,'field'=>$field,'isModule'=>true];
        $return = getModuleApiData( $symbol, $id, $param);
        return $return;
    }

    /**
     * 资金模块-充值成功之后，改变订单状态
     * @param  [type]  $data     [description]
     */
    private function fund_addsuccess($trade_sn,$transaction_id){
        $symbol ='wechat_pay';
        $id = 'fund_one';
        $param = ['trade_sn'=>$trade_sn,'transaction_id'=>$transaction_id,'isModule'=>true];
        $return = getModuleApiData( $symbol, $id, $param);
        return $return;
    }

    /**
     * 站点配置-获取当前的域名
     * @param  [type]  $data     [description]
     */
    private function siteconfiguration_c(){
        $symbol ='wechat_pay';
        $id = 'siteconfiguration_one';
        $param = ['data'=>null,'isModule'=>true];
        $return = getModuleApiData( $symbol, $id, $param);
        return $return;
    }

    // ====================== 调用其他模块操作





}
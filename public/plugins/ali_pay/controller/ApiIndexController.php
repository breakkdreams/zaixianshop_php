<?php
namespace plugins\ali_pay\controller;
/**
 * @Author: user
 * @Date:   2019-03-07 16:21:19
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-20 09:56:01
 */
use cmf\controller\PluginRestBaseController;//引用插件基类
use plugins\ali_pay\Model\PluginApiIndexModel;
use think\Db;
use think\Loader;
use aliPay\config;
use aliPay\Alipay;


Loader::import('aliPay.wap.wappay.service.AlipayTradeService',EXTEND_PATH);
Loader::import('aliPay.wap.wappay.buildermodel.AlipayTradeWapPayContentBuilder',EXTEND_PATH);
Loader::import('aliPay.wap.wappay.buildermodel.AlipayTradeRefundContentBuilder',EXTEND_PATH);


/**
 * api控制器
 */
class ApiIndexController extends PluginRestBaseController
{
    public function index(){
      return [];
    }




	/**
	* 例子页面_网页支付-异步回调
	*/
	public function notify_url() 
	{

		/* *
		 * 功能：支付宝服务器异步通知页面
		 * 版本：2.0
		 * 修改日期：2017-05-01
		 * 说明：
		 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

		 *************************页面功能说明*************************
		 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
		 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
		 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
		 */

	    $config_class = new config();
	    $config = $config_class->configInfo();


		$arr=$_POST;
		
		$alipaySevice = new \AlipayTradeService($config); 
		//$alipaySevice->writeLog(var_export($_POST,true));
		$result = $alipaySevice->check($arr);
		//s$alipaySevice->writeLog($result);


		/* 实际验证过程建议商户添加以下校验。
		1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
		2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
		3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
		4、验证app_id是否为该商户本身。
		*/
		if($result) {//验证成功
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//请在这里加上商户的业务逻辑程序代
			//$alipaySevice->writeLog('支付宝支付异步回调：'.$result.'======'.json_encode($_POST));

			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			
		    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
			
			//商户订单号

			$out_trade_no = $_POST['out_trade_no'];

			//支付宝交易号

			$trade_no = $_POST['trade_no'];

			//交易状态
			$trade_status = $_POST['trade_status'];


		    if($_POST['trade_status'] == 'TRADE_FINISHED') {

				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//请务必判断请求时的total_amount与通知时获取的total_fee为一致的
					//如果有做过处理，不执行商户的业务程序
						
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
		    }
		    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//请务必判断请求时的total_amount与通知时获取的total_fee为一致的
					//如果有做过处理，不执行商户的业务程序			
				//注意：
				//付款完成后，支付宝系统发送该交易状态通知
				$orderinfo = Db::name('ali_pay')->where('out_trade_no',$out_trade_no)->find();
				$mm = '\plugins\\'.$orderinfo['module'].'\controller\ApiIndexController';
		        $obj = new $mm;
		        $method = 'ali_notify';
		        $param = ['out_trade_no'=>$out_trade_no,'transaction_id'=>$trade_no,'type'=>$orderinfo['type']];

				$res = call_user_func_array( [ $obj , $method ] , $param );
		        if($res['status']=='success'){
					Db::name('ali_pay')
					    ->where('out_trade_no', $out_trade_no)
					    ->update(['status'=>1,'transaction_id'=>$trade_no,'update_time'=>time(),'describe'=>$res['message']]);
		        }else{
					Db::name('ali_pay')
					    ->where('out_trade_no', $out_trade_no)
					    ->update(['transaction_id'=>$trade_no,'update_time'=>time(),'describe'=>$res['message']]);
		        }


		    }
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
			echo "success";	//请不要修改或删除
			return "success";
		}else {
		    //验证失败
		    echo "fail";
			return "fail";
		}

	}




    /**
     * 支付宝支付-移动端
     * @return [type] [description]
     */
    public function wap($order_sn=null,$money=null,$title=null,$module=null,$return_url=null,$type=null,$isModule=false){

        $data=$this->request->get();
        //$data = zy_decodeData($data,$isModule);
  
        $order_sn = isset( $data['order_sn'] ) ? $data['order_sn'] : $order_sn ;
        $money = isset( $data['money'] ) ? $data['money'] : $money ;
        $title = isset( $data['title'] ) ? $data['title'] : $title ;
        $module = isset( $data['module'] ) ? $data['module'] : $module ;
        $return_url = isset( $data['return_url'] ) ? $data['return_url'] : $return_url ;
        $type = isset( $data['type'] ) ? $data['type'] : $type ;

    	if(empty($order_sn) || empty($money) || empty($title) || empty($module) || empty($return_url)){
            return zy_array(false,'操作失败！','',-1 ,$isModule);
		}

		if( $module!='fund' ){
			return zy_array(false,'除资金外不可直接调用该方法','',-1 ,$isModule);
		}
    	//获取站点配置
    	$siteconfiguration_c = $this->siteconfiguration_c();
    	if($siteconfiguration_c['status']=='error'){
            return zy_array(false,$siteconfiguration_c['message'],'',-100 ,$isModule);
    	}
		// ====验证这个模块下面的方法存不存在
		$module_config_c = new \plugins\module_config\common\Common();
		$methodExistsCheck = $module_config_c->methodExistsCheck('\plugins\\'.$module.'\controller\ApiIndexController','ali_notify');
		if($methodExistsCheck==0){
            return zy_array(false,'模块方法不存在！','',-2 ,$isModule);
		}

	    //商户订单号，商户网站订单系统中唯一订单号，必填
	    //$out_trade_no = "sdkphp".date("YmdHis");
	    //订单名称，必填
	    //$subject = '我是标题';
	    //付款金额，必填
	    //$total_amount = '0.01';
	    //商品描述，可空
	    //$body = '这里是描述';
	    //超时时间
	    //$timeout_express="1m";
	    $out_trade_no = $order_sn;
	    $subject = $title;
	    $body = $title;
	    $total_amount = $money;
	    $timeout_express = "1m";
	    //回调
	    $notify_url = $siteconfiguration_c['data']['basic']['site_domain'].'/plugin/ali_pay/api_index/notify_url';
	    // $return_url = $siteconfiguration_c['data']['basic']['site_domain'].$return_url;
	    $return_url = 'http://tp.300c.cn/zy_137/subsystem20190906/page/#'.$return_url;

		// ====如果成功，那么就加入到数据库
		$wecaht_db = ['out_trade_no'=>$order_sn,'module'=>$module,'type'=>$type,'create_time'=>time()];
		Db::name('ali_pay')->insert($wecaht_db);


	    $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
	    $payRequestBuilder->setBody($body);
	    $payRequestBuilder->setSubject($subject);
	    $payRequestBuilder->setOutTradeNo($out_trade_no);
	    $payRequestBuilder->setTotalAmount($total_amount);
	    $payRequestBuilder->setTimeExpress($timeout_express);
	    $config_class = new config();
	    $config = $config_class->configInfo();

	    $payResponse = new \AlipayTradeService($config);
	    $result=$payResponse->wapPay($payRequestBuilder,$return_url,$notify_url);

	    return $result;
    }



    /**
     * 支付宝支付-订单退款
     * @param  [type]  $transaction_id [商户订单号（商户订单号，和支付宝交易号二选一）]
     * @param  [type]  $out_trade_no   [支付宝交易号（支付宝交易号，和商户订单号二选一）]
     * @param  integer $refund_amount  [退款金额]
     * @param  [type]  $refund_reason  [退款原因]
     * @param  [type]  $out_request_no [退款订单号（如是部分退款，则参数退款单号（out_request_no）必传。）]
     * @param  boolean $isModule       [true 模块标识]
     */
	public function ali_refund($transaction_id=null,$out_trade_no=null,$refund_amount=0,$refund_reason=null,$out_request_no=null,$isModule=false)
	{
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);

        //下面还没写完
        $transaction_id = isset( $data['transaction_id'] ) ? $data['transaction_id'] : $transaction_id ;
        $out_trade_no = isset( $data['out_trade_no'] ) ? $data['out_trade_no'] : $out_trade_no ;
        $refund_amount = isset( $data['refund_amount'] ) ? $data['refund_amount'] : $refund_amount ;
        $refund_reason = isset( $data['refund_reason'] ) ? $data['refund_reason'] : $refund_reason ;
        $out_request_no = isset( $data['out_request_no'] ) ? $data['out_request_no'] : $out_request_no ;

        if(empty($refund_amount) || empty($refund_reason)){
            return zy_array(false,'参数错误','',-2 ,$isModule);
        }

		if($transaction_id || $out_trade_no){
		    $config_class = new config();
		    $config = $config_class->configInfo();

		    $RequestBuilder = new \AlipayTradeRefundContentBuilder();
		    $RequestBuilder->setTradeNo($transaction_id);
		    $RequestBuilder->setOutTradeNo($out_trade_no);
		    $RequestBuilder->setRefundAmount($refund_amount);
		    $RequestBuilder->setRefundReason($refund_reason);
		    $RequestBuilder->setOutRequestNo($out_request_no);

		    $Response = new \AlipayTradeService($config);
		    $result=$Response->Refund($RequestBuilder);
		    $result = json_decode(json_encode($result),true);

		    if($result['code']!="10000"){
		        return zy_array(false,'操作错误',$result,-4 ,$isModule);
		    }

        $out_request_no = isset($out_request_no) ? $out_request_no : '';
			$ali_pay_refund = [
				'out_trade_no'=>$result['out_trade_no'],
				'transaction_id'=>$result['trade_no'],
				'create_time'=>time(),
				'refund_fee'=>$refund_amount,
				'yrefund_fee'=>$result['refund_fee'],
				'out_refund_no'=>$out_request_no,
				'refund_reason'=>$refund_reason,
				'refund_id'=>'',
				'buyer_logon_id'=>$result['buyer_logon_id'],
				'buyer_user_id'=>$result['buyer_user_id'],
				'fund_change'=>$result['fund_change'],
			];
			Db::name('ali_pay_refund')->insert($ali_pay_refund);

	        return zy_array(true,'操作成功',$result,200 ,$isModule);

		    //dump($result);
		    //return ;
		}
        return zy_array(false,'操作错误','',-100 ,$isModule);


	}









	/**
     * 支付宝转账
     * @param $totalFee 金额
     * @param $outTradeNo 订单编号
     * @param $account  支付宝账号
     * @param $realName 姓名
     * @param string $remark 备注
     * @return mixed
	 */
	public function fundTransfer( $module = null ,$totalFee = 0, $outTradeNo = null, $account = null ,$realName = null ,$remark= null, $isModule=false){

		if($this->request->isPost()){
	        $data=$this->request->post();
	        $data = zy_decodeData($data,$isModule);
	        $totalFee = $data['totalFee'];
	        $outTradeNo = $data['outTradeNo'];
	        $account = $data['account'];
	        $realName = $data['realName'];
	        $remark = $data['remark'];
		}

		if( empty($totalFee) || empty($outTradeNo) || empty($account) || empty($realName)  || empty($remark) ){
	        return zy_array(false,'参数不能为空',$result,-1,$isModule);
		}

		if( $module!='fund' ){
			return zy_array(false,'除资金外不可直接调用该方法',null,-1,$isModule);
		}

	    $config_class = new config();
	    $config = $config_class->configInfo();
		$totalFee = 0.1;	//*********
	    
	    $Alipay = new Alipay($config['app_id'],$config['merchant_private_key'],$config['alipay_public_key']);
	    $result = $Alipay->Transfer($totalFee, $outTradeNo, $account,$realName,$remark='');
	    // $result = $Alipay->Transfer('0.1', time().'494456', 15067614058,'叶洋洋',$remark='测试专用');

	    if($result['alipay_fund_trans_toaccount_transfer_response']['code']=="10000"){

			$ali_pay_transfer = [
				'out_trade_no'=>$result['alipay_fund_trans_toaccount_transfer_response']['out_biz_no'],
				'order_id'=>$result['alipay_fund_trans_toaccount_transfer_response']['order_id'],
				'create_time'=>$result['alipay_fund_trans_toaccount_transfer_response']['pay_date'],
				'account'=>$account,
				'name'=>$realName,
				'money'=>$totalFee,
				'remark'=>$remark,
			];
			Db::name('ali_pay_transfer')->insert($ali_pay_transfer);

	        return zy_array(true,'操作成功',$result,200,$isModule);
	    }else{
	        return zy_array(false,'操作失败',$result,-2,$isModule);
	    }

	}








    // ====================== 调用其他模块操作

    /**
     * 站点配置-获取当前的域名
     * @param  [type]  $data     [description]
     */
    private function siteconfiguration_c(){
        $symbol ='ali_pay';
        $id = 'siteconfiguration_one';
        $param = ['isModule'=>true];
        $return = getModuleApiData( $symbol, $id, $param);
        return $return;
    }

    // ====================== 调用其他模块操作




}
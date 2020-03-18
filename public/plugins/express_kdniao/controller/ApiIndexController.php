<?php
namespace plugins\express_kdniao\controller;
/**
 * @Author: user
 * @Date:   2019-03-07 16:21:19
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-20 09:56:01
 */
use cmf\controller\PluginRestBaseController;//引用插件基类
use plugins\express_kdniao\Model\PluginApiIndexModel;
use think\Db;

/**
 * api控制器
 */
class ApiIndexController extends PluginRestBaseController
{
    public function index(){
      return [];
    }


    /**
	 * Json方式 查询订单物流轨迹
	 *	配置参数--$EXInfo
	 *	物流单号--LogisticCode
	 *	快递公司编码--ShipperCode
	 *	订单编号--OrderCode
	 */
	public function getOrderTracesByJson($ShipperCode = null ,$LogisticCode = null ,$OrderCode = null ,$isModule = false){

		if ($this->request->isPost()) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		$ShipperCode = isset( $data['shipper_code'] ) ? $data['shipper_code'] : $ShipperCode ;
        $LogisticCode = isset( $data['logistics_order'] ) ? $data['logistics_order'] : $LogisticCode ;
        $OrderCode = isset( $data['ordersn'] ) ? $data['ordersn'] : $OrderCode ;

		//读取配置文件
        $EXInfo = getModuleConfig('express_kdniao','config','zysystem.json');
        $EXInfo = json_decode($EXInfo,true);

		$requestData= "{'OrderCode':'".$OrderCode."','ShipperCode':'".$ShipperCode."','LogisticCode':'".$LogisticCode."'}";
		// $requestData= "{'OrderCode':'','ShipperCode':'".$shipper_code."','LogisticCode':'".$logistics_order."'}";
		
		$datas = array(
	        'EBusinessID' => $EXInfo["EBusinessID"],
	        'RequestType' => '1002',
	        'RequestData' => urlencode($requestData) ,
	        'DataType' => '2',
	    );
	    $datas['DataSign'] = $this->encrypt($requestData, $EXInfo["AppKey"]);

		$result=$this->sendPost($EXInfo["ReqURL"], $datas);	
		
		return zy_array(true,'成功',$result,200,$isModule);
		// $result = json_decode($result,true);

		// //根据公司业务处理返回的信息......
		// if($result['Success']=='true'){
		// 	return zy_array(true,'成功',$result,200,$isModule);
		// }else{
		// 	return zy_array(false,$result['Reason'],'',-4,$isModule);
		// }
		// dump($result);exit;
		
		
	}
 
	/**
	 *  post提交数据 
	 * @param  string $url 请求Url
	 * @param  array $datas 提交的数据 
	 * @return url响应返回的html
	 */
	private function sendPost($url, $datas) {
	    $temps = array();	
	    foreach ($datas as $key => $value) {
	        $temps[] = sprintf('%s=%s', $key, $value);		
	    }	
	    $post_data = implode('&', $temps);
	    $url_info = parse_url($url);
		if(empty($url_info['port']))
		{
			$url_info['port']=80;	
		}
	    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
	    $httpheader.= "Host:" . $url_info['host'] . "\r\n";
	    $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
	    $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
	    $httpheader.= "Connection:close\r\n\r\n";
	    $httpheader.= $post_data;
	    $fd = fsockopen($url_info['host'], $url_info['port']);
	    fwrite($fd, $httpheader);
	    $gets = "";
		$headerFlag = true;
		while (!feof($fd)) {
			if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
				break;
			}
		}
	    while (!feof($fd)) {
			$gets.= fread($fd, 128);
	    }
	    fclose($fd);  
	    
	    return $gets;
	}

	/**
	 * 电商Sign签名生成
	 * @param data 内容   
	 * @param appkey Appkey
	 * @return DataSign签名
	 */
	private function encrypt($data, $appkey) {
	    return urlencode(base64_encode(md5($data.$appkey)));
	}





	/**
	 * [duqukuaidi 读取快递信息]
	 * @return [type] [description]
	 */
	public function duqukuaidi($isModule=false){

		if ($this->request->isPost()) {
			$data = $this->request->post();
			// $data = zy_decodeData($data,$isModule);
		}

		$zyexpress = Db::name('zyexpress')->select();

		return zy_array(true,'获取成功',$zyexpress,200, $isModule);

	}



	/*
	 * 根据快递编号获取快递信息
	 */
	public function kdInfo($code=null,$isModule=false){

		$data = $this->request->post();

		$zy = Db::name('zyexpress')->where('code',$code)->find();

		return zy_array(true,'获取成功',$zy,200, $isModule);
	}








}
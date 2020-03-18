<?php
namespace plugins\after_sales\controller;
/**
 * @Author: user
 * @Date:   2019-03-07 16:21:19
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-20 09:56:01
 */
use cmf\controller\PluginRestBaseController;//引用插件基类
use plugins\after_sales\Model\PluginApiIndexModel;
use think\Db;

/**
 * api控制器
 */
class ApiIndexController extends PluginRestBaseController
{
    public function index(){
      return [];
    }


    /*
     * 添加售后
     */
	public function addShouhou($uid=null,$order_id=null,$goods_id=null,$reason=null,$money=null,$remark=null,$lx_name=null,$lx_mobile=null,$transaction_id=null,$pay_type=null,$dianpu_name=null,$proof=null,$isModule=false){
		
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
		}
			
		if (empty($uid)) {
			return zy_array(false ,'uid不能为空' ,'' ,-4 ,$isModule);
		}
		if (empty($order_id)) {
			return zy_array(false ,'order_id不能为空' ,'' ,-4 ,$isModule);
		}
		if (empty($goods_id)) {
			return zy_array(false ,'goods_id不能为空' ,'' ,-4 ,$isModule);
		}
		if (empty($reason)) {
			return zy_array(false ,'reason不能为空' ,'' ,-4 ,$isModule);
		}
		if (empty($money)) {
			return zy_array(false ,'money不能为空' ,'' ,-4 ,$isModule);
		}
		if (empty($remark)) {
			return zy_array(false ,'remark不能为空' ,'' ,-4 ,$isModule);
		}
		if (empty($lx_name)) {
			return zy_array(false ,'lx_name不能为空' ,'' ,-4 ,$isModule);
		}
		if (empty($lx_mobile)) {
			return zy_array(false ,'lx_mobile不能为空' ,'' ,-4 ,$isModule);
		}
		if (empty($transaction_id)) {
			return zy_array(false ,'transaction_id不能为空' ,'' ,-4 ,$isModule);
		}
		if (empty($pay_type)) {
			return zy_array(false ,'pay_type不能为空' ,'' ,-4 ,$isModule);
		}
		if (empty($dianpu_name)) {
			return zy_array(false ,'dianpu_name不能为空' ,'' ,-4 ,$isModule);
		}


		
		$da['uid'] = $uid; //用户id
		$da['order_id'] = $order_id; //订单id
		$da['goods_id'] = $goods_id; //商品id
		$da['reason'] = $reason; //退货原因
		$da['money'] = $money; //退货金额
		$da['remark'] = $remark; //退款说明
		$da['time'] = time();
		$da['refund_ordersn'] = time().rand('100000','999999'); //退款订单编号
		$da['status'] = 1; //1.审核中 2.待退货 3.进行中 4.已完成
		$da['lx_name'] = $lx_name; //联系人
		$da['lx_mobile'] = $lx_mobile; //联系电话
		$da['transaction_id'] = $transaction_id; //交易单号
		$da['pay_type'] = $pay_type; //支付类型
		$da['audit_status'] = 0; //审核状态
		$da['dianpu_name'] = $dianpu_name;

		if (isset($proof) && !empty($proof) ) {
			$arr = []; //转换后的图片路径
			foreach ($proof as $key => $value) {
				$imghref = $this->base64_image_content($value);
				$arr[$key] = $imghref;
			}

			$da['proof'] = json_encode($arr); //凭证
		} else {
			$da['proof'] = json_encode(array()); //凭证
		}

		Db::name('after_sales')->insert($da);

		//更改订单状态
		// Db::name('order')->where('id',$data['orderid'])->update(['shstatus'=>4,'shouhou_time'=>time(),'is_shouhou'=>1,'status'=>7]);

		return zy_array(true ,'操作成功' ,null ,200 ,$isModule);
	}







	/**
	 * [base64_image_content base64编码字符串 保存并生成图片链接]
	 * @param  [type] $image [base64编码字符串]
	 * @return [type]        [description]
	 */
	private function base64_image_content($image){

		$sui=mt_rand(1000,9999);
        $imageName = date('YmdHis').$sui.".png"; //设置生成的图片名字

		if (strstr($image,",")){
			$image = explode(',',$image);
			$image = $image[1];
		}

		//获取网站路径
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $wzurl = $http_type.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
        $wzurl= substr($wzurl,0,strrpos($wzurl,'/'));

		$path = "./upload/orderneedtu"; // 设置图片保存路径
		if (!is_dir($path)){ //判断目录是否存在 不存在就创建 并赋予777权限
			mkdir($path,0777,true);
		}

		$imageSrc= $path."/". $imageName; //图片名字

		$r = file_put_contents($imageSrc, base64_decode($image));//返回的是字节数
		if (!$r) {
			return zy_json_echo(error,'图片生成失败',null,-1);
		}else{

			$pic = explode('.',$imageSrc,2)['1'];

			return $pic;
		}
	}







	/**
	 * [newApply 重新申请售后]
	 */
	public function newApply($order_id=null,$reason=null,$remark=null,$proof=null,$isModule=false){
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			// $data = zy_decodeData($data,$isModule);
		}

		if (empty($order_id)) {
			return zy_array(false ,'orderid不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($reason)) {
			return zy_array(false ,'reason不能为空' ,'' ,-1 ,$isModule);
		}

		// if ( !isset($data['orderid']) || empty($data['orderid']) ) {
		// 	return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		// }
		// if ( !isset($data['reason']) || empty($data['reason']) ) {
		// 	return zy_array(false ,'请选择退货原因' ,'' ,-3 ,$isModule);
		// }

		$after = Db::name('after_sales')->where('order_id',$order_id)->find();

		$start=date("Y-m-d",time())." 0:0:0";
        $begintime=strtotime($start);

		if ($after['time'] > $begintime) {
			return zy_array(false ,'当天不能重复申请' ,null ,-8 ,$isModule);
		}



		// $da['uid'] = $order['uid']; //用户id
		$da['order_id'] = $order_id; //订单id
		$da['reason'] = $reason; //退货原因
		$da['remark'] = $remark; //退款说明
		$da['time'] = time();
		$da['status'] = 1; //1.审核中 2.待退货 3.进行中 4.已完成
		$da['audit_status'] = 0; //审核状态

		if ($proof!=null) {
			$arr = []; //转换后的图片路径
			foreach ($proof as $key => $value) {
				$imghref = $this->base64_image_content($value);
				$arr[$key] = $imghref;
			}

			$da['proof'] = json_encode($arr); //凭证
		} else {
			$da['proof'] = json_encode(array()); //凭证
		}
		
		Db::name('after_sales')->where('order_id',$order_id)->update($da);

		//更改订单状态
		// Db::name('order')->where('id',$data['orderid'])->update(['shstatus'=>4,'shouhou_time'=>time(),'is_shouhou'=>1,'status'=>7]);

		return zy_array(true ,'操作成功' ,null ,200 ,$isModule);

	}











	/**
	 * [shouhouList 售后中心列表]
	 * @param  [type]  $uid      	[用户id]
	 * @param  [type]  $status      [状态] 1.审核中 2.待退货 3.进行中 4.已完成
	 * @param  boolean $isModule [description]
	 * @return [type]            [description]
	 */
	public function shouhouList($isModule=false){

		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['uid']) || empty($data['uid']) ) {
			return zy_array(false ,'uid未正确传参' ,'' ,-1 ,$isModule);
		}
		$arr = [1,2,3,4];
		if ( !isset($data['status']) || empty($data['status']) || !in_array($data['status'],$arr) ) {
			return zy_array(false ,'status未正确传参' ,'' ,-1 ,$isModule);
		}
		if ( isset($data['pageNum']) && !empty($data['pageNum']) ){
			$yeshu = $data['pageNum'];
		} else {
			$yeshu = 10;
		}

		$where = 1;
		//商品名称搜索
		if ( isset($data['keyword']) && !empty($data['keyword']) ){
			//取出所有订单id
			$alldd_id = Db::name('after_sales')->where('uid',$data['uid'])->where('status',$data['status'])->select();
			$dd_id = [];
			foreach ($alldd_id as $key => $value) {
				$dd_id[$key] = $value['order_id'];
			}
			$g = Db::name('order_goods')->where('order_id','in',$dd_id)->where('goods_name','like',"%".$data['keyword']."%")->group('order_id')->select();

			$dingdan_id = '';
			foreach ($g as $key => $value) {
				$dingdan_id .= $value['order_id'].',';
			}

			if (strlen($dingdan_id)==0) {
				$where .= " and order_id = 0";
			}else{
				$dingdan_id = substr($dingdan_id,0,strlen($dingdan_id)-1);
				$where .= " and order_id in (".$dingdan_id.")";
			}
		}

		$shouhou = Db::name('after_sales')->where('uid',$data['uid'])->where('status',$data['status'])->order('time','desc')->where($where)->paginate($yeshu,false,['page'=>$data['page']]);

		if (count($shouhou)==0) {
			$shouhou = Db::name('after_sales')->where('id',0)->select();
			return zy_array(true ,'操作成功' ,$shouhou ,200 ,$isModule);
		}

		$currentPage = $shouhou->currentPage();
		$lastPage = $shouhou->lastPage();
		$listRows = $shouhou->listRows();
		$total = $shouhou->total();

		//读取商品
		foreach ($shouhou as $key => $value) {
			// $shangpin_id = explode(',',$value['goods_id']);
			$da[$key]['dianpu_name'] = $value['dianpu_name'];
			$da[$key]['order_id'] = $value['order_id'];
			$da[$key]['status'] = $value['status'];
			$da[$key]['audit_status'] = $value['audit_status'];


			$da[$key]['currentPage'] = $currentPage; //当前页
			$da[$key]['lastPage'] = $lastPage; //总页数
			$da[$key]['listRows'] = $listRows; //每页数量
			$da[$key]['total'] = $total; //总条数


			$symbol = 'after_sales';
        
		    $id = 'readaftersalesinfo';

		    $arr = ['order_id'=>$value['order_id'],'goods_id'=>$value['goods_id'],'isModule'=>true];

		    //获取售后商品信息
		    $Info = getModuleApiData($symbol,$id,$arr);

		    if ($Info['status']=='success' && isset($Info['data'])) {
		    	$goods = $Info['data'];
		    } else {
		    	$goods = array();
		    }
 

			// $goods = Db::name('order_goods')->field('goods_id,goods_name,goods_num,goods_img,goods_price,specid_name')->where('order_id',$value['order_id'])->where('goods_id','in',$shangpin_id)->select();
			$da[$key]['shop'] = $goods;
		}

		return zy_array(true ,'操作成功' ,$da ,200 ,$isModule);
	}








	/**
	 * [shouhouDetail 售后详情]
	 * @return [type] [description]
	 */
	public function shouhouDetail($isModule=false){
		
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		}
		//售后订单
		$after = Db::name('after_sales')->where('order_id',$data['orderid'])->find();

		//商品
		$shangpin_id = explode(',',$after['goods_id']);
		$goods = Db::name('order_goods')->where('order_id',$data['orderid'])->where('goods_id','in',$shangpin_id)->field('goods_id,goods_name,goods_num,goods_img,goods_price,specid_name')->select();

		$da['status'] = $after['status'];
		$da['dianpu_num'] = $after['dianpu_name'];
		$da['remark'] = $after['remark']; //退款留言
		$da['time'] = date("Y-m-d H:i:s",$after['time']); //申请时间
		$da['ordersn'] = $after['refund_ordersn']; //订单编号
		$da['type'] = '仅退款'; //服务类型
		$da['fangshi'] = '原返'; //退款方式
		$da['reason'] = $after['reason']; //退款理由
		$da['lx_name'] = $after['lx_name']; //联系人
		$da['lx_mobile'] = $after['lx_mobile']; //联系电话
		$da['shop'] = $goods;



		$symbol = 'after_sales';
        
        $id = 'readconfiginfo';

        $arr = ['data'=>null,'isModule'=>true];

        //调用配置接口
        $web_config = getModuleApiData($symbol,$id,$arr);

        $url = $web_config['data']['basic']['site_domain'];

		$da['proof'] = json_decode($after['proof']); //凭证

		if (!empty($da['proof'])) {
			foreach ($da['proof'] as $key => $value) {
				$da['proof'][$key] = $url.$value;
			}
		}


		$da['reason'] = $after['reason']; //退款理由
		$da['audit_status'] = $after['audit_status']; //审核状态
		$da['refuse_cause'] = $after['refuse_cause']; //拒绝理由

		return zy_array(true ,'操作成功' ,$da ,200 ,$isModule);
	}







	/**
	 * [tuihuoNum 售后买家发货]
	 * @param  [type]  $orderid         [订单id]
	 * @param  [type]  $company         [快递名称]
	 * @param  [type]  $code            [快递公司编号]
	 * @param  [type]  $logistics_order [物流单号]
	 * @param  boolean $isModule        [description]
	 * @return [type]                   [description]
	 */
	public function tuihuoNum($isModule=false){

		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		}
		if ( !isset($data['code']) || empty($data['code']) ) {
			return zy_array(false ,'code未正确传参' ,'' ,-3 ,$isModule);
		}
		if ( !isset($data['logistics_order']) || empty($data['logistics_order']) ) {
			return zy_array(false ,'logistics_order未正确传参' ,'' ,-4 ,$isModule);
		}

		$symbol = 'after_sales';
        $id = 'kdInfo';
        $arr = ['code'=>$data['code'],'isModule'=>true];
        $info = getModuleApiData($symbol,$id,$arr); //获取快递公司信息

        $zy = $info['data'];

		$da['shipper_name'] = $zy['company'];
		$da['shipper_code'] = $data['code'];
		$da['logistics_order'] = $data['logistics_order'];
		$da['fhtime'] = time();
		$da['status'] = 3;

		$after = Db::name('after_sales')->where('order_id',$data['orderid'])->update($da);

		return zy_array(true ,'发货成功' ,null ,200 ,$isModule);
	}





	/*
	 * 售后原因
	 */
	public function reason($isModule=false)
	{
		$after = Db::name('after_set')->field('name')->where('status',1)->select();

		return zy_array(true ,'获取成功' ,$after ,200 ,$isModule);
	}




	/*
     * 店铺售后列表
     */
    public function storeAfterList($isModule=false)
    {
		$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (!isset($data['uid']) || empty($data['uid'])) {
			return zy_array(false ,'uid不能为空' ,'' ,-1 ,$isModule);
		}
		//1.待审核 2.待确认 3.已完成
		$arr = [1,3,4,5];
		if ( !isset($data['leixing']) || empty($data['leixing']) || !in_array($data['leixing'],$arr) ) {
			return zy_array(false ,'leixing必须在1,3,4,5内数字' ,'' ,-1 ,$isModule);
		}

		if (!isset($data['page']) || empty($data['page'])) {
			$page = 1;
		} else {
			$page = $data['page'];
		}
		
		if ( isset($data['pageNum']) && !empty($data['pageNum']) ){
			$pageNum = $data['pageNum'];
		} else {
			$pageNum = 10; //默认每页显示10条
		}

		$where = 1;
		switch ($data['leixing']) {
			case 1:
				$where .= " and status = 1";
				break;
			case 3:
				$where .= " and status = 3";
				break;
			case 4:
				$where .= " and status = 4";
				break;
			case 5:
				$where .= " and status in (1,3,4)";
		}

		

		//添加搜索条件(商品名称搜索)
		if (isset($data['keyword']) && !empty($data['keyword'])) {
			$sales = Db::name('after_sales')->where('uid',$data['uid'])->select();

			//取出所有售后订单的商品
			$shuz = [];
			foreach ($sales as $key => $value) {
				$value['goods_id'] = explode(',',$value['goods_id']);
				$shuz = array_merge($shuz,$value['goods_id']); //合并数组
			}

			// dump($shuz);

			//先取出店铺下所有订单对应的商品
			$od_s = Db::name('order_goods')->where('goods_id','in',$shuz)
				->where('goods_name','like','%'.$data['keyword'].'%')
				->group('order_id')
				->column('order_id');
			$str = !empty($od_s)?implode(',',$od_s):0;
			$where .= " and order_id in ({$str})";
		}

		$shouhou = Db::name('after_sales')->order('time','desc')->where('uid',$data['uid'])->where($where)->paginate($pageNum,false,['page'=>$page]);

		if (count($shouhou)==0) {
			$shouhou = Db::name('after_sales')->where('id',0)->select();
			return zy_array(true ,'操作成功' ,$shouhou ,200 ,$isModule);
		}

		$currentPage = $shouhou->currentPage();
		$lastPage = $shouhou->lastPage();
		$listRows = $shouhou->listRows();
		$total = $shouhou->total();


		//读取商品
		foreach ($shouhou as $key => $value) {
			// $shangpin_id = explode(',',$value['goods_id']);
			$da[$key]['dianpu_name'] = $value['dianpu_name'];
			$da[$key]['order_id'] = $value['order_id'];
			$da[$key]['status'] = $value['status'];
			$da[$key]['audit_status'] = $value['audit_status'];


			$da[$key]['currentPage'] = $currentPage; //当前页
			$da[$key]['lastPage'] = $lastPage; //总页数
			$da[$key]['listRows'] = $listRows; //每页数量
			$da[$key]['total'] = $total; //总条数

			$symbol = 'after_sales';
        
		    $id = 'readaftersalesinfo';

		    $arr = ['order_id'=>$value['order_id'],'goods_id'=>$value['goods_id'],'isModule'=>true];

		    //获取售后商品信息
		    $Info = getModuleApiData($symbol,$id,$arr);

		    if ($Info['status']=='success' && isset($Info['data'])) {
		    	$goods = $Info['data'];
		    } else {
		    	$goods = array();
		    }

			// $goods = Db::name('order_goods')->field('goods_id,goods_name,goods_num,goods_img,goods_price,specid_name')->where('order_id',$value['order_id'])->where('goods_id','in',$shangpin_id)->select();
			$da[$key]['shop'] = $goods;
		}

		return zy_array(true ,'操作成功' ,$da ,200 ,$isModule);
    }	




    /*
     * 同意审核
     */
    public function saletongguo($isModule=false)
    {
    	$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (!isset($data['orderid']) || empty($data['orderid'])) {
			return zy_array(false ,'orderid不能为空' ,'' ,-1 ,$isModule);
		}

		$aa = Db::name('after_sales')->where('order_id',$data['orderid'])->update(['status'=>2,'audit_status'=>1]);

		if ($aa) {
			return zy_array(true ,'操作成功' ,null ,200 ,$isModule);
		} else {
			return zy_array(false ,'操作失败' ,null ,-1 ,$isModule);
		}
    }




    /*
     * 拒绝审核
     */
    public function salesjujue($isModule=false)
    {	
    	$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (!isset($data['orderid']) || empty($data['orderid'])) {
			return zy_array(false ,'orderid不能为空' ,'' ,-1 ,$isModule);
		}

		//拒绝原因
		$yuanyin = isset($data['yuanyin']) ? $data['yuanyin'] : '';

    	Db::name('order')->where('id',$data['orderid'])->update(['shstatus'=>0,'is_shouhou'=>0]);

        Db::name('after_sales')->where('order_id',$data['orderid'])->update(['status'=>4,'audit_status'=>2,'refuse_cause'=>$yuanyin]);

        return zy_array(true ,'操作成功' ,null ,200 ,$isModule);
    }








    /*
     * 确认退款
     */
    public function querenRefund($isModule=false)
    {	
    	$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (!isset($data['orderid']) || empty($data['orderid'])) {
			return zy_array(false ,'orderid不能为空' ,'' ,-1 ,$isModule);
		}

		$sales = Db::name('after_sales')->where('order_id',$data['orderid'])->find();

		switch ($sales['pay_type']) {
			case 1: //支付宝
                $symbol = 'after_sales';
                $id = 'zhifubaorefund';
                $arr = ['transaction_id'=>$sales['transaction_id'],'out_trade_no'=>'','refund_amount'=>$sales['money'],'refund_reason'=>'订单退款','out_request_no'=>$sales['refund_ordersn'],'isModule'=>true];

                $refund = getModuleApiData($symbol,$id,$arr);

                if ($refund['status'] == 'error') {
                    return zy_array(false ,'操作失败' ,$refund['data']['sub_msg'] ,200 ,$isModule);
                }

                break;
                case 2: //微信
                    $symbol = 'after_sales';
                    $id = 'wechatrefund';
                    $arr = ['transaction_id'=>$sales['transaction_id'],'out_trade_no'=>'','total_fee'=>$sales['money'],'refund_fee'=>$sales['money'],'isModule'=>true];
                    $refund = getModuleApiData($symbol,$id,$arr);

                    if ($refund['status']=='error') {
                    	return zy_array(false ,'操作失败' ,$refund['data']['err_code_des'] ,200 ,$isModule);
                    }
                    break;
                case 3: //银行卡
                        
                    break;
                case 4: //余额

                    $symbol = 'after_sales';
                    $id = 'balancerefund';
                    $arr = ['order_number'=>$sales['transaction_id'],'money'=>$sales['money'],'isModule'=>true];
                    $refund = getModuleApiData($symbol,$id,$arr);
                    if ($refund['status']=='error') {
                    	return zy_array(false ,'操作失败' ,$refund['message'] ,200 ,$isModule);
                    }
                    break;
		}


		Db::name('after_sales')->where('order_id',$data['orderid'])->update(['status'=>4,'tksucc_time'=>time()]);

		Db::name('order')->where('id',$data['orderid'])->update(['is_shouhou'=>2]);

        return zy_array(true ,'操作成功' ,null ,200 ,$isModule);        
    }
















    


}
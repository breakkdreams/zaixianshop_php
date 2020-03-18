<?php
namespace plugins\order\controller;
/**
 * @Author: user
 * @Date:   2019-03-07 16:21:19
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-20 09:56:01
 */
use cmf\controller\PluginRestBaseController;//引用插件基类
use plugins\demo\Model\PluginApiIndexModel;
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
   	 * [sureMakeOrder 提交订单]
   	 * @param  [type]  $uid      [用户id]
   	 * @param  [type]  $city_id  [地址id]
   	 * @param  [type]  $cids  	 [店铺群，分隔]
   	 * @param  [type]  $liuyan   [留言]
   	 * @param  boolean $isModule [description]
   	 * @return [type]            [description] $uid,$city_id,$cids,$liuyan=null,
   	 */
	public function sureMakeOrder($isModule=false){

		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		//立即购买确认订单
		if ( isset($data['uid']) && isset($data['city_id']) && isset($data['gid']) && isset($data['specid']) && isset($data['num']) ) {

			if ( empty($data['uid']) ) {
				return zy_array(false ,'uid不能为空' ,'' ,-1 ,$isModule);
			}
			if ( empty($data['city_id']) ) {
				return zy_array(false ,'city_id不能为空' ,'' ,-1 ,$isModule);
			}
			if ( empty($data['gid']) ) {
				return zy_array(false ,'gid不能为空' ,'' ,-1 ,$isModule);
			}
			if ( empty($data['num']) ) {
				return zy_array(false ,'num不能为空' ,'' ,-1 ,$isModule);
			}
			if ( isset($data['liuyan']) && !empty($data['liuyan']) ) {
				$liuyan = $data['liuyan'];
			} else {
				$liuyan = '';
			}

			$this->querenmai($data['uid'],$data['city_id'],$data['gid'],$data['specid'],$data['num'],$liuyan,$isModule);

		}


		if ( !isset($data['uid']) || empty($data['uid']) ) {
			return zy_array(false ,'uid未正确传参' ,'' ,-1 ,$isModule);
		}
		if ( !isset($data['city_id']) || empty($data['city_id']) ) {
			return zy_array(false ,'city_id未正确传参' ,'' ,-2 ,$isModule);
		}
		if ( !isset($data['cids']) || empty($data['cids']) ) {
			return zy_array(false ,'cids未正确传参' ,'' ,-2 ,$isModule);
		}


		$symbol = 'order';
        $id = 'readAddress_one';
        $arr = ['data'=>['uid'=>$data['uid']],'isModule'=>true];
        //获取用户地址
        $dizhi = getModuleApiData($symbol,$id,$arr);
        if ($dizhi['status'] == 'error') {
        	return zy_array(false ,'未设置地址' ,null ,-6 ,$isModule);
        } else {
        	$mrdz = []; //去除对应id的地址
	        foreach ($dizhi['data'] as $key => $value) {
	        	if ($value['id'] != $data['city_id']) {
	        		continue;
	        	}
	        	$mrdz = $value;
	        }
	        if (empty($mrdz)) {
	        	return zy_array(false ,'未选择地址' ,null ,-6 ,$isModule);
	        }
        }

        $dizhi = $mrdz['show_address'];


        //获取会员个人信息
        $sym1 = 'order';
        $id1 = 'readmemberinfo';
        $arr1 = ['uid'=>$data['uid'],'field'=>'nickname,avatar','isModule'=>true];
        $memberinfo = getModuleApiData($sym1,$id1,$arr1);
        $nickname = $memberinfo['data']['nickname']; //用户昵称


        //获取商品信息
        $symbol = 'order';
        $id = 'lookShopInfo_one';
        $arr = ['uid'=>$data['uid'],'cids'=>$data['cids'],'isModule'=>true];
        $shop = getModuleApiData($symbol,$id,$arr);

        $ord_id = '';	//组装订单id 返回值
        $ordersn = ''; //组装订单编号	 返回值

        foreach ($shop['data']['data']['shops'] as $key => $value) {

	        //订单表
			$order_biao = [
				'uid' => $data['uid'], //用户id
				'ordersn' => time().rand('100000','999999'), //订单编号
				'storeid' => $value['shopid'], //店铺id
				'buycarid' => '0', //购物车id
				'status' => 1, //待支付
				'lx_mobile' => $mrdz['receive_phone'], //联系电话
				'lx_name' => $mrdz['receive_name'], //联系人
				'lx_code' => $mrdz['postal_code'], //邮编
				'province' => $dizhi[0], //收货地址——省
				'city' => $dizhi[1], //收货地址——市
				'area' => $dizhi[2], //收货地址——区
				'area_code' => $mrdz['cri_code'], //地区编码
				'address' => $mrdz['address'], //收货地址——详细地址
				'addtime' => time(), //添加时间
				'freeship' => 0, //免邮
				'freight' => 0, //运费
				'totalprice' => $value['stprice'], //总价
				'dianpu_name' => $value['shopname'],
				'usernote' => isset($data['liuyan'])?$data['liuyan']:'',
				'user_name' => $nickname,
				'leixing' => isset($data['leixing'])?$data['leixing']:'', //订单类型 1.商品 2.拼团 3.砍价
			];

			$order_id = Db::name('order')->insertGetId($order_biao);

			$ord_id .= $order_id.",";
			$ordersn .= $order_biao['ordersn'].",";

			foreach ($value['cartinfo'] as $k => $v) {
				$order_goods = [
		        	'uid' => $data['uid'], //用户id
		        	'order_id' => $order_id, //关联订单id
		        	'goods_id' => $v['goodsid'], //商品id
		        	'goods_name' => $v['goodsname'], //商品名称
		        	'goods_num' => $v['cartnum'], //购买数量
		        	'goods_img' => $v['goodsimg'], //商品图片
		        	'final_price' => '0', //最终价格
		        	'goods_price' => $v['goodsprice'], //商品实际价格
		        	'specid' => $v['goodsspec'], //规格参数
		        	'specid_name' => $v['goodsspecs'], //规格对应中文
		        	'is_comment' => '0', //是否评价 1.评 0.未评
		        ];

		        Db::name('order_goods')->insert($order_goods);
			}
        }

        $ord_id = substr($ord_id,0,strlen($ord_id)-1);
		$ordersn = substr($ordersn,0,strlen($ordersn)-1);

		$d['order_id'] = $ord_id;
		$d['ordersn'] = $ordersn;

        return zy_array(true ,'下单成功' ,$d ,200 ,$isModule);

	}




	/*
	 * 立即购买确认订单
	 */
	private function querenmai($uid,$city_id,$gid,$specid,$num,$liuyan='',$isModule=false){

		$symbol = 'order';
        
        $id = 'lijigoumai_one';

        $arr = ['uid'=>$uid,'gid'=>$gid,'specid'=>$specid,'num'=>$num,'isModule'=>true];
        //获取商品信息
        $shop = getModuleApiData($symbol,$id,$arr);

        $shop = $shop['data'];

        $symbol = 'order';
        $id = 'readAddress_one';
        $arr = ['data'=>['uid'=>$uid],'isModule'=>true];
        //获取用户地址
        $dizhi = getModuleApiData($symbol,$id,$arr);
        if ($dizhi['status'] == 'error') {
        	return zy_array(false ,'未设置地址' ,null ,-6 ,$isModule);
        } else {
        	$mrdz = []; //去除对应id的地址
	        foreach ($dizhi['data'] as $key => $value) {
	        	if ($value['id'] != $city_id) {
	        		continue;
	        	}
	        	$mrdz = $value;
	        }
	        if (empty($mrdz)) {
	        	return zy_array(false ,'未选择地址' ,null ,-6 ,$isModule);
	        }
        }
        $dizhi = $mrdz['show_address'];


        //获取会员个人信息
        $sym1 = 'order';
        $id1 = 'readmemberinfo';
        $arr1 = ['uid'=>$uid,'field'=>'nickname,avatar','isModule'=>true];
        $memberinfo = getModuleApiData($sym1,$id1,$arr1);
        $nickname = $memberinfo['data']['nickname']; //用户昵称

        //订单表
		$order_biao = [
			'uid' => $uid, //用户id
			'ordersn' => time().rand('100000','999999'), //订单编号
			'storeid' => $shop['data']['shops'][0]['shopid'], //店铺id
			'buycarid' => '0', //购物车id
			'status' => 1, //待支付
			'lx_mobile' => $mrdz['receive_phone'], //联系电话
			'lx_name' => $mrdz['receive_name'], //联系人
			'lx_code' => $mrdz['postal_code'], //邮编
			'province' => $dizhi[0], //收货地址——省
			'city' => $dizhi[1], //收货地址——市
			'area' => $dizhi[2], //收货地址——区
			'area_code' => $mrdz['cri_code'], //地区编码
			'address' => $mrdz['address'], //收货地址——详细地址
			'addtime' => time(), //添加时间
			'freeship' => 0, //免邮
			'freight' => 0, //运费
			'totalprice' =>  $shop['data']['shops'][0]['stprice'], //总价
			'dianpu_name' => $shop['data']['shops'][0]['shopname'],
			'usernote' => $liuyan,
			'user_name' => $nickname,
			'leixing' => isset($data['leixing'])?$data['leixing']:'', //订单类型 1.商品 2.拼团 3.砍价
		];

		$order_id = Db::name('order')->insertGetId($order_biao);

		foreach ($shop['data']['shops'][0]['cartinfo'] as $k => $v) {
			$order_goods = [
	        	'uid' => $uid, //用户id
	        	'order_id' => $order_id, //关联订单id
	        	'goods_id' => $v['goodsid'], //商品id
	        	'goods_name' => $v['goodsname'], //商品名称
	        	'goods_num' => $v['cartnum'], //购买数量
	        	'goods_img' => $v['goodsimg'], //商品图片
	        	'final_price' => '0', //最终价格
	        	'goods_price' => $v['goodsprice'], //商品实际价格
	        	'specid' => $v['goodsspec'], //规格参数
	        	'specid_name' => $v['goodsspecs'], //规格对应中文
	        	'is_comment' => '0', //是否评价 1.评 0.未评
	        ];

	        Db::name('order_goods')->insert($order_goods);
		}

		$d['order_id'] = $order_id;
		$d['ordersn'] = $order_biao['ordersn'];


		return zy_array(true ,'下单成功' ,$d ,200 ,$isModule);
	}





	/**
	 * [$sts 订单状态]
	 * @var 1    全部订单
	 * @var 2    待付款
	 * @var 3    待发货
	 * @var 4    待收货
	 * @var 5    待评价
	 */
	protected $sts = [1,2,3,4,5];

	/**
	 * [allOrder 订单列表全部]
	 * @param  [type]  $uid      [用户id]
	 * @param  [type]  $status   [状态] 1.查全部 2.代付款 2.待发货 4.待收货 5.待评价
	 * @param  [type]  $keyword  [商品名称搜索]
	 * @param  [type]  $page     [当前页]
	 * @param  [type]  $pageNum  [每页数量]
	 * @param  boolean $isModule [description]
	 * @return [type]            [description]
	 */
	public function allOrder($uid=null,$status=null,$keyword=null,$page=null,$pageNum=null,$isModule=false){
		//$uid,$status,$keyword=null,$page=null,$pageNum=null,
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['uid']) || empty($data['uid']) ) {
			return zy_array(false ,'uid未正确传参' ,null ,-3 ,$isModule);
		}
		if ( !isset($data['status']) || empty($data['status']) || !in_array($data['status'],$this->sts) ) {
			return zy_array(false ,'status未正确传参' ,null ,-4 ,$isModule);
		}
		if ( isset($data['pageNum']) && !empty($data['pageNum']) ){
			$yeshu = $data['pageNum'];
		} else {
			$yeshu = 10; //默认为每页十条
		}

		switch ($data['status']) {
			case 1:
				$where = 1;
				break;
			case 2:
				$where = 'status=1'; //待付款
				break;
			case 3:
				$where = 'status=2'; //待发货
				break;
			case 4:
				$where = 'status=3'; //待收货
				break;
			case 5:
				$where = 'status=4'; //待评价
				break;
		}

		//商品名称搜索
		if ( isset($data['keyword']) && !empty($data['keyword']) ){

			// 待完善 去掉取出所有订单  提交订单时 order_goods表中添加uid
			// $g = Db::name('order_goods')->where('uid',$data['uid'])->where('goods_name','like',"%".$data['keyword']."%")->group('order_id')->select();

			//取出所有订单id     
			$alldd_id = Db::name('order')->where('uid',$data['uid'])->where('status in (1,2,3,4)')->where('is_del',2)->select();
			$dd_id = [];
			foreach ($alldd_id as $key => $value) {
				$dd_id[$key] = $value['id'];
			}
			$g = Db::name('order_goods')->where('order_id','in',$dd_id)->where('goods_name','like',"%".$data['keyword']."%")->group('order_id')->select();

			$dingdan_id = '';
			foreach ($g as $key => $value) {
				$dingdan_id .= $value['order_id'].',';

			}
			if (strlen($dingdan_id)==0) {
				$where .= " and id = 0";
			}else{
				$dingdan_id = substr($dingdan_id,0,strlen($dingdan_id)-1);
				$where .= " and id in (".$dingdan_id.")";
			}
		}

		//读配置
        $time_config = getModuleConfig('order','config','time_config.json');
        $time_config = json_decode($time_config,true);
        $pingjia = $time_config['pingjia']; //评价保留时间

		//去除收货7天后的订单
		$seven7day = strtotime('-'.$pingjia.'day');
		//去除掉超过待评价的订单
		$qcdds = Db::name('order')->where('uid',$data['uid'])->where('status',4)->where('is_del',2)->where('shouhuo_time','<',$seven7day)->column('id');
		// $qcdds = [];

		// foreach ($qcd as $key => $value) {
		// 	$qcdds[$key] = $value['id'];
		// }

		$where .= " and status in (1,2,3,4,6)";
		$where .= " and is_del = 2"; //非删除
		$where .= " and uid = ".$data['uid']; //用户ID

		$order = Db::name('order')->field('id,status,addtime,totalprice,remind,is_shouhou,is_pingjia,dianpu_name')->where($where)->where('id','not in',$qcdds)->order('id','desc')->paginate($yeshu,false,['page'=>$data['page']]);


		if (count($order)==0) {
			$order = Db::name('order')->where('id',0)->select();
			return zy_array(true ,'操作成功' ,$order ,200 ,$isModule);
		}


		$currentPage = $order->currentPage();
		$lastPage = $order->lastPage();
		$listRows = $order->listRows();
		$total = $order->total();

		$da = [];
		foreach ($order as $key => $value) {

			// $da['dianpu_name'] = $value['dianpu_name']; //店铺名字
			// $da['dianpu_name'] = $value['dianpu_name']; //店铺名字
			// $da['addtime'] = date('Y-m-d H:i:s',$value['addtime']); //下单时间

			// $da['currentPage'] = $currentPage; //当前页
			// $da['lastPage'] = $lastPage; //总页数
			// $da['listRows'] = $listRows; //每页数量
			// $da['total'] = $total; //总条数

			// $da['data'][$key]['status'] = $value['status'];

			$value['dianpu_name'] = $value['dianpu_name']; //店铺名字
			$value['addtime'] = date('Y-m-d H:i:s',$value['addtime']); //下单时间

			$value['currentPage'] = $currentPage; //当前页
			$value['lastPage'] = $lastPage; //总页数
			$value['listRows'] = $listRows; //每页数量
			$value['total'] = $total; //总条数


			$goods = Db::name('order_goods')->field('goods_id,goods_name,goods_num,goods_img,goods_price,specid_name')->where('order_id',$value['id'])->select()->toArray();

			// $da['shop'] = $goods;

			// foreach ($goods as $k => $v) {
			// 	$value['shop'][$k] = $v;
			// }
			$value['shop'] = $goods;
			$order[$key] = $value;
		}

		// $order = $order->toArray();

		return zy_array(true ,'操作成功' ,$order->items() ,200 ,$isModule);
	}




	



	/**
	 * [orderDetail 订单详情]
	 * @param  [type]  $orderid   [description]
	 * @param  boolean $isModule [description]
	 * @return [type]            [description]
	 */
	public function orderDetail($isModule=false){
		
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-4 ,$isModule);
		}
		
		$order = Db::name('order')->where('id',$data['orderid'])->find();

		if (empty($order)) {
			return zy_array(false ,'请传入有效订单id' ,'' ,-5 ,$isModule);
		}

		$da['dianpu_name'] = $order['dianpu_name']; //店铺名称

		$da['ordersn'] = $order['ordersn']; //订单编号

		$da['order_id'] = $data['orderid']; //订单id

		$da['is_pingjia'] = $order['is_pingjia']; //是否评价

		$da['remind'] = $order['remind']; //是否提醒

		$da['is_shouhou'] = $order['is_shouhou']; //是否售后

		$da['addtime'] = !empty($order['addtime'])?date('Y-m-d H:i:s',$order['addtime']):''; //下单时间

		$da['paytime'] = !empty($order['paytime'])?date('Y-m-d H:i:s',$order['paytime']):''; //支付时间

		$da['shipper_name'] = $order['shipper_name']; //配送方式

		$da['status'] = $order['status']; //订单状态

		$da['freeship'] = $order['freeship']; //是否免邮 1.免邮 2.不免邮

		$da['freight'] = $order['freight']; //运费

		switch ($order['pay_type']) {
			case 1:
				$da['pay_type'] = '支付宝支付';
				break;
			case 2:
				$da['pay_type'] = '微信支付';
				break;
			case 3:
				$da['pay_type'] = '银行卡支付';
				break;
			case 4:
				$da['pay_type'] = '余额支付';
				break;
		}

		$goods = Db::name('order_goods')->field('goods_id,goods_name,goods_num,goods_img,goods_price,specid_name')->where('order_id',$data['orderid'])->select();

		foreach ($goods as $key => $value) {
			$da['shop'][$key] = $value;
		}

		return zy_array(true ,'操作成功' ,$da ,200 ,$isModule);
	}



	/**
	 * [cancelOrder 取消订单]
	 * @param  [type] $orderid [订单id]
	 * @return [type] [description]
	 */
	public function orderCancel($isModule=false){

		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-4 ,$isModule);
		}

		$od['status'] = 6;
		$upd = Db::name('order')->where('id',$data['orderid'])->update($od);

		return zy_array(true ,'操作成功' ,'' ,200 ,$isModule);
	}

 

	/**
	 * [jiesuanInfo 结算信息]
	 * @param  [type]  $orderid  [订单id]
	 * @param  boolean $isModule [description]
	 * @return [type]            [description]
	 */
	public function jiesuanInfo($isModule=false){

		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-4 ,$isModule);
		}

		$ord_id = explode(',',$data['orderid']);

		foreach ($ord_id as $key => $value) {
			$order = Db::name('order')->where('id',$value)->find();

			$da['dingdan'][$key]['storeid'] = $order['storeid']; //店铺id
			$da['dingdan'][$key]['dianpu_name'] = $order['dianpu_name'];
			$da['dingdan'][$key]['ordersn'] = $order['ordersn'];
			$da['dingdan'][$key]['status'] = $order['status'];

			$da['lx_name'] = $order['lx_name']; //收货人姓名
			$da['lx_mobile'] = $order['lx_mobile']; //联系电话
			$da['province'] = $order['province']; //省
			$da['city'] = $order['city']; //市
			$da['area'] = $order['area']; //区、县
			$da['address'] = $order['address']; //详细地址
			$da['freight'] = $order['freight']; //运费
			$da['dingdan'][$key]['usernote'] = $order['usernote']; //留言
			$da['dingdan'][$key]['totalprice'] = $order['totalprice']; //总价
			
			$da['dingdan'][$key]['order_id'] = $data['orderid']; //订单id

		 	$goods = Db::name('order_goods')->field('goods_id,goods_name,goods_num,goods_img,goods_price,specid_name')->where('order_id',$value)->select();

			$da['dingdan'][$key]['shopnum'] = 0;
			foreach ($goods as $k => $v) {
				$da['dingdan'][$key]['shop'][$k] = $v;
				$da['dingdan'][$key]['shopnum'] += $v['goods_num'];
			}

		}
		
		if (!empty($order)) {
			return zy_array(true ,'成功' ,$da ,200 ,$isModule);
		} else {
			return zy_array(false ,'未找到订单' ,null ,-3 ,$isModule);
		}
	}





	/**
	 * [pay 付款]
	 * @param  [type] $orderid [订单id]
	 * @param  [type] $type    [付款方式] 1.余额 2.支付宝 3.微信
	 * @return [type]          [description]
	 */
	public function pay($isModule=false){
		
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-4 ,$isModule);
		}
		$arr = [1,2,3];
		if ( !isset($data['type']) || empty($data['type']) || !in_array($data['type'],$arr)) {
			return zy_array(false ,'type未正确传参' ,'' ,-4 ,$isModule);
		}

		$ord = explode(',', $data['orderid']);

		foreach ($ord as $key => $value) {
			$da['status'] = 2; //状态改为待发货
			$da['paytime'] = time(); 
			$da['pay_type'] = $data['type']; //支付类型

			Db::name('order')->where('id',$value)->update($da);
		}

		

		return zy_array(true ,'付款成功' ,'' ,200 ,$isModule);
	}




    /**
     * [wechat_notify 更改订单状态]
     * @param  [type]  $out_trade_no   [商户单号]
     * @param  [type]  $transaction_id [交易单号]
     * @param  [type]  $pay_type       [支付类型]
     * @param  boolean $isModule       [description]
     * @return [type]                  [description]
     */
    public function wechat_notify($out_trade_no,$transaction_id,$pay_type,$isModule=false){

		$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		$out_trade_no = isset( $data['out_trade_no'] ) ? $data['out_trade_no'] : $out_trade_no ;
        $transaction_id = isset( $data['transaction_id'] ) ? $data['transaction_id'] : $transaction_id ;
        $pay_type = isset( $data['pay_type'] ) ? $data['pay_type'] : $pay_type ;

        if(empty($out_trade_no)){
            return zy_array (false,'out_trade_no不能为空','',-1 ,$isModule);
        }
        if(empty($transaction_id)){
            return zy_array (false,'transaction_id不能为空','',-1 ,$isModule);
        }
        if(empty($pay_type)){
            return zy_array (false,'pay_type不能为空','',-1 ,$isModule);
        }

        $da['status'] = 2; //状态改为待发货
		$da['paytime'] = time(); 
		$da['pay_type'] = $pay_type; //支付类型
		$da['transaction_id'] = $transaction_id; //交易单号

        $upd = Db::name('order')->where('ordersn',$out_trade_no)->update($da);


        $order = Db::name('order')->where('ordersn',$out_trade_no)->find();
        $goods = Db::name('order_goods')->field('goods_id,specid,goods_num')->where('order_id',$order['id'])->select()->toArray();



        $da['shop'] = $goods;

        $symbol = 'order';
        
        $id = 'goodspaytongji';

        $arr = ['data'=>$goods,'isModule'=>true];
        //添加销量统计
        $shop = getModuleApiData($symbol,$id,$arr);

        if ($upd) {
        	return zy_array(true,'操作成功！',$da,200,$isModule);
        } else {
        	return zy_array(false,'没有该类型或id不存在',null,122,$isModule);
        }
    }









	/**
	 * [querenShouhuo 确认收货]
	 * @param  [type] $orderid [订单id]
	 * @return [type]          [description]
	 */
	public function querenShouhuo($isModule=false){
		
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-4 ,$isModule);
		}

		Db::name('order')->where('id',$data['orderid'])->update(['status'=>4,'shouhuo_time'=>time()]);

		return zy_array(true ,'操作成功' ,'' ,200 ,$isModule);
	}




	/**
	 * [delOrder 删除订单]
	 * @param  [type] $orderid [description]
	 * @return [type]          [description]
	 */
	public function delOrder($isModule=false){
		
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-4 ,$isModule);
		}

		//删除
		$upd = Db::name('order')->where('id',$data['orderid'])->update(['is_del'=>1]);

		return zy_array(true ,'删除成功' ,null ,200 ,$isModule);
	}



	/**
	 * [txFahuo 提醒发货]
	 * @param  [type] $orderid [订单id]
	 * @return [type]          [description]
	 */
	public function txFahuo($isModule=false){
		
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-4 ,$isModule);
		}

		$upd = Db::name('order')->where('id',$data['orderid'])->update(['remind'=>'提醒发货']);

		return zy_array(true ,'操作成功' ,null ,200 ,$isModule);
	}







	/**
    * 查看物流
    * @param  [type] [$orderid] [订单id]
	* @return [type] [description]
    */
	public function lookLogistics($isModule=false){
		
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-4 ,$isModule);
		}

		$order = Db::name('order')->where('id',$data['orderid'])->find();

		$symbol = 'order';
        
        $id = 'lookLogistics_one';

        $arr = ['ShipperCode'=>$order['shipper_code'],'LogisticCode'=>$order['logistics_order'],'OrderCode'=>$order['ordersn'],'isModule'=>true];

        //调用快递接口
        $logisticResult = getModuleApiData($symbol,$id,$arr);

        $logisticResult = json_decode($logisticResult['data'],true);

        $logisticResult['shipper_name'] = $order['shipper_name'];

        if ($logisticResult['Success']=='true') {
        	return zy_array(true ,'成功' ,$logisticResult ,200 ,$isModule);
        } else {
        	return zy_array(false ,'失败' ,$logisticResult ,-3 ,$isModule);
        }
	}










	/**
	 * [evaluate 评价]
	 * @param  [type]  $orderid  	[订单id]
	 * @param  [type]  $goods_id   	[商品id]
	 * @param  [type]  $status   	[是否匿名] 1.匿名  不填不匿名
	 * @param  [type]  $score    	[评分]
	 * @param  [type]  $comment  	[评论]
	 * @param  [type]  $thumb   	[评价图片]
	 * @param  [type]  $dianpu_fen  [店铺评分]
	 * @param  boolean $isModule 	[description]
	 * @return [type]            	[description]
	 */
	public function evaluate($isModule=false){
		
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			// $data = zy_decodeData($data,$isModule);
			$data['uid'] = zy_userid_jwt($data['uid'],$status='de')['data'];
		}



		if ( !isset($data['uid']) || empty($data['uid']) ) {
			return zy_array(false ,'uid未正确传参' ,'' ,-1 ,$isModule);
		}
		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		}
		if ( !isset($data['goods_id']) || empty($data['goods_id']) ) {
			return zy_array(false ,'goods_id未正确传参' ,'' ,-2 ,$isModule);
		}
		if ( !isset($data['score']) || empty($data['score']) ) {
			return zy_array(false ,'score未正确传参' ,'' ,-3 ,$isModule);
		}
		if ( !isset($data['comment']) || empty($data['comment']) ) {
			return zy_array(false ,'comment未正确传参' ,'' ,-4 ,$isModule);
		}
		if ( !isset($data['dianpu_fen']) || empty($data['dianpu_fen']) ) {
			return zy_array(false ,'dianpu_fen未正确传参' ,'' ,-5 ,$isModule);
		}



		$order = Db::name('order')->where('id',$data['orderid'])->find();

		if (empty($order)) return zy_array(false ,'订单不存在' ,'' ,-6 ,$isModule);

		//商品id
		$shangpin_id = $data['goods_id'];
		//评分
		$pingfen = $data['score'];

		foreach ($shangpin_id as $k => $v) {
			
			if ($pingfen[$k] == 0) {
				return zy_array(false ,'评分未填写' ,'' ,102 ,$isModule);
			}
			if ($data['comment'][$k] == '') {
				return zy_array(false ,'评论内容未填写' ,'' ,102 ,$isModule);
			}
		}

		$dpf = explode(',',$data['dianpu_fen']);

		foreach ($dpf as $key => $value) {
			if ($value == 0) {
				return zy_array(false ,'店铺评分未填写' ,'' ,102 ,$isModule);
			}
		}

		$symbol = 'order';
        
        $id = 'readmemberinfo';

        $arr = ['uid'=>$data['uid'],'field'=>'nickname,avatar','isModule'=>true];
        //获取会员个人信息
        $memberinfo = getModuleApiData($symbol,$id,$arr);

        $nickname = $memberinfo['data']['nickname']; //用户昵称

        //调用评价添加接口
        $da['uid'] = $data['uid'];
        $da['orderid'] = $data['orderid'];
        $da['goods_id'] = $data['goods_id'];
        if (isset($data['status']) && $data['status']==1) {
			$da['status'] = 1; //是否匿名
		} else {
			$da['status'] = 0;
		}

        $da['score'] = $data['score'];
        $da['comment'] = $data['comment'];
        
		if (isset($data['thumb'])) {
			$da['thumb'] = $data['thumb'];
		} else {
			$da['thumb'] = null;
		}
        $goods = Db::name('order_goods')->where('order_id',$data['orderid'])->where('goods_id','in',$data['goods_id'])->select()->toArray();
        $da['shop'] = $goods;
        $da['avatar'] = $memberinfo['data']['avatar'];
        $da['nickname'] = $nickname;
        $da['isModule'] = true;

        $symbol = 'order';
        
        $id = 'addPingjia';

        //获取会员个人信息
        $addpingjia = getModuleApiData($symbol,$id,$da);

		Db::name('order')->where('id',$data['orderid'])->update(['dianpu_fen'=>$data['dianpu_fen'],'is_pingjia'=>1]);

		return zy_array(true ,'操作成功' ,null ,200 ,$isModule);
	}






















	/**
	 * [uploadImg 图片上传]
	 * @return [type] [description]
	 */
	public function uploadImg(){
		$data=$this->request->param();
        if(empty($_FILES)){
            return zy_json_echo(false,'非法上传内容！',null,110);
        }

        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        //获取网站路径
        $wzurl = $http_type.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];

        $wzurl= substr($wzurl,0,strrpos($wzurl,'/'));

        $file=$_FILES['tupian'];
        $upload_path = "./upload/orderneedtu/";
        //照片是否存在
        if($file['name'] <> ""){
            if(is_uploaded_file($file['tmp_name'])){
                if(preg_match('/\\.(gif|jpeg|png|bmp|jpg|tiff|)$/i', $file['name'])){
                    $kz=substr($file['name'],strrpos($file['name'],'.'));
                    $sui=mt_rand(1000,9999);
                    $filename=date('YmdHis').$sui.$kz;
                    $pic=$upload_path.$filename;
                    //判断pingzheng文件夹是否存在
                    if(!file_exists($upload_path)){
                        mkdir($upload_path,0777,true);
                    }
                    if(move_uploaded_file($file['tmp_name'], $pic)){
                        $this->compressedImage($pic,$pic);
                        $pic= $wzurl.explode('.',$pic,2)['1'];
                        
                        return json(['status'=>'true','msg'=>'上传成功','data'=>$pic,'code'=>200]);
                    }else{
                        return json(['status'=>'error','msg'=>'上传失败','data'=>null,'code'=>'-4']);
                    }
                }else{
                    return zy_json_echo(false,'文件不是图片格式！',null,105);
                }
            }else{
                return zy_json_echo(false,"图片获取路径错误！",null,120);
            }
        }else{
            return zy_json_echo(false,"没有上传图片！",null,108);
        }
    }



	/**
    * desription 压缩图片
    * @param sting $imgsrc 图片路径
    * @param string $imgdst 压缩后保存路径
    */
    private function compressedImage($imgsrc, $imgdst) {
        list($width, $height, $type) = getimagesize($imgsrc);
        $new_width = $width;//压缩后的图片宽
        $new_height = $height;//压缩后的图片高
        if($width >= 600){
            $per = 600 / $width;//计算比例
            $new_width = $width * $per;
            $new_height = $height * $per;
        }
        switch ($type) {
            case 1:
                $giftype = check_gifcartoon($imgsrc);
                if ($giftype) {
                    header('Content-Type:image/gif');
                    $image_wp = imagecreatetruecolor($new_width, $new_height);
                    $image = imagecreatefromgif($imgsrc);
                    imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    //90代表的是质量、压缩图片容量大小
                    imagejpeg($image_wp, $imgdst, 90);
                    imagedestroy($image_wp);
                    imagedestroy($image);
                }
                break;
            case 2:
                header('Content-Type:image/jpeg');
                $image_wp = imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefromjpeg($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                //90代表的是质量、压缩图片容量大小
                imagejpeg($image_wp, $imgdst, 90);
                imagedestroy($image_wp);
                imagedestroy($image);
                break;
            case 3:
                header('Content-Type:image/png');
                $image_wp = imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefrompng($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                //90代表的是质量、压缩图片容量大小
                imagejpeg($image_wp, $imgdst, 90);
                imagedestroy($image_wp);
                imagedestroy($image);
                break;
        }
    }





    






    /**
     * [applyRefundPage 申请退款页]
     * @param  [type]  $orderid  [订单id]
     * @param  [type]  $goods_id [商品id]
     * @param  boolean $isModule [description]
     * @return [type]            [description]
     */
    public function applyRefundPage($isModule=false){

    	if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

    	if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		}
		if ( !isset($data['goods_id']) || empty($data['goods_id']) ) {
			return zy_array(false ,'goods_id未正确传参' ,'' ,-1 ,$isModule);
		}

    	//商品id
		$shangpin_id = explode(',',$data['goods_id']);

		$zongjia = 0; //计算总价
		foreach ($shangpin_id as $key => $value) {
			$goods = Db::name('order_goods')->field('goods_id,goods_name,goods_num,goods_img,goods_price,specid_name')->where('order_id',$data['orderid'])->where('goods_id',$value)->find();

			$da['shop'][$key] = $goods;
			
			$zongjia += $goods['goods_price']*$goods['goods_num'];
		}

		$da['zongjia'] = $zongjia; //商品总价
		$da['order_id'] = $data['orderid']; //订单id
		
		return zy_array(true ,'成功' ,$da ,-1 ,$isModule);

    }






	// /**
	//  * [applyShouhou 申请售后]
	//  * @param  [type] $orderid  [订单id]
	//  * @param  [type] $goods_id [商品id]
	//  * @param  [type] $reason   [退款理由]
	//  * @param  [type] $money    [退货金额]
	//  * @param  [type] $remark   [退货说明]
	//  * @param  [type] $proof    [凭证]
	//  * @return [type]           [description]
	//  */
	// public function applyShouhou($isModule=false){
		
	// 	if ( $this->request->isPost() ) {
	// 		$data = $this->request->post();
	// 		// $data = zy_decodeData($data,$isModule);
	// 		$data['uid'] = zy_userid_jwt($data['uid'],$status='de')['data'];
	// 	}

	// 	if ( !isset($data['orderid']) || empty($data['orderid']) ) {
	// 		return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
	// 	}
	// 	if ( !isset($data['goods_id']) || empty($data['goods_id']) ) {
	// 		return zy_array(false ,'请选择需要售后的商品' ,'' ,-2 ,$isModule);
	// 	}
	// 	if ( !isset($data['reason']) || empty($data['reason']) ) {
	// 		return zy_array(false ,'请选择退货原因' ,'' ,-3 ,$isModule);
	// 	}
	// 	if ( !isset($data['money']) || empty($data['money']) ) {
	// 		return zy_array(false ,'money未正确传参' ,'' ,-4 ,$isModule);
	// 	}
	// 	// if ( !isset($data['proof']) || empty($data['proof']) ) {
	// 	// 	return zy_array(false ,'请上传凭证图片' ,'' ,-5 ,$isModule);
	// 	// }

	// 	$order = Db::name('order')->where('id',$data['orderid'])->find();

	// 	// $start=date("Y-m-d",time())." 0:0:0";
 //  //       $begintime=strtotime($start);

	// 	// if ($order['shouhou_time'] > $begintime) {
	// 	// 	return zy_array(false ,'当天不能重复申请' ,null ,-8 ,$isModule);
	// 	// }

	// 	if (empty($order)) return zy_array(false ,'订单不存在' ,'' ,-6 ,$isModule);


	// 	//待完善 此处需要调用售后添加接口
		
	// 	$da['uid'] = $order['uid']; //用户id
	// 	$da['order_id'] = $data['orderid']; //订单id
	// 	$da['goods_id'] = $data['goods_id']; //商品id
	// 	$da['reason'] = $data['reason']; //退货原因
	// 	$da['money'] = $data['money']; //退货金额
	// 	$da['remark'] = $data['remark']; //退款说明
	// 	$da['time'] = time();
	// 	$da['refund_ordersn'] = time().rand('100000','999999'); //退款订单编号
	// 	$da['status'] = 1; //1.审核中 2.待退货 3.进行中 4.已完成
	// 	$da['lx_name'] = $order['lx_name']; //联系人
	// 	$da['lx_mobile'] = $order['lx_mobile']; //联系电话
	// 	$da['transaction_id'] = $order['transaction_id']; //交易单号
	// 	$da['pay_type'] = $order['pay_type']; //支付类型
	// 	$da['audit_status'] = 0; //审核状态
	// 	$da['dianpu_name'] = $order['dianpu_name'];

	// 	if (isset($data['proof']) && !empty($data['proof']) ) {
	// 		$arr = []; //转换后的图片路径
	// 		foreach ($data['proof'] as $key => $value) {
	// 			$imghref = $this->base64_image_content($value);
	// 			$arr[$key] = $imghref;
	// 		}

	// 		$da['proof'] = json_encode($arr); //凭证
	// 	} else {
	// 		$da['proof'] = json_encode(array()); //凭证
	// 	}


		
		
	// 	Db::name('after_sales')->insert($da);

	// 	//更改订单状态
	// 	Db::name('order')->where('id',$data['orderid'])->update(['shstatus'=>4,'shouhou_time'=>time(),'is_shouhou'=>1,'status'=>7]);

	// 	return zy_array(true ,'操作成功' ,null ,200 ,$isModule);
	// }





	/**
	 * [applyShouhou 申请售后]
	 * @param  [type] $orderid  [订单id]
	 * @param  [type] $goods_id [商品id]
	 * @param  [type] $reason   [退款理由]
	 * @param  [type] $money    [退货金额]
	 * @param  [type] $remark   [退货说明]
	 * @param  [type] $proof    [凭证]
	 * @return [type]           [description]
	 */
	public function applyShouhou($isModule=false){
		
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data['uid'] = zy_userid_jwt($data['uid'],$status='de')['data'];
			// $data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		}
		if ( !isset($data['goods_id']) || empty($data['goods_id']) ) {
			return zy_array(false ,'请选择需要售后的商品' ,'' ,-2 ,$isModule);
		}
		if ( !isset($data['reason']) || empty($data['reason']) ) {
			return zy_array(false ,'请选择退货原因' ,'' ,-3 ,$isModule);
		}
		if ( !isset($data['money']) || empty($data['money']) ) {
			return zy_array(false ,'money未正确传参' ,'' ,-4 ,$isModule);
		}

		$order = Db::name('order')->where('id',$data['orderid'])->find();

		// $start=date("Y-m-d",time())." 0:0:0";
  //       $begintime=strtotime($start);

		// if ($order['shouhou_time'] > $begintime) {
		// 	return zy_array(false ,'当天不能重复申请' ,null ,-8 ,$isModule);
		// }

		if (empty($order)) return zy_array(false ,'订单不存在' ,'' ,-6 ,$isModule);
		
		$da['uid'] = $order['uid']; //用户id
		$da['order_id'] = $data['orderid']; //订单id
		$da['goods_id'] = $data['goods_id']; //商品id
		$da['reason'] = $data['reason']; //退货原因
		$da['money'] = $data['money']; //退货金额
		$da['remark'] = $data['remark']; //退款说明
		// $da['time'] = time();
		// $da['refund_ordersn'] = time().rand('100000','999999'); //退款订单编号
		// $da['status'] = 1; //1.审核中 2.待退货 3.进行中 4.已完成
		$da['lx_name'] = $order['lx_name']; //联系人
		$da['lx_mobile'] = $order['lx_mobile']; //联系电话
		$da['transaction_id'] = $order['transaction_id']; //交易单号
		$da['pay_type'] = $order['pay_type']; //支付类型
		// $da['audit_status'] = 0; //审核状态
		$da['dianpu_name'] = $order['dianpu_name'];
		$da['proof'] = isset($data['proof'])?$data['proof']:null;
		$da['isModule'] = true;

		$symbol = 'order';
        
        $id = 'addShouhou';

        //添加售后
        $addShouhou = getModuleApiData($symbol,$id,$da);

		//更改订单状态
		Db::name('order')->where('id',$data['orderid'])->update(['shstatus'=>4,'shouhou_time'=>time(),'is_shouhou'=>1,'status'=>7]);

		return zy_array(true ,'操作成功' ,null ,200 ,$isModule);
	}













	// /**
	//  * [newApplyShouhou 重新申请售后]
	//  * @return [type] [description]
	//  */
	// public function newApplyShouhou($isModule=false){
	// 	if ( $this->request->isPost() ) {
	// 		$data = $this->request->post();
	// 		$data = zy_decodeData($data,$isModule);
	// 	}

	// 	if ( !isset($data['orderid']) || empty($data['orderid']) ) {
	// 		return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
	// 	}
	// 	if ( !isset($data['reason']) || empty($data['reason']) ) {
	// 		return zy_array(false ,'请选择退货原因' ,'' ,-3 ,$isModule);
	// 	}
	// 	// if ( !isset($data['proof']) || empty($data['proof']) ) {
	// 	// 	return zy_array(false ,'请上传凭证图片' ,'' ,-5 ,$isModule);
	// 	// }

	// 	$after = Db::name('after_sales')->where('order_id',$data['orderid'])->find();

	// 	$start=date("Y-m-d",time())." 0:0:0";
 //        $begintime=strtotime($start);

	// 	if ($after['time'] > $begintime) {
	// 		return zy_array(false ,'当天不能重复申请' ,null ,-8 ,$isModule);
	// 	}



	// 	// $da['uid'] = $order['uid']; //用户id
	// 	$da['order_id'] = $data['orderid']; //订单id
	// 	$da['reason'] = $data['reason']; //退货原因
	// 	$da['remark'] = $data['remark']; //退款说明
	// 	$da['time'] = time();
	// 	$da['status'] = 1; //1.审核中 2.待退货 3.进行中 4.已完成
	// 	$da['audit_status'] = 0; //审核状态


	// 	if (isset($data['proof']) && !empty($data['proof']) ) {
	// 		$arr = []; //转换后的图片路径
	// 		foreach ($data['proof'] as $key => $value) {
	// 			$imghref = $this->base64_image_content($value);
	// 			$arr[$key] = $imghref;
	// 		}

	// 		$da['proof'] = json_encode($arr); //凭证
	// 	} else {
	// 		$da['proof'] = json_encode(array()); //凭证
	// 	}


	// 	// $arr = []; //转换后的图片路径
	// 	// foreach ($data['proof'] as $key => $value) {
	// 	// 	$imghref = $this->base64_image_content($value);
	// 	// 	$arr[$key] = $imghref;
	// 	// }

	// 	// $da['proof'] = json_encode($arr); //凭证
		
	// 	Db::name('after_sales')->where('order_id',$data['orderid'])->update($da);

	// 	//更改订单状态
	// 	Db::name('order')->where('id',$data['orderid'])->update(['shstatus'=>4,'shouhou_time'=>time(),'is_shouhou'=>1,'status'=>7]);

	// 	return zy_array(true ,'操作成功' ,null ,200 ,$isModule);

	// }







	/**
	 * [test 重新申请售后]
	 * @return [type] [description]
	 */
	public function newApplyShouhou($isModule=false){
		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		}
		if ( !isset($data['reason']) || empty($data['reason']) ) {
			return zy_array(false ,'请选择退货原因' ,'' ,-3 ,$isModule);
		}

		$da['order_id'] = $data['orderid']; //订单id
		$da['reason'] = $data['reason']; //退货原因
		$da['remark'] = $data['remark']; //退款说明
		if (isset($data['proof']) && !empty($data['proof']) ) {
			$da['proof'] = $data['proof']; //凭证
		} else {
			$da['proof'] = null; //凭证
		}
		$da['isModule'] = true;


		$symbol = 'order';
        
        $id = 'newApply';

        //重新申请售后
        $newApply = getModuleApiData($symbol,$id,$da);

        if ($newApply['status']=='error') {
        	return zy_array(false ,$newApply['message'] ,null ,-8 ,$isModule);
        }

		//更改订单状态
		Db::name('order')->where('id',$data['orderid'])->update(['shstatus'=>4,'shouhou_time'=>time(),'is_shouhou'=>1,'status'=>7]);

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
			$shangpin_id = explode(',',$value['goods_id']);
			$da[$key]['dianpu_name'] = $value['dianpu_name'];
			$da[$key]['order_id'] = $value['order_id'];
			$da[$key]['status'] = $value['status'];
			$da[$key]['audit_status'] = $value['audit_status'];


			$da[$key]['currentPage'] = $currentPage; //当前页
			$da[$key]['lastPage'] = $lastPage; //总页数
			$da[$key]['listRows'] = $listRows; //每页数量
			$da[$key]['total'] = $total; //总条数

			$goods = Db::name('order_goods')->field('goods_id,goods_name,goods_num,goods_img,goods_price,specid_name')->where('order_id',$value['order_id'])->where('goods_id','in',$shangpin_id)->select();
			$da[$key]['shop'] = $goods;
		}

		return zy_array(true ,'操作成功' ,$da ,200 ,$isModule);
	}






	/**
	 * [quxiaoShouhou 取消售后]
	 * @return [type] [description]
	 */
	public function quxiaoShouhou($isModule=false){

		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}

		if ( !isset($data['orderid']) || empty($data['orderid']) ) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		}

		Db::name('order')->where('id',$data['orderid'])->update(['shstatus'=>0,'is_shouhou'=>0,'status'=>4]);

		Db::name('after_sales')->where('order_id',$data['orderid'])->delete();
		
		return zy_array(true ,'操作成功' ,null ,200 ,$isModule);
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



		$symbol = 'order';
        
        $id = 'readConfigInfo_one';

        $arr = ['isModule'=>true];

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

		$zy = Db::name('zyexpress')->where('code',$data['code'])->find();

		$da['shipper_name'] = $zy['company'];
		$da['shipper_code'] = $data['code'];
		$da['logistics_order'] = $data['logistics_order'];
		$da['fhtime'] = time();
		$da['status'] = 3;

		$after = Db::name('after_sales')->where('order_id',$data['orderid'])->update($da);

		return zy_array(true ,'发货成功' ,null ,200 ,$isModule);

	}









	/**
	 * [readShopInfo 根据订单编号获取商品信息]
	 * @param  [type]  $ordersn  [订单编号]
	 * @param  boolean $isModule [description]
	 * @return [type]            [description]
	 */
	public function readShopInfo($ordersn,$isModule=false){

		if ( !isset($ordersn) || empty($ordersn) ) {
			return zy_array(false ,'ordersn未正确传参' ,'' ,-1 ,$isModule);
		}

		$odsn = Db::name('order')->field('ordersn,id')->where('ordersn',$ordersn)->find();

		$goods = Db::name('order_goods')->field('goods_id,goods_name,goods_num,goods_img,goods_price,specid_name')->where('order_id',$odsn['id'])->select();

		
        $da['shop'] = $goods;
        $da['ordersn'] = $odsn['ordersn'];
		


		return zy_array(true ,'获取成功' ,$da ,200 ,$isModule);
	}


 






	/**
	 * [statistics 统计订单数量]
	 * @param  boolean $isModule [description]
	 * @return [type]            [description]
	 */
	public function statistics($isModule=false){

		if ( $this->request->isPost() ) {
			$data = $this->request->post();
			$data = zy_decodeData($data,$isModule);
		}
		if ( !isset($data['uid']) || empty($data['uid']) ) {
			return zy_array(false ,'uid未正确传参' ,'' ,-1 ,$isModule);
		}


		//待支付
		$wait_pay = Db::name('order')->where('uid',$data['uid'])->where('status',1)->where('is_del',2)->count();
		//待发货
		$wait_fahuo = Db::name('order')->where('uid',$data['uid'])->where('status',2)->where('is_del',2)->count();
		//待收货
		$wait_shouhuo = Db::name('order')->where('uid',$data['uid'])->where('status',3)->where('is_del',2)->count();

		$da['wait_pay'] = $wait_pay;
		$da['wait_fahuo'] = $wait_fahuo;
		$da['wait_shouhuo'] = $wait_shouhuo;

		return zy_array(true ,'获取成功' ,$da ,200 ,$isModule);
	}








	/**
	 * [editSales 售后拒绝]
	 * @return [type] [description]
	 */
	public function afterSalesRefuse($isModule){

		$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (empty($data['orderid'])) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		}

		Db::name('order')->where('id',$data['orderid'])->update(['shstatus'=>0,'is_shouhou'=>0]);

		return zy_array(true ,'获取成功' ,null ,200 ,$isModule);

	}






	/*
     * 网站配置信息
     */
    private function readConfig(){

        $symbol = 'order';
        
        $id = 'readConfigInfo_one';

        $arr = ['isModule'=>true];

        //调用快递接口
        $web_config = getModuleApiData($symbol,$id,$arr);

        return $web_config['data'];
    }




	/**
	 * [pingjiaList 评价列表]
	 * @return [type] [description]
	 */
	public function pingjiaList($isModule=false){

		$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (empty($data['goods_id'])) {
			return zy_array(false ,'goods_id不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($data['type'])) {
			return zy_array(false ,'type不能为空' ,'' ,-1 ,$isModule);
		}
		if ( isset($data['pageNum']) && !empty($data['pageNum']) ){
			$yeshu = $data['pageNum'];
		} else {
			$yeshu = 10; //默认为每页十条
		}

		$where = 1;
		switch ($data['type']) {
			case 1:
				break;
			case 2:
				$where .= " and score = 5"; //好评
				break;
			case 3:
				$where .= " and score in (2,3,4)"; //中评 
				break;
			case 4:
				$where .= " and score = 1"; //中评
				break;
			default:
				# code...
				break;
		}


		$haoping = Db::name('pingjia')->where('score',5)->where('productid',$data['goods_id'])->count();
		$zhongping = Db::name('pingjia')->where('score','in','2,3,4')->where('productid',$data['goods_id'])->count();
		$chaping = Db::name('pingjia')->where('score',1)->where('productid',$data['goods_id'])->count();




		$pingjai = Db::name('pingjia')
					->field('productid,score,comment,status,thumb,time,nickname,avatar,specid_name')
					->order('time','desc')
					->where($where)
					->where('productid',$data['goods_id'])
					->paginate($yeshu,false,['page'=>$data['page']]);

		if (count($pingjai)==0) {
			return zy_array(true ,'获取成功' ,$pingjai ,200 ,$isModule);
		}


		$currentPage = $pingjai->currentPage();
		$lastPage = $pingjai->lastPage();
		$listRows = $pingjai->listRows();
		$total = $pingjai->total();

		//读取网站配置信息
        $config = $this->readConfig();

		$da = [];
		foreach ($pingjai as $key => $value) {

			$da['currentPage'] = $currentPage; //当前页
			$da['lastPage'] = $lastPage; //总页数
			$da['listRows'] = $listRows; //每页数量
			$da['total'] = $total; //总条数

			$da['haoping'] = $haoping; //好评
			$da['zhongping'] = $zhongping; //中评
			$da['chaping'] = $chaping; //差评

			if ($value['status'] == 1) {
				$value['nickname'] = '匿名用户'; //用户昵称 mb_substr($value['nickname'], 0 ,1 ,'utf-8')."**".mb_substr($value['nickname'], -1 ,1 ,'utf-8')
			}

			$value['time'] = date('Y-m-d',$value['time']);
			$value['thumb'] = json_decode($value['thumb']);

			foreach ($value['thumb'] as $k => $v) {
				$value['thumb'][$k] = $config['basic']['site_domain'].$v;
			}

			$da['pingjia'][$key] = $value;
		}



		return zy_array(true ,'获取成功' ,$da ,200 ,$isModule);

	}





	/**
	 * [myPingjia 我的评价]
	 * @return [type] [description]
	 */
	public function myPingjia($isModule=false){

		$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (empty($data['uid'])) {
			return zy_array(false ,'uid不能为空' ,'' ,-1 ,$isModule);
		}
		if ( isset($data['pageNum']) && !empty($data['pageNum']) ){
			$yeshu = $data['pageNum'];
		} else {
			$yeshu = 10; //默认为每页十条
		}

		$pingjai = Db::name('pingjia')
					->field('productid,score,comment,status,thumb,time,nickname,avatar,specid_name,uid')
					->order('time','desc')
					->where('uid',$data['uid'])
					->paginate($yeshu,false,['page'=>$data['page']]);

		// dump($pingjai);exit;


		// dump(count($pingjai));exit;

		if (count($pingjai)==0) {
			return zy_array(true ,'成功' ,$pingjai ,200 ,$isModule);
		}

		$currentPage = $pingjai->currentPage();
		$lastPage = $pingjai->lastPage();
		$listRows = $pingjai->listRows();
		$total = $pingjai->total();

		//读取网站配置信息
        $config = $this->readConfig();

		$da = [];
		foreach ($pingjai as $key => $value) {

			$da['currentPage'] = $currentPage; //当前页
			$da['lastPage'] = $lastPage; //总页数
			$da['listRows'] = $listRows; //每页数量
			$da['total'] = $total; //总条数

			$value['time'] = date('Y-m-d',$value['time']);
			$value['thumb'] = json_decode($value['thumb']);

			foreach ($value['thumb'] as $k => $v) {
				$value['thumb'][$k] = $config['basic']['site_domain'].$v;
			}

			$goods = Db::name('order_goods')->field('goods_id,goods_name,goods_num,goods_img,goods_price,specid_name')->where('order_id',$value['productid'])->where('uid',$value['uid'])->select();

			$value['shop'] = $goods;


			$da['pingjia'][$key] = $value;
		}

		return zy_array(true ,'获取成功' ,$da ,200 ,$isModule);
	}





	/*
	 * 获取要售后的商品信息
	 */
	public function afterSalesGoodsInfo($order_id=null,$goods_id=null,$isModule=false){

		$data = $this->request->post();

		if (empty($order_id)) {
			return zy_array(false ,'订单id不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($goods_id)) {
			return zy_array(false ,'商品id不能为空' ,'' ,-1 ,$isModule);
		}

		$shopinfo = Db::name('order_goods')->where('order_id',$order_id)->where('goods_id','in',$goods_id)->select()->toArray();

		return zy_array(true ,'获取成功' ,$shopinfo ,200 ,$isModule);
	}



																										
//、、、、、、、、、店铺订单中心																																														

							
	/*
	 * 店铺订单数量
	 */
	public function storeOrder($isModule=false)
	{
		$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		// if (empty($data['storeid'])) {
		// 	return zy_array(false ,'店铺id是必传值' ,'' ,-1 ,$isModule);
		// }
		if (empty($data['uid'])) {
			return zy_array(false ,'uid是必传值' ,'' ,-1 ,$isModule);
		}

		$order = Db::name('order')->where('uid',$data['uid']);

		//今日订单数
		$today_order = $order->whereTime('addtime','today')->count();

		//全部待付款
		$dai_pay = $order->where('status',1)->count();

		//已发货数量
		$yifahuo_num = $order->where('status',3)->count();

		//全部待发货
		$dai_fahuo = $order->where('status',2)->count();

		//全部退款中
		$all_tuikuan = $order->where('is_shouhou',1)->count();

		$da = [
			'today_order' => $today_order,
			'dai_pay' => $dai_pay,
			'dai_fahuo' => $dai_fahuo,
			'all_tuikuan' => $all_tuikuan,
			'yifahuo_num' =>$yifahuo_num,
		];

		return zy_array(true ,'获取成功' ,$da ,200 ,$isModule);
	}




	/*
	 * 店铺订单列表
	 */
	public function storeOrderList($isModule=false)
	{
		$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (empty($data['uid'])) {
			return zy_array(false ,'uid不能为空' ,null ,-1 ,$isModule);
		}
		if (empty($data['leixing'])) {
			return zy_array(false ,'订单类型不能为空' ,null ,-1 ,$isModule);
		}

		//当前页 默认第一页
		$page = isset($data['page'])?$data['page']:1;

		//每页数量 默认10
		$pageNum = isset($data['pageNum'])?$data['pageNum']:10;
		
		//1.待支付 2.待发货 3.已发货
		$leixing = [1,2,3,4];

		if (!in_array($data['leixing'],$leixing)) return zy_array(false,'订单类型传递错误，请填写1,2,3内数字',null,-1,$isModule);

		//状态筛选
		$where = 1;
		switch ($data['leixing']) {
			case 1:
				$where .= " and status = 1";
				break;
			case 2:
				$where .= " and status = 2";
				break;
			case 3:
				$where .= " and status = 3";
				break;
			case 4:
				$where .= " and status in (1,2,3)"; //获取全部
				break;
		}

		$order = Db::name('order')->where('uid',$data['uid'])->where('is_del',2);

		//添加搜索条件(商品名称搜索)
		if (isset($data['keyword']) && !empty($data['keyword'])) {
			//先取出店铺下所有订单对应的商品
			$ids = $order->column('id');
			$od_s = Db::name('order_goods')->where('order_id','in',$ids)
				->where('goods_name','like','%'.$data['keyword'].'%')
				->group('order_id')
				->column('order_id');
			$str = !empty($od_s)?implode(',',$od_s):0;
			$where .= " and id in ({$str})";
		}

		$ord = $order->where($where)->order('id','desc')->paginate($pageNum,false,['page'=>$page]);

		//组装数据
		$da = [];
		$da['currentPage'] = $ord->currentPage(); //当前页
		$da['lastPage'] = $ord->lastPage();	//总页数
		$da['total'] = $ord->total();	//总条数
		$da['listRows'] = $ord->listRows(); //每页显示数量

		if (count($ord)==0) {
			$da['info'] = [];
		}

		$zongshu = 0;
		foreach ($ord as $key => $value) {
			//订单对应的商品
			$goods = Db::name('order_goods')
					->field('goods_id,goods_name as title,goods_img as thumb,goods_price as price,goods_num as num,specid_name as tags')
					->where('order_id',$value['id'])
					->select()->toArray();

			//商品总数量
			$a = array_column($goods,'num');
			$b = array_sum($a);



			$da['info'][$key]['order_id'] = $value['id']; //订单id
			$da['info'][$key]['type'] = $value['status']; //订单装填
			$da['info'][$key]['order'] = $value['ordersn']; //订单编号
			$da['info'][$key]['time'] = date('Y-m-d H:i',$value['addtime']); //下单时间
			$da['info'][$key]['shishou'] = $value['totalprice'] + $value['freight']; //实收 = 总价+运费
			$da['info'][$key]['freight'] = $value['freight']; //运费

			$da['info'][$key]['goods_zongshu'] = $b; //商品总数
			//商品
			$da['info'][$key]['shop'] = $goods;

			//地址
			$da['info'][$key]['addressInfo']['name'] =  $value['lx_name']; 
			$da['info'][$key]['addressInfo']['tel'] =  $value['lx_mobile'];
			$da['info'][$key]['addressInfo']['province'] =  $value['province']; //省
			$da['info'][$key]['addressInfo']['city'] =  $value['city']; //市
			$da['info'][$key]['addressInfo']['county'] =  $value['area']; //区、县
			$da['info'][$key]['addressInfo']['addressDetail'] = $value['address'];  //详细地址
			$da['info'][$key]['addressInfo']['postalCode'] =  $value['lx_code'];	//邮编
			$da['info'][$key]['addressInfo']['areaCode'] = $value['area_code']; //地区编码
		}

		return zy_array(true ,'获取成功' ,$da ,200 ,$isModule);
	}




	/*
	 * 修改订单的商品价格
	 */
	public function editPrice($isModule=false)
	{
		$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		//字符串类型
		if (empty($data['orderid'])) {
			return zy_array(false ,'订单id是必传值' ,'' ,-1 ,$isModule);
		}
		//传过来的是数组
		if (empty($data['goods_id'])) {
			return zy_array(false ,'商品id不能为空' ,'' ,-1 ,$isModule);
		}
		//订单商品对应的新价格
		if (empty($data['new_price'])) {
			return zy_array(false ,'新价格不能为空并且要和商品id对应' ,'' ,-1 ,$isModule);
		}
		//是 1 否 2
		if (empty($data['freeship'])) {
			return zy_array(false ,'是否免邮不能为空' ,'' ,-1 ,$isModule);
		}

		//订单总价
		$zong_money = 0;

		//修改订单商品价格
		foreach ($data['goods_id'] as $key => $value) {

			if ($data['new_price'][$key] != 0) {
				$goods = Db::name('order_goods')->where('order_id',$data['orderid'])->where('goods_id',$value)->find();

				Db::name('order_goods')->where('order_id',$data['orderid'])->where('goods_id',$value)->update(['goods_price'=>$data['new_price'][$key]]);
				$zong_money += $goods['goods_num'] * $data['new_price'][$key];
			}
		}

		$upd = [
			'freeship' => $data['freeship'],
			'freight' => isset($data['freight'])?$data['freight']:0,
			'totalprice' =>  $zong_money, //计算总价
		];

		Db::name('order')->where('id',$data['orderid'])->update($upd);

		return zy_array(true ,'修改成功' ,null ,200 ,$isModule);
	}








	/*
	 * 修改订单收货地址
	 */
    public function editShouhuo($isModule=false){

		$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (empty($data['orderid'])) {
			return zy_array(false ,'订单id不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($data['province'])) {
			return zy_array(false ,'省不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($data['city'])) {
			return zy_array(false ,'市不能为空' ,'' ,-1 ,$isModule);
		}
		if(empty($data['county'])) {
			return zy_array(false ,'县、区不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($data['addressDetail'])) {
			return zy_array(false ,'地址详情不能为空' ,'' ,-1 ,$isModule);
		}
		if(empty($data['name'])) {
			return zy_array(false ,'收货人姓名不能为空' ,'' ,-1 ,$isModule);
		}
		if(empty($data['tel'])) {
			return zy_array(false ,'收货人手机号不能为空' ,'' ,-1 ,$isModule);
		}
		if(empty($data['areaCode'])) {
			return zy_array(false ,'地区编码不能为空' ,'' ,-1 ,$isModule);
		}
		// if(empty($data['postalCode'])) {
		// 	return zy_array(false ,'邮编不能为空' ,'' ,-1 ,$isModule);
		// }


		$da = [
			'province' => $data['province'],
			'city' => $data['city'],
			'area' => $data['county'],
			'address' => $data['addressDetail'],
			'lx_name' => $data['name'],
			'lx_mobile' => $data['tel'],
			'lx_code' => $data['postalCode'],
			'area_code' => isset($data['areaCode'])?$data['areaCode']:'',
		];

		Db::name('order')->where('id',$data['orderid'])->update($da);

        return zy_array(true ,'修改成功' ,null ,200 ,$isModule);   
    }






    /*
     * 订单发货
     */
    public function orderFahuo($isModule=false)
    {
    	$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (empty($data['orderid'])) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		}
		if (empty($data['code'])) {
			return zy_array(false ,'code未正确传参' ,'' ,-1 ,$isModule);
		}
		if (empty($data['logistics_order'])) {
			return zy_array(false ,'logistics_order未正确传参' ,'' ,-1 ,$isModule);
		}

		$zy = Db::name('zyexpress')->where('code',$data['code'])->find();

		$da['shipper_name'] = $zy['company'];
		$da['shipper_code'] = $data['code'];
		$da['logistics_order'] = $data['logistics_order'];
		$da['fhtime'] = time();
		$da['status'] = 3;

		$after = Db::name('order')->where('id',$data['orderid'])->update($da);

		return zy_array(true ,'发货成功' ,null ,200 ,$isModule);

    }



    //店铺订单详情
    public function storeOrderDetail($isModule=false)
    {
    	$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (empty($data['orderid'])) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		}
		//1.待支付 2.待发货 3.已发货
		if (empty($data['type'])) {
			return zy_array(false ,'订单类型' ,'' ,-1 ,$isModule);
		}

		$order = Db::name('order')->where('id',$data['orderid'])->find();

		//读配置
        $time_config = getModuleConfig('order','config','time_config.json');
        $time_config = json_decode($time_config,true);

        switch ($data['type']) {
        	case 1:
        		//剩余时间 = 支付时间+3小时 - 当前时间
        		$cuifu = $time_config['cuifu'];
		        $pay_time = $order['paytime'];
		        $daojishi = $pay_time + ($cuifu*60*60) - time(); //待支付倒计时
        		break;
        	case 2:
        		$fahuo = $time_config['fahuo'];
        		$pay_time = $order['paytime'];
        		$daojishi = $pay_time+($fahuo*60*60) - time(); //待发货倒计时
        		break;
        	case 3:
        		//剩余时间 = 发货时间 + 配置发货时间 - 当前时间
        		$shouhuo = $time_config['shouhuo'];
        		$fhtime = $order['fhtime'];

        		$daojishi = $fhtime + ($shouhuo*86400) - time(); //待支付倒计时
        		break;
        }

        if ($daojishi > 3600 ){
        	$hous = floor($daojishi / 3600); //时
        } else {
        	$hous = 0; //时
        }

        if ($daojishi > 60) {
        	$fen = floor(($daojishi-($hous*3600)) / 60); //分
        } else {
        	$fen = 0; //分
        }

        if ($daojishi > 0) {
        	$miao = floor($daojishi - ($hous * 3600) - ($fen * 60)); //秒
        } else {
        	$miao = 0;
        }

        $shijian = $hous."*".$fen."*".$miao."*1000";
        
        $goods = Db::name('order_goods')
					->field('goods_id,goods_name as title,goods_img as thumb,goods_price as price,goods_num as num,specid_name as tags')
					->where('order_id',$data['orderid'])
					->select()->toArray();

        $da = [
        	'order_id' => $order['id'], 
        	'status' => $order['status'],
        	'logistics_order' => $order['logistics_order'], //物流单号
        	'dao_time' => $shijian, //倒计时
        	'usernote' => $order['usernote'], //备注 留言
        	'user_name' => $order['user_name'], //用户名
        	'ordersn' => $order['ordersn'], //订单编号
        	'addtime' => date('Y-m-d H:i:s',$order['addtime']),
        	'paytime' => $order['paytime']?date('Y-m-d H:i:s',$order['paytime']):'',
        	'fhtime' => $order['fhtime']?date('Y-m-d H:i:s',$order['fhtime']):'',
        	'buytype' => '一口价', //交易类型
        	'buylai' => '手机', //交易来源


        	//地址
			'name' =>  $order['lx_name'],
			'tel' =>  $order['lx_mobile'],
			'province' =>  $order['province'], //省
			'city' =>  $order['city'], //市
			'county' =>  $order['area'], //区、县
			'addressDetail' => $order['address'],  //详细地址
			'postalCode' =>  $order['lx_code'],	//邮编

			'shop'=>$goods,
			'shishou' => $order['totalprice'] + $order['freight'], //实收
			'freight' => $order['freight'], //运费
        ];

        return zy_array(true ,'获取成功' ,$da ,200 ,$isModule);
    }




    /*
     * 延长收货页
     */
    public function prolongShou($isModule=false)
    {
    	$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		$yanshi = Db::name('order_delayed')->field('ys_name')->where('ys_status',1)->select()->toArray();

		return zy_array(true ,'获取成功' ,$yanshi ,200 ,$isModule);
    }




    /*
     * 修改延长收货
     */
    public function editYanshi($isModule=false)
    {
    	$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (empty($data['orderid'])) {
			return zy_array(false ,'orderid未正确传参' ,'' ,-1 ,$isModule);
		}
		if (empty($data['ys_name'])) {
			return zy_array(false ,'未选择延时时间' ,'' ,-1 ,$isModule);
		}

		Db::name('order')->where('id',$data['orderid'])->update(['sh_delayed'=>$data['ys_name']]);

		return zy_array(true ,'修改成功' ,'' ,200 ,$isModule);
    }





    /*
     * 定时器自动执行方法
     */
    public function dingshi($isModule=false)
    {
  		//$data = $this->request->post();
		// $data = zy_decodeData($data,$isModule);

    	$order = Db::name('order');

    	//读订单配置
        $time_config = getModuleConfig('order','config','time_config.json');
        $time_config = json_decode($time_config,true);

        $cuifu = $time_config['cuifu']; //下单预留支付时间(小时)
        $fahuo = $time_config['fahuo']; //待发货时间  到期未发货订单自动退款（小时）
        $shouhuo = $time_config['shouhuo']; //到期未收货 系统自动确认收货 并给店铺账户打钱（天）

        $time = strtotime('-'.$cuifu.'hour');

        $time2 = strtotime('-'.$fahuo.'hour');

        $time3 = strtotime('-'.$shouhuo.'day');

        //超过支付时间的直接订单关闭 
        $guanbi = $order->where('status',1)->where('paytime','<',$time);

        //到期未发货订单自动退款
        $tuikuan = $order->where('status',2)->where('paytime','<',$time)->select();
        foreach ($tuikuan as $key => $value) {
        	//写入退款操作 此处调用退款接口
        }

        //未手动收货的自动确认收货 并把钱转到店铺账户
        $queren_shouhuo = $order->where('status',3)->where('paytime','<',$time)->select();
        foreach ($queren_shouhuo as $key => $value) {
        	//此处给店铺打钱 需调用接口
        }


    }












}
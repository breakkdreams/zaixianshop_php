<?php
namespace plugins\evaluation\controller;
/**
 * @Author: user
 * @Date:   2019-03-07 16:21:19
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-20 09:56:01
 */
use cmf\controller\PluginRestBaseController;//引用插件基类
use plugins\evaluation\Model\PluginApiIndexModel;
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
     * [addPingjia 添加评价]
     * @param [type]  $uid  	[uid]
     * @param [type]  $orderid  [订单id]
     * @param [type]  $goods_id [商品id]
     * @param [type]  $status   [是否匿名] 1.匿名  不填不匿名
     * @param [type]  $score    [评分]
     * @param [type]  $comment  [评论]
     * @param [type]  $thumb    [评价图片]
     * @param [type]  $shop      商品信息
     * @param [type]  $avatar      头像
     * @param [type]  $nickname      昵称
     * @param boolean $isModule 
     */
    public function addPingjia($uid,$orderid,$goods_id,$status=null,$score,$comment,$thumb=null,$shop,$avatar,$nickname,$isModule=false){

    	$data = $this->request->post();
		$data = zy_decodeData($data,$isModule);

		if (empty($orderid)) {
			return zy_array(false ,'orderid不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($goods_id)) {
			return zy_array(false ,'goods_id不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($score)) {
			return zy_array(false ,'score不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($comment)) {
			return zy_array(false ,'comment不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($shop)) {
			return zy_array(false ,'shop不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($avatar)) {
			return zy_array(false ,'avatar不能为空' ,'' ,-1 ,$isModule);
		}
		if (empty($nickname)) {
			return zy_array(false ,'nickname不能为空' ,'' ,-1 ,$isModule);
		}

		foreach ($goods_id as $k => $v) {
			
			if ($score[$k] == 0) {
				return zy_array(false ,'评分未填写' ,'' ,102 ,$isModule);
			}
			if ($comment[$k] == '') {
				return zy_array(false ,'评论内容未填写' ,'' ,102 ,$isModule);
			}
		}

		foreach ($goods_id as $key => $value) {

			$goods = $shop[$key];
			$add['goods_name'] = $goods['goods_name']; //商品名称
			$add['specid_name'] = $goods['specid_name']; //商品规格
			$add['goods_img'] = $goods['goods_img']; //商品主图
			$add['goods_num'] = $goods['goods_num']; //商品数量
			$add['goods_price'] = $goods['goods_price']; //商品单价

			$add['avatar'] = $avatar; //用户头像
			$add['uid'] = $uid; //用户id
			$add['productid'] = $goods_id[$key]; //商品id
			$add['comment'] = $comment[$key]; //评论内容

			// if (!isset($thumb)) {
			// 	$add['thumb'] = json_encode(array());
			// } else {
				
			// 	if ($thumb[$key]==1){
			// 		$add['thumb'] = ''; //图片
			// 	}else{

			// 		$imghref = [];
			// 		foreach ($thumb[$key] as $k => $v) {
			// 			$imghref[$k] =  $this->base64_image_content($v);
			// 		}
			// 		$add['thumb'] = json_encode($imghref);
			// 	}
			// }

			if (empty($thumb)) {
				$add['thumb'] = json_encode(array());
			} else {
				if (isset($thumb[$key])) {
					$imghref = [];
					foreach ($thumb[$key] as $k => $v) {
						$imghref[$k] =  $this->base64_image_content($v);
					}
					$add['thumb'] = json_encode($imghref);
				} else {
					$add['thumb'] = json_encode(array());
				}
				
			}

			$add['orderid'] = $orderid; //订单id
			$add['score'] = $score[$key]; //评分
			$add['time'] = time();

			if (isset($status) && $status==1) {
				$add['status'] = 1; //是否匿名
			} else {
				$add['status'] = 0; //是否匿名
			}

			$add['nickname'] = $nickname; //用户昵称
			$add['is_pingtai'] = 2; //是否是平台评价 1.平台 2.用户
			$a = Db::name('pingjia')->insert($add);
		}

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

		$path = "./upload/pingjia"; // 设置图片保存路径
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



	/*
     * 网站配置信息
     */
    private function readConfig(){

        $symbol = 'evaluation';
        
        $id = 'readConfigInfo_one';

        $arr = ['data'=>null,'isModule'=>true];

        //调用快递接口
        $web_config = getModuleApiData($symbol,$id,$arr);

        return $web_config['data'];
    }



	/**
	 * [pingjiaList 评价列表]
	 * @return [type] [description]
	 */
	public function pingjiaList($page=null,$pageNum=null,$isModule=false){

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
	public function myPingjia($page=null,$pageNum=null,$isModule=false){

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
					->field('productid,score,comment,status,thumb,time,nickname,avatar,specid_name,uid,goods_name,goods_num,goods_img,goods_price,specid_name')
					->order('time','desc')
					->where('uid',$data['uid'])
					->paginate($yeshu,false,['page'=>$data['page']]);

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

			$da['pingjia'][$key] = $value;
		}

		return zy_array(true ,'获取成功' ,$da ,200 ,$isModule);
	}






    

    
   
   	












}
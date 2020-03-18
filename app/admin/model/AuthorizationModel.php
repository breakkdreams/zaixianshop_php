<?php
namespace app\admin\model;
use think\Model;
use think\Config;
use app\admin\model\AuthAccessModel;
use app\admin\model\AccessModel;
/**
 * 授权模型类
 */
class AuthorizationModel extends Model
{
	protected $accessModel=null;
	private $token=null;
	function __construct()
	{
		//实例化
		$this->accessModel=new AccessModel();
		$this->token=$this->accessModel->createAccessId();
	}
	/**
	 * 检查菜单是否授权
	 * @param  [type] $rule_names [description]
	 * @return [type]             [description]
	 */
	public function index()
	{
		$serverMenus=$this->getServerMenu();
		$serverMenus=json_decode(json_decode($serverMenus,true)['data'],true);
		return $serverMenus;
	}

	/**
	 * 获取服务器菜单
	 */
	public function getServerMenu()
	{
		//读取配置
		$host=Config::get('admin_module_config.api_host');
		$url=$host.'/api/Api_Authorization/getAdminMenus';
		//连接远程接口
		$data['token']=$this->token;
		$res=$this->curlPost($url,$data);
		 return $res;
	}


	/**
    * 调试开关打开时过滤调试信息
    * @param  string  要处理的字符串
    * @return 	返回处理后的字符串
    */
   public function getSubstr($str){
	   	if(APP_DEBUG){
	   		$pos=strpos($str,'<div',0);
	   		if($pos>0){//找到了才截取
				$str=substr($str,0,$pos);
	   		}
	   	}
	   	return $str;
   } 

   /**
    * upload module menu to server
    */
   public function uploadMenu( $symbol , $moduleId )
   {
   	 	$menu = $this->getModuleMenu( $symbol );
   	 	if( empty( $menu ) ){
   	 		return ['error'=>false,'message'=>'未获取到模块主菜单数据:AdminIndex/index@adminMenu'];
   	 	}
   	 	//读取配置
		$host = Config::get('admin_module_config.api_host');
		$url = $host.'/api/Api_Authorization/saveModuleMenu';
		$data['token'] = $this->token;
		$data['menu'] = $menu;
		$data [ 'moduleId' ] = $moduleId; 
		$res = $this->curlPost($url,$data);
		return $res;
   }

   /**
    * update module menu to server
    */
   public function updateMenu( $symbol , $moduleId )
   {
   	 	$menu = $this->getModuleMenu( $symbol );
   	 	if( empty( $menu ) ){
   	 		return ['error'=>false,'message'=>'未获取到模块主菜单数据:AdminIndex/index@adminMenu'];
   	 	}
   	 	//读取配置
		$host = Config::get('admin_module_config.api_host');
		$url = $host.'/api/Api_Authorization/updateModuleMenu';
		$data['token'] = $this->token;
		$data['menu'] = $menu;
		$data['moduleId'] = $moduleId;
		$res = $this->curlPost($url,$data);
		return $res;
   }


   /**
    * get admin menu
    */
   private function getModuleMenu( $symbol )
   {
   		$menu = new \app\common\lib\ModuleAdminMenu();
   		return $menu->getModuleMenuList( $symbol );
   }

   /**
    * delete module menu
    * @param array  $data  参数
    */
   public function deleteMenu($moduleMenuId)
   {
   		//读取配置
		$host=Config::get('admin_module_config.api_host');
		$url=$host.'/api/Api_Authorization/deleteModuleMenu';
		//读取公司信息
		$data['token']=$this->token;
		$data['moduleMenuId']=$moduleMenuId;
		$res=$this->curlPost($url,$data);
		return $res;
   }
   /**
    * curl request for post
    * @param  [type] $url  [description]
    * @param  array $data 
    * @return [type]       [description]
    */
   protected function curlPost($url,$data)
   {
   		$data=http_build_query($data);
   		 //初使化init方法
		   $ch = curl_init();
		   //指定URL
		   curl_setopt($ch, CURLOPT_URL, $url);
		   //设定请求后返回结果
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		   //声明使用POST方式来进行发送
		   curl_setopt($ch, CURLOPT_POST, 1);
		   //发送什么数据呢
		   curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		   //忽略证书
		   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		   //忽略header头信息
		   curl_setopt($ch, CURLOPT_HEADER, 0);
		   //设置超时时间
		   curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		   //发送请求

		   $output = curl_exec($ch);
		 if($output === FALSE ){
		 	return json_decode(zy_json(false,"CURL Error:".curl_error($ch),null,-3),true);
		 }
		//关闭curl
		curl_close($ch);
		 $output=$this->getSubstr($output);
		 $output=json_decode($output,true); 
		   //返回数据
		 return $output;
   }

   /**
    * curl request for  get
    * @param  [type] $url  [description]
    * @return [type]       [description]
    */
   protected function curlGet($url)
   {
   		//连接远程接口
		/*********使用curl**************/
		// 1. 初始化
		 $ch = curl_init();
		 // 2. 设置选项，包括URL
		 curl_setopt($ch,CURLOPT_URL,$url);
		 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		 curl_setopt($ch,CURLOPT_HEADER,0);
		 // 3. 执行并获取HTML文档内容
		 $output = curl_exec($ch);
		 // 4. 释放curl句柄
		 curl_close($ch);
		 if($output === FALSE ){
		 	return zy_json(false,"CURL Error:".curl_error($ch),null,-3);
		 }
		 $output=$this->getSubstr($output);
		 $output=json_decode($output,true);
		 return $output;
   }
   /**
    * [firstLogin description]
    * @return [type] [description]
    */
   public function firstLogin($data)
   {
   		//读取配置
		$host=Config::get('admin_module_config.api_host');
		$url=$host.'/api/Api_Login/doLogin';
		$data['username']=base64_encode($data['username']);
		$data['password']=base64_encode($data['password']);
		$res=$this->curlPost($url,$data);
		return $res;
   }	
   /**
    * 
    */
   public function updateActiveStatus($data)
   {
   		//读取配置
		$host=Config::get('admin_module_config.api_host');
		$url=$host.'/api/Api_Login/updateActiveStatus';
		$data['username']=base64_encode($data['username']);
		$data['password']=base64_encode($data['password']);
		$res=$this->curlPost($url,$data);
		return $res;

   }

   /**
    * MODIFY PWD
    */
   public function modifyPwd($data)
   {
   		//读取配置
		$host=Config::get('admin_module_config.api_host');
		$url=$host.'/api/Api_Authorization/modifyPwd';
		//连接远程接口
		$data['token']=$this->token;
		$data['password']=base64_encode($data['password']);
		$res=$this->curlPost($url,$data);
		return $res;
   }
}
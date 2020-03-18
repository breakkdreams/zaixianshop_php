<?php

/**
 * @Author: 刘洪
 * @Date:   2018-11-08 10:34:46
 * @Last Modified by:   user
 * @Last Modified time: 2018-12-15 13:51:55
 */
namespace app\api\controller;

use think\Db;
use app\admin\model\AdminMenuModel;
use app\api\model\AccessModel;


/**
 * app菜单管理
 */
class ApiMenuAccessController extends \think\Controller
{
	/**
	 *@method get_accessmenu()根据用户id查询被授权菜单 
	 *@param  $userid 用户id
	 *@return 1  请求类型错误或者用户id不存在
	 */
	public function get_accessmenu()
	{
		if(!$this->request->isPost()) return 1; //只接受post请求

		$userid=$this->request->post('userid');
		//检查userid是否存在  id不存在则返回1
		$id=Db::name('user')->where('id',$userid)->find();
		if(!$id) return 1;

		//入驻app上的菜单  
		$accessApp    = Db::name('AdminMenu')->where('isapp=1')->order(["list_order" => "ASC"])->select()->toArray();


		//根据角色id 查询被授权菜单 []
		
		 //框架闭包子查询
         /*
		 $m=Db::table('cmf_auth_access')->field('rule_name')->where('role_id','=',function($query) use($userid){

		 	$query->table('cmf_role_user')->where('user_id','=',$userid)->field('role_id');

		 } )->select()->toArray();*/


		$subSql="(select role_id from cmf_role_user where user_id=".$userid.")";//子查询语句

		$sql="select rule_name from cmf_auth_access where role_id=".$subSql;//主查询

		 $m=Db::query($sql);//查询用户被授权的菜单项
		 $userAccessMenu=[];

		 
		 //数组重组存入userAccessMenu
		 foreach ($m as $key => $value) {
		 	
		 	array_push($userAccessMenu,$value['rule_name']);
		 }
		 
		 
		 $menus=[];//暂存菜单容器

		 //遍历用户菜单项  存在于入驻app上的菜单 加入容器中 $menus
		 foreach ($accessApp as $key => $value) {

		 	$app=$value['app'];
		 	$controller=$value['controller'];
		 	$action=$value['action'];

		 	$str=strtolower("$app/$controller/$action");

		 	//不为空重组输出用户被授权菜单  || 为空输出所有菜单
		 	if(in_array($str, $userAccessMenu) || empty($userAccessMenu)){

		 		$item['menu_id']=$value['id'];
		 		$item['menu_name']=$value['name'];

		 		array_push($menus,$item);//重组菜单只包含菜单id和菜单名称

		 	}

		 }

		//返回被授权菜单列表
		return json($menus);
	}


	public function test($id){
		$m=new AccessModel();

		dump($m->readDepartmentLeader($id));
		dump($m->readRootAdmin());
	}
}
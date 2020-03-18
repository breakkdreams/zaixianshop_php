<?php
namespace  app\admin\model;

use think\Model;
use think\Db;
use think\Cache;

/**
 * 读取用户权限模型类
 * @package app\admin\Model
 * @access public 
 * @copyright 卓远网络
 * @author 刘洪
 * @date 2019-1-9
 * @last Modified by:   user
 * @last Modified time: 2019-1-9
 */

class AccessModel extends Model
{
	/**
	 *当前用户所在公司最高角色信息
	 * @var array
	 */
	public  $company_highest_role_info=null;

	/**
	 * 当前用户所在公司信息
	 * @var array 
	 */
	public $company_info=null;

	/**
	 * 子类构造
	 * 
	 */
	function __construct()
	{
		//执行父类构造
		parent::__construct();

		//获取公司最高信息角色
		$this->company_highest_role_info=$this->get_company_highest_role_info();

		//获取当前用户所在公司信息
		$this->company_info=$this->get_company_info(cmf_get_current_admin_id());

	}

	/**
	 * 更新公司ceo授权菜单
	 *@param  $companyId 公司id
	 *@param  $menuData 菜单数据
	 * @param  $versions_id  版本id
	 */
	public function update_ceo_menu($companyId,$menuData,$versions_id)
	{
		//所需参数 公司id  菜单数据
		//获取公司最高角色id
		$list=[];
		$highest_role=$this->get_company_highest_role_info($companyId);
		foreach ($menuData as $key => $value) {
			$v['role_id']=$highest_role['id'];
			$v['company']=$companyId;
			$v['rule_name']=$menuData[$key]['rule_name'];
			$v['type']=$menuData[$key]['type'];
			$v['menu_id']=$menuData[$key]['menu_id'];
			$v['access_versions_id']=$versions_id;
			$list[]=$v;
		}
		$res=Db::name('auth_access')->insertAll($list);
		return $res;
	}

	/**
	 * 获取公司信息 部门
	 * @param $user_id 用户id   默认获取当前登入用户id  需要查询指定用户所在公司请传入此参数
	 * @return array  公司信息 包含部门信息
	 */
	public function get_company_info($user_id=null)
	{
		if(empty($user_id)){
			$user_id=cmf_get_current_admin_id();
		}
		$res=Db::name('user_attach')->alias('a')->join('company b','a.company_id  =  b.id')->where('a.user_id',$user_id)->field('b.id,b.company_name,b.company_type')->find();
		if(!empty($res)){
			//读取公司部门信息
			$res['department']=Db::name('department')->where('company_id','=',$res['id'])->select();
		}
		return  empty($res)?null:$res;
	}
	/**
	 * 获取公司信息
	 * @return [type] [description]
	 */
	public function getCompanyInfo()
	{
		$res=Db::name('company')->order('create_time desc')->find();
		return $res;
	}	
	/**
	 * [createAccessId description]
	 * @return [type] [description]
	 */
	public function createAccessId()
	{
		Cache::set('zy_token',null);
		$str=Cache::get('zy_token');
		if(!empty($str) && $str!=false){
			return $str;
		}
		//访问码  公司id+管理员id
		$res=$this->getCompanyInfo();
		if(empty($res)){
			return null; 
		}
		$str['ACCESSID']=md5(md5($res['company_id'].$res['admin_id']));
		$user=Db::name('user')->where('id',$res['super_admin'])->find();
		$str['KEYID']=md5(md5($res['super_login'].$user['user_pass']));
		$str['ADMINID']=base64_encode($res['admin_id']);
		Cache::set('zy_token',$str,60);
		return $str;
	}
		/**
		 * 	获取用户的角色信息
		 * 	@param $userId  用户id
		 * @return array 用户的角色信息
		 */
	
	public function get_user_role_info($userId)
	{
		$res=Db::table('cmf_role')->where('id','=',function($query) use ($userId){
				$query->table('cmf_user_attach')->where('user_id',$userId)->field('role_id');
		})->find();
		return $res;
	}


	/**
	 * 根据角色id查询授权菜单
	 * @return 授权菜单
	 */
	public function get_menu($roleId)
	{
		//根据角色id去  role_user中获取角色授权菜单
		$menu=Db::name('auth_access')->where('role_id',$roleId)->select();
		return $menu;
	}

	/**
	 * 读取公司最高角色信息 有就返回最高角色信息  没有角色返回null
	 * @param companyId 公司id  默认获取的是当前登入角色的公司id  （注意） 需查询指定公司角色信息请传入此参数
	 */
	public function get_company_highest_role_info($companyId=null)
	{
		if($companyId==null){
			//读取公司信息
			$companyId=$this->get_company_info(cmf_get_current_admin_id())['id'];
		}
		
		$roles=Db::name('role')->where('company_id',$companyId)->select();

		$role=null;

		foreach ($roles as $key => $value) {

			$v=$this->get_role_parent_id($value['parent_id'],$roles);
			//如果返回的父级id 等于当前遍历数据的父级id
			if($v==$value['parent_id']){
				//保存最高角色信息
				$role = $value;
				break;
			}
		}
		return $role;
	}

	/**
	 *  获取传入角色的最高父级id   关联get_company_highest_role_info方法
	 * @param  	int 		$id  要查找的id
	 * @param  	array 	$arr       角色数组
	 * @return 	int 		最高父级id
	 */
	protected  function get_role_parent_id($id,$arr)
	{
		$flag=null;

		foreach ($arr as $key => $value) {
			//如果查找的id 在数据中有记录 那就跳出当前循环 继续将记录的parent_id往上查
			//否则flag保持之前记录直到循环结束
			if($id==$value['id']){

				$flag=$value['parent_id'];

				break;
			}
		}
		//如果没有查到数据就返回当前$id  否则返回找到的parent_id
		if(empty($flag)){

			return $id;

		}else{

			return $this->get_role_parent_id($flag,$arr);
		}
	}

	/**
	 * 读取当前登录用户所在公司的角色层次 
	 * depth 栏目深度  
	 * [get_role_hierarchy description]
	 * @param  [type]  $roles     [description]
	 * @param  [type]  $id        [description]
	 * @param  integer $depth 栏目遍历深度  null,0 无限级栏目
	 * @param  integer $level     记录层级数
	 * @return array 返回栏目
	 */
	public function get_role_hierarchy($roles,$id,$depth=null,$level=0)
	{
		$list=[];
		//栏目深度 null,0 无限级栏目  
		$flag=empty($depth)?true:($depth>$level);
		foreach ($roles as $key => $value) {
			if($value['parent_id']==$id && $flag){
				$v['level']=$level;
				$v['id']=$value['id'];
				$v['parent_id']=$value['parent_id'];
				$v['text']=$value['name'];
				$v['nodes']=$this->get_role_hierarchy($roles,$value['id'],$depth,$level+1);
				if($depth==null && empty($v['nodes'])){
					unset($v['nodes']);
				}
				$list[]=$v;
			}
		}
		return $list;
	}
/**
 * 菜单数据勾选
 * @return boolean [description]
 */
	public function isChecked($data,$selectData)
	{
		 foreach ($data as $key => $value) {
            $app=$value['app'];
            $controller=$value['controller'];
            $action=$value['action'];
            $x="$app/$controller/$action";
           foreach ($selectData as $k=> $v) {
                if($x==$v['rule_name']){
                    $value['checked']=true;
                    $data[$key]=$value;
                }
           }
        }
		return $data;
	}

		/**
	 * 构建树形菜单必须数据 配合zy_treeview.js插件使用
	 * 必要的返回字段说明 :
	 * 	text  string 显示的文本
	 * 	id  int  栏目的id
	 * 	parent int 栏目的父级id
	 * 	checked bool 是否被选中  用于带复选框的树形菜单使用，不使用可忽略
	 * 	nodes  array 子级栏目
	 * @param  array  $data 带有分级的源数据
	 * @param  int $id 定义一级栏目的id  默认0 常用parent_id
	 * @param  int  $depth 遍历深度，需要提取菜单级数  默认无限极提取
	 * @param  int $level  保存栏目的当前层级深度 默认0  无需传入
	 * @return array  返回带层级的栏目数据  配合插件可之间生成树形菜单
	 * 
	 */
	public function get_hierarchy($data,$id,$depth=null,$level=0)
	{
		$list=[];
		//栏目深度 null,0 无限级栏目  
		$flag=empty($depth)?true:($depth>$level);
		foreach ($data as $key => $value) {
			if($value['parent_id']==$id && $flag){
				$arr['text']=$value['name'];
				$arr['level']=$level;
				$arr['checked']=isset($value['checked'])?$value['checked']:false;
				$arr['id']=$value['id'];
				$arr['parent_id']=$value['parent_id'];
				$arr['nodes']=$this->get_hierarchy($data,$value['id'],$depth,$level+1);
				if(empty($arr['nodes'])){unset($arr['nodes']);}
				$list[]=$arr;
			}
		}
		return $list;
	}



	/**
	 * 获取公司角色所有id 用于角色授权  
	 * 备注：为完全控制子账号，再不传入company 公司id时 对公司层级没有进行区分，需要查询指定公司时请传入公司id。
	 * @param  [type] $roles 角色列表  
	 * @param  [type] $id    最高角色id   
	 * @param  $company   公司id   需要查询指定公司时请传入此参数
	 * @return array  返回的是角色$id下所有角色id
	 */
	public function get_company_role_ids($roles,$id,$company=null,$ids=[])
	{
		//将被查询的id一并返回
		if(!in_array($id,$ids)){
			$ids[]=$id;
		}
		
		foreach ($roles as $key => $value) {
			//如果查询的id 和遍历的数据父级id相等 记录该条数据，并继续深度查询
			if($id==$value['parent_id']){
				//查询指定公司
				if(!empty($company) && $value['company_id']!=$company) {
					continue;
				}
				$ids[]=$value['id'];
				$ids=$this->get_company_role_ids($roles,$value['id'],$company,$ids);
			}
		}
		return $ids;
	}

	/**
	 * 获取所在公司角色列表信息
	 * @param $companyId  int  公司id  默认查询当前登入角色所在公司的id （注意： 需查询指定公司角色列表请传入此参数）
	 * @return  array  角色所在公司的角色信息列表
	 */
	public function get_company_role_list($companyId=null)
	{
		if(empty($companyId)){
			$companyId=$this->get_company_info()['id'];//获取登入角色所在的公司
		}
		//数据库读取角色表
		$roles=Db::name('role')->select();
		//获取公司最高角色 
		$highest_role=$this->get_company_highest_role_info($companyId);
		//获取公司最高角色下所有id 包括最高角色
		$ids=$this->get_company_role_ids($roles,$highest_role['id']);
		//根据角色ids 在角色表中筛选所在公司的角色列表信息
		$list = [];
		foreach ($roles as $key => $value) {
			//如果遍历的数据id 存在于 $ids[]中 
			if(in_array ($value['id'], $ids) ){
				//确认过眼神 是想要的数据 带走
				$list[] = $value;
			}
		}
		return $list;
	}

	/**
	 * 读取当前登入用户的角色信息
	 * @param userId  可查询传入的用户id  默认查询当前登入用户
	 */
	public function getRoleInfoByUserId($userId=null)
	{
		//获取当前用户id
		if(empty($userId)){
			$userId=cmf_get_current_admin_id();
		}
		$res=Db::table('cmf_role')->where('id','=',function($query) use ($userId){
			$query->table('cmf_user_attach')->where('user_id',$userId)->field('role_id');
		})->find();
		return $res;
	}

	/**
	 * 读取用户所在公司角色下的所有用户
	 * @param  userId 用户id 
	 * @return array userIds  角色下所有用的id  否则null
	 */
	public function getAllUserTheCurrentRole($userId)
	{
		//获取用户的角色id
		$current_role=$this->getRoleInfoByUserId($userId);

		if(empty($current_role)){
			return null;
		}
		$isBase=Db::name('role')->where('parent_id',$current_role['id'])->find();
		//如果在最基层返回自己的id
		if(empty($isBase)){
			$user[]=$userId;
			return $user;
		}
		//获取用户所在公司所有角色信息
		$role_list=$this->get_company_role_list($current_role['company_id']);
		//获取当前登入角色所在公司传入角色下的所有id  get_company_role_ids
		$ids=$this->get_company_role_ids($role_list,$current_role['id'],$current_role['company_id']);
		//根据公司角色ids查询角色下的所有用户id
		$userIds=Db::name('user_attach')->where('role_id','in',$ids)->column('user_id');
		return $userIds;
	}

	/**
	 * 判断登入的角色是否是最底层
	 */
	public function baseRole()
	{

	}

}
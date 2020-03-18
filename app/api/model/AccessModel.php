<?php

namespace  app\api\Model;

use think\Model;
use think\Db;

/**
 * 读取用户权限模型类
 * @package app\api\Model
 * @access public 
 * @copyright 卓远
 * @property protected $table string 模型表
 * @method array readAccess(int $userid) 返回自己和下级id数组
 * @author 刘洪
 * @date 2018-11-09 14:45:57
 * @last Modified by:   user
 * @last Modified time: 2018-11-14 09:43:32
 */
class AccessModel extends Model
{
	//表
	protected $table='cmf_role_user';

	//用户角色id
	private $roleId="";

	//管理权限下的所属id 数组[]
	private $accessIds=null;

	/**
	 * readAccess  读取权限
	 * @access public 
	 * @param 	$userid 	用户id
	 * @return	返回可读取权限的用户id  || null[]  数组
	 *	
	 */
	public function readAccess($userid)
	{
		$userid=intval($userid);
		$this->roleId=$this->readRole($userid);//读取用户角色
		//echo '用户角色：';
		//dump($this->roleId);

		//角色不存在返回空
		if(empty($this->roleId)){

			$this->accessIds=[];

		}/*else if(in_array(1,$this->roleId)){

			//最高权限角色返回所有后台用id usertype=1
			$this->accessIds=Db::name('user')->where('user_type',1)->column('id');
			//解决最高权限同级无法查看信息的问题，不需要信息共享，用开子账号的方式解决

		}*/
		else{

			//获取子级id
			$this->readNode($this->roleId);

			//$this->accessIds=Db::name('user')->where('id','in',$this->accessIds)->column('id');

			//返回的id列表中加入自己的id
			//如果角色下没有找到id 已经在最底层  也返回自己id 数组

			array_push($this->accessIds,$userid);
		}
		
		return $this->accessIds;

	}


	/**
	 * readRole	查询角色id
	 * @access private
	 * @param 	$userid 	用户id
	 * @return  角色id 		数组[]
	 */
	private function readRole($userid)
	{

		return $this->where('user_id',$userid)->column('role_id');
	}

	/**
	 * readNode 	查询下属id
	 * @access private
	 * @param 	$roleId 	角色id
	 * 
	 */
	private function readNode($roleId,$accessIds=[])
	{
		////把角色id当作parentid 去role表查询下属角色id
		$nodeid=Db::name('role')->where('parent_id','in',$roleId)->column('id');

		//dump($nodeid);//exit;

		if(empty($nodeid)){
			//如果没有下属角色了就结束递归
			$this->accessIds= $accessIds;
		}else{

			//利用角色nodeid 去role_user表里查询所属角色的用户id
			$ids=$this->where('role_id','in',$nodeid)->column('user_id');

			$accessIds=array_merge($ids,$accessIds);//合并数据

			$this->readNode($nodeid,$accessIds);//传入下属角色id继续深度查询
		}
	}

	/**
	 * 读取部门负责人id
	 * @return [type] [description]
	 */
	public function readDepartmentLeader($userId=-1)
	{
		//所在部门id
		$departmentId=Db::name('department_user')->where('user_id='.$userId)->find()['department_id'];
		if(empty($departmentId)){
			return null;
		}
		//获取角色id
		$roleId=Db::name('role_user')->where('user_id='.$userId)->find()['role_id'];
		//获取所在部门最高角色
		return $this->findMaxRole($roleId,$departmentId);
	}

	private function findMaxRole($roleId,$departmentId)
	{
		//根据角色id查询数据
		$role=Db::name('role')->where('id='.$roleId)->find();
		//还在同一个部门 继续向上查
		if($departmentId==$role['department_id']){
			return $this->findMaxRole($role['parent_id'],$departmentId);
		}else{
			//如果是当前部门最高级 返回负责人id
			$d=Db::view('role')->view('role_user','user_id','role.id=role_user.role_id')->where('parent_id='.$roleId)->find()['user_id'];
			return $d;
		}

	}


	/**
	 * 读取最高权限管理员
	 * @return [type] [description]
	 */
	public function readRootAdmin()
	{
		//读取最高部门的id
		$departmentId=Db::name('department')->where('parent_id=0')->find()['id'];
		//读取最高权限管理员列表
		$ids=Db::view('role')->view('role_user','user_id','role.id=role_user.role_id')->where('department_id='.$departmentId)->column('user_id');
		return $ids;
	}


}
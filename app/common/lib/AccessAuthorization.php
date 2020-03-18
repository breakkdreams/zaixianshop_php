<?php 
namespace app\common\lib;

use think\Db;

/**
 * 
 *数据访问权限类(AccessAuthorization)
 *命名空间： app\common\lib
 *权限依据：给每个用户赋予角色信息，根据角色授权
 *权限读取规则：	
 *		1、上级可读取下级信息
 *		2、平级不能互相读取
 *公开静态调用方法:
 *		static getAccessibleIdsByUserId( $userId ) 	根据用户id 读取可访问的(下级)用户id
 *  	static getUserInfo( $userId )   				获取用户的个人信息，包含公司id ，角色id  部门id 等信息
 *  	static gethighestRoleInfo ( $userId )			获取用户所在公司的最高角色信息
 */

class AccessAuthorization 
{
	/**
	 * 根据用户角色读取可访问的用户id
	 * @param   $userId 	用户id
	 */
	public static function getAccessibleIdsByUserId( $userId )
	{
		if( empty( $userId ) ) return [];
		//获取用户的角色id
		$userAttach = Db::name( 'user_attach' )->where( 'user_id' , $userId )->find();
		if( empty( $userAttach ) ) return [];
		$companyId = $userAttach[ 'company_id' ];
		//查询当前用户所在公司的角色
		$isBase = Db::name('role')->where( 'parent_id' , $userAttach ['role_id'] )->where( 'company_id' , $companyId ) ->find();
		//如果在最基层返回自己的id 平级查看不能互相查看
		if( empty( $isBase ) ) return [ $userId ] ;
		
		//读取当前用户所在公司的下级角色
		$roleIds = ( new AccessAuthorization() )->getSubordinateRoleByRoleId( $userAttach ['role_id'] , $companyId );

		//获取当前用户所在公司的角色
		$userIds = Db::name( 'user_attach' )->where( "role_id" , 'IN' ,$roleIds ) ->where ( 'company_id' , $companyId )->column( 'user_id' );
		array_push( $userIds, $userId );
		return $userIds;
	}

	/**
	 * 获取下级角色
	 * @return [type] [description]
	 */
	private function getSubordinateRoleByRoleId( $roleId , $companyId )
	{
		$list = Db::name( 'role' )->where( 'company_id' , $companyId )->select()->toArray();
		return $this->getSubordinateRoleRecursion( $list , $companyId , $roleId );
	}

	/**
	 * 递归查询
	 * @return [type] [description]
	 */
	private function getSubordinateRoleRecursion( $list , $companyId , $roleId )
	{
		$returnList = [];
		foreach ($list as $key => $value) {
			if( $value ['parent_id'] == $roleId ){
				$returnList [] = $value ['id'];
				$returnList = array_merge( $returnList , $this->getSubordinateRoleRecursion( $list , $companyId , $value['id'] ) );
			}
		}
		return $returnList;
	}



	/**
	 * 读取当前用户当前公司里的上级角色id
	 * @return [type] [description]
	 */
	public static function getHigherUpIdsByuserId( $userId )
	{
		if( empty( $userId ) ) return [];
		//获取用户的角色id
		$userAttach = Db::name( 'user_attach' )->where( 'user_id' , $userId )->find();
		if( empty( $userAttach ) ) return [];
		$companyId = $userAttach ['company_id'];
		$roleInfo = Db::name( 'role' )->where( 'id' , $userAttach ['role_id'] ) -> where( 'company_id' , $companyId ) ->find();
		$isUp = Db::name('role')->where( 'id' , $roleInfo ['parent_id'] )  -> where( 'company_id' , $companyId ) ->find();
		//如果在最上层返回自己的id
		if( empty( $isUp ) ) return [ $userId ] ;
		
		$roleIds = ( new AccessAuthorization() )->getHigherUpRole( $userAttach ['role_id'] , $compayId );

		$userIds = Db::name( 'user_attach' )->where( "role_id" , 'IN' ,$roleIds ) -> where( 'company_id' , $companyId ) ->column( 'user_id' );
		return $userIds;
	}

	/**
	 * 获取上级角色
	 */
	private function getHigherUpRole( $roleId  , $companyId )
	{
		$list = Db::name( 'role' )->where( 'company_id' , $companyId )->select()->toArray();
		return $this->getHigherUpRoleRecursion( $list , $roleId , $companyId );
	}

	/**
	 * 递归查询
	 */
	private function getHigherUpRoleRecursion( $list , $roleId ,$companyId )
	{
		$returnList = [];
		foreach ($list as $key => $value) {
			if( $value ['id'] == $roleId ){
				$returnList [] = $value ['parent_id'];
				$returnList = array_merge( $returnList , $this->getSubordinateRoleRecursion( $list, $value['parent_id'] ) , $companyId );
			}
		}
		return $returnList;
	}


	/**
	 * 获取用户的个人信息，包含公司id as company_id ，角色id as role_id   部门id  as department_id 等信息
	 */
	public static function getUserInfo( $userId )
	{
		if( empty( $userId ) ){
			return [];
		}
		$userInfo = Db::name( 'user' )
				    ->field( 'a.* , b.*') 
				    ->alias('a')
					->join('user_attach b','a.id = b.user_id')
					->where( 'a.id' , $userId )
				    ->find();
		return $userInfo ;
	}

	/**
	 * 获取当前用户公司最高角色信息
	 */
	public static function getHighestRoleInfo( $userId )
	{
		$info = self::getUserInfo( $userId );
		$companyId = $info ['company_id'];
		$roleList = Db::name( 'role' )->where( 'company_id' , $companyId )->select()->toArray();
		return  ( new AccessAuthorization() )->getHighestRoleInfoRecursion( $roleList );
	}

	/**
	 * 递归
	 */
	private function getHighestRoleInfoRecursion( $list , $roleInfo = [] )
	{
		foreach ( $list as  $arr ) {
			if( empty( $roleInfo ) || $arr['id'] == $roleInfo ['parent_id'] ){
				$roleInfo = $this->getHighestRoleInfoRecursion( $list , $arr );
			}
		}
		return $roleInfo ;
	}



}
<?php
namespace app\admin\model;
/**
 * @Author: user
 * @Date:   2019-04-01 18:55:35
 * @Last Modified by:   user
 * @Last Modified time: 2019-04-04 11:53:35
 */
use think\Model;
use think\Db;
use app\admin\model\AccessModel;
/**
 * 
 */
class AuthAccessModel extends Model
{
	/**
	 * 获取菜单
	 * @return [type] [description]
	 */
	public function updateAccessMenu($menus)
	{
		//读取本地ceo授权菜单项
		//获取最高权限角色id
		$accessModel=new AccessModel();
		$highest=$accessModel->get_company_highest_role_info();
		//读取最高角色授权菜单
		$authAccessMenu=Db::name('auth_access')->where('role_id',$highest['id'])->select();
		$authAccessMenu=json_decode($authAccessMenu,true);
		//比对数据
		$result=$this->dataProcessing($authAccessMenu,$menus);
		//删除有菜单，但是模块不存在的数据
		
		//更新数据库信息
		if(!empty($result['delete'])){
			//删除
			Db::name('auth_access')->where('menu_id','IN',$result['delete']['menu_id'])->delete();
		}
		if(!empty($result['insert'])){
			//新增
			$insert=$this->regroupData($result['insert'],$highest['id']);
			Db::name('auth_access')->insertAll($insert);
		}
		$company=Db::name( 'company' )->where('id = 1')->find();
		foreach ($menus as $key => $value) {
			$status = $this->checkModuleIsExists( $value['app'] );
			if( !$status ){
				//删除
				$app = $value['app'];
				$controller = $value['controller'];
				$action = $value['action'];
				$rule_name = "$app/$controller/$action";
				$res = Db::name( 'auth_access' )->where( 'rule_name' , $rule_name )->where( 'company' , $company['company_id'] )->delete();
			}
		}
	}
		/**
	 * 更新权限菜单20190401
	 * @param $versionsId int  版本id
	 * @param $newMenu [] 新菜单
	 */
	protected function dataProcessing ($oldMenu,$newMenu){
		$delete=[];//多余数据
		$insert=[];//新数据
		//数据比对
		//比对逻辑  遍历新菜单  二重循环遍历旧菜单   
		//判断方法  新菜单menu_id  与  旧菜单menu_id一一比对，如果不存在就是要新增的，反向删除多余的
		//
		foreach ($newMenu as $key => $value) {
			$isInsert=true;
			foreach ($oldMenu as $ok => $ov) {
				if($value['id']==$ov['menu_id']){
					$isInsert=false;
				}
			}
			if($isInsert){//保存要新增的数据
				$insert[]=$value;
			}
		}
		//找到要删除的数据
		foreach ($oldMenu as $key => $value) {
			$isDelete=true;
			foreach ($newMenu as $nk => $nv) {
				if($value['menu_id']==$nv['id']){
					$isDelete=false;
				}
			}
			//保存要删除的数据
			if( $isDelete ){
				$delete['id'][]=$value['id'];
				$delete['menu_id'][]=$value['menu_id'];
			}
		}
		return ['delete'=>$delete,'insert'=>$insert];
	}

	/**
	 * 重组数据
	 */
	protected function regroupData($data,$roleId)
	{
		$company=Db::name('company')->where('id=1')->find();
		$insert=[];
		foreach ($data as $key => $value) {
			$app = $value['app'];
			$controller=$value['controller'];
			$action=$value['action'];
			$v['role_id']=$roleId;
			$v['rule_name']="$app/$controller/$action";
			$v['type']=$value['type'];
			$v['menu_id']=$value['id'];
			$v['company']=$company['company_id'];
			$v['access_versions_id']=$company['versions_type'];
			$insert[]=$v;
		}
		unset($data['0']);
		return $insert;
	}
	/**
	 * 检测模块是否安装 未安装模块的时候菜单不显示
	 */
	private function checkModuleIsExists( $app )
	{
		$appArr = explode( '/' , $app );
		if( count($appArr) == 2 ){
			$path = PLUGINS_PATH.cmf_parse_name( $appArr[1] );
			if( $appArr[0] == 'plugin' && file_exists($path) ){
				return true;
			}
		}else{
			return true;
		}
		return false;
	}
}
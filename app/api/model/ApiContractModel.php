<?php

/**
 * @Author: user
 * @Date:   2018-12-18 15:06:30
 * @Last Modified by:   user
 * @Last Modified time: 2018-12-21 11:27:04
 */
namespace app\api\model;

use think\Model;
use think\Db;

/**
 * 线上合同模型
 */
class ApiContractModel extends Model
{
	protected $table="cmf_contract";

	/**
	 * 通过用户id获取合同列表
	 * @param $accessIds array 用户ids
	 * @return [type] [description]
	 */
	public function getContractList($accessIds)
	{
		//return $this->where('principal_id','in',$accessIds)->select();
		$list=Db::view('contract')->view('lx','name as client_name,mobile','lx.id = contract.client_id')->view('user','user_login as principal_name','contract.principal_id = user.id')->view('kehu','company','kehu.id=lx.kh_id')->where('principal_id','in',$accessIds)->select();
		return $list;
	}
	/**
	 * 新增数据
	 * @param  array $data 数据['hetong'=>,'contract'=>]
	 * @return int  新增成功返回自增id  新增失败返回false
	 */
	public function saveContract($data)
	{
		//hetong表存一份数据
		$res=Db::name('hetong')->insertGetId($data['hetong']);

		if($res){
			//contract中保存
			$data['contract']['hetong_id']=$res;//存入hetong表的新增id
			
			$res=$this->insertGetId($data['contract']);
		}
		
		return $res;
	}

	/**
	 * 更新数据
	 * @param  [type] $data['hetong'=>,'contract'=>]
	 * @return [type]       [description]
	 */
	public function updateContract($data)
	{
		//hetong表更新
		$res=Db::name('hetong')->where('id='.$data['hetong']['id'])->update($data['hetong']);

		if($res){
			//contract更新
			$res=$this->where('id='.$data['contract']['id'])->update($data['contract']);
		}
		
		return $res;
	}

	/**
	 * 删除合同
	 * @param  array 				['hetong_id'=>,'contract_id'=>]
	 * @return [type]             [description]
	 */
	public function deleteContract($id)
	{
		//先删除hetong表中的数据
		$res=Db::name('hetong')->where('id='.$id['hetong_id'])->delete();

		if($res){
			//删除contract表中数据
			$res=$this->where('id='.$id['id'])->delete();
		}
		return $res;
	}

	/**
	 * 获取合同，根据合同id
	 * @return [type] [description]
	 */
	public function getContractById($id)
	{
		return Db::view('contract')->view('lx','name as client_name,mobile','lx.id = contract.client_id')->view('user','user_login as principal_name','contract.principal_id = user.id')->view('kehu','company','kehu.id=lx.kh_id')->where('contract.id','=',$id)->find();
	}


}
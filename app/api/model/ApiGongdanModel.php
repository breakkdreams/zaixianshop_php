<?php

/**
 * @Author: user
 * @Date:   2018-11-01 14:29:49
 * @Last Modified by:   user
 * @Last Modified time: 2019-01-07 10:30:04
 */

namespace app\api\model;
use think\Model;
use think\Db;
/**
 * 工单模型
 */
class ApiGongdanModel extends Model
{
	//表名
	protected $table="cmf_gongdan";

	//通过工单id获取工单信息
	public function getInfoByGdId($id){
		return $this->where('id='.$id)->select();
	}

	/**
	 *通过userid获取工单列表 
	 */
	public function getListByUserid()
	{
		return $this->order('add_time desc')->select();
	}

	/**
	 * 新增工单
	 */

	public function insertGongdan($data)
	{
		$data['num']=time().rand(1111,9999);//工单编号
		$data['add_time']=time();//提交时间 时间戳
		$data['status']=1;//工单状态
		$data['state']=1;//消息状态
		return $this->allowField(true)->save($data);
	}
}
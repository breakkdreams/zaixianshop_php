<?php

/**
 * @Author: user
 * @Date:   2018-11-01 14:29:49
 * @Last Modified by:   user
 * @Last Modified time: 2018-11-05 15:07:39
 */

namespace app\api\model;
use think\Model;
use think\Db;
/**
 * 工单回复模型
 */
class ApiGongdanReplyModel extends Model
{
	//表名
	protected $table="cmf_gongdan_xq";

	/**
	 * 新增回复
	 */

	public function addReply($data)
	{
		$data['add_time']=time();
		return $this->allowField(true)->save($data);
	}

	/**
	 * 获取回复 根据工单id
	 */
	public function getReplyByGdId($id)
	{
		return $this->where('gd_id',$id)->order('add_time','desc')->select();
	}

	public function insertReply($data){
		$data['add_time']=time();
		return $this->allowField(true)->save($data);
	}
}
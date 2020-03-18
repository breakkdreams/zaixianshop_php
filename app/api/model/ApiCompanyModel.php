<?php

/**
 * @Author: user
 * @Date:   2019-03-05 10:49:50
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-05 10:58:37
 */
namespace app\api\model;
use think\Model;
use think\Db;
/**
 * 公司管理模型类
 */
class ApiCompanyModel extends Model
{
	protected $table='company';
	//获取行业分类信息列表
	public function  getCompanyTypeList()
	{
		$res=Db::name('company_type')->select();
		return $res;
	}
}
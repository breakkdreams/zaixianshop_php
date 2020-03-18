<?php

/**
 * @Author: user
 * @Date:   2019-03-05 10:48:44
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-05 11:04:19
 */
/**
 * 公司管理api
 */
namespace app\api\controller;
use think\Controller;
use think\Db;
use app\api\model\ApiCompanyModel;

/**
 * 公司管理api控制器类
 */
class ApiCompanyController extends Controller
{
	//模型
	protected $companyModel=null;
	/**
	 * 获取公司行业分类列表信息
	 * @return  json  分类信息列表
	 */
	public function getCompanyTypeList()
	{
		$companyModel=new ApiCompanyModel();
		$result=$companyModel->getCompanyTypeList();
		return  cmf_replace_content_file_url(htmlspecialchars_decode(json_encode($result,JSON_UNESCAPED_UNICODE)));
	}
}

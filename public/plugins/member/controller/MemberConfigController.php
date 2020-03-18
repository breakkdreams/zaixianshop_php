<?php 
namespace plugins\member\controller; 

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

/**
 * 会员基本配置控制器
 */
class MemberConfigController extends PluginAdminBaseController
{
	public function index()
	{	
		$request = request();
		$module_info = getModuleConfig('member','config','config.json');
		$module_info = json_decode($module_info,true);

		$data['agreement'] = $module_info['agreement'];
		$data['integral'] = $module_info['integral'];
		$this->assign('data',$data);
		return $this->fetch('/memberConfig/index');

	}


	public function editSetting(){
		$param = $this->request->param();
		$config = [
			'integral'=>$param['integral'],
			'agreement'=>$param['agreement']
		];
		saveModuleConfigData('member','config','config.json',$config);

		$this->success("修改成功");
	}

}	
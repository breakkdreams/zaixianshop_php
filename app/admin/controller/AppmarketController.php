<?php
namespace app\admin\controller;

use cmf\controller\AdminBaseController;

/**
 * 应用市场
 */
class AppmarketController extends AdminBaseController
{
	//应用市场首页
	public function index ()
	{
		$url = cmf_plugin_url('SubAppmarket://adminIndex/index' );

		$this->redirect( $url );
	}
}
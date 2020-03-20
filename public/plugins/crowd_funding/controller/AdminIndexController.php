<?php
namespace plugins\crowd_funding\controller;

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

//AdminIndexController类和类的index()方法是必须存在的 index() 指向admin_index.html模板也就是模块后台首页
// 并且继承PluginAdminBaseController
//

class AdminIndexController extends PluginAdminBaseController
{
    protected function _initialize()
    {
        parent::_initialize();
        $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        }
    }

    /**
     * @adminMenu(
     *     'name'   => '众彩模块',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '众彩模块',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $demo = new \plugins\crowd_funding\controller\CrowdFundingController();
        return $demo->index();
    }


}

<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace plugins\zymember\controller; //Demo插件英文名，改成你的插件英文就行了

use cmf\controller\PluginAdminBaseController;
use think\Db;

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
     * 会员模块
     * @adminMenu(
     *     'name'   => '会员模块',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '演示插件',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        echo cmf_plugin_url('Zymember://api_index/index');
        $users = Db::name("user")->limit(0, 5)->select();
        //$demos = PluginDemoModel::all();

        // print_r($demos);

        $this->assign("users", $users);


        $this->assign("users", $users);

        return $this->fetch('/admin_index');
    }

    /**
     * 演示插件设置
     * @adminMenu(
     *     'name'   => '演示插件设置',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '演示插件设置',
     *     'param'  => ''
     * )
     */
    public function setting()
    {
        $users = Db::name("user")->limit(0, 5)->select();
        //$demos = PluginDemoModel::all();

        // print_r($demos);

        $this->assign("users", $users);


        $this->assign("users", $users);

        return $this->fetch('/admin_index');
    }

}

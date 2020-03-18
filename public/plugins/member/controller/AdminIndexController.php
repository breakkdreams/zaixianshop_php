<?php
namespace plugins\member\controller; 

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

/**
* 需求配置首页
*@actionInfo(
*  'name' => '会员模块',
*  'symbol' => 'member',
*  'list' => [
*      'notice_one' => [
*          'demandName' => '消息推送' ,
*          'demandSymbol' => 'notice',
*          'explain' => '验证短信是否正确'
*      ],
*      'notice_two' => [
*          'demandName' => '消息推送' ,
*          'demandSymbol' => 'notice',
*          'explain' => '验证成功自动清空'
*      ],
*  ]
 *)
*/
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
     * 演示插件 菜单注解 一般修改name remark内容就行
     * @adminMenu(
     *     'name'   => '演示插件',
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
        $member = new \plugins\member\controller\MemberController();
        return $member->index();
    }




}

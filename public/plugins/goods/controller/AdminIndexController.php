<?php
namespace plugins\goods\controller; 

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

/**
* 需求配置首页
*@actionInfo(
*  'name' => '商品模块',
*  'symbol' => 'goods',
*  'list' => [
*      'member_one' => [
*          'demandName' => '消息模块' ,
*          'demandSymbol' => 'member',
*          'explain' => '登录用户是否收藏商品判断'
*      ],
*      'member_two' => [
*          'demandName' => '消息模块' ,
*          'demandSymbol' => 'member',
*          'explain' => '获取店铺信息'
*      ]
*  ]
 *)
*/

//AdminIndexController类和类的index()方法是必须存在的 index() 指向admin_index.html模板也就是模块后台首页
// 并且继承PluginAdminBaseController
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
        $goods = new \plugins\goods\controller\GoodsController();
        return $goods->index();
    }




}

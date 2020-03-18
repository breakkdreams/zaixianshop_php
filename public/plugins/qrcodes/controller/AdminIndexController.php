<?php
namespace plugins\qrcodes\controller;
//Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
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
    protected $config = array(
        'coupon_status' => array(
            '0'=>'未使用',
            '1'=>'已使用',
            '2'=>'已过期',
            '3'=>'已收回'
        )
    );
    /**
     * 菜单注解 一般修改name remark内容就行
     * @adminMenu(
     *     'name'   => '一维码二维码管理',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '二维码一维码',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $Qrcode = new QrcodeController();
        return $Qrcode->index();
    }

    /**
     * @adminMenu(
     *     'name'   => '测试更新菜单',
     *     'parent' => 'index',
     *     'display'=> true,
     *     'hasView'=> false,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '测试更新菜单',
     *     'param'  => ''
     * )
     */
    public function testEwmcd(){

    }
}
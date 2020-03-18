<?php
namespace plugins\wechat_pay\controller; 

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

/**
* 需求配置首页
*@actionInfo(
*  'name' => '微信支付模块',
*  'symbol' => 'wechat_pay',
*  'list' => [
*      'member_one' => [
*          'demandName' => '会员模块' ,
*          'demandSymbol' => 'member',
*          'explain' => '获取会员的会员信息'
*      ],
*      'fund_one' => [
*          'demandName' => '资金模块' ,
*          'demandSymbol' => 'fund',
*          'explain' => '充值成功之后，改变订单状态'
*      ],
*      'siteconfiguration_one' => [
*          'demandName' => '站点配置' ,
*          'demandSymbol' => 'SiteConfiguration',
*          'explain' => '获取地址'
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
     * @adminMenu(
     *     'name'   => '微信支付',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '微信支付',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $request = request();
        $module_info = getModuleConfig('wechat_pay','config','config.json');
        $module_info = json_decode($module_info,true);

        $data['wechatpay_off'] = $module_info['wechatpay_off'];
        $data['wechatpay_appid'] = $module_info['wechatpay_appid'];
        $data['wechatpay_appsecret'] = $module_info['wechatpay_appsecret'];
        $data['wechatpay_mchid'] = $module_info['wechatpay_mchid'];
        $data['wechatpay_key'] = $module_info['wechatpay_key'];
        $this->assign('data',$data);

        return $this->fetch();
    }

    public function editSetting(){
        $param = $this->request->param();
        $config = [
            'wechatpay_off'=>$param['setconfig']['wechatpay_off'],
            'wechatpay_appid'=>$param['setconfig']['wechatpay_appid'],
            'wechatpay_appsecret'=>$param['setconfig']['wechatpay_appsecret'],
            'wechatpay_mchid'=>$param['setconfig']['wechatpay_mchid'],
            'wechatpay_key'=>$param['setconfig']['wechatpay_key'],
        ];
        saveModuleConfigData('wechat_pay','config','config.json',$config);
        $this->success("修改成功");
    }


}

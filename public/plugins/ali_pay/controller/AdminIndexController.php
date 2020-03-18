<?php
namespace plugins\ali_pay\controller; 

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;


/**
* 需求配置首页
*@actionInfo(
*  'name' => '支付宝支付模块',
*  'symbol' => 'ali_pay',
*  'list' => [
*      'siteconfiguration_one' => [
*          'demandName' => '站点配置' ,
*          'demandSymbol' => 'SiteConfiguration',
*          'explain' => '获取当前的域名'
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
     *     'name'   => '支付宝支付',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '支付宝支付',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $request = request();
        $module_info = getModuleConfig('ali_pay','config','config.json');
        $module_info = json_decode($module_info,true);

        $data['alipay_off'] = $module_info['alipay_off'];
        $data['alipay_appid'] = $module_info['alipay_appid'];
        $data['alipay_private_key'] = $module_info['alipay_private_key'];
        $data['alipay_public_key'] = $module_info['alipay_public_key'];
        $data['alipay_gatewayUrl'] = $module_info['alipay_gatewayUrl'];
        $this->assign('data',$data);

        return $this->fetch();
    }


    public function editSetting(){
        $param = $this->request->param();
        $config = [
            'alipay_off'=>$param['setconfig']['alipay_off'],
            'alipay_appid'=>$param['setconfig']['alipay_appid'],
            'alipay_private_key'=>$param['setconfig']['alipay_private_key'],
            'alipay_public_key'=>$param['setconfig']['alipay_public_key'],
            'alipay_gatewayUrl'=>$param['setconfig']['alipay_gatewayUrl'],
        ];
        saveModuleConfigData('ali_pay','config','config.json',$config);
        $this->success("修改成功");
    }


}

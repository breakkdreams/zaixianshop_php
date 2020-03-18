<?php
namespace plugins\notice\controller; //Demo插件英文名，改成你的插件英文就行了

use cmf\controller\PluginAdminBaseController;
use plugins\Notice\Model\PluginMessageModel;
use think\Db;

class ConfigController extends PluginAdminBaseController
{   

    protected function _initialize()
    {
        parent::_initialize();
        $adminId = cmf_get_current_admin_id();
        //获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        }
    }


    /**
     * @adminMenu(
     *     'name'   => '邮箱配置',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '邮箱配置',
     *     'param'  => ''
     * )
     */
    public function index()
    {   
        $data = $this->request->param();

        $module_info = getModuleConfig('notice','config','email_config.json');

        $module_info = json_decode($module_info,true);

        $this->assign('data',$module_info);

        return $this->fetch();
    }


    /*
     * 保存邮箱配置
     */
    public function mailboxMsg(){

        $data = $this->request->param();

        unset($data['_plugin'],$data['_controller'],$data['_action']);

        saveModuleConfigData('notice','config','email_config.json',$data);

        $this->success("修改成功");
    }





    /**
     * 短信配置
     * @adminMenu(
     *     'name'   => '短信配置',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '短信配置',
     *     'param'  => ''
     * )
     */
    public function note()
    {   
        $data = $this->request->param();

        $module_info = getModuleConfig('notice','config','note_config.json');

        $module_info = json_decode($module_info,true);

        $this->assign('data',$module_info);

        return $this->fetch('/config/note');

    }

    

    /*
     * 保存短信配置
     */
    public function noteMsg()
    {
        $data = $this->request->param();

        unset($data['notice'],$data['_controller'],$data['_action']);

        saveModuleConfigData('notice','config','note_config.json',$data);

        $this->success("修改成功");
    }





    /*
     * 个推页
     */
    public function personalPush()
    {
        $data = $this->request->param();

        $module_info = getModuleConfig('notice','config','getui.json');

        $module_info = json_decode($module_info,true);

        $this->assign('peizhi',$module_info);

        return $this->fetch();
    }


    /*
     * 保存个推配置
     */
    public function saveGeTui()
    {
        $data = $this->request->param();

        unset($data['notice'],$data['_controller'],$data['_action']);

        saveModuleConfigData('notice','config','getui.json',$data);

        $this->success("修改成功");
    }




    /*
     * 站内信配置
     */
    public function InMail()
    {
        $data = $this->request->param();

        $module_info = getModuleConfig('notice','config','in_mail.json');

        $module_info = json_decode($module_info,true);

        $this->assign('peizhi',$module_info);

        return $this->fetch();
    }


    /*
     * 保存站内信配置
     */
    public function saveInMail()
    {
        $data = $this->request->param();

        unset($data['notice'],$data['_controller'],$data['_action']);

        saveModuleConfigData('notice','config','in_mail.json',$data);

        $this->success("修改成功");
    }






}

<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use app\admin\model\PluginModel;
use app\admin\model\HookPluginModel;
use think\Db;
use think\Validate;
use app\admin\model\AuthorizationModel;

/**
 * Class PluginController
 * @package app\admin\controller
 * @adminMenuRoot(
 *     'name'   =>'插件管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 20,
 *     'icon'   =>'cloud',
 *     'remark' =>'插件管理'
 * )
 */
class PluginController extends AdminBaseController
{

    public $pluginModuleId;

    public function _initialize()
    {
        parent::_initialize();
        $data=$this->request->action();
        if($data=='default'){
            die('REQUEST CONTROLLER IS NOT EXISTS ! ');
        }
    }
    
    public function test()
    {
        $res = (new \app\common\lib\ModuleAdminMenu())->getModuleMenuList( 'system' );
        /*$pluginData = new \app\admin\model\PluginDbTableModel();
        //$res = $pluginData->installDataTable( 'system' );
        $res = $pluginData->uninstallDataTable( 'system' );*/
        dump($res);
    }

    /**
     * 插件列表
     * @adminMenu(
     *     'name'   => '插件列表',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '插件列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $pluginModel = new PluginModel();
        $plugins     = $pluginModel->getList();
        $this->assign("plugins", $plugins);
        return $this->fetch();
    }

    /**
     * 插件启用/禁用
     * @adminMenu(
     *     'name'   => '插件启用禁用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '插件启用禁用',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $id = $this->request->param('id', 0, 'intval');

        $pluginModel = PluginModel::get($id);

        if (empty($pluginModel)) {
            $this->error('插件不存在！');
    
        }

        $status         = 1;
        $successMessage = "启用成功！";

        if ($this->request->param('disable')) {
            $status         = 0;
            $successMessage = "禁用成功！";
        }

        $pluginModel->startTrans();

        try {
            $pluginModel->save(['status' => $status], ['id' => $id]);

            $hookPluginModel = new HookPluginModel();

            $hookPluginModel->save(['status' => $status], ['plugin' => $pluginModel->name]);

            $pluginModel->commit();

        } catch (\Exception $e) {

            $pluginModel->rollback();

            $this->error('操作失败！');

        }

        $this->success($successMessage);
    }

    /**
     * 插件设置
     * @adminMenu(
     *     'name'   => '插件设置',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '插件设置',
     *     'param'  => ''
     * )
     */
    public function setting()
    {
        $id = $this->request->param('id', 0, 'intval');

        $pluginModel = new PluginModel();
        $plugin      = $pluginModel->find($id);

        if (empty($plugin)) {
            $this->error('插件未安装!');
        }

        $plugin = $plugin->toArray();

        $pluginClass = cmf_get_plugin_class($plugin['name']);
        if (!class_exists($pluginClass)) {
            $this->error('插件不存在!');
        }

        $pluginObj = new $pluginClass;
        //$plugin['plugin_path']   = $pluginObj->plugin_path;
        //$plugin['custom_config'] = $pluginObj->custom_config;
        $pluginConfigInDb = $plugin['config'];
        $plugin['config'] = include $pluginObj->getConfigFilePath();

        if ($pluginConfigInDb) {
            $pluginConfigInDb = json_decode($pluginConfigInDb, true);
            foreach ($plugin['config'] as $key => $value) {
                if ($value['type'] != 'group') {
                    if (isset($pluginConfigInDb[$key])) {
                        $plugin['config'][$key]['value'] = $pluginConfigInDb[$key];
                    }
                } else {
                    foreach ($value['options'] as $group => $options) {
                        foreach ($options['options'] as $gkey => $value) {
                            if (isset($pluginConfigInDb[$gkey])) {
                                $plugin['config'][$key]['options'][$group]['options'][$gkey]['value'] = $pluginConfigInDb[$gkey];
                            }
                        }
                    }
                }
            }
        }

        $this->assign('data', $plugin);
//        if ($plugin['custom_config']) {
//            $this->assign('custom_config', $this->fetch($plugin['plugin_path'] . $plugin['custom_config']));
//        }

        $this->assign('id', $id);
        return $this->fetch();

    }

    /**
     * 插件设置提交
     * @adminMenu(
     *     'name'   => '插件设置提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '插件设置提交',
     *     'param'  => ''
     * )
     */
    public function settingPost()
    {
        if ($this->request->isPost()) {
            $id = $this->request->param('id', 0, 'intval');

            $pluginModel = new PluginModel();
            $plugin      = $pluginModel->find($id)->toArray();

            if (!$plugin) {
                $this->error('插件未安装!');
            }

            $pluginClass = cmf_get_plugin_class($plugin['name']);
            if (!class_exists($pluginClass)) {
                $this->error('插件不存在!');
            }

            $pluginObj = new $pluginClass;
            //$plugin['plugin_path']   = $pluginObj->plugin_path;
            //$plugin['custom_config'] = $pluginObj->custom_config;
            $pluginConfigInDb = $plugin['config'];
            $plugin['config'] = include $pluginObj->getConfigFilePath();

            $rules    = [];
            $messages = [];

            foreach ($plugin['config'] as $key => $value) {
                if ($value['type'] != 'group') {
                    if (isset($value['rule'])) {
                        $rules[$key] = $this->_parseRules($value['rule']);
                    }

                    if (isset($value['message'])) {
                        foreach ($value['message'] as $rule => $msg) {
                            $messages[$key . '.' . $rule] = $msg;
                        }
                    }

                } else {
                    foreach ($value['options'] as $group => $options) {
                        foreach ($options['options'] as $gkey => $value) {
                            if (isset($value['rule'])) {
                                $rules[$gkey] = $this->_parseRules($value['rule']);
                            }

                            if (isset($value['message'])) {
                                foreach ($value['message'] as $rule => $msg) {
                                    $messages[$gkey . '.' . $rule] = $msg;
                                }
                            }
                        }
                    }
                }
            }

            $config = $this->request->param('config/a');

            $validate = new Validate($rules, $messages);
            $result   = $validate->check($config);
            if ($result !== true) {
                $this->error($validate->getError());
            }

            $pluginModel = new PluginModel();
            $pluginModel->save(['config' => json_encode($config)], ['id' => $id]);
            $this->success('保存成功', '');
        }
    }

    /**
     * 解析插件配置验证规则
     * @param $rules
     * @return array
     */
    private function _parseRules($rules)
    {
        $newRules = [];

        $simpleRules = [
            'require', 'number',
            'integer', 'float', 'boolean', 'email',
            'array', 'accepted', 'date', 'alpha',
            'alphaNum', 'alphaDash', 'activeUrl',
            'url', 'ip'];
        foreach ($rules as $key => $rule) {
            if (in_array($key, $simpleRules) && $rule) {
                array_push($newRules, $key);
            }
        }

        return $newRules;
    }

    /**
     * 插件安装
     * @adminMenu(
     *     'name'   => '插件安装',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '插件安装',
     *     'param'  => ''
     * )
     */
    public function install($pluginName = null )
    {
        if( empty($pluginName )) {
            $pluginName = $this->request->param('name', '', 'trim');
        }
        

        $class      = cmf_get_plugin_class($pluginName);
        if (!class_exists($class)) {
            $this->error('插件不存在!');
        }

        $pluginModel = new PluginModel();
        $pluginCount = $pluginModel->where('name', $pluginName)->count();

        if ($pluginCount > 0) {
            $this->error('插件已安装!');
        }

        $plugin = new $class;
        $info   = $plugin->info;
        if (!$info || !$plugin->checkInfo()) {//检测信息的正确性
            $this->error('插件信息缺失!');
        }

        $installSuccess = $plugin->install();
        if ( !$installSuccess ) {
            $this->error('插件预安装失败!');
        }

        $methods = get_class_methods($plugin);

        foreach ($methods as $methodKey => $method) {
            $methods[$methodKey] = cmf_parse_name($method);
        }

        $systemHooks = $pluginModel->getHooks(true);

        $pluginHooks = array_intersect($systemHooks, $methods);

        //$info['hooks'] = implode(",", $pluginHooks);

        if ( !empty( $plugin->hasAdmin ) ) {
            $info['has_admin'] = 1;
        } else {
            $info['has_admin'] = 0;
        }

        $info['config'] = json_encode($plugin->getConfig());

        
        $dbTable = new \app\admin\model\PluginDbTableModel();

        //检测所需配置
        $checkDemandModuleIsExists = $dbTable->checkDemandModuleIsExists( $pluginName );
        if( true !== $checkDemandModuleIsExists ) {
            $this->error( $checkDemandModuleIsExists [ 'message' ] );
        }

        $checkModule = $dbTable->checkModuleInfo( $pluginName );
        //检查是否配置pluginInfo
        if( isset( $checkModule['error'] ) ){
            $this->error( $checkModule['message'] );
        }

        $status  =  $dbTable->chechModuleAndTableIsExists( $pluginName ) ;
        if( !$status ){
            $this->error('安装失败，所需配置数据表不存在!');
        }
        //安装数据表
        $res = $dbTable->installDataTable( $pluginName );
        if( isset( $res['error'] ) ){
            $this->error($res['message']);
        }
        if( !$res ){
            $this->error('插件预安装失败,数据库安装失败!');
        }
        //安装
        //模块菜单
        $author = new AuthorizationModel();
//        $res = $author->uploadMenu( $pluginName , $this->pluginModuleId );
        $res = 1;

        if( isset( $res['error'] ) ){
            $this->error( $res['message'] );
        }
        if(isset($res['status']) && $res['status']==false || empty($res) || -1== $res ){
            $this->error('插件预安装失败,请稍后再试:X001!');
        }
        $info['module_menu_id'] = $res;//保存模块菜单id
        $pluginModel->data($info)->allowField(true)->save();
        $hookPluginModel = new HookPluginModel();
        foreach ($pluginHooks as $pluginHook) {
            $hookPluginModel->data(['hook' => $pluginHook, 'plugin' => $pluginName, 'status' => 1])->isUpdate(false)->save();
        }
       cache(null, 'admin_menus');// 删除后台菜单缓存
        $this->success('安装成功!');
    }




    /**
     * 插件更新
     */
    public function updatePlugin( $pluginName )
    {
        //模块菜单更新
        $author = new AuthorizationModel();
        $res = $author->updateMenu( $pluginName , $this->pluginModuleId );
        return $res;
    }


    /**
     * 卸载插件
     * @adminMenu(
     *     'name'   => '卸载插件',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '卸载插件',
     *     'param'  => ''
     * )
     */
    public function uninstall( $id = null )
    {

        $pluginModel = new PluginModel();
        if( empty( $id ) ){
            $id          = $this->request->param( 'id', 0, 'intval');
        }
        //删除admin_menu中的菜单
        $plugin = $pluginModel->where( 'id' , $id )->find();
        $author = new AuthorizationModel();
        $res = $author->deleteMenu( $plugin['module_menu_id'] );
        if( isset($res['status']) && $res['status'] == false || empty($res) ){
            $this->error('插件卸载失败，请稍后再试：X002!');
        }
        $result = $pluginModel->uninstall( $id );
        if ( $result !== true ) {
            $this->error('卸载失败!');
        }
         //检测所需配置
        $dbTable = new \app\admin\model\PluginDbTableModel();

        //卸载数据表
        $res = @$dbTable->uninstallDataTable( $plugin['name'] );
        $path = PLUGINS_PATH.DS.cmf_parse_name($plugin['name']);
        $this->delDirAndFile( $path );
        $this->success('卸载成功!');
    }

    //循环删除目录和文件函数 
    private function delDirAndFile( $dirName ) 
    { 
        if( is_file($dirName) ){
            unlink($dirName);
            return true;
        }
        if ( $handle = opendir( "$dirName" ) ) { 
            while ( false !== ( $item = readdir( $handle ) ) ) { 
                if ( $item != "." && $item != ".." ) { 
                    $itemPath = "$dirName/$item";
                    //$itemPath = mb_convert_encoding ( "$dirName/$item" , 'gbk' , 'utf-8'  ); 
                    if ( is_dir( $itemPath ) ) { 
                        $this->delDirAndFile( $itemPath ); 
                    } else { 
                        if( unlink( $itemPath ) );
                    } 
                } 
            } 
            closedir( $handle ); 
            if( rmdir( $dirName ) ) return true; 
        } 
    }


}
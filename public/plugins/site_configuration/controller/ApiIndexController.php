<?php
namespace plugins\site_configuration\controller;
// use cmf\controller\ApiBaseController;//引用插件基类
use cmf\controller\PluginRestBaseController;//引用插件基类
use think\Db;
use think\Request;


/**
 * api控制器
 */
// class ApiIndexController extends ApiBaseController
class ApiIndexController extends PluginRestBaseController
{
   
    
    public function index($isModule = false)//index(命名规范)
    {
        $param = $this->request->param();
        return zy_array(true,'连入成功',$param,200,$isModule);
    }



    /**
     * 接口获取信息    
     * @return   [data]              [所有站点配置信息] 
     */
    public function getInfo($data = null, $isModule=false)
    {
        $param=$this->request->post();
        // $param = zy_decodeData($param,$isModule);
        $module_info = getModuleConfig('site_configuration','config','config.json');
        $module_info = json_decode($module_info,true);
        $data['SEO']['title'] = $module_info['title'];
        $data['SEO']['keyword'] = $module_info['keyword'];
        $data['SEO']['description'] = $module_info['description'];
        $data['SEO']['img_url'] = $module_info['img_url'];

        unset($module_info);
        $module_info = getModuleConfig('site_configuration','config','basicconfig.json');
        $module_info = json_decode($module_info,true); 
        $data['basic']['site_name'] = $module_info['site_name'];
        $data['basic']['site_table'] = $module_info['site_table'];
        $data['basic']['site_domain'] = $module_info['site_domain'];
        $data['basic']['app_path'] = $module_info['app_path'];

        return zy_array(true,'查询成功',$data,200,$isModule);
    }


}   
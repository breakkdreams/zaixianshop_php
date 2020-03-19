<?php
namespace plugins\home_configuration\controller;
use cmf\controller\PluginRestBaseController;//引用插件基类
use think\Db;
use think\Request;


/**
 * api控制器
 */
class ApiIndexController extends PluginRestBaseController
{
    
    public function index($isModule = false)//index(命名规范)
    {
        $param = $this->request->param();
        return zy_array(true,'连入成功',$param,200,$isModule);
    }



    /**
     * 获取皮肤信息    
     * @return   [data]        
     */
    public function getSkinInfo($isModule=false)
    {
        $param=$this->request->post();
        $patam['a'] = 1;
        $param = zy_decodeData($param,$isModule);
        $data['APP'] = Db::name('home_configuration_skin')->where(['type'=>1,'status'=>1])->select()->Toarray();
        $data['WEB'] = Db::name('home_configuration_skin')->where(['type'=>2,'status'=>1])->select()->Toarray();
        $return = $this->getDomain();
        if(!empty($data['APP'])){
            foreach($data['APP'] as $k=>$v){
                $data['APP'][$k]['img_url'] = $return.$v['img_url'];
            }
        }
        if(!empty($data['WEB'])){
            foreach($data['WEB'] as $k=>$v){
                $data['WEB'][$k]['img_url'] = $return.$v['img_url'];
            }
        }
        return zy_array(true,'查询成功',$data,200,$isModule);
    }




    /**
     * 获取轮播图信息    
     * @return   [data]              [所有信息] 
     */
    public function getCarouselInfo($isModule=false)
    {
        $param=$this->request->post();
        $patam['a'] = 1;
        $param = zy_decodeData($param,$isModule);
        $data['APP'] = Db::name('home_configuration_carousel')->where(['type'=>1,'status'=>1])->select()->Toarray();
        $data['WEB'] = Db::name('home_configuration_carousel')->where(['type'=>2,'status'=>1])->select()->Toarray();
        $return = $this->getDomain();
        if(!empty($data['APP'])){

            foreach($data['APP'] as $k=>$v){
                $data['APP'][$k]['img_url'] = $return.$v['img_url'];
            }
        }
        if(!empty($data['WEB'])){
            foreach($data['WEB'] as $k=>$v){
                $data['WEB'][$k]['img_url'] = $return.$v['img_url'];
            }
        }
        // return zy_json_echo(true,'查询成功',$data,200);
        return zy_array(true,'查询成功',$data,200,$isModule);
    }


    /**
     * 获取首页分类信息    
     * @return   [data]              [所有信息] 
     */
    public function getTypeInfo($isModule=false)
    {
        $param=$this->request->post();
        $patam['a'] = 1;
        $return = $this->getDomain();
        $param = zy_decodeData($param,$isModule);
        $data['APP'] = Db::name('home_configuration_type')->where(['type'=>1,'status'=>1])->select()->Toarray();
        $data['WEB'] = Db::name('home_configuration_type')->where(['type'=>2,'status'=>1])->select()->Toarray();
        if(!empty($data['APP'])){
            foreach($data['APP'] as $k=>$v){
                $data['APP'][$k]['img_url'] = $return.$v['img_url'];
            }
        }
        if(!empty($data['WEB'])){
            foreach($data['WEB'] as $k=>$v){
                $data['WEB'][$k]['img_url'] = $return.$v['img_url'];
            }
        }
        // return zy_json_echo(true,'查询成功',$data,200);
        return zy_array(true,'查询成功',$data,200,$isModule);
    }



    /**
     * 获取轮播图信息    
     * @return   [data]              [所有信息] 
     */
    public function getRecomInfo($isModule=false)
    {
        $param=$this->request->post();
        $patam['a'] = 1;
        $return = $this->getDomain();
        $param = zy_decodeData($param,$isModule);
        $data['APP'] = Db::name('home_configuration_recom')->where(['type'=>1,'status'=>1])->select()->Toarray();
        $data['WEB'] = Db::name('home_configuration_recom')->where(['type'=>2,'status'=>1])->select()->Toarray();
        if(!empty($data['APP'])){
            foreach($data['APP'] as $k=>$v){
                $data['APP'][$k]['img_url'] = $return.$v['img_url'];
            }
        }
        if(!empty($data['WEB'])){
            foreach($data['WEB'] as $k=>$v){
                $data['WEB'][$k]['img_url'] = $return.$v['img_url'];
            }
        }
        // return zy_json_echo(true,'查询成功',$data,200);
        return zy_array(true,'查询成功',$data,200,$isModule);
    }



//------------------------------------------------------------------------------------------↑↓模块配置调用

    public function getDomain(){
        $data = [];
        $symbol ='home_configuration';
        $id = 'one_site_configuration';
        $param = ['data'=>$data,'isModule'=>true];
        $return = getModuleApiData( $symbol, $id, $param);
        $da = $return['data']['basic']['site_domain'];
        return $da;
    }




}
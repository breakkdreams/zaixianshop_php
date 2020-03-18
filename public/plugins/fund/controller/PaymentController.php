<?php
namespace plugins\fund\controller; //Demo插件英文名，改成你的插件英文就行了

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

class PaymentController extends PluginAdminBaseController{

/**
    * 初始化
    */

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
    * 支付宝配置首页
    */
    
    public function index(){
       $data = $this->request->param();

       $da = Db::name("fund_type")->select();
      
       $this->assign('da',$da);

       return $this->fetch();
    }


    /**
     * 添加支付宝配置
     */
    public function config_add(){
        $data = $this->request->param();

        $da = ['0'=>$data['setconfig']];
       
        $da = saveModuleConfigData('fund','config','config.json',$da);
         
        return $this->success("修改成功");
    }


    /**
    * 微信配置首页
    */


    public function wechat(){
    	$data = $this->request->param();

        $da   = getModuleConfig('fund','config','weixin.json');

        if(!isset(json_decode($da,true)['1']) || empty(json_decode($da,true)['1'])){
            $da['wechatpay_off']='';
            $da['wechatpay_appid']='';
            $da['wechatpay_appsecret']='';
            $da['wechatpay_mchid']='';
            $da['wechatpay_key']='';
            $this->assign('da',$da);
        }else{
            $this->assign('da',json_decode($da,true)['1']);
        }
  
    	return $this->fetch();
    }


    /**
     * 添加微信配置
     */
    public function config_wechat(){
        $data = $this->request->param();

        $da   = ['1'=>$data['setconfig']];
       
        $da = saveModuleConfigData('fund','config','weixin.json',$da);
 
        return $this->success("修改成功");
    }


    /**
     * 设置默认提现方式
     */

    public function setup(){
        $data = $this->request->param();

        $type = $data['type'];
      
        if( empty($type) ){
            return $this->error("请选择至少一种提现方式"); 
        }

        if( $type['0']!=1 ){
            $type = ['0'=>'0','1'=>$type['0']];
        }

        $type = json_encode($type);
        
        $da = Db::name("fund_type")->where('id',1)->update(['type'=>$type]);

        if( $da ){
            return $this->success("操作成功");
        }else{
            return $this->error("操作失败");
        }
    }



    /**
     * 修改类型
     */
    public function uptype(){
        $data = $this->request->param();

        if($data['type']=='1'){
            $da = Db::name("fund_type")->where('id',$data['id'])->update(['type'=>'2']);
        }

        if($data['type']=='2'){
            $da = Db::name("fund_type")->where('id',$data['id'])->update(['type'=>'1']);
        }

        if($da) {
            return $this->success("修改成功");
        }
    }





 }
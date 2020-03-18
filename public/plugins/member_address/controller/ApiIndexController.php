<?php
namespace plugins\member_address\controller;
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
    /**
     * 执行构造
     */
    function __construct()
    {
        header("content-type:text/html;charset=utf-8");
        parent::__construct();
    }



    
    public function index()//index(命名规范)
    {
        $param=$this->request->post($data = null ,$isModule=false);
        $param = zy_decodeData($param,$isModule);
        if($isModule==true){
            $param = $data;
        }
        return zy_array(true,'连入成功',null,200,$isModule);
    }


  




    /**
     * 根据地区代码获取下级地区代码
     * @param    [str]   cri_code   [地区代码]    (可选)     
     * @return   [arr]              [下级地区信息] 
     */
    public function getSubord($data = null ,$isModule=false)
    {
        $param=$this->request->post();
        $param = zy_decodeData($param,$isModule);
        if($isModule==true){
            $param = $data;
        }
        if(!isset($param['cri_code'])){
            $cri_code = '000000';
        }else{
            $cri_code = $param['cri_code'];
        }
        $da = Db::name('memberaddress_cn_region')->where(['cri_superior_code'=>$cri_code,'status'=>1])->order('cri_sort','asc')->select()->Toarray();
        if(empty($da)){
            // return zy_json_echo(false,'该地区代码无下级',null,100);
            return zy_array(false,'该地区代码无下级',null,100,$isModule);
        }
        // dump($da);
        foreach($da as $key=>$value){
            $data[$key]['cri_code'] = $value['cri_code'];
            $data[$key]['cri_name'] = $value['cri_name'];
            $data[$key]['cri_sort'] = $value['cri_sort'];
        }
        // return zy_json_echo(true,'查询成功',$data,200);
        return zy_array(true,'查询成功',$data,200,$isModule);
    }




    /**
     * 根据地区代码获取上级地区代码
     * @param    [str]   cri_code   [地区代码]    (可选)     
     * @return   [arr]              [下级地区信息] 
     */
    public function getSuperior($data = null,$isModule=false)
    {
        $param=$this->request->post();
        $param = zy_decodeData($param,$isModule);
        if($isModule==true){
        	$param = $data;
        }
        if(!isset($param['cri_code'])){
            // return zy_json_echo(false,'该地区代码无上级',null,1000);

            return zy_array(false,'该地区代码无上级',null,100,$isModule);
        }
        $cri_code = $param['cri_code'];
        $return=[];
        $return = $this->findSuperior($cri_code,0,[]);
		// return zy_json_echo(true,'aa',$return,200);
        return zy_array(true,'查询成功',$return,200,$isModule);
    }



    /**
     * 输入地区编号  返回所有上级地区信息
     */
    public function findSuperior($cri_code = null ,$i=0,$return){
    	$da = Db::name('memberaddress_cn_region')->where('cri_code',(integer)$cri_code)->find();
    	$data = Db::name('memberaddress_cn_region')->where('cri_code',(integer)$da['cri_superior_code'])->find();
    	$i = $i+1;
    	$return[$i] = $data;
    	
    	if(!empty((integer)$data['cri_superior_code'])){
    		$return = $this->findSuperior($da['cri_superior_code'],$i,$return);
    	}else{
    		return $return;
    	}
    	$return[0] = $da; 
    	return $return;
    }



    /**
     * 获取所有地区信息
     */
    public function getAreainfo($isModule=false)
    {
        $param=$this->request->post();
        $param = zy_decodeData($param,$isModule);

        $da1 = Db::name('memberaddress_cn_region')->where(['cri_level'=>1,'status'=>1])->order('cri_sort','asc')->select()->Toarray();
        $da2 = Db::name('memberaddress_cn_region')->where(['cri_level'=>2,'status'=>1])->order('cri_sort','asc')->select()->Toarray();
        $da3 = Db::name('memberaddress_cn_region')->where(['cri_level'=>3,'status'=>1])->order('cri_sort','asc')->select()->Toarray();
        foreach($da1 as $key=>$value){
            $data['province_list'][$value['cri_code']] = $value['cri_name'];
        }
        foreach($da2 as $key2=>$value2){
            $data['city_list'][$value2['cri_code']] = $value2['cri_name'];
        }
        foreach($da3 as $key3=>$value3){
            $data['county_list'][$value3['cri_code']] = $value3['cri_name'];
        }
        // return zy_json_echo(true,'查询成功',$data,200);
        return zy_array(true,'查询成功',$data,200,$isModule);
        
        // $param=$this->request->post();
        // $param = zy_decodeData($param,$isModule);
        
        // $module_info = getModuleConfig('member_address','config','area.json');
        // $module_info = json_decode($module_info,true);
        

        // return zy_array(true,'查询成功',$module_info,200,$isModule);
    }


    /**
     * 获取所有地区信息
     */
    public function getAreaInformation(){
        // $da1 = Db::name('memberaddress_cn_region')->where(['cri_level'=>1,'status'=>1])->order('cri_sort','asc')->select()->Toarray();
        // $da2 = Db::name('memberaddress_cn_region')->where(['cri_level'=>2,'status'=>1])->order('cri_sort','asc')->select()->Toarray();
        // $da3 = Db::name('memberaddress_cn_region')->where(['cri_level'=>3,'status'=>1])->order('cri_sort','asc')->select()->Toarray();
        // foreach($da1 as $key=>$value){
        //     $data['province_list'][$value['cri_code']] = $value['cri_name'];
        // }
        // foreach($da2 as $key2=>$value2){
        //     $data['city_list'][$value2['cri_code']] = $value2['cri_name'];
        // }
        // foreach($da3 as $key3=>$value3){
        //     $data['county_list'][$value3['cri_code']] = $value3['cri_name'];
        // }
        // return zy_json_echo(true,'查询成功',$data,200);
        // return zy_array(true,'查询成功',$data,200);
        
        // $param=$this->request->post();
        // $param = zy_decodeData($param,$isModule);
        
        $module_info = getModuleConfig('member_address','config','area.json');
        $module_info = json_decode($module_info,true);
        
        return zy_json_echo(true,'查询成功',$module_info,200);

        // return zy_array(true,'查询成功',$module_info,200,$isModule);
    }



    /**
     * 添加用户收货地址 
     * @param    [int]   uid        [用户ID]      
     * @param    [str]   receive_name   [收货人名称]      
     * @param    [int]   receive_phone  [收货人电话]      
     * @param    [str]   cri_code       [地区代码]      
     * @param    [str]   cri_name       [地区名称]      
     * @param    [str]   address        [详细地址]      
     * @param    [int]   postal_code    [邮政编码]      
     * @param    [int]   is_default     [是否为默认收货地址]           
     * @return   [str]                  [是否添加成功] 
     */  
    public function addAdress($data = null ,$isModule=false)
    {
        $param=$this->request->post();
        $param = zy_decodeData($param,$isModule);
        if($isModule==true){
            $param = $data;
        }
        if(empty($param['uid'])){
            return zy_array(false,'用户ID不可为空',null,100,$isModule);
        }
        if(empty($param['receive_name'])){
            return zy_array(false,'收货人名称不可为空',null,100,$isModule);
        }
        if(empty($param['receive_phone'])){
            return zy_array(false,'收货人电话不可为空',null,100,$isModule);
        }
        if(empty($param['cri_code'])){
            return zy_array(false,'地区代码不可为空',null,100,$isModule);
        }
        
        if(empty($param['address'])){
            return zy_array(false,'详细地址不可为空',null,100,$isModule);
        }
        $add['uid'] = $param['uid'];
        $add['receive_name'] = $param['receive_name'];
        $add['receive_phone'] = $param['receive_phone'];
        $add['cri_code'] = $param['cri_code'];
        $add['cri_name'] = '';
        $cri_data = Db::name('memberaddress_cn_region')->where('cri_code',$param['cri_code'])->find();
        if(!empty($cri_data)){
            $add['cri_name'] = $cri_data['cri_name'];
        }
        $add['address'] = $param['address'];
        if(!empty($param['postal_code'])){
            $add['postal_code'] = (string)$param['postal_code']; 
        }
        $a = $this->findSuperior($param['cri_code'],0,[]);
        foreach($a as $k=>$v){
            if($v['cri_level']==1 ||$v['cri_level']==2 ||$v['cri_level']==3 ){
                $b[$v['cri_code']] = $v['cri_name'];
            }
        }

        $add['show_address'] = json_encode($b);
        $data = Db::name('memberaddress_cn_region')->where('cri_code',$param['cri_code'])->find();
        if(empty($data)){
            // return zy_json_echo(false,'无该地区代码对应的信息',null,101);
            return zy_array(false,'无该地区代码对应的信息',null,101,$isModule);
        }
        $return = $this->userJudge($param['uid']);
        if($return['status']!='success'){
            return zy_array(false,$return['message'],null,102,$isModule);
        }
        if(!empty($param['is_default'])){
            $add['is_default'] = $param['is_default'];
            if($param['is_default'] == 1){
                //如果设为默认则让所有该用户下的收货地址都变为非默认
                Db::name('memberaddress_user_addresslist')->where('uid',$param['uid'])->update(['is_default'=>2]);
            }
        }
        if(!empty($param['postal_code'])){
            $add['postal_code'] = (string)$param['postal_code'];
        }
        $re = Db::name('memberaddress_user_addresslist')->insert($add);
        if(empty($re)){
            // return zy_json_echo(false,'添加失败',null,102);
            return zy_array(false,'添加失败',null,103,$isModule);

        }
        // return zy_json_echo(true,'添加成功',$re,200);
        return zy_array(true,'添加成功',$re,200,$isModule);
    }




    /**
     * 获取所有目标用户收货地址
     * @param    [int]   uid        [用户ID]             
     * @return   [arr]                  [用户下所有收货地址信息] 
     */ 
    public function getAddressList($data = null,$isModule=false)
    {
            // return zy_array(false,'未传递参数uid',null,100,$isModule);

        $param = $this->request->post();
        $param = zy_decodeData($param,$isModule);
        if($isModule==true){
        	$param = $data;
        }
        if(empty($param['uid'])){
            // return zy_json_echo(false,'未传递参数uid',null,100);
            return zy_array(false,'未传递参数uid',null,100,$isModule);
        }
        $return = $this->userJudge($param['uid']);
        if($return['status']!='success'){
            return zy_array(false,$return['message'],null,101,$isModule);
        }
        $data = Db::name('memberaddress_user_addresslist')->where('uid',$param['uid'])->order('is_default','asc')->select()->Toarray();
        if(empty($data)){
            // return zy_json_echo(false,'查无数据',null,102);
            return zy_array(false,'查无数据',null,102,$isModule);
        }
        foreach($data as $key =>$value){
            $show_address=array();
            $show_address = (array)json_decode($value['show_address']);
            ksort($show_address);
            $data[$key]['show_address'] = array_values($show_address);
        }
        // return zy_json_echo(true,'查询成功',$data,200);
        return zy_array(true,'查询成功',$data,200,$isModule);
    }



    /**
     * 获取 目标用户默认收货地址
     * @param    [int]   uid            [用户ID]             
     * @return   [arr]                  [用户下所有收货地址信息] 
     */ 
    public function getDefaultAddress($data = null,$isModule=false)
    {
        $param = $this->request->post();
        $param = zy_decodeData($param,$isModule);
        if($isModule==true){
            $param = $data;
        }
        if(empty($param['uid'])){
            // return zy_json_echo(false,'未传递参数uid',null,100);
            return zy_array(false,'未传递参数uid',null,100,$isModule);
        }
        $return = $this->userJudge($param['uid']);
        if($return['status']!='success'){
            return zy_array(false,$return['message'],null,101,$isModule);
        }
        $da = Db::name('memberaddress_user_addresslist')->where(['uid'=>$param['uid'],'is_default'=>1])->find();
        // return zy_json_echo(true,'查询成功',$data,200);
        return zy_array(true,'查询成功',$da,200,$isModule);
    }






    /**
     * 获取单条用户收货地址信息
     * @param    [int]   id             [地址ID]             
     * @return   [arr]                  [该收货地址信息] 
     */ 
    public function getAddressInfo($isModule=false)
    {
        $param = $this->request->post();
        $param = zy_decodeData($param,$isModule);
        if($isModule==true){
            $param = $data;
        }
        if(empty($param['id'])){
            // return zy_json_echo(false,'未传递参数id',null,100);
            return zy_array(false,'未传递参数id',null,100,$isModule);
        }
        if(empty($param['uid'])){
            // return zy_json_echo(false,'未传递参数id',null,100);
            return zy_array(false,'未传递参数uid',null,100,$isModule);
        }
        $id = $param['id'];
        $data = Db::name('memberaddress_user_addresslist')->where('id',$id)->find();
        if(empty($data)){
            // return zy_json_echo(false,'查无该id数据',null,101);
            return zy_array(false,'查无该id数据',null,101,$isModule);
        }
        if($data['uid']!=$param['uid']){
            return zy_array(false,'id与uid不匹配',null,101,$isModule);
        }
        // return zy_json_echo(true,'查询成功',$data,200);
            return zy_array(true,'查询成功',$data,200,$isModule);
    }




    /**
     * 编辑地址信息
     * @param    [int]   id             [地址ID]   
     * @param    [int]   uid            [用户ID]      
     * @param    [str]   receive_name   [收货人名称]      
     * @param    [int]   receive_phone  [收货人电话]      
     * @param    [str]   cri_code       [地区代码]      
     * @param    [str]   cri_name       [地区名称]      
     * @param    [str]   address        [详细地址]      
     * @param    [int]   postal_code    [邮政编码]      
     * @param    [int]   is_default     [是否为默认收货地址]           
     * @return   [str]                  [是否修改成功]          
     */ 
    public function editAddress($data = null,$isModule=false)
    {
        $param=$this->request->post();
        $param = zy_decodeData($param,$isModule);
        if($isModule==true){
            $param = $data;
        }
        if(empty($param['uid'])){
            return zy_array(false,'用户ID不可为空',null,100,$isModule);
        }
        if(empty($param['id'])){
            return zy_array(false,'地址ID不可为空',null,100,$isModule);
        }
        if(empty($param['receive_name'])){
            return zy_array(false,'收货人名称不可为空',null,100,$isModule);
        }
        if(empty($param['receive_phone'])){
            return zy_array(false,'收货人电话不可为空',null,100,$isModule);
        }
        if(empty($param['cri_code'])){
            return zy_array(false,'地区代码不可为空',null,100,$isModule);
        }
        if(empty($param['address'])){
            return zy_array(false,'详细地址不可为空',null,100,$isModule);
        }




        $update['id'] = $param['id']; 
        $update['uid'] = $param['uid'];
        $data = Db::name('memberaddress_user_addresslist')->where('id',$param['id'])->find();
        if(empty($data)){
            // return zy_json_echo(false,'查无该地址id数据',null,101);
            return zy_array(false,'查无该地址id数据',null,101,$isModule);

        }
        $return = $this->userJudge($param['uid']);
        if($return['status']!='success'){
            return zy_array(false,$return['message'],null,102,$isModule);
        }
        if($data['uid']!=$param['uid']){
            // return zy_json_echo(false,'传入的用户uid不正确',null,102);
            return zy_array(false,'传入的用户uid不正确',null,103,$isModule);

        }
        $update['receive_name'] = $param['receive_name']; 
        $update['receive_phone'] = $param['receive_phone']; 
        $update['cri_code'] = $param['cri_code']; 
        $update['cri_name'] = '';
        $cri_data = Db::name('memberaddress_cn_region')->where('cri_code',$param['cri_code'])->find();
        if(!empty($cri_data)){
            $update['cri_name'] = $cri_data['cri_name'];
        }
        $update['address'] = $param['address']; 
        if(!empty($param['postal_code'])){
            $update['postal_code'] = (string)$param['postal_code']; 
        }else{
            $update['postal_code'] = ""; 
        }
        if(!empty($param['is_default'])){
            $update['is_default'] = $param['is_default'];
            if($param['is_default'] == 1){
                //如果设为默认则让所有该用户下的收货地址都变为非默认
                Db::name('memberaddress_user_addresslist')->where('uid',$param['uid'])->update(['is_default'=>2]);
            }
        }
        $re = Db::name('memberaddress_user_addresslist')->update($update);
        if(empty($re)){
            // return zy_json_echo(false,'更新失败',null,103);
            return zy_array(false,'更新失败',null,104,$isModule);

        }
        // return zy_json_echo(true,'更新成功',$re,200);
        return zy_array(true,'更新成功',$re,200,$isModule);
    }





    /**
     * 删除单条用户收货地址信息
     * @param    [int]   id             [地址ID]             
     * @param    [int]   uid             [用户ID]             
     * @return   [arr]                  [该收货地址信息] 
     */ 
    public function deleteAddress($data = null,$isModule=false)
    {
        $param = $this->request->post();
        $param = zy_decodeData($param,$isModule);
        if($isModule==true){
            $param = $data;
        }
        if(empty($param['id'])){
            // return zy_json_echo(false,'未传递参数id',null,100);
            return zy_array(false,'未传递参数id',null,100,$isModule);
        }
        if(empty($param['uid'])){
            // return zy_json_echo(false,'未传递参数id',null,100);
            return zy_array(false,'未传递参数uid',null,100,$isModule);
        }
        $id = $param['id'];
        $data = Db::name('memberaddress_user_addresslist')->where('id',$id)->find();
        if(empty($data)){
            // return zy_json_echo(false,'查无该id数据',null,101);
            return zy_array(false,'查无该id数据',null,101,$isModule);
        }
        if($data['uid']!=$param['uid']){
            return zy_array(false,'id与uid不匹配',null,101,$isModule);
        }
        $re = Db::name('memberaddress_user_addresslist')->where('id',$id)->delete();
        if(empty($re)){
            return zy_array(false,'删除失败',null,102,$isModule);
        }
        return zy_array(true,'删除成功',$re,200,$isModule);
    }



    //===================================================调用其他模块接口
      public function userJudge($uid,$isModule=false){
        $symbol ='member_address';
        $id = 'member_one';
        $param = ['uid'=>$uid,'field'=>'','isModule'=>true];
        $return = getModuleApiData( $symbol, $id, $param);
        return $return;
    }

    public function test($data,$isModule=false){
        // $data = ['mobile'=>'123','content'=>'zzzz'];
        // $data = ['send_userid'=>'10','subject'=>'zzzz','msg'=>'xxxx','email'=>'281143343@qq.com'];
        $data = ['cri_code'=>'410102007'];
        $daat = [];
        $symbol ='member_address';
        $id = 'site_configuration_one';
        $param = ['data'=>$data,'isModule'=>true];
        $return = getModuleApiData( $symbol, $id, $param);
        return $return;
    }





//========================================
}
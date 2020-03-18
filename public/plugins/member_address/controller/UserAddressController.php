<?php
namespace plugins\member_address\controller;
use cmf\controller\PluginAdminBaseController;
use think\Db;


   
class UserAddressController extends PluginAdminBaseController
{
	protected function _initialize()
    {
        parent::_initialize();
        $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        }
    }
    


    //index后台页面
	public function index(){
		$param = $this->request->param();
        $keyword='';
		$where = 1; 
		$search['keyword'] = '';
		$search['stype'] = '';
        if(!empty($param['keyword'])){
            $keyword=$param['keyword'];
			$search['keyword'] = $param['keyword'];
        }
        if(!empty($param['stype'])){
			$search['stype'] = $param['stype'];

        	if($param['stype']==1){
        		$where .= " and receive_name like '%".$keyword."%'";
        	}else if($param['stype']==2){
        		$where .= " and receive_phone like '%".$keyword."%'";
        	}else if($param['stype']==3){
        		$where .= " and uid like '%".$keyword."%'";
        	}else if($param['stype']==4){
        		$where .= " and cri_code like '%".$keyword."%'";
        	}else if($param['stype']==5){
        		$where .= " and address like '%".$keyword."%'";
        	}
        	
        }
		$data = Db::name('memberaddress_user_addresslist')->where($where)->order('uid','asc')->order('is_default','asc')->paginate(10);
		$page = $data->render();
		$this->assign('page',$page);//单独提取分页出来
		$this->assign('data',$data);
		$this->assign('search',$search);
		return $this->fetch();
	}



    //add添加页面
	public function addPage(){
		$module_info = getModuleConfig('member_address','config','area.json');
        $module_info = json_decode($module_info,true);

		$this->assign('data',json_encode($module_info['area']));
		return $this->fetch();
	}



    //add添加操作
	public function add(){
		$param = $this->request->param();
		if(!isset($param['uid']) || !isset($param['postal_code']) ||empty($param['receive_phone']) || empty($param['receive_name']) || empty($param['address']) || empty($param['key']) || empty($param['value'])){
			return zy_json_echo(false,'传参错误',null,100);
		}
		$add['uid'] = $param['uid'];
		$return = $this->userJudge($param['uid']);
        if($return['status']!='success'){
            return zy_json_echo(false,$return['message'],null,102);
        }
		$add['postal_code'] = $param['postal_code'];
		$add['receive_phone'] = $param['receive_phone'];
		$add['receive_name'] = $param['receive_name'];
		$add['address'] = $param['address'];
		$add['cri_code'] = $param['key'][2];
  		$add['cri_name'] = $param['value'][2];    
  		


  		$add['key'] = $param['key'];    
  		$add['value'] = $param['value'];    
  		$add['show_address']=json_encode(array_combine($add['key'],$add['value']));
  		unset($add['key']);
  		unset($add['value']);
        // $add['show_address'] = implode(",",array_values($param['value']));
        $add['is_default'] = 2;
        $re = Db::name('memberaddress_user_addresslist')->insert($add);
        if(empty($re)){
			return zy_json_echo(false,'添加失败',$add,101);
        }
		return zy_json_echo(true,'添加成功',null,200);
	}

    //edit修改页面
	public function editPage(){
		$param = $this->request->param();
		$module_info = getModuleConfig('member_address','config','area.json');
        $module_info = json_decode($module_info,true);
        $da = Db::name('memberaddress_user_addresslist')->where('id',$param['id'])->find();
        $da['show_address'] = json_decode($da['show_address']);
        foreach($da['show_address'] as $key=>$value){
        	$mounted[] = $key;
        }
        asort($mounted);
        $mounted = array_values($mounted);
        $mounted = json_encode($mounted);
		$this->assign('data',json_encode($module_info['area']));
		$this->assign('da',$da);
		$this->assign('mounted',$mounted);
		return $this->fetch();
	}



    //修改操作
	public function edit(){
		$param = $this->request->param();
		if(!isset($param['uid']) || !isset($param['postal_code']) ||empty($param['receive_phone']) || empty($param['receive_name']) || empty($param['address']) || empty($param['id']) || empty($param['is_default'])){
			return zy_json_echo(false,'传参错误',null,100);
		}

		if(!empty($param['key']) && !empty($param['value'])){
			$update['cri_code'] = $param['key'][2];
        	$update['cri_name'] = $param['value'][2];


        	$key = $param['key'];    
	  		$value = $param['value'];    
	  		$add['show_address']=json_encode(array_combine($key,$value));
		}


		$update['id'] = $param['id'];
		$update['uid'] = $param['uid'];
		$update['postal_code'] = $param['postal_code'];
		$update['receive_phone'] = $param['receive_phone'];
		$update['receive_name'] = $param['receive_name'];
		$update['address'] = $param['address'];
		$update['is_default'] = $param['is_default'];
		
        if($param['is_default']==1){
        	Db::name('memberaddress_user_addresslist')->where('uid',$param['uid'])->update(['is_default'=>2]);
        }
        $re = Db::name('memberaddress_user_addresslist')->update($update);
        if(empty($re)){
			return zy_json_echo(false,'修改失败',$update,101);
        }
		return zy_json_echo(true,'修改成功',null,200);
	}

    //删除操作
	public function delAddress(){
		$param = $this->request->param();
        if(empty($param['id'])){
            return $this->error('缺少关键参数id');
        }
        $re = Db::name('memberaddress_user_addresslist')->where('id',$param['id'])->delete();
        if(empty($re)){
            return $this->error('删除失败');
        }
        return $this->success('删除成功');
	}



	/**
     * 多条删除操作
     */
    public function allDelete(){
        $param = $this->request->param();
        if(empty($param['ids'])){
            return json(['type'=>'error','msg'=>'参数ids未传']);
        }
        $ids = explode(",", $param['ids']);
        foreach($ids as $key=>$value){
            $re ='';
            $re = Db::name('memberaddress_user_addresslist')->where('id',$value)->delete();
            if(empty($re)){
                return json(['type'=>'error','msg'=>'删除失败']);
            }
        }
        return json(['type'=>'success','msg'=>'删除成功']);
    }



    //===================================================调用其他模块接口
	  public function userJudge($uid,$isModule=false){
        $symbol ='member_address';
        $id = 'member_one';
        $param = ['uid'=>$uid,'field'=>'','isModule'=>true];
        $return = getModuleApiData( $symbol, $id, $param);
        return $return;
    }
}
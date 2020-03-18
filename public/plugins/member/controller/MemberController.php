<?php 
namespace plugins\member\controller; 

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;
use sign\Jwt;


/**
 * 会员管理控制器
 */
class MemberController extends PluginAdminBaseController
{
	public function index()
	{	
		$param = $this->request->param();
		$search['condition1'] = 1;
		$search['keyword'] = '';
		$search['start_time'] = '';
		$search['end_time'] = '';
		$search['islock'] = '';
		$search['groupid'] = '';
		if(isset($param['condition1'])){
			$search['condition1'] = $param['condition1'];
		}
		if(isset($param['keyword'])){
			$search['keyword'] = $param['keyword'];
		}
		if(isset($param['start_time'])){
			$search['start_time'] = $param['start_time'];
		}
		if(isset($param['end_time'])){
			$search['end_time'] = $param['end_time'];
		}
		if(isset($param['islock'])){
			$search['islock'] = $param['islock'];
		}
		if(isset($param['groupid'])){
			$search['groupid'] = $param['groupid'];
		}
		$where = 1;
		if($search['keyword']){
            $keyword="'%".$search['keyword']."%'";
            if($search['condition1']==1){
	        	$where .= " and `username` like ".$keyword;
			}elseif($search['condition1']==2){
				$where .= " and `mobile` like ".$keyword;
			}elseif($search['condition1']==3){
				$where .= " and `nickname` like ".$keyword;
			}
        }

		if(!empty($search['islock'])){
			$where .= " and `islock` =".$search['islock'];
		}
		if(!empty($search['groupid'])){
			$where .= " and `groupid` =".$search['groupid'];
		}
		
        if(!empty($search['start_time'])) {
            $where .= " and create_time >= '".strtotime($search['start_time'])."'";   
        }
        if(!empty($search['end_time'])) {
            $where .= " and create_time <= '".strtotime($search['end_time'])."'";
        } 

		$data = Db::name('member')->where($where)->order('uid','sac')->paginate(20);
		$member_group = Db::name('member_group')->select();

		$this->assign('page', $data->render());//单独提取分页出来
		$this->assign('search',$search);
		$this->assign('data',$data);
		$this->assign('member_group',$member_group);
		return $this->fetch('/member/index');
	}

	/**
	 * 添加会员页面
	 */
	public function addMemberPage()
	{	
		$member_group = Db::name('member_group')->select();

		$this->assign('member_group',$member_group);
		return $this->fetch('/member/addMemberPage');
	}

	/**
	 * 添加会员操作
	 */
	public function addMember(){
		$param = $this->request->param();
		if(empty($param['nickname']) || !isset($param['password']) || !isset($param['re_password']) || empty($param['email']) || empty($param['groupid'])){
			return json(['type'=>'error','msg'=>$param]);
		}
		$add= [];
		$add['groupid'] = $param['groupid'];
		$add['create_ip'] = get_client_ip();
		$add['username'] = $this->random(8,'all');//原随机 暂定一个
		$add['nickname'] = $param['nickname'];	
		$add['create_time'] = time();
		$add['last_login_time'] = '';
		if($param['password']!=$param['re_password']){
			return json(['type'=>'error','msg'=>'密码不一致，添加失败']);
		}else{
			$add['password'] = cmf_password($param['password']);//加密
		}
		if(!empty($param['mobile'])){
			$add['mobile'] = $param['mobile'];
		}
		if(!empty($param['email'])){
			$add['email'] = $param['email'];
		}
		if(!empty($param['vip']) && $param['vip']=='on'){
			$add['vip'] = 1;
		}
		if(!empty($param['overduedate'])){
			$add['overduedate'] = $param['overduedate'];
		}
		$uid = Db::name('member')->insertGetId($add);
		$re = Db::name('member_detail')->insert(['uid'=>$uid]);
		if(empty($re)){
			return json(['type'=>'error','msg'=>'添加失败']);
		}
		return json(['type'=>'success','msg'=>'添加成功']);
	}





	/**
	 * 修改会员页面
	 */
	public function editMemberPage()
	{	

		$param = $this->request->param();
		if(empty($param['uid'])){
			return json(['type'=>'error','msg'=>'无id']);
		}
		$uid = $param['uid'];
		$data = Db::name('member')->where('uid',$uid)->find();
		$member_group = Db::name('member_group')->select();

		$this->assign('data',$data);
		$this->assign('member_group',$member_group);
		return $this->fetch('/member/editMemberPage');
	}




	/**
	 * 修改会员资料
	 */
	public function editMember(){
		$param = $this->request->param();
		if(empty($param['uid']) || empty($param['groupid']) ||empty($param['islock']) ||empty($param['email']) ){
			return json(['type'=>'error','msg'=>'传参错误']);
		}
		$update['uid'] = (int)$param['uid'];
		$update['mobile'] = $param['mobile'];
		$update['islock'] = $param['islock'];
		$update['groupid'] = $param['groupid'];
		
		$update['email'] = $param['email'];
		$update['vip'] = 0;


		$update['overduedate'] = 0;
		if(!empty($param['overduedate'])){
			$update['overduedate'] = strtotime($param['overduedate']);
		}
		$repeat_data = Db::name('member')->where('nickname',$param['nickname'])->find();
		if(!empty($repeat_data)){
			if((int)$repeat_data['uid']!==$update['uid']){
				return json(['type'=>'error','msg'=>'该昵称已被使用']);
			}
		}
		$update['nickname'] = $param['nickname'];

		if(!empty($param['del_avatar'])){
			if($param['del_avatar']=="on"){
				//执行删除头像操作
				$update['avatar'] = "";
			}
		}
		if(!empty($param['vip'])){
			if($param['vip']=="on"){
				$update['vip'] = 1;
			}
		}
		if(!empty($param['password'])){
			if($param['password']!==$param['re_password']){
				return json(['type'=>'error','msg'=>'两次密码不相同']);
			}else{
				$update['password'] = cmf_password($param['password']);
			}
		}
		


		// 需要更新的数据
		$re = Db::name('member')->update($update);
		if(empty($re)){
			return json(['type'=>'error','msg'=>'修改失败']);
		}
		return json(['type'=>'success','msg'=>'修改成功']);
	}





	/**
	 * 查看会员详情
	 */
	public function detailMemberPage(){
		$param = $this->request->param();
		if(empty($param['uid'])){
			return json(['type'=>'error','msg'=>'无id']);
		}

		$uid = $param['uid'];
		$data = Db::name('member')->where('uid',$uid)->find();
		$member_group = Db::name('member_group')->select();



		$this->assign('data',$data);
		$this->assign('member_group',$member_group);
		return $this->fetch('/member/detailMemberPage');
	}




	/**
	 * 禁用/启用
	 */	
	public function disable(){
		$param = $this->request->param();
		if(empty($param['uid'])){
			$this->error("传参错误");
		}
		$uid = $param['uid'];
		$type = $param['type'];

		if($type==1){	//启用
			$re = Db::name('member')
					->where('uid',$uid)
					->update(['islock' => 1]);
		}elseif($type==2){	//禁用
			$re = Db::name('member')
					->where('uid',$uid)
					->update(['islock' => 2]);
		}

		if(empty($re)){
			$this->error("操作失败");
		}
		$this->success("操作成功");
	}


	/**
	 * 删除会员
	 */
	public function deleteMember(){
		$param = $this->request->param();
		if(empty($param['uid'])){
			$this->error("传参错误");
		}
		$uid = $param['uid'];
		$re = Db::name('member')->where('uid',$uid)->delete();
		if(empty($re)){
			$this->error("删除失败");
		}
		$this->success("删除成功");
	}



	/**
	 * 随机字符
	 * @param number $length 长度
	 * @param string $type 类型
	 * @param number $convert 转换大小写
	 * @return string
	 */
	function random($length=6, $type='string', $convert=0){
	    $config = array(
	        'number'=>'1234567890',
	        'letter'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
	        'string'=>'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
	        'all'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
	    );
	 
	    if(!isset($config[$type])) $type = 'string';
	    $string = $config[$type];
	 
	    $code = '';
	    $strlen = strlen($string) -1;
	    for($i = 0; $i < $length; $i++){
	        $code .= $string{mt_rand(0, $strlen)};
	    }
	    if(!empty($convert)){
	        $code = ($convert > 0)? strtoupper($code) : strtolower($code);
	    }
	    $re = '';
	    $re = Db::name('member')->where('username',$code)->find();

	    if(!empty($re)){
	    	$code = $this->random($length,$type);	
	    }
	    return $code;
	}






}
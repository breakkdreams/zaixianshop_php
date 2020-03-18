<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\api\controller;
use cmf\controller\AdminBaseController;
use app\admin\model\KehuModel;
use app\api\model\AccessModel;
use think\Db;
use tree\Tree;

class LoginController extends AdminBaseController
{
    public function _initialize()
    {
    }
    
    public function isLogin()
    {
    	return cmf_get_current_admin_id();
    }
    /**
     * 登录验证
     */
    public function doLogin()
    {
	if(!$this->request->isPost()){return json(['status'=>6]);}//请求类型错误
    $name = $this->request->param("username");
        if (empty($name)) {
			
			$data=[
			
				"status"=>4,
			];
			return json($data);
           // $this->error(lang('USERNAME_OR_EMAIL_EMPTY')); //用户名不存在
        }
        $pass = $this->request->param("password");
        if (empty($pass)) {
			
			$data=[
			
				"status"=>5,
			];
			return json($data);
            //$this->error(lang('PASSWORD_REQUIRED')); //密码不存在
        }
		
       
        $result = Db::name('user')->where("user_login",$name)->find();

        if (!empty($result) ) {
            if (cmf_compare_password($pass, $result['user_pass'])) {
            	/*
                $groups = Db::name('RoleUser')
                    ->alias("a")
                    ->join('__ROLE__ b', 'a.role_id =b.id')
                    ->where(["user_id" => $result["id"], "status" => 1])
                    ->value("role_id");
                if ($result["id"] != 1 && (empty($groups) || empty($result['user_status']))) {
                    $this->error(lang('USE_DISABLED'));
					
                }*/
                //登入成功页面跳转
                session('ADMIN_ID', $result["id"]);
                session('name', $result["user_login"]);
                $result['last_login_ip']   = get_client_ip(0, true);
                $result['last_login_time'] = time();
                $token                     = cmf_generate_user_token($result["id"], 'web');
                if (!empty($token)) {
                    session('token', $token);
                }
                Db::name('user')->update($result);
                cookie("admin_username", $name, 3600 * 24 * 30);
                session("__LOGIN_BY_CMF_ADMIN_PW__", null);
                $result['status']=1;
                return json($result);
				$data=[
					
					"status"=>1,
					"userid"=>$result["id"],
					"user_login"=>$result['user_login'],
					'user_type'=>$result['user_type']
				];
			    return json($data);
             //   $this->success(lang('LOGIN_SUCCESS'), url("admin/Index/index")); //登陆成功
            } else {
			
				$data=[
			
				"status"=>2,
			];
			return json($data);
              // $this->error(lang('PASSWORD_NOT_RIGHT')); //密码错误
            }
        } else {
			$data=[
			
				"status"=>3,
			];
			return json($data);
           // $this->error(lang('USERNAME_NOT_EXIST')); //用户名错误
        }
    }
	
	 /*kehu*/
    public function kehu(){
       $Kh = new kehuModel();
		
		
		$category1 = $this->request->param("category");
        $keyword = $this->request->param("keyword");

        //读取权限
        $id=$this->request->param('id');
        $a=new AccessModel();
        $accessIds=$a->readAccess($id);

        $aids=implode(',',$accessIds);

        $where="founder_id in (".$aids.") ";
		
	if($keyword){
			
			if($category1==1){
				$where .= " and `kh_name` = '$keyword'";
			}
			if($category1==2){
				$where .= " and `kh_type` = '$keyword'";
			}
			if($category1==3){
				$where .= " and `process` = '$keyword'";
			}
			if($category1==4){
				$where .= " and `industry` = '$keyword'";
			}
			if($category1==5){
				$where .= " and `person` = '$keyword'";
			}
			if($category1==6){
				$where .= " and `founder` = '$keyword'";
			}
				
		}
		
        $data  = $Kh::order("id DESC")->where($where)->select(); 
        
		if($data){

			 return json($data);
			 
		}else{
			$data=[
				
				'status'=>2,
				
			];
			
		}
		
    }
	
	
	  public function hetong(){
    	
	    $category1 = $this->request->param("category");
        $keyword = $this->request->param("keyword");

 		//读取权限
        $id=$this->request->param('id');
        $a=new AccessModel();
        $accessIds=$a->readAccess($id);

        $aids=implode(',',$accessIds);

        $where="user_id in (".$aids.") ";


		if($keyword){
			
			if($category1==1){
				$where .= " and `title` = '$keyword'";
			
			}
			if($category1==2){
				$where .= " and `contract_num` = '$keyword'";
			}
			if($category1==3){
				$where .= " and `username` = '$keyword'";
				
			}
			if($category1==4){
				$where .= " and `mobile` = '$keyword' ";
			}
				
		}
		  
       $data  = Db::name('hetong')->where($where)->order("id DESC")->select();		  
      	if($data){
			 
			 return json($data);
			
		}else{
			$data=[
				
				'status'=>2,
				
			];

		}

     }
	
	
	  public function htxx(){
      $id  = $this->request->param('id', 0, 'intval');
      $data  = Db::name('hetong')->where('id',$id)->find();
      $data['content']=cmf_replace_content_file_url(htmlspecialchars_decode($data['content']));
	  if($data){
		  
		 return json($data); 
		  
	  }else{
		  $date=[
			  'status'=>2,
		  ];
		  return json($date); 
	  } 
        
     }
	
	  protected function _getLevel($id, $array = [], $i = 0)
    {
        if ($array[$id]['parent_id'] == 0 || empty($array[$array[$id]['parent_id']]) || $array[$id]['parent_id'] == $id) {
            return $i;
        } else {
            $i++;
            return $this->_getLevel($array[$id]['parent_id'], $array, $i);
        }
    }
	
	   public function roleList()
    {
        $result = Db::name('role')->column('');
        $tree       = new Tree();
        $tree->icon = ['│ ', '├─ ', '└─ '];
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $m) {
            $newMenus[$m['id']] = $m;
        }

        foreach ($result as $n => $t) {
            $result[$n]['level']        = $this->_getLevel($t['id'], $newMenus);
            $result[$n]['style']        = empty($t['parent_id']) ? '' : 'display:none;';
            $result[$n]['parentIdNode'] = ($t['parent_id']) ? ' class="child-of-node-' . $t['parent_id'] . '"' : '';
        }
        $str = "<option value = \$id>
                <tr id='node-\$id'\$parentIdNode  style='\$style'>
                   <td style='padding-left:30px;'>\$spacer\$name</td>
                </tr>
                </option>";
        $tree->init($result);
        $role = $tree->getTree(0, $str);
        $this->assign('rolelist' ,$role);
    }
	
	 public function user()
    {
		
        $id    = $this->request->param('id', 0, 'intval');
        //获取用户所在的公司信息
        $user = DB::name('user')->where(["id" => $id])->find();
        $company=Db::view('company','*')->view('company_user','*','company.id=company_user.company_id')->where('company_user.user_id',$user['id'])->find();
        $user['signature']=cmf_replace_content_file_url(htmlspecialchars_decode($user['signature']));
		$this->assign($user);
		$info = json_decode($user['fujian'], true);
	     
		$role_user = DB::name('role_user')->where(["user_id" => $id])->find();
		
		$role = DB::name('role')->where(["id" => $role_user['role_id']])->find();

		$result=Db::name('business_card')->where('userid='.$id)->order(['sort'=>'desc','modify_time'=>'desc'])->select();
        foreach ($result as $key=>$value) {
            $value['content']=cmf_replace_content_file_url(htmlspecialchars_decode($value['content']));
            $result[$key]=$value;
        }
        $this->assign('company',$company);
        //保存联系人二维码
       	$url='BEGIN:VCARD
                    VERSION:3.0
                    FN:'.$user["user_login"].'
                    ADR;WORK;POSTAL:'.$company['address'].'
                    ORG:'.$company['company_name'].' 工号：'.$user["employee_id"].' 
                    TITLE:'.$role["name"].'
                    TEL;CELL;VOICE:'.$user["mobile"].'
                    TEL;WORK;VOICE:'.$company['tel'].'
                    URL:'.$company['url'].'
                    X-QQ:'.$user["qq"].'
                    EMAIL:'.$user["user_email"].'
                    END:VCARD';
       	$lxr_ewm=createTempQrcode($url)['data'];
       	$this->assign('lxr_ewm',$lxr_ewm);
        $this->assign('list',$result);

		$this->assign('role', $role);
		$this->roleList();
	    $this->assign('info', $info);
	    return $this->fetch();

}
	
	
	public function addht(){
		
	 //实例化模型
       $this->request->isPost();//接收post
            $data = $this->request->param();//接收post所有數據
			$sh  = $data['sh_time']*86400;		
			$start = strtotime($data['start_time']);//shengxiao
           

            $data['contract_time']=strtotime($data['contract_time']);
			$data['end_time']= $start+$sh;
			$data['start_time']=$start;
			
		
            $userid = $data['user'];
            $uid = Db::name('user')->where("id",$userid)->find();
			$data['user']=$uid['user_login'];
		   
          // 插入數據

           $da = Db::name('hetong')->insert($data); 	
		
	       if($da){
			   $data=[
				   
				   'status'=>1
				   
			   ];
			    return json($data);
			   
		   }else{
			   
			   
			    $data=[
				   
				   'status'=>2
				   
			   ];
			    return json($data);
			   
		   }
		
	}
		public function addkh(){
			
			$data = $this->request->param();
			
			if(!$data){
				  $data=[
				   
				   'status'=>3,
				   
			   ];
			    return json($data);
			}
			$userid =  cmf_get_current_admin_id();	
			
		    $uid = Db::name('user')->where("id",$userid)->find();
			$data['founder']=$uid['user_login']; 

			$data['create_time']=time();
			$data['update_time']=time();
		 
            $da = Db::name('kehu')->insert($data); // 插入數據
	
			
			  if($da){
			   $data=[
				   
				   'status'=>1,
				   
			   ];
			    return json($data);
			   
		   }else{
			   
			   
			    $data=[
				   
				   'status'=>2,
				   
			   ];
			    return json($data);
			   
		   }
			
			
			
			
		    $userId = Db::name('kehu')->getLastInsID();
                $datalx=[
				"call"=>$data['call'],
				"name"=>$data['name'],
				"kh_name"=>$data['kh_name'],
				"position"=>$data['position'],
				"qq"=>$data['qq'],
				"mailbox"=>$data['mailbox'],
				"mobile"=>$data['mobile'],
				"kh_id"=>$userId,
				"status"=>1,
			];
             
			$lx = Db::name('lx')->insert($datalx);
		
	}
	
	 public function del()
    {
        $request = $this->request;
        $re =  $request->param('id');
         Db::name('kehu')->where("id",$re)->delete();
		 if($re){
			 $data=[
				   'status'=>1,
			   ];
			 return json($data);
		 }else{
			 $data=[
				   'status'=>2,
			   ];
			 return json($data);
			 
			 
		 }		
}
	
 public function khedit()
    {
        $request = $this->request;
        $data =  $request->param();
         Db::name('kehu')->update($data);
		 if($re){
			 $data=[
				   'status'=>1,
			   ];
			 return json($data);
		 }else{
			 $data=[
				   'status'=>2,
			   ];
			 return json($data);
			 
			 
		 }		
}

	//合同修改
 public function htedit()
    {
        $request = $this->request;
        $data =  $request->param();
		
		if(!$data['id']){
			 $data=[
				   'status'=>1,
			   ];
			 return json($data);
		}
            $sh = $data['sh_time']*86400;
            $start = strtotime($data['start_time']);
            $data['contract_time']=strtotime($data['contract_time']);
            $data['start_time']=strtotime($data['start_time']);
            $data['end_time']=$sh+$start;
           $re = Db::name('hetong')->update($data);
		   if($re){
			 $data=[
				   'status'=>2,
			   ];
			 return json($data);
		  }else{

			 $data=[
				   'status'=>3,
			   ];
			 return json($data);

		 }		
}
	
//用户
 public function userinfo()
{       

	if(!$this->request->isPost()){return json(null);}//请求类型错误
		$id = $this->request->param("id");
    
        $user = DB::name('user')->where(["id" => $id])->find();

        $user['sfz']="xxxxxxxxxxxxxxxxxxx";
	    
		return json($user);	
}




	//用户头像
 public function userimg()
{       $id = $this->request->param("id");
	    
    
	
        $user = DB::name('user')->where(["id" => $id])->find();
	    

       
		exit("$user[avatar]");
		

		
}


	
	
	//目标
 public function mubiao()
{       
	
	$data = DB::name('mymub')->select();
	    
    return json($data);
     		
}	
//角色	
public function role(){
	
	 $result = DB::name('role')->column('');
	 
	 foreach ($result as $m) {
            $newMenus[$m['id']] = $m;
        }
	  
	
	 foreach ($result as $n => $t) {
            $result[$n]['level']        = $this->_getLevel($t['id'], $newMenus);
            
           
        }
		 
	var_dump($result);

	
}
	
	public function caiwu_bb(){
		
		$start = strtotime(date('Y0101'.'00:00:00'));
		
		 $end = strtotime("+1 month",$start);
        for($i=0;$i<12;$i++){
            $timedate = Db::name('hetong')->where("contract_time",">=",$start)->where("contract_time","<",$end)->field("sum(contract_money) as  money1")->select();
            $datatime[]=$timedate[0]["money1"];
            $start = strtotime("+1 month",$start);
            $end = strtotime("+1 month",$start);
        }
		
		
		//12个月的已收金额
		if(!isset($data)){
				$start1 = strtotime(date('Y0101'.'00:00:00'));
				
			}else{
				$start1 = strtotime(date($data.'0101'.'00:00:00'));
			}
        $end1 = strtotime("+1 month",$start1);
        for($i=0;$i<12;$i++){
            $timedate1 = Db::name('hetong')->where("contract_time",">=",$start1)->where("contract_time","<",$end1)->field("sum(sk_money) as  money1")->select();
            $datatime1[]=$timedate1[0]["money1"];
            $start1 = strtotime("+1 month",$start1);
            $end1= strtotime("+1 month",$start1);
        }
        
		
		
		$ying_ze  =  Db::name('hetong')->field("sum(contract_money) as  ying_money")->find()['ying_money'];
		
		
		$yi_ze  = Db::name('caiwu')->field("sum(money) as  yi_money")->find()['yi_money'];
		
		
		$date['yingshou']= $datatime;
		$date['yishou']= $datatime1;
		$date['zying']=$ying_ze;
		$date['zyi']=$yi_ze;
		return json($date);
		
		
		
	}	

	/**
	 * 个人名片
	 * @param int userid 用户id
	 */
	public function businesscard()
	{
		if($this->request->isPost()){
			$data=$this->request->post();
			$id=$data['userid'];
			$us=Db::name('user')->where('id='.$id)->find();
			$bc=Db::name('business_card')->where('userid='.$id)->order(['modify_time'=>'desc','sort'=>'desc'])->select();

			$user['name']=$us['user_login'];
			$user['mobile']=$us['mobile'];
			$user['email']=$us['user_email'];
			$user['signature']=$us['signature'];
			$user['avatar']=$us['avatar'];
			$res['info']=[];
			foreach ($bc as $key => $value) {
				$res['info'][]=cmf_replace_content_file_url(htmlspecialchars_decode($value['content']));
			}
			$res['user']=$user;
			return json($res);

		}
	}




	
}

   
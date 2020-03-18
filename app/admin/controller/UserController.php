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
namespace app\admin\controller;
use app\admin\model\UserModel;
use cmf\controller\AdminBaseController;
use app\admin\model\HetongModel;
use app\admin\model\AttendanceModel;
use app\admin\model\AccessModel;//权限查询
use think\Db;
use tree\Tree;
use think\Request;


/**
 * Class UserController
 * @package app\admin\controller
 * @adminMenuRoot(
 *     'name'   => '管理组',
 *     'action' => 'default',
 *     'parent' => 'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   => '',
 *     'remark' => '管理组'
 * )
 */
class UserController extends AdminBaseController
{
    public $departmentids = array();

    /**
     * 用户状态
     */
    private $statusStr = ['禁用','正常'];

    /**
     * 管理员列表
     * @adminMenu(
     *     'name'   => '管理员',
     *     'parent' => 'default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        //获取管理员列表信息
        $userList = Db::name('user')->paginate(20);
        $company = Db::name('company')->where('id',1)->find();

        foreach ($userList as $key => $value) {
            //获取用户部门//获取用户角色
            $userAttach = Db::name('user_attach')->where('user_id',$value['id'])->find();
            $value['department'] = Db::name('department')->where('id',$userAttach['department_id'])->value('name');
            $value['role'] = Db::name('role')->where('id',$userAttach['role_id'])->value('name');
            $userList[$key] = $value;
        }
        
        $this->assign('company',$company);
        $this->assign('userList',$userList);
        $this->assign('statusStr',$this->statusStr);
        return $this->fetch();
    }

    //部门下溯各个子部门
    public function getDepartment($departmentid)
    {
        $departments = Db::name('department')->where(['parent_id'=>$departmentid])->column('id');
        if (count($departments) != 0) {
            array_push($this->departmentids, $departments);
        }
        for ($i=0; $i < count($departments); $i++) { 
            $departmentid = $departments[$i];
            $this->getDepartment($departmentid);
        }
    }

    //角色列表
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
    //部门列表
    public function department()
    {   
        $result = Db::name('department')->column('');
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
        $str2 = "<option value = \$id>
                <tr id='node-\$id'\$parentIdNode  style='\$style'>
                   <td style='padding-left:30px;'>\$spacer\$name</td>
                </tr>
                </option>";
        $tree->init($result);
        $department = $tree->getTree(0, $str2);
        $this->assign('department' ,$department);
    }

    /**
     * 获取菜单深度
     * @param $id
     * @param array $array
     * @param int $i
     * @return int
     */
    protected function _getLevel($id, $array = [], $i = 0)
    {
        if ($array[$id]['parent_id'] == 0 || empty($array[$array[$id]['parent_id']]) || $array[$id]['parent_id'] == $id) {
            return $i;
        } else {
            $i++;
            return $this->_getLevel($array[$id]['parent_id'], $array, $i);
        }
    }

    /**
     * 管理员添加
     * @adminMenu(
     *     'name'   => '管理员添加',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员添加',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $roles = Db::name('role')->where(['status' => 1])->order("id DESC")->select();
		
		$id = cmf_get_current_admin_id();
		
		$info = Db::name('user_attach')->where(['user_id' => $id])->select();
		
        //读取所在公司角色列表 并分层级
        //读取当前登入账号所在的公司
        $accessModel=new AccessModel();      
        $company=$accessModel->get_company_info();  
        //读取所在公司角色列表 并分层级
        $accessModel=new AccessModel();
        //获取当前用户所在公司角色列表信息
        $roles = $accessModel->get_company_role_list();
        $result =$roles;
        $tree       = new Tree();
        //角色
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
        $str2 = "<option value = \$id data-dep=\$department_id>\$spacer\$name</option>";
        $tree->init($result);
        $role = $tree->getTree(0, $str2);
// dump($company['id']);
        $this->assign('roleList',$role);
        // $this->assign('company',$company['id']);
        $this->assign('company',$company);
        $this->assign("roles", $roles);
		$this->assign("userid", $id);
       // $this->roleList();
        return $this->fetch();
    }


    /**
     *管路员添加提交 
     */
    public function addPost()
    {
        if(!$this->request->isPost()){
            $this->errro('请求类型错误！');
        }
        $res = $this->request->param();
        $data = $res['post'];
        if(empty($data['user_login'])){
            $this->errro('用户名不能为空！');
        }
        if( empty($data['user_pass']) || empty($data['d_pass']) ){
            $this->errro('密码不能为空！');
        }
        if( strlen($data['user_pass']) < 6  || strlen($data['user_pass']) > 16 ){
            $this->errro('密码长度不合法，长度应为6-16位！');
        }
        if( strlen($data['d_pass']) < 6  || strlen($data['d_pass']) > 16 ){
            $this->errro('确认密码长度不合法，长度应为6-16位！');
        }
        if($data['user_pass'] !== $data['d_pass'] ){
            $this->error('两次输入的密码不一致！');
        }
        unset($data['d_pass']);
        //生成密码
        $data['user_pass'] = cmf_password($data['user_pass']);
        // 启动事务
        Db::startTrans();
        try{
            $userId = Db::name('user')->insertGetId($data);
            $attach['department_id'] = Db::name('role')->where('id',$res['role_id'])->value('department_id');
            $attach['role_id'] = $res['role_id'];
            $attach['company_id'] = $res['company_id'];
            $attach['user_id'] = $userId;
            Db::name('user_attach')->insert($attach);
            // 提交事务
            Db::commit();    
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error('添加失败，请稍后再试！');
        }
        $this->success('添加成功！');
    }



    /**
     * 管理员添加提交
     * @adminMenu(
     *     'name'   => '管理员添加提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员添加提交',
     *     'param'  => ''
     * )
     */
	public function erwei($id){
		
		Vendor('phpqrcode.phpqrcode');
        $url= 'http://oa.300c.cn/api/login/user?id='.$id;
        $url = urldecode($url);
        $object = new \QRcode();
        ob_clean();//这个一定要加上，清除缓冲区
        $object->png($url,false,'Q','6','2');
		
	}		
public function xiangxi(){
	    $id    = $this->request->param('id', 0, 'intval');
        $user = DB::name('user')->where(["id" =>$id])->find();
		$this->assign($user);
		$info = json_decode($user['fujian'], true); 
		$this->roleList();
	    $this->assign('info', $info);
	    
	    $date = DB::name('role_user')->where(["user_id" => $id])->find();
        
	    $gw =  DB::name('role')->where(["id" => $date['role_id']])->find();
	
	    $sjgw = DB::name('role')->where(["id" => $gw['parent_id']])->find();


	    $this->assign('sjgw', $sjgw);
	    $this->assign('gw', $gw);
	 return $this->fetch();
    }
	
	public function bak(){
		$id    = $this->request->param('id', 0, 'intval');
		
		$date = DB::name('user_attach')->where(["user_id" => $id])->find();
		
		$userid =  DB::name('role')->where(["id" => $date['role_id']])->find();
	
	   
		$gw =  DB::name('role')->where(["department_id" => $userid['department_id']])->select();
	  
	    $this->assign('gw', $gw);
		
		return $this->fetch();
	}
	

    /**
     * 管理员编辑
     * @adminMenu(
     *     'name'   => '管理员编辑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员编辑',
     *     'param'  => ''
     * )
     */
    public function edit()
    {
        $data=$this->request->param();
        $id    = $this->request->param('id', 0, 'intval');
        $user = DB::name('user')->where(["id" => $id])->find();
        $role=Db::table('cmf_role')
            ->where('id','=',function($query) use ($id) {
                $query->table('cmf_user_attach')->where('user_id',$id)->field('role_id');
            })->find();
        $this->assign('role',json_encode($role));
        $this->assign($user);
        //读取所在公司角色列表 并分层级
        $accessModel=new AccessModel();
        $company=$accessModel->get_company_info(); 
        //获取当前用户所在公司角色列表信息
        $roles = $accessModel->get_company_role_list();
        $result =$roles;
        $tree       = new Tree();
        //角色
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
        $str2 = "<option value = \$id data-dep=\$department_id>\$spacer\$name</option>";
        $tree->init($result);
        $role = $tree->getTree(0, $str2);

        $this->assign('company',$company);
        $this->assign('roleList',$role);
		$this->assign("userid", $id); 
        return $this->fetch();
    }

    /**
     * 管理员资料编辑提交
     */
    public function editPost()
    {
        if(!$this->request->isPost()){
            $this->error('请求类型错误！');
        }
        $res = $this->request->param();
        $userId = $res['id'];
        $data = $res['post'];
        if(empty($data['user_login'])){
            $this->error('用户名不能为空！');
        }

        if( cmf_get_current_admin_id() != 1 &&  $userId == 1 ){
            $this->error('超级管理员账号信息禁止修改！');
        }
        if( $userId == 1  &&  $res['role_id'] != 1 ){
            $this->error("超级管理员角色禁止修改！");
        }
        //检查用户名是否重复
        $user = Db::name('user')->where('user_login',$data['user_login'])->find();
        if( !empty($user) && $user['id'] != $userId ){
            $this->error('用户名已经存在，请重新输入！');
        }

        if( !empty($res['user_pass']) ){
            if( empty($res['user_pass']) || empty($res['d_pass']) ){
                $this->error('密码不能为空！');
            }
            if( strlen($res['user_pass']) < 6  || strlen($res['user_pass']) > 16 ){
                $this->error('密码长度不合法，长度应为6-16位！');
            }
            if( strlen($res['d_pass']) < 6  || strlen($res['d_pass']) > 16 ){
                $this->error('确认密码长度不合法，长度应为6-16位！');
            }
            if($res['user_pass'] !== $res['d_pass'] ){
                $this->error('两次输入的密码不一致！');
            }
            $data['user_pass'] = cmf_password($res['d_pass']);
        }
        // 启动事务
        Db::startTrans();
        try{
            $updateRes = Db::name('user')->where('id',$userId)->update($data);
            $attach['department_id'] = Db::name('role')->where('id',$res['role_id'])->value('department_id');
            $attach['role_id'] = $res['role_id'];
            Db::name('user_attach')->where('user_id',$userId)->update($attach);
            // 提交事务
            Db::commit();    
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error('信息更新失败，请稍后再试！');
        }        
        $this->success('信息更新成功！');        
    }
	

	
    /**
     * 管理员个人信息修改
     * @adminMenu(
     *     'name'   => '个人信息',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员个人信息修改',
     *     'param'  => ''
     * )
     
    public function userInfo()
    {

        $id    = cmf_get_current_admin_id();
        $user = DB::name('user')->where(["id" => $id])->find();
        $this->assign($user);
        $this->roleList();
	 	
	   $roles = Db::name('role')->where(['status' => 1])->order("id DESC")->select();
		
		
		$info1 = Db::name('user_attach')->where(['user_id' => $id])->find();
        
		
		$ids = cmf_get_current_admin_id();
	
		$qx = Db::name('user_attach')->where(['user_id' => $ids])->find();
	    $name =	Db::name('role')->where(['id' => $info1["role_id"]])->find();
		
		$this->assign("info1", $info1);
		
        $this->assign("roles", $roles);
		$this->assign("qx", $qx);
		$this->assign("name", $name);
		$this->assign("userid", $id);
		
		$info = json_decode($user['fujian'], true); 

	    $this->assign('info', $info);
		
		   return $this->fetch();
    }

    /**
     * 管理员个人信息修改提交
     * @adminMenu(
     *     'name'   => '管理员个人信息修改提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员个人信息修改提交',
     *     'param'  => ''
     * )
     */
    public function userInfoPost()
    {
        if ($this->request->isPost()) {

            $data             = $this->request->post();
            $data['birthday'] = strtotime($data['birthday']);
            $data['id']       = cmf_get_current_admin_id();
            $create_result    = Db::name('user')->update($data);;
            if ($create_result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }

    /**
     * 管理员删除
     * @adminMenu(
     *     'name'   => '管理员删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '管理员删除',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id == 1) {
            $this->error("最高管理员不能删除！");
        }
        if (Db::name('user')->delete($id) !== false) {
            Db::name('user_attach')->where('user_id',$id)->delete();
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     * 停用管理员
     * @adminMenu(
     *     'name'   => '停用管理员',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '停用管理员',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = $this->request->param('id', 0, 'intval');
        if (!empty($id)) {
            $result = Db::name('user')->where(["id" => $id, "user_type" => 1])->setField('user_status', '0');
            if ($result !== false) {
                $this->success("管理员停用成功！", url("user/index"));
            } else {
                $this->error('管理员停用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 启用管理员
     * @adminMenu(
     *     'name'   => '启用管理员',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '启用管理员',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = $this->request->param('id', 0, 'intval');
        if (!empty($id)) {
            $result = Db::name('user')->where(["id" => $id, "user_type" => 1])->setField('user_status', '1');
            if ($result !== false) {
                $this->success("管理员启用成功！", url("user/index"));
            } else {
                $this->error('管理员启用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    public function userInfoEdit()
    {
        $id = cmf_get_current_admin_id();
        $data = $this->request->param();
        if ($data) {
            $data['id'] = $id;
            unset($data['d_pass']);
            $res = Db::name('user')->update($data);
            if ($res !== false) {
                $this->success('更改成功');
            }else{
                $this->error('更改失败');
            }
        }
        if (!$data) {
            $data = Db::name('user')->find($id);
            $this->assign('data',$data);
            $this->roleList();
            return $this->fetch();   
        }
    }
	
	public function qingjia()
    {
      $userid = $this->request->param('id', 0, 'intval'); 
	  
	  $username = Db::name('user')->where('id',$userid)->find();
		
	  $this->assign('userid', $userid);
	  $this->assign('username', $username);
	return $this->fetch();  	
	}
	
	
	public function ck_qj()
    {
	  $Qj = new AttendanceModel();
      $userid = $this->request->param('id', 0, 'intval'); 
	  $this->request->isPost();//接收post
      $start = $this->request->param("start");//接收post所有數據
	  $end = $this->request->param("end");//接收post所有數據
		
	  if($start!=0  && $end!=0){
		$start =   strtotime($start);
		$end =   strtotime($end); 

		  
	    $info = $Qj::where('start_time','>',$start)->where('end_time','<',$end)->where('userid',$userid)->select();
		  
		
	  }else{
		  
		  $info = $Qj::where('userid',$userid)->select();
	  }
	  	
	  $this->view->assign('info',$info);
	$this->assign('userid', $userid);
	return $this->fetch();  	
	}
	
	
	
	
	public function qj_add()
    {
	$Qj = new AttendanceModel();
		
	$data = $this->request->param();
	
	$data['post']['start_time'] = strtotime($data['post']['start_time']);	
		
	$data['post']['end_time']=$data['post']['start_time']+$data['post']['time']*86400;
	
	
	
	$userid = Db::name('user_attach')->where('user_id',$data['post']['userid'])->find();
		
		if($userid['role_id']==62){
			
			$time = Db::name('user')->where('id',$data['post']['userid'])->find();
			
			$rz_time=[
				'rz_time'=>$time['rz_time']+$data['post']['time']*86400,
			];
			
			Db::name('user')->where('id',$data['post']['userid'])->update($rz_time);	
		}
		
		$result = $Qj::insert($data['post']);
	if ($result) {
                $this->success('添加成功',url("user/index"));
            }else{
                $this->error('添加失败');
            }		
	}
	
	 public function qj_del()
    {
        $id = $this->request->param('id', 0, 'intval');
		
		$uid = Db::name('attendance')->where('id',$id)->find();
		
        $userid = Db::name('user_attach')->where('user_id',$uid['userid'])->find();
          if($userid['role_id']==62){
			
			$time = Db::name('user')->where('id',$uid['userid'])->find();
			
			$rz_time=[
				
				'rz_time'=>$time['rz_time']-$uid['time']*86400,
			];
			
			Db::name('user')->where('id',$uid['userid'])->update($rz_time);	
		}
		
	
	
          $del =  Db::name('attendance')->where(['id'=>$id])->delete();
			 
		 
		
		if($del){
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }
	
	
	 public function qj_edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        $Qj = new AttendanceModel();
        
        $info =  Db::name('attendance')->where(['id'=>$id])->find();
		$this->view->assign('info',$info);
		if($this->request->isPost()){
		$data = $this->request->param();
		
	    $data['post']['start_time'] = strtotime($data['post']['start_time']);	
		
	    $data['post']['end_time']=$data['post']['start_time']+$data['post']['time']*86400;
			
			$uid = Db::name('attendance')->where('id',$data['id'])->find();
			
		    $userid = Db::name('user_attach')->where('user_id',$uid['userid'])->find();
			
          if($userid['role_id']==62){
			
			$time = Db::name('user')->where('id',$uid['userid'])->find();
			
			$rz_time=[
				
				'rz_time'=>$time['rz_time']-$uid['time']*86400+$data['post']['time']*86400,
			];
			
			Db::name('user')->where('id',$uid['userid'])->update($rz_time);	
		}
			
			
			
		$edit =  $Qj::where('id',$data['id'])->update($data['post']);
		
		if($edit){
            $this->success("修改成功！",url("user/ck_qj"));
        } else {
            $this->error("修改失败！");
			
        }
			
		}
		return $this->fetch(); 
    }


    /**
     * 个人电子名片页面
     */
    public function businesscard()
    {

        $result=Db::name('business_card')->where('userid='.cmf_get_current_admin_id())->order(['sort'=>'desc','modify_time'=>'desc'])->select();
        foreach ($result as $key=>$value) {
            $value['content']=cmf_replace_content_file_url(htmlspecialchars_decode($value['content']));
            $result[$key]=$value;
        }
        $signature=Db::name('user')->where('id='.cmf_get_current_admin_id())->find()['signature'];
        $this->assign('userid',cmf_get_current_admin_id());
        $this->assign('signature',cmf_replace_content_file_url(htmlspecialchars_decode($signature)));
        $this->assign('list',$result);
        return $this->fetch();
    }

    /**
     * 修改个性签名
     */
    public function setSignature(){
        $con=$this->request->param()['content'];
        Db::name('user')->where('id='.cmf_get_current_admin_id())->update(['signature'=>$con]);
    }

    /**
     * 个人名片页面提交
     * @return [type] [description]
     */
    public function businesscard_post()
    {
        if($this->request->isPost()){
           //dump($this->request);
            
            if(!isset($this->request->post()['post'])){
                 $this->error('请填写展示内容，再提交！');
            }
            $data=$this->request->post()['post'];
            $in['content']=$data['bak'];
            if(strlen(trim($in['content']))==0){
                $this->error('请填写展示内容，再提交！');
            }
            $in['add_time']=time();
            $in['modify_time']=time();
            $in['userid']=cmf_get_current_admin_id();

            Db::name('business_card')->insert($in);
            $this->success('添加成功！');

        }else{
            $this->error('请求错误！');
        }
    }
    /**
     * 个人名片操作
     * @return [type] [description]
     */
    public function operate()
    {
        if($this->request->isPost()){
            $data=$this->request->post();
            if(-1==$data['type']){//删除
                Db::name('business_card')->where('id='.$data['id']." and userid=".$data['userid'])->delete();
            }else{
                $up=['sort'=>$data['type']];
                //置顶取消置顶  
                if($data['type']==1){
                    $up['modify_time']=time();
                }
                Db::name('business_card')->where('id='.$data['id']." and userid=".$data['userid'])->update($up);
            }
            return json(['code'=>1,'msg'=>'']);
           }else{
            return json(['code'=>0,'msg'=>'请求类型错误！']);
        }
    }

    /**
     * 用户签名页面
     * @return [type] [description]
     */
    public function sign(){
        $id=cmf_get_current_admin_id();
        $user=Db::name('user')->where('id='.$id)->find();
        $this->assign('user',$user);
        return $this->fetch();
    }
    /**
     * 用户签名二维码
     * @return [type] [description]
     */
    public function yhqm_erweima($id){
        $request = Request::instance();
        $domain=$request->domain();
        Vendor('phpqrcode.phpqrcode');
        $url= $domain.'/portal/index/yhqm?id='.$id;
        $url = urldecode($url);
        $object = new \QRcode();
        ob_clean();//这个一定要加上，清除缓冲区
        $object->png($url,false,'Q','6','2');
    }	


    

    /**
     * 删除签名
     * @return [type] [description]
     */
    public function delesign(){
        $data=$this->request->post();
        Db::name('user')->where("id=".$data['id'])->update(['sign'=>""]);
        
        return 1;
    }

    /**
     * 获取token
     * @return [type] [description]
     */
    public function getToken()
    {
        if($this->request->isPost()){
            $data=$this->request->post();
            $user=Db::name('user')->where('id='.$data['id'])->find();
            $res=get_rongyun_token($user['id'],$user['user_login']);
            if($res['code']==200){
                //将获取的token放入数据库
                Db::name('user')->where('id='.$data['id'])->update(['ry_token'=>$res['token']]);
                return 1;
            }else{
                return $res['code'];
            }
        }
    }


    /**
     * 更新员工编号
     * @return [type] [description]
     */
    public function update_employee_id()
    {
        $user=Db::name('user')->where('user_type=1 and user_status=1')->select();
        foreach ($user as $arr) {
            $NO=getEmployeeId($arr['id']);
            Db::name('user')->where('id='.$arr['id'])->update(['employee_id'=>$NO]);
        }
        return $this->success('更新成功！');
    }




	
}
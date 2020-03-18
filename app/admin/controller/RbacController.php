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

use cmf\controller\AdminBaseController;
use think\Db;
use tree\Tree;
use app\admin\model\AdminMenuModel;
use app\admin\model\AccessModel;

class RbacController extends AdminBaseController
{

    /**
     * 角色管理列表
     * @adminMenu(
     *     'name'   => '角色管理',
     *     'parent' => 'admin/User/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '角色管理',
     *     'param'  => ''
     * )
     */
    // public function index()
    // {
    //     $data = Db::name('role')->order(["list_order" => "ASC", "id" => "DESC"])->select();
    //     $this->assign("roles", $data);
    //     return $this->fetch();
    // }

    /**
     * 添加角色
     * @adminMenu(
     *     'name'   => '添加角色',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加角色',
     *     'param'  => ''
     * )
     */
    public function roleAdd()
    {
        return $this->fetch();
    }

    /**
     * 添加角色提交
     * @adminMenu(
     *     'name'   => '添加角色提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加角色提交',
     *     'param'  => ''
     * )
     * 2019221
     */
    public function roleAddPost()
    {
        if(!$this->request->isPost()){
            return json(['status'=>'error','message'=>'请求类型错误！']);
        }
        $data=$this->request->param();
        if(!isset($data['department'])){
            return json(['status'=>'error','message'=>'请添加部门信息']);
        }
        //判断角色与部门是否匹配
        $dpid=Db::name('role')->where('id',$data['role'])->value('department_id');
        if($dpid>$data['department']){
            return json(['status'=>'error','message'=>'部门等级与角色等级不匹配，请检查！']);
        }
        $insert['parent_id']=$data['role'];
        $insert['department_id']=$data['department'];
        $insert['status']=1;
        $insert['create_time']=time();
        $insert['name']=$data['role_name'];
        $insert['remark']=isset($data['remark'])?$data['remark']:'';
        $insert['company_id'] = $this->getCompanyIdByUserId( cmf_get_current_admin_id() );
        $res=Db::name('role')->insert($insert);
        if($res){
            return json(['status'=>'success','message'=>'角色添加成功！']);
        }else{
            return json(['status'=>'error','message'=>'角色添加失败，请稍后再试！']);
        }
/*        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'role');
            if ($result !== true) {
                // 验证失败 输出错误信息
                $this->error($result);
            } else {
                $result = Db::name('role')->insert($data);
                if ($result) {
                    $this->success("添加角色成功", url("rbac/index"));
                } else {
                    $this->error("添加角色失败");
                }

            }
        }*/
    }


    /**
     *根据用户id获取公司id
     */
    private function getCompanyIdByUserId($userId)
    {
        $cid = Db::name('user_attach')->where('user_id',$userId)->value('company_id');
        return $cid;
    }

    /**
     * 编辑角色
     * @adminMenu(
     *     'name'   => '编辑角色',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑角色',
     *     'param'  => ''
     * )
     */
    public function roleEdit()
    {
        $id = $this->request->param("id", 0, 'intval');
        if ($id == 1) {
            $this->error("超级管理员角色不能被修改！");
        }
        $data = Db::name('role')->where(["id" => $id])->find();
        if (!$data) {
            $this->error("该角色不存在！");
        }
        $this->assign("data", $data);
        return $this->fetch();
    }

    /**
     * 编辑角色提交
     * @adminMenu(
     *     'name'   => '编辑角色提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑角色提交',
     *     'param'  => ''
     * )
     */
    public function roleEditPost()
    {
        if(!$this->request->isPost()){
            return json(['status'=>'error','message'=>'请求类型错误！']);
        }
        $data=$this->request->param();
        if(!isset($data['department'])){
            return json(['status'=>'error','message'=>'请添加部门信息']);
        }
        //判断角色与部门是否匹配
        $dpid=Db::name('role')->where('id',$data['role'])->value('department_id');
        if($dpid>$data['department']){
            return json(['status'=>'error','message'=>'部门等级与角色等级不匹配，请检查！']);
        }
        if($data['role']==$data['role_id']){
            return json(['status'=>'error','message'=>'无法将上级角色设置为当前角色，请检查!']);
        }
        $insert['department_id']=$data['department'];
        $insert['status']=1;
        $insert['parent_id']=$data['role'];
        $insert['update_time']=time();
        $insert['name']=$data['role_name'];
        $insert['remark']=isset($data['remark'])?$data['remark']:'';
        $insert['company_id'] = $this->getCompanyIdByUserId( cmf_get_current_admin_id() );
        $res=Db::name('role')->where('id',$data['role_id'])->update($insert);

        if($res){
            return json(['status'=>'success','message'=>'角色编辑成功！']);
        }else{
            return json(['status'=>'error','message'=>'角色编辑失败，请稍后再试！']);
        }

/*        $id = $this->request->param("id", 0, 'intval');
        if ($id == 1) {
            $this->error("超级管理员角色不能被修改！");
        }
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $result = $this->validate($data, 'role');
            if ($result !== true) {
                // 验证失败 输出错误信息
                $this->error($result);

            } else {
                if (Db::name('role')->update($data) !== false) {
                    $this->success("保存成功！", url('rbac/index'));
                } else {
                    $this->error("保存失败！");
                }
            }
        }*/
    }

    /**
     * 删除角色
     * @adminMenu(
     *     'name'   => '删除角色',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除角色',
     *     'param'  => ''
     * )
     */
    public function roleDelete()
    {
        $id = $this->request->param("id", 0, 'intval');
        if ($id == 1) {
            return json(['status'=>'error','message'=>"超级管理员角色不能被删除！"]);
        }
        $count=Db::name('role')->where('parent_id',$id)->count();
        if ($count > 0) {
            return json(['status'=>'error','message'=>"该角色还有子级角色,不能删除
                ,请检查！"]);
        } 
        $count = Db::name('user_attach')->where(['role_id' => $id])->count();
        if ($count > 0) {
             return json(['status'=>'error','message'=>"该角色已经有用户,不能删除，请检查！"]);
        } else {
            //删除角色下的授权菜单信息
            Db::name('auth_access')->where('role_id',$id)->delete();
            $status = Db::name('role')->delete($id);
            if (!empty($status)) {
                return json(['status'=>'success','message'=>"删除成功！"]);
            } else {
                return json(['status'=>'error','message'=>"删除失败！"]);
            }
        }
    }

    /**
     * 设置角色权限
     * @adminMenu(
     *     'name'   => '设置角色权限',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '设置角色权限',
     *     'param'  => ''
     * )
     */
    public function authorize()
    {
        $AuthAccess     = Db::name("AuthAccess");
        $adminMenuModel = new AdminMenuModel();
        //角色ID
        $roleId = $this->request->param("id", 0, 'intval');
        if (empty($roleId)) {
            $this->error("参数错误！");
        }

        $tree       = new Tree();
        $tree->icon = ['│ ', '├─ ', '└─ '];
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';

        $result = $adminMenuModel->menuCache();
        $newMenus      = [];
        $privilegeData = $AuthAccess->where(["role_id" => $roleId])->column("rule_name");//获取权限表数据

        foreach ($result as $m) {
            $newMenus[$m['id']] = $m;
        }

        foreach ($result as $n => $t) {
            $result[$n]['checked']      = ($this->_isChecked($t, $privilegeData)) ? ' checked' : '';
            $result[$n]['level']        = $this->_getLevel($t['id'], $newMenus);
            $result[$n]['style']        = empty($t['parent_id']) ? '' : 'display:none;';
            $result[$n]['parentIdNode'] = ($t['parent_id']) ? ' class="child-of-node-' . $t['parent_id'] . '"' : '';
        }

        $str = "<tr id='node-\$id'\$parentIdNode  style='\$style'>
                   <td style='padding-left:30px;'>\$spacer<input type='checkbox' name='menuId[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'> \$name</td>
    			</tr>";
        $tree->init($result);

        $category = $tree->getTree(0, $str);
        $this->assign("category", $category);
        $this->assign("roleId", $roleId);
        return $this->fetch();
    }

    /**
     * 角色授权提交
     * @adminMenu(
     *     'name'   => '角色授权提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '角色授权提交',
     *     'param'  => ''
     * )
     */
    public function authorizePost()
    {
        if ($this->request->isPost()) {
            $roleId = $this->request->param("roleId", 0, 'intval');
            if (!$roleId) {
                $this->error("需要授权的角色不存在！");
            }
            if (is_array($this->request->param('menuId/a')) && count($this->request->param('menuId/a')) > 0) {

                Db::name("authAccess")->where(["role_id" => $roleId, 'type' => 'admin_url'])->delete();
                foreach ($_POST['menuId'] as $menuId) {
                    $menu = Db::name("adminMenu")->where(["id" => $menuId])->field("app,controller,action")->find();
                    if ($menu) {
                        $app    = $menu['app'];
                        $model  = $menu['controller'];
                        $action = $menu['action'];
                        $name   = strtolower("$app/$model/$action");
                        Db::name("authAccess")->insert(["role_id" => $roleId, "rule_name" => $name, 'type' => 'admin_url']);
                    }
                }

                $this->success("授权成功！");
            } else {
                //当没有数据时，清除当前角色授权
                Db::name("authAccess")->where(["role_id" => $roleId])->delete();
                $this->error("没有接收到数据，执行清除授权成功！");
            }
        }
    }

    /**
     * 检查指定菜单是否有权限
     * @param array $menu menu表中数组
     * @param $privData
     * @return bool
     */
    private function _isChecked($menu, $privData)
    {
        $app    = $menu['app'];
        $model  = $menu['controller'];
        $action = $menu['action'];
        $name   = strtolower("$app/$model/$action");
        if ($privData) {
            if (in_array($name, $privData)) {
                return true·;
            } else {
                return false;
            }
        } else {
            return false;
        }

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
                                        
    //角色成员管理
    public function member()
    {
        //TODO 添加角色成员管理

    }

    /**
     * 获取人数
     * @param  [type] $res [description]
     * @return [type]      [description]
     */
    public function getCount($res,$panel=null)
    {
            foreach ($res as $key => $value) {
            $c=Db::name('user_attach')->where('role_id',$value['id'])->count();
            $str=empty($c)?"":'<span class="badge">'.$c.'</span>';
            $value['text']='<span class="nodeText">'.$value['text']."</span>  ".$str." ".(empty($panel)?"":$panel).($value['level']==0?"":'<span class="apanel glyphicon glyphicon-edit editgw" title="编辑岗位"></span><span class="apanel glyphicon glyphicon-saved qxgl" title="权限管理"></span><span class="apanel glyphicon glyphicon-trash deletejs" data-msg="确定要删除此角色吗？" title="删除角色"></span>');
            if(isset($value['nodes'])){
                $value['nodes']=$this->getCount($value['nodes'],$panel);
            }
            $res[$key]=$value;
        }
        return $res;
    }

    /**
     * 角色首页
     * @return [type] [description]
     */
    public function index()
    { 
        //访问权限模型
        $accessModel=new AccessModel();
       //获取当前用户所在公司角色列表信息
       $roles = $accessModel->get_company_role_list();
        //获取公司最高角色信息
       $highest=$accessModel->company_highest_role_info;
       //获取公司信息
       $company=$accessModel->get_company_info(cmf_get_current_admin_id());
       $this->assign('company',$company);
        //将公司的角色表信息构建为结构是一二级层级目录数据
        $res=$accessModel->get_role_hierarchy($roles,$highest['parent_id'],2);
        //获取角色下的人数
        $panel='<span class="apanel glyphicon glyphicon-plus-sign addgw" title="添加岗位"></span><span class="apanel glyphicon glyphicon-user adduser" title="添加人员"></span>';
        $res=$this->getCount($res,$panel);
        $this->assign('list',json_encode($res));
        return $this->fetch();

/*         $AuthAccess     = Db::name("AuthAccess");
        $adminMenuModel = new AdminMenuModel();
        $result = $adminMenuModel->menuCache();

        dump($result);*/

/*       

        //获取所有的角色信息 数据处理中 
         $result = Db::name('role')->where('company_id',$company['id'])->column('');
       //$result = Db::name('role')->column('');
       //获取该公司的最高角色父级id
       // $highestRole_parent_id=get_company_highest_role_id($company['id']);
       // echo $highestRole_parent_id;
        $tree       = new Tree();
        $tree->icon = ['│ ', '├─ ', '└─ '];
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $m) {
            $newMenus[$m['id']] = $m;
        }
        foreach ($result as $n => $t) {
            $result[$n]['level']        = $this->_getLevel($t['id'], $newMenus);
            $result[$n]['style']        =empty($t['parent_id'])? "" : '';
            $result[$n]['parentIdNode'] = ($t['parent_id']) ? ' class="child-of-node-' . $t['parent_id'] .'"': '';
        }

        $str = "<tr id='node-\$id' \$parentIdNode  style='\$style'>
                   <td style='padding-left:30px;'>\$spacer<span class='role' roleid = '\$id'>\$name</span>
                   <span class='badge'></span>
                        <a title = '添加岗位' class='ajax-add' data-toggle='modal' data-target='#myModal' id = '\$id' department_id = '\$department_id'>
                            <span class='icon glyphicon glyphicon-plus-sign'></span></a>
                        <a title = '编辑岗位' class='ajax-edit' data-toggle='modal' data-target='#myModal' id = '\$id' url='ajaxEdit/id/\$id'><span class='icon glyphicon glyphicon-edit'></span></a>
                        <a title = '添加用户' class='ajax-user' href='ajaxAddUser/id/\$id' id = '\$id'><span class='icon glyphicon glyphicon-user'></span></a>
                        <a title = '权限管理' class='ajax-auth' href='ajaxAuthorize/id/\$id' id = '\$id'><span class='icon glyphicon glyphicon-check'></span></a>
                        <a title = '删除岗位' class='js-ajax-delete' href='ajaxDelete/id/\$id'><span class=' icon glyphicon glyphicon-trash'></span></a>
                   </td>
                </tr>";
        $str2 = "<option value = \$id>
                <tr id='node-\$id'\$parentIdNode  style='\$style'>
                   <td style='padding-left:30px;'>\$spacer\$name</td>
                </tr>
                </option>";
        //dump($result);
        $tree->init($result);
        //第一个参数:最高角色的parent_id
        $role = $tree->getTree(0, $str);
        $role2 = $tree->getTree(0, $str2);
/*        echo 'role';
        dump($role);
        echo 'role1';
        dump($role2);
        $this->assign('role' ,$role);
        $this->assign('role2',$role2);
        $this->department();
        return $this->fetch();*/
    }

    /**
     * 获取角色的信息
     * 2019221
     */
    public function getRoleInfo(){
        if(!$this->request->isPost()){
            return json(['status'=>'error','message'=>'访问类型错误']);
        }
        $data=$this->request->param();
        $id=$data['id'];
        /*$str="";
        foreach ($department as $value) {
            $selected="";
            if(isset($data['tag'])){
                if($role['department_id']==$value['id']){
                    $selected="selected";
                }
            }
            $str.='<option value="'.$value['id'].'"  '.$selected.'>'.$value['name'].'</option>';
        }
        $role['remark']=htmlspecialchars_decode($role['remark']);
        return ['role'=>$role,'department'=>$str];*/
        //访问权限模型
        $accessModel=new AccessModel();
        //获取当前用户所在公司角色列表信息
        $roles = $accessModel->get_company_role_list();
        /*//获取公司最高角色信息
        $highest=$accessModel->company_highest_role_info;
        //获取公司信息
        $company=$accessModel->get_company_info(cmf_get_current_admin_id());
        //将公司的角色表信息构建为结构是一二级层级目录数据
        //$res=$accessModel->get_role_hierarchy($roles,$highest['parent_id'],2);
        //get_hierarchy($data,$id,$depth=null,$level=0)
        $res=$accessModel->get_hierarchy($roles,0);
        //dump($res);
        $res=$this->createTree($res,'&nbsp;&nbsp;&nbsp;&nbsp;');
        return ['role'=>'','department'=>$res];
        dump($res);exit;*/
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
        $str2 = "<option value = '\$id' data-dep='\$department_id'>\$spacer\$name</option>";
        $tree->init($result);
        $role = $tree->getTree(0, $str2);

        //部门
        $department=Db::name('department')->column('');
        $tree->icon = ['│ ', '├─ ', '└─ '];
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($department as $m) {
            $newMenus[$m['id']] = $m;
        }

        foreach ($department as $n => $t) {
            $department[$n]['level']        = $this->_getLevel($t['id'], $newMenus);
            $department[$n]['style']        = empty($t['parent_id']) ? '' : 'display:none;';
            $department[$n]['parentIdNode'] = ($t['parent_id']) ? ' class="child-of-node-' . $t['parent_id'] . '"' : '';
        }
        $str2 = "<option value = \$id>
                <tr id='node-\$id'\$parentIdNode  style='\$style'>
                   <td style='padding-left:30px;'>\$spacer\$name</td>
                </tr>
                </option>";
        $tree->init($department);
        $department = $tree->getTree(0, $str2);

        if(isset($data['tag']) && $data['tag']=='edit'){
            //编辑岗位查询 
            $roleInfo=Db::name('role')->where('id',$id)->find();
            return ['role'=>$role,'department'=>$department,'roleInfo'=>autoHtmlspecialcharsDecode($roleInfo)];
        }else{
            return ['role'=>$role,'department'=>$department];
        }         
    }
    /**
     * 获取角色菜单
     * @return [type] [description]
     */
    public function ajaxGetRoleMenu()
    {
        if(!$this->request->isAjax()){
             return json(['status'=>'error','message'=>'访问类型错误']);
        }

        $id=$this->request->param();
        $currRole=Db::name('role')->where('id',$id['id'])->find();
        //获取当前角色菜单
        $accessModel=new AccessModel();
        $roleMenu=$accessModel->get_menu($currRole['id']);

        //1读取主菜单 auth_access中最高角色菜单
        $highest=$accessModel->get_company_highest_role_info();
        $mainMenu=Db::name('auth_access')->where('role_id',$highest['id'])->select();

        //获取所有菜单
        $adminMenuModel = new AdminMenuModel();
        $allMenu = $adminMenuModel->menuCache();

        //筛选数据
        $newMenu=[];
        foreach ($allMenu as $key => $value) {
            $app=$value['app'];
            $controller=$value['controller'];
            $action=$value['action'];
            $x="$app/$controller/$action";
           foreach ($mainMenu as $k=> $v) {
                if($x==$v['rule_name']){
                    $newMenu[]=$value;
                }
           }
        }

        //勾选已授权的数据
        $data=$accessModel->isChecked($newMenu,$roleMenu);
        //组成树形菜单
        $data=$accessModel->get_hierarchy($data,0);

        return $data;
    }
    //ajax方式添加角色
    /**
     * 
     */
    public function ajaxAdd()
    {
        if ($this->request->isAjax()) {
            $data = $this->request->param();
            $validate = $this->validate($data,'role');
            if ($validate !== true){
                $this->error($validate);
            }else{
                $department_id = intval($data['department_id']);
                $parent_id = $data['parent_id'];
                $parnet_department = Db::name('role')->where('id','=',$parent_id)->field('department_id')->find();
                $parnet_department = $parnet_department['department_id'];
                $flag = true;
                if($department_id != $parnet_department && $parent_id != 1){
                    $flag = false;
                    while ($department_id !=1) {
                        $department_id = Db::name('department')->where('id','=',$department_id)->field('parent_id')->find();
                        $department_id = $department_id['parent_id'];
                        if ($department_id == $parnet_department) {
                            $flag = true;
                            break;
                        }
                    }
                }
                if ($flag == false) {
                    $this->error('部门等级与角色等级不匹配');
                }
                //获取公司信息
                $data['company_id']=get_current_company_info()['id'];
                $res = Db::name('role')->insertGetId($data);
                if ($res) {
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }
        }
    }
    /**
     * 角色授权菜单添加
     * @return [type] [description]
     */
    public function ajax_role_menu_add()
    {
        if(!$this->request->isAjax()){
            return json(['status'=>'error','message'=>'访问类型错误']);
        }
        $data=$this->request->param();
        //角色id
        $roleId=$data['id'];
        //获取角色所在的公司
        $role=Db::name('role')->where('id',$roleId)->find();
        $role['company_id']=1;//
        $company=Db::name('company')->where('id',$role['company_id'])->find();
        //获取缓存菜单
        $adminMenuModel = new AdminMenuModel();
        $result = $adminMenuModel->menuCache();

        if(!isset($data['list'])){
            $data['list']=[];
        }
        //提取选中的菜单
        $newMenu=[];
        foreach ($result as $key => $value) {
            foreach ($data['list'] as $index => $num) {
                if($num==$value['id']){
                    $app=$value['app'];
                    $controller=$value['controller'];
                    $action=$value['action']; 
                     //rule_name role_id type
                    $insert['rule_name']="$app/$controller/$action";
                    $insert['role_id']=$roleId;
                    $insert['type']='admin_url';
                    $insert['company']=$company['id'];
                    $insert['menu_id']=$value['id'];
                    $insert['access_versions_id']=$company['versions_type'];
                    $newMenu[]=$insert;
                }
            }
        }
        //更新授权菜单信息
        $oldMenu=Db::name('auth_access')->where('role_id',$roleId)->select();

        $oldMenu=json_decode($oldMenu,true);
        $res=$this->updateAccessMenu($oldMenu,$newMenu);

        if(!empty($res['delete'])){
            //删除菜单
            $res=Db::name('auth_access')->where('role_id',$roleId)->where('menu_id','IN',$res['delete']['menu_id'])->delete();
        }
        if(!empty($res['insert'])){
            //插入新菜单
            $res=Db::name('auth_access')->insertAll($res['insert']);
        }
        return  json(['status'=>'success','message'=>'授权信息已保存！']);
    }

    /**
     * 更新授权菜单
     * 
     */
    public function  updateAccessMenu($oldMenu,$newMenu)
    {
        $delete=[];//多余数据
        $insert=[];//新数据
        //数据比对
        //比对逻辑  遍历新菜单  二重循环遍历旧菜单   
        //判断方法  新菜单menu_id  与  旧菜单menu_id一一比对，如果不存在就是要新增的，反向遍历删除多余的
        foreach ($newMenu as $key => $value) {
            $isInsert=true;
            foreach ($oldMenu as $ok => $ov) {
                if($value['menu_id']==$ov['menu_id']){
                    $isInsert=false;
                }
            }
            if($isInsert){//保存要新增的数据
                $insert[]=$value;
            }
        }
        //找到要删除的数据
        foreach ($oldMenu as $key => $value) {
            $isDelete=true;
            foreach ($newMenu as $nk => $nv) {
                if($value['menu_id']==$nv['menu_id']){
                    $isDelete=false;
                }
            }
            //保存要删除的数据
            if($isDelete){
                $delete['id'][]=$value['id'];
                $delete['menu_id'][]=$value['menu_id'];
            }
        }
        return ['delete'=>$delete,'insert'=>$insert];
    }


    /**
     * 获取子级角色
     * @return [type] [description]
     */
    public function ajax_get_child()
    {
        if(!$this->request->isAjax()){
            return json(['status'=>'error','message'=>'访问类型错误']);
        }
        $data=$this->request->param();
        //获取所属子角色
        $accessModel=new AccessModel();
       //获取当前用户所在公司角色列表信息
       $roles = $accessModel->get_company_role_list();
        $res=$accessModel->get_role_hierarchy($roles,$data['parent_id'],null,$data['level']+1);
        $panel='<span class="apanel glyphicon glyphicon-plus-sign addgw" title="添加岗位"></span><span class="apanel glyphicon glyphicon-user adduser" title="添加人员"></span>';
        $res=$this->getCount($res,$panel);
        return json($res);
    }

    //ajax方式修改
    public function ajaxEdit()
    {
        if ($this->request->isAjax()) {
            $id = $this->request->param('id',0 ,'intval');
			
			$role = new RoleModel();
			
            $res = $role::where(['id' => $id])->find();
			
            echo json_encode($res,JSON_UNESCAPED_UNICODE);
			
			
        }
    }
    //ajax更新提交
    public function ajaxEditPost()
    {
        if ($this->request->isAjax()) {
            $data = $this->request->param();
            $validate = $this->validate($data,'role');
            $department_id = intval($data['department_id']);
            $department_update_id = $department_id;
            $role_id = $data['id']; 
            $parent_id = $data['parent_id'];
			
            $parnet_department = Db::name('role')->where('id','=',$parent_id)->field('department_id')->find();
            $parnet_department = $parnet_department['department_id'];
            $flag = true;
            if($department_id != $parnet_department && $parent_id != 1 ){
                $flag = false;
                while ($department_id !=1) {
                    $department_id = Db::name('department')->where('id','=',$department_id)->field('parent_id')->find();
                    $department_id = $department_id['parent_id'];
                    if ($department_id == $parnet_department) {
                        $flag = true;
                        break;
                    }
                }
            }
            if ($flag == false) {
                $this->error('部门等级与角色等级不匹配');
            }
            if ($validate !== true) {
                $this->error($validate);
            }else{
                $res = Db::name('role')->update($data);
                $userids = Db::name('RoleUser')->where(['role_id'=>$role_id])->column('user_id');
                $res2 = Db::name('DepartmentUser')->where('user_id','in',$userids)->update(['department_id'=>$department_update_id]);
                if ($res || $res2) {
                    $this->success('修改成功');
                }else{
                    $this->error('修改失败或未改变值');
                }
            }
        }
    }
    //删除方法
    public function ajaxDelete()
    {
        if ($this->request->isAjax()) {
            $id = $this->request->param("id", 0, 'intval');
            if ($id == 1) {
                $this->error("超级管理员角色不能被删除！");
            }
            $count = Db::name('role_user')->where(['role_id' => $id])->count();
            $count2 = Db::name('role')->where(['parent_id' => $id])->count();
            if ($count2 > 0) {
                $this->error('该角色有下级');
            }
            if ($count > 0) {
                $this->error('该角色已经存在员工');
            }else{
                $res = Db::name('role')->delete($id);
                
                if (!empty($res)) {
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
            }
        }
    }

    //添加用户
    public function ajaxAddUser()
    {
        if ($this->request->isAjax()) {
            $res = Db::name('role')->where('id','=',$_POST['role_id'])->find();
            if (intval($res['status']) == 0) {
               // $this->error('该角色尚未激活');
                return json(['status'=>'error','message'=>'该角色尚未激活！']);
            }
            if (!empty($_POST['role_id'])) {
                $role_id = $_POST['role_id'];
                unset($_POST['role_id']);
                $result = $this->validate($this->request->param(), 'User');
                unset($_POST['d_pass']);
                if ($result !== true) {
                    //$this->error($result);
                    return json(['status'=>'error','message'=>$result]);
                } else {
                    $_POST['user_pass'] = cmf_password($_POST['user_pass']);
                    $_POST['user_type']=1;
                    $_POST['user_status']=1;
					$_POST['create_time']=time();
                    $result             = DB::name('user')->insertGetId($_POST);
                    $department_id=$res['department_id'];
                   // $department_id = Db::name('role')->where(['id' => $role_id])->field('department_id')->find();
                    if ($result !== false) {
                            if (cmf_get_current_admin_id() != 1 && $role_id == 1) {
                              //  $this->error("为了网站的安全，非网站创建者不可创建超级管理员！");
                                return json(['status'=>'error','message'=>'为了网站的安全，非网站创建者不可创建超级管理员！']);
                            }
                            /*Db::name('RoleUser')->insert(["role_id" => $role_id, "user_id" => $result]);
                            Db::name('DepartmentUser')->insert(["department_id" => $department_id, "user_id" => $result]);
                            Db::name('CompanyUser')->insert(["company_id" => $res['company_id'] , "user_id" => $result]);	*/	
                            Db::name('user_attach')->insert(['role_id'=>$role_id,'department_id'=>$department_id,'company_id'=>1,'user_id'=>$result]);				
                        //$this->success("添加成功！");
                        return json(['status'=>'success','message'=>'添加成功！']);
                    } else {
                      //  $this->error("添加失败！");
                        return json(['status'=>'error','message'=>'添加失败！']);
                    }
                }
            } else {
                //$this->error("请为此用户指定角色！");
                return json(['status'=>'error','message'=>'请为此用户指定角色！']);
            }
        }
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

    //权限列表
    public function ajaxAuthorize()
    {
        $AuthAccess     = Db::name("AuthAccess");
        $adminMenuModel = new AdminMenuModel();
        //角色ID
        $roleId = $this->request->param("id", 0, 'intval');
        if (empty($roleId)) {
            $this->error("参数错误！");
        }

        $tree       = new Tree();
        $tree->icon = ['│ ', '├─ ', '└─ '];
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';

        $result = $adminMenuModel->menuCache();
        $newMenus      = [];
        $privilegeData = $AuthAccess->where(["role_id" => $roleId])->column("rule_name");//获取权限表数据

        foreach ($result as $m) {
            $newMenus[$m['id']] = $m;
        }

        foreach ($result as $n => $t) {
            $result[$n]['checked']      = ($this->_isChecked($t, $privilegeData)) ? ' checked' : '';
            $result[$n]['level']        = $this->_getLevel($t['id'], $newMenus);
            $result[$n]['style']        = empty($t['parent_id']) ? '' : 'display:none;';
            $result[$n]['parentIdNode'] = ($t['parent_id']) ? ' class="child-of-node-' . $t['parent_id'] . '"' : '';
        }

        $str = "<tr id='node-\$id'\$parentIdNode  style='\$style'>
                   <td style='padding-left:30px;'>\$spacer<input type='checkbox' name='menuId[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'> \$name</td>
                </tr>";
        $tree->init($result);

        $category = $tree->getTree(0, $str);
        echo $category;
        //$this->assign("category", $category);
        //$this->assign("roleId", $roleId);
    }

    // ajax获取用户
    public function ajaxGetUser()
    {
        if (!$this->request->isAjax()) {
            $this->error('非法请求');
        }
        $roleid = $this->request->param('roleid',0,'intval');
        $userids = Db::name('role_user')->where(['role_id' => $roleid])->field('user_id')->select();
        if(empty($userids['0'])){
            $this->error('该角色尚未有用户');
        }
        for ($i=0; $i < count($userids); $i++) { 
            $userid = $userids[$i]['user_id'];
            $data[$i] = Db::name('user')->where(['id' => $userid])->field('id,user_login,user_email')->select();
        }
        $data = json_encode($data,JSON_UNESCAPED_UNICODE);
        echo $data;
        //dump();
    }

    /*//通过ajax方式获取人员数量
    public function getUser()
    {
        if ($this->request->isAjax()) {
            $id = $this->request->param('id',0,'intval');
            $count = Db::name('RoleUser')->where(['role_id'=>$id])->count();
            echo $count;
        }
    }*/

    //ajax获取用户信息
    public function getUserInfo($id)
    {
        if($this->request->isAjax()){
            $userIds=Db::name('user_attach')->where('role_id',$id)->column('user_id');
            $list=Db::name('user')->where('id','IN',$userIds)->select();
            $str='';
            foreach ($list as $key => $value) {
                $tr='<tr>';
                $tr.='<td>'.($key+1).'</td>'; 
                $tr.='<td>'.$value['employee_id'].'</td>'; 
                $tr.='<td>'.$value['user_login'].'</td>';      
                $tr.='<td>'.$value['mobile'].'</td>';    
                $tr.='<td>'.$value['user_email'].'</td>';                        
                $tr.='<td>'.date('Y-m-d H:i:s',$value['last_login_time']).'</td>'; 
                $tr.='<td>'.(($value['user_status']==0)?'禁用':'正常').'</td>'; 
                $tr.='</tr>';
                $str.=$tr;
            }
            return zy_json(true,'',$str,200);
        }
    }
}


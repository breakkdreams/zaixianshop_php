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
use think\Validate;
use plugins\contract\contract_template\Config;
use app\admin\model\AuthorizationModel;

class PublicController extends AdminBaseController
{
    public function _initialize()
    {
    }
    public function index()
    {
        return $this->login();
    }

    /**
     * 后台登陆界面
     */
    public function login()
    {
        $loginAllowed = session("__LOGIN_BY_CMF_ADMIN_PW__");
        if (empty($loginAllowed)) {
            $this->error('非法登录!', cmf_get_root() . '/');
        }
        $admin_id = session('ADMIN_ID');
        if (!empty($admin_id)) {//已经登录
            redirect(url("admin/Index/index"));
        } else {
            $site_admin_url_password = config("cmf_SITE_ADMIN_URL_PASSWORD");
            $upw                     = session("__CMF_UPW__");
            if (!empty($site_admin_url_password) && $upw != $site_admin_url_password) {
                redirect(ROOT_PATH . "/");
            } else {
                session("__SP_ADMIN_LOGIN_PAGE_SHOWED_SUCCESS__", true);
                $result = hook_one('admin_login');
                if (!empty($result)) {
                    return $result;
                }
                return $this->fetch(":login");
            }
        }
    }

    /**
     * 登录验证
     */
    public function doLogin()
    {
        $loginAllowed = session("__LOGIN_BY_CMF_ADMIN_PW__");
        if (empty($loginAllowed)) {
            $this->error('非法登录!', cmf_get_root() . '/');
        }       
        $captcha = $this->request->param('captcha');
        if (empty($captcha)) {
            $this->error(lang('CAPTCHA_REQUIRED'));
        }
        //验证码
        if (!cmf_captcha_check($captcha)) {
            $this->error(lang('CAPTCHA_NOT_RIGHT'));
        }

        $name = $this->request->param("username");
        if (empty($name)) {
            $this->error(lang('USERNAME_OR_EMAIL_EMPTY'));
        }
        $pass = $this->request->param("password");
        if (empty($pass)) {
            $this->error(lang('PASSWORD_REQUIRED'));
        }
        if (strpos($name, "@") > 0) {//邮箱登陆
            $where['user_email'] = $name;
        } else {
            $where['user_login'] = $name;
        }

        $result = Db::name('user')->where($where)->find();
        if (!empty($result) && $result['user_type'] == 1) {
            if (cmf_compare_password($pass, $result['user_pass'])) {
                $groups = Db::name('user_attach')
                    ->alias("a")
                    ->join('role b', 'a.role_id =b.id','LEFT')
                    ->where(["a.user_id" => $result["id"], "b.status" => 1])
                    ->value("a.role_id");
                if ((empty($groups) || empty($result['user_status']))) {
                   // $this->error(lang('USE_DISABLED'));
                   $this->error('账户已被停用！');
                }
                if($result['user_status']==3){
                    $this->error('申请账号未被通过！');
                }
                if($result['user_status']==2){
                    $this->error('申请账号正在审核中，请等待！');
                }
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
                $this->success(lang('LOGIN_SUCCESS'), url("admin/Index/index"));
            } else {
                $this->error(lang('PASSWORD_NOT_RIGHT'));
            }
        } else {
            $company=Db::name('company')->column('company_id');
            if(empty($company)){
                //$data['username']=$this->request->param('username');
                $data['username']=\think\Config::get('super_login');
                $data['password']=$this->request->param('password');
                $author=new AuthorizationModel();
                $res=$author->firstLogin($data);

                if(!isset($res['status']) || empty($res) ){
                    $this->error('请求错误！');
                }
                if($res['status']=='error'){
                    $this->error($res['message']);
                }
                if(!isset($res['data'])){
                    $this->error('未获取到相关数据，激活账号失败，请联系管理员！');
                }
                //保存数据
                $res=$this->saveData($res['data']);
                
                if($res){
                    //激活成功更新激活状态
                    $active=$author->updateActiveStatus($data);
                    if(empty($active) || !isset($active['status']) || $active['status']=='error'){
                        //数据保存失败，清空表数据
                        $this->clearTable();
                        $this->error('账号激活失败，请稍后再试！');
                    }
                    $this->success('账号激活成功，请重新登录！',url('public/login'));
                }else{
                    //数据保存失败，清空表数据
                    $this->clearTable();
                    $this->error('获取账户信息失败，请联系管理员！');
                }
                //$this->success('登录成功！',url("admin/Index/index"));
            }else{
                $this->error('账号不正确，请检查！');
            }
         //   $this->error(lang('USERNAME_NOT_EXIST'));
        }
    }
    /**
     * [saveData description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected  function saveData($data)
    {
        $isOk=true;
        Db::startTrans();
         try{
            $data['user']['employee_id']='10001001';
            $data['user']['user_login']='admin';
            $userId=Db::name('user')->insertGetId($data['user']);
            $data['company']['super_admin']=$userId;
            $data['company']['super_login']=\think\Config::get('super_login');
			$data['company']['id'] = 1;
            $companyId=Db::name('company')->insertGetId($data['company']);
            //添加角色 部门
            $department['status']=1;
            $department['parent_id']=0;
            $department['department_NO']='10';
            $department['name']='办公室';
            $department['remark']='办公室';
            $department['company_id']=$companyId;
            $departmentId=Db::name('department')->insertGetId($department);

            $role['parent_id']=0;
            $role['department_id']=$departmentId;
            $role['status']=1;
            $role['create_time']=time();
            $role['name']='CEO';
            $role['remark']='CEO';
            $role['company_id']=$companyId;
            $roleId=Db::name('role')->insertGetId($role);

            $attach['user_id']=$userId;
            $attach['role_id']=$roleId;
            $attach['department_id']=$departmentId;
            $attach['company_id']=$companyId;
            Db::name('user_attach')->insert($attach);
            // 提交事务
            Db::commit();    
        } catch (\Exception $e) {
            $isOk=false;
            // 回滚事务
            Db::rollback();
        }
        return $isOk;
    }

    /**
     * 后台管理员退出
     */
    public function logout()
    {
        session('ADMIN_ID', null);
        $adminMenu=new \app\admin\model\AdminMenuModel();
        $adminMenu->clearCacheMenu();
        return redirect(url('/', [], false, true));
    }

    /**
     * 清空表数据 
     */
    public function clearTable()
    {
        $table=['cmf_user','cmf_company','cmf_role','cmf_user_attach','cmf_department','cmf_auth_access'];
        foreach ($table as $key => $value) {
           $res= Db::execute('truncate table '.$table[$key]);
        }
    }



}
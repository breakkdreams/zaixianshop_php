<?php

/**
 * @Author: user
 * @Date:   2018-11-05 09:28:45
 * @Last Modified by:   user
 * @Last Modified time: 2018-11-05 11:13:42
 */
namespace app\api\controller;

use think\Model;
use think\Controller;
use think\Db;
use think\Validate;
use app\user\model\UserModel;

/**
 * 用户注册接口
 */
class ApiRegisterController extends Controller
{
	
	/**
	 * 新增用户
	 */
	public function add_user(){

            $rules = [
            	'mobile'	=>'require',
                'code'     => 'require',
                'password' => 'require|min:6|max:32',

            ];

            $isOpenRegistration=cmf_is_open_registration();

            if ($isOpenRegistration) {
                unset($rules['code']);
            }

            $validate = new Validate($rules);
            $validate->message([
                'code.require'     => '验证码不能为空',
                'password.require' => '密码不能为空',
                'password.max'     => '密码不能超过32个字符',
                'password.min'     => '密码不能小于6个字符',
                'mobile.require'  => '手机号码不能为空',
            ]);

            $data = $this->request->param();
            if (!$validate->check($data)) {
               return json(['code'=>1,'msg'=>$validate->getError()]);
            }
            
			$start = time()-3000;
			$end = time();
			
			
			$code = Db::name('sms_report')->order('id','DESC')->where('posttime','>=',$start)->where('posttime','<=',$end)->where(['mobile'=>$data['mobile']])->find();
			
	
			
			
			if ($data['code']!=$code['id_code']) {
                return json(['code'=>1,'msg'=>'手机验证码错误！']);
            }          
						

            $register          = new UserModel();
            $user['user_pass'] = $data['password'];
 			if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['mobile'])) {
                $user['mobile'] = $data['mobile'];
                $log            = $register->registerMobile($user);
            } else {
                $log = 2;
            }
            $sessionLoginHttpReferer = session('login_http_referer');
            //$redirect                = empty($sessionLoginHttpReferer) ? cmf_get_root() . '/' : $sessionLoginHttpReferer;
            switch ($log) {
                case 0:
                    return json(['code'=>2,'msg'=>'注册成功！']);
                    break;
                case 1:
                    return json(['code'=>1,'msg'=>'您已经注册过了！']);
                    break;
                case 2:
                    return json(['code'=>1,'msg'=>'您输入的账号格式错误！']);
                    break;
                default :
                    return json(['code'=>1,'msg'=>'未受理的请求！']);
            }

	}


	public function modify_pwd(){
            $validate = new Validate([
                'old_password' => 'require|min:6|max:32',
                'password'     => 'require|min:6|max:32',
                'repassword'   => 'require|min:6|max:32',
            ]);
            $validate->message([
                'old_password.require' => '旧密码不能为空',
                'old_password.max'     => '旧密码不能超过32个字符',
                'old_password.min'     => '旧密码不能小于6个字符',
                'password.require'     => '新密码不能为空',
                'password.max'         => '新密码不能超过32个字符',
                'password.min'         => '新密码不能小于6个字符',
                'repassword.require'   => '重复密码不能为空',
                'repassword.max'       => '重复密码不能超过32个字符',
                'repassword.min'       => '重复密码不能小于6个字符',
            ]);

            $data = $this->request->param();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            if($data['password']!=$data['repassword']){
            	return 	json(['code'=>1,'msg'=>'两次输入的密码不一样！']);
            }

            $newPwd=cmf_password($data['repassword']);

            $user=Db::name('user')->where('id','=',$data['userid'])->find();

            if(cmf_password($data['old_password'])!=$user['user_pass']){

            	return json(['code'=>1,'msg'=>'原始密码输入不正确！']);
            }

            Db::name('user')->where('id',$data['userid'])->update(['user_pass'=>$newPwd]);
            return json(['code'=>1,'msg'=>'密码修改成功！']);
	}



}

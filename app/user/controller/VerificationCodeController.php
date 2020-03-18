<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\controller\HomeBaseController;
use think\Validate;

class VerificationCodeController extends HomeBaseController
{
    public function send()
    {
        $validate = new Validate([
            'username' => 'require',
        ]);

        $validate->message([
            'username.require' => '请输入手机号或邮箱!',
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }

        $accountType = '';

        if (Validate::is($data['username'], 'email')) {
            $accountType = 'email';
        } else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d|19\d)\d{8})$/', $data['username'])) {
            $accountType = 'mobile';
        } else {
            $this->error("请输入正确的手机或者邮箱格式!");
        }

        //TODO 限制 每个ip 的发送次数

        $code = cmf_get_code($data['username']);
        if ($code==1) {
            $this->error("验证码发送过多,请明天再试:");
        }

		
		
		
        if ($accountType == 'email') {

            $emailTemplate = cmf_get_option('email_template_verification_code');

            $user     = cmf_get_current_user();
            $username = empty($user['user_nickname']) ? $user['user_login'] : $user['user_nickname'];

            $message = htmlspecialchars_decode($emailTemplate['template']);
            $message = $this->display($message, ['code' => $code, 'username' => $username]);
            $subject = empty($emailTemplate['subject']) ? '验证码' : $emailTemplate['subject'];
            $result  = cmf_send_email($data['username'], $subject, $message);

            if (empty($result['error'])) {
                cmf_verification_code_log($data['username'], $code);
                $this->success("验证码已经发送成功!");
            } else {
                $this->error("邮箱验证码发送失败:" . $result['message']);
            }

        } else if ($accountType == 'mobile') {
             
			$result = sms_yzm_send($data['username']);
            
            if ($result !== false && !empty($result['error'])) {
                $this->error($result['message']);
            }

            
            if (!empty($result['message'])) {
                $this->success($result['message']);
            } else {
                $this->success('验证码发送成功');
            }

        }


    }




    /**
     * 验证码api
     */

    public function sendCode()
    {
        $validate = new Validate([
            'mobile' => 'require',
        ]);

        $validate->message([
            'mobile.require' => '请输入手机号!',
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
           return json(['code'=>2,'msg'=>$validate->getError()]);
        }

        if (!preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d|19\d)\d{8})$/', $data['mobile'])) {
            return json(['code'=>2,'msg'=>'请输入正确的手机格式！']);;
        }

        //TODO 限制 每个ip 的发送次数

        $code = cmf_get_code($data['mobile']);
        if ($code==1) {
            return json(['code'=>2,'msg'=>"验证码发送过多,请明天再试:"]);
        }

             
        $result = sms_yzm_send($data['mobile']);
            
        if ($result !== false && !empty($result['error'])) {
             return json(['code'=>2,'msg'=>$result['message']]);
        }

            
        if (!empty($result['message'])) {
             return json(['code'=>1,'msg'=>$result['message']]);
        } else {
             return json(['code'=>1,'msg'=>'验证码为：'.$result['code']]);
        }


    }

}

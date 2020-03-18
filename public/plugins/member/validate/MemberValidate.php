<?php
namespace plugins\member\validate;

use think\Validate;

class MemberValidate extends Validate
{
    protected $rule = [
        // 用|分开
        'account'       => 'require',

        'mobile'        =>  'require|number|max:11|regex:/^1[3-8]{1}[0-9]{9}$/',
        'mobile_code'   =>  'require|number',
        'nickname'      =>  'require|min:6|max:32',
        'email'         =>  'require|email',
        
        'password'      => 'require|min:6|max:32',
        're_password'   =>  'require|confirm:password',
    ];

    protected $message = [
        'account.require'       => '账号不能为空',

        'password.require'      => '密码不能为空',
        'password.max'          => '密码不能超过32个字符',
        'password.min'          => '密码不能小于6个字符',
        
        're_password.require'   => '重复密码不能为空',
        're_password.confirm'   => '输入密码不一致',

        'mobile.require'        => '手机号不能为空',
        'mobile.number'         => '手机号必须是数字',
        'mobile.max'            => '手机号码格式不正确',
        'mobile.regex'          => '手机号码格式不正确',

        'mobile_code.require'   => '验证码不能为空',
        'mobile_code.number'    => '验证码必须是数字',

        'nickname.require'      => '昵称不能为空',
        'nickname.max'          => '昵称不能超过32个字符',
        'nickname.min'          => '昵称不能小于6个字符',

        'email.require'           => '邮箱不能为空',
        'email.email'           => '邮箱格式错误',
    ];


    protected $scene = [
        'accountLogin'      =>  ['account','password'],
        'mobileLogin'      =>  ['mobile','mobile_code'],
        'mobileRegister'    =>  ['mobile','mobile_code','nickname','password','re_password'],
        'emailRegister'    =>  ['email','email_code','nickname','password','re_password'],
    ];

    /*protected function checkName($value,$rule,$data)
    {

    }*/

}
<?php
namespace plugins\message\validate;

use think\Validate;

class NoteValidate extends Validate
{
    protected $regex = [
        'mobile'=>'/1[345789]{1}\d{9}$/i',
    ];
    protected $rule = [
        // 用|分开
        'mobile'       => 'require|mobile',
        'content' => 'require|max:500'
    ];

    protected $message = [
        'mobile.require'       => "mobile不能为空！",
        'mobile.mobile'       => "mobile格式不正确！",
        'content.require' => 'content内容不能为空',
        'content.max' => 'content长度不能超过500'
    ];

}
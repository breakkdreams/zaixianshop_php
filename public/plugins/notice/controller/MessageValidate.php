<?php
namespace plugins\notice\validate;

use think\Validate;

class MessageValidate extends Validate
{
    protected $rule = [
        // 用|分开
        'uid'       => 'require',
        'title'     => 'require',
        'content' => 'require|max:500'
    ];

    protected $message = [
        'uid.require'       => "uid不能为空！",
        'title.require'     => "title不能为空!",
        'content.require' => 'content内容不能为空',
        'content.max' => 'content长度不能超过500'
    ];

}
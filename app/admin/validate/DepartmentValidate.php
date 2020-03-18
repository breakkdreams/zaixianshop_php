<?php 
namespace app\admin\validate;

use think\Validate;
/**
* 部门字符串验证
*/
class DepartmentValidate extends Validate
{
	protected $rule = [
        'name' => 'require|max:15',
        'parent_id' => 'require',
        'status' => 'require',
        'department_NO'=>'require'

    ];

    protected $message = [
        'name.require' => '部门名称不能为空',
        'name.max' => '名称长度不得超过15字',
        'status' => '部门状态不得为空',
        'department_NO'=>'部门编号不能为空'
    ];

    protected $scene = [
        'add' => ['name','department_NO'],
    ];
}
<?php
namespace app\admin\model;

use think\Model;
/**
* 用户模块
*/
class UserModel extends Model
{

	
    /**
     * post_content 自动转化
     * @param $value
     * @return string
     */
    public function getBakAttr($value)
    {
        return cmf_replace_content_file_url(htmlspecialchars_decode($value));
    }

    /**
     * post_content 自动转化
     * @param $value
     * @return string
     */
    public function setBakAttr($value)
    {
        return htmlspecialchars(cmf_replace_content_file_url(htmlspecialchars_decode($value), true));
    }
	
	
}
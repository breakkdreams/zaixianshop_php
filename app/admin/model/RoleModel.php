<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/7/14
 * Time: 15:44
 */

namespace app\admin\model;

use think\Model;

class RoleModel extends Model
{
    protected $type = [
        'more' => 'array',
    ];
    public function children($ids){
        $model=new RoleModel();
        $re='';
        foreach ($ids as $id) {
            $da = '';
            $da = $model->where('parent_id', $id['id'])->select();
        if ($da!=''){
            $re.=','.$this->children($da);
        }
        $re.=','.$id['id'];
        }
        $re=explode(',',$re);
        $re=array_filter($re);
        $re=implode(',',$re);
        return $re;
    }
	
	  public function getRemarkAttr($value)
    {
        return cmf_replace_content_file_url(htmlspecialchars_decode($value));
    }

    /**
     * post_content 自动转化
     * @param $value
     * @return string
     */
    public function setRemarkAttr($value)
    {
        return htmlspecialchars(cmf_replace_content_file_url(htmlspecialchars_decode($value), true));
    }
	
	
	
}
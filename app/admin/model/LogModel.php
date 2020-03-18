<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/6/27
 * Time: 10:42
 */

namespace app\admin\model;

use think\Model;
class LogModel extends Model
{
    protected $type = [
        'more' => 'array',
    ];
    /**
     * post_content 自动转化
     * @param $value
     * @return string
     */
    public function getConAttr($value)
    {
        return cmf_replace_content_file_url(htmlspecialchars_decode($value));
    }

    /**
     * post_content 自动转化
     * @param $value
     * @return string
     */
    public function setConAttr($value)
    {
        return htmlspecialchars(cmf_replace_content_file_url(htmlspecialchars_decode($value), true));
    }
}
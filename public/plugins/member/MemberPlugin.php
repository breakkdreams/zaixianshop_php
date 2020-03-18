<?php
//模块的主类
namespace plugins\member;//Demo插件英文名，改成你的插件英文就行了
use cmf\lib\Plugin;//必须引入此库
/**Demo插件英文名，改成你的插件英文就行了 继承此库
 * @pluginInfo('name'=>'会员管理','symbol'=>'Member')
 */
class MemberPlugin extends Plugin
{
    /**
     * 插件基本信息
     * 
     */
    public $info = [
        'name'        => 'Member',//Demo插件英文名，改成你的插件英文就行了
        'title'       => '会员管理',
        'description' => '会员管理基础版：登录、注册、个人中心、安全中心、修改资料',
        'status'      => 1,
        'author'      => '叶洋洋',
        'version'     => '1.0',
    ];

    public $hasAdmin = 1;//插件是否有后台管理界面

    // 插件安装
    public function install()
    {
        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        return true;//卸载成功返回true，失败false
    }

}
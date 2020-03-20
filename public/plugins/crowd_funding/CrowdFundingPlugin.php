<?php
//模块的主类
namespace plugins\crowd_funding;//Demo插件英文名，改成你的插件英文就行了
use cmf\lib\Plugin;//必须引入此库
/**Demo插件英文名，改成你的插件英文就行了 继承此库
 * @pluginInfo('name'=>'众彩模块','symbol'=>'CrowdFunding')
 */
class CrowdFundingPlugin extends Plugin
{
    /**
     * 插件基本信息
     * 
     */
    public $info = [
        'name'        => 'CrowdFunding',//Demo插件英文名，改成你的插件英文就行了
        'title'       => '众彩模块',
        'description' => '众彩模块',
        'status'      => 1,
        'author'      => 'wbd',
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
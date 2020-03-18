<?php
//模块的主类
namespace plugins\order;//Demo插件英文名，改成你的插件英文就行了
use cmf\lib\Plugin;//必须引入此库

/**
 * @pluginInfo('name'=>'订单模块','symbol'=>'Order')
 */

//Demo插件英文名，改成你的插件英文就行了 继承此库
class OrderPlugin extends Plugin
{
    /**
     * 插件基本信息
     * @var [type]
     */
    public $info = [
        'name'        => 'Order',//Demo插件英文名，改成你的插件英文就行了  带驼峰的名字这样写  DemoOne
        'title'       => '订单模块',
        'description' => '订单模块',
        'status'      => 1,
        'author'      => '罗先豪',
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
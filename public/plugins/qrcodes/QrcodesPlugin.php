<?php

namespace plugins\qrcodes;
use cmf\lib\Plugin;

/**
 * @pluginInfo('name'=>'二维码模块','symbol'=>'Qrcodes')
 */
class QrcodesPlugin extends Plugin
{
    /**
     * 插件基本信息
     *
     */
    public $info = [
        'name'        => 'Qrcodes',//Demo插件英文名，改成你的插件英文就行了  带驼峰的名字这样写  DemoOne
        'title'       => '二维码模块',
        'description' => '二维码模块',
        'status'      => 1,
        'author'      => '谭智文',
        'version'     => '1.0'
    ];

    public $hasAdmin = 1; //插件是否有后台管理界面

    // 插件安装
    public function install()
    {
        return true; //安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        return true; //卸载成功返回true，失败false
    }

}

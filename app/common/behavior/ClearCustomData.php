<?php

/**
 * @Author: user
 * @Date:   2018-11-07 09:41:58
 * @Last Modified by:   user
 * @Last Modified time: 2018-11-07 10:55:04
 */
namespace app\common\behavior;

use think\Exception;
use think\Response;

/**
 * 钩子的位置再 admin\SettingController\clearCache
 * 用于清除一些自定义的缓存数据 session  cookie cache等等 一切
 */
class ClearCustomData
{
	public function run( &$param ){
        session( 'access_token' , null);//销毁当前用户请求的token缓存数据 
        session( 'jsapi_ticket' , null );//清除票据缓存
        session( 'expires_in' , null );//expires_in
    }
}
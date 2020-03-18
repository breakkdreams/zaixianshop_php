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
 * 跨域请求  行为  绑定在app/tags.php  中
 */
class CronRun
{
	
	public function run(&$dispatch){

        header("Access-Control-Allow-Origin:*");

        $host_name = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "*";

        $headers = [

            "Access-Control-Allow-Origin" => $host_name,

            "Access-Control-Allow-Credentials" => 'true',

            "Access-Control-Allow-Headers" => "x-token,x-uid,x-token-check,x-requested-with,content-type,Host"

        ];


        if($dispatch instanceof Response) {

            $dispatch->header($headers);

        } else if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

            $dispatch['type'] = 'response';

            $response = new Response('', 200, $headers);

            $dispatch['response'] = $response;
        }
    }
}
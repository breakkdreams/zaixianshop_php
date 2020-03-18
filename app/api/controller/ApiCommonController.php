<?php

/**
 * @Author: user
 * @Date:   2019-03-05 10:48:44
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-05 11:04:19
 */
/**
 * 公司接口加密例子api
 */
namespace app\api\controller;
use think\Controller;
use think\Db;
use app\api\model\ApiCommonModel;

/**
 * 公司接口加密例子api控制器类
 */
class ApiCommonController extends Controller
{
    //模型
    protected $commonModel=null;



    /**
     * 用户id加密
     * @param status en加密；de解密
     * @param uid 用户uid
     * @return [type] [description]
     */
    public function uidJwt(){
        $data=$this->request->get();
        $status = $data['status'];
        $uid = $data['uid'];

        if(!$status || !$uid){
            return zy_json_echo(false,'参数错误','',-1);
        }
        if($status=='en'){
            $result = zy_userid_jwt($uid);
        }elseif($status=='de'){
            $result = zy_userid_jwt($uid,'de');
        }

        if($result['status']=='error'){
            return zy_json_echo(false,$result['message'],'',-2);
        }

        return zy_json_echo(true,'成功！',$result['data'],200);
    }


    /**
     * 1、获取token access_token
     * @param appid 第三方应用唯一凭证
     * @param appsecret 第三方应用唯一凭证密钥，即appsecret
     * @return [type] [description]
     */
    public function accessToken(){
        $data=$this->request->get();

        $config = Config('sign_config');
        $key = $config['key'];
        $appid = $config['appid'];
        $appsecret = $config['appsecret'];
        $Jwt = new \sign\Jwt($key);
        if($appid != $data['appid'] || $appsecret != $data['appsecret']){
            return zy_json_echo(false,'参数不匹配','',-1);
        }

        //获取token
        $arr = [
            'appid'=>$appid,
            'appsecret'=>$appsecret,
        ];
        $accessToken = $Jwt->accessToken($arr);
        if($accessToken==false){
            return zy_json_echo(false,'传入格式错误','',-2);
        }
        return zy_json_echo(true,'成功！',array('access_token'=>$accessToken),200);

    }

    /**
     * 2、获取jwt token
     * @param appid 第三方应用唯一凭证
     * @param appsecret 第三方应用唯一凭证密钥，即appsecret
     * @return [type] [description]
     */
    public function getToken(){
        $data=$this->request->get();
        $config = Config('sign_config');
        $key = $config['key'];
        $Jwt = new \sign\Jwt($key);

        // 验证jti
        $verifyaccessToken = $Jwt->verifyaccessToken($data['jti']);
        if($verifyaccessToken['status']=='error'){
            return zy_json_echo(false,$verifyaccessToken['message'],'',-1);
        }

        $data['exp']=time()+7200;
        $jsapi_ticket = $Jwt->getToken($data);
        if($jsapi_ticket==false){
            return zy_json_echo(false,'传入格式错误','',-2);
        }

        return zy_json_echo(true,'成功！',$jsapi_ticket,200);

    }

    /**
     * 3、生成签名
     * @param appid 第三方应用唯一凭证
     * @param appsecret 第三方应用唯一凭证密钥，即appsecret
     * @return [type] [description]
     */
    public function signDe(){
        $data=$this->request->get();
        $config = Config('sign_config');
        $key = $config['key'];
        $appid = $config['appid'];
        $appsecret = $config['appsecret'];
        $Generatesign = new \sign\Generatesign();
        $Jwt = new \sign\Jwt($key);

        if($appid!=$data['appid']){
            return zy_json_echo(false,'appid错误','',-1);
        }

        // 验证jsapi_token
        $verifyToken=$Jwt->verifyToken($data['jsapi_ticket']);
        if($verifyToken['status']=='error'){
            return zy_json_echo(false,$verifyToken['message'],'',-2);
        }
        $verifyaccessToken=$Jwt->verifyaccessToken($verifyToken['data']['jti']);

        if($verifyaccessToken['status']=='error'){
            return zy_json_echo(false,$verifyaccessToken['message'],'',-3);
        }
        if($verifyaccessToken['data']['appid']!=$appid || $verifyaccessToken['data']['appsecret']!=$appsecret){
            return zy_json_echo(false,'凭证不匹配','',-4);
        }

        //如果没有sign，那么就是加密，获得sign
        if(empty($data['signature'])){
            $signature = $Generatesign->signature($data);
            $signature = $signature['data']['sign'];
        }else{
            $signV = $data['signature'];
            unset($data['signature']);
            $signature = $Generatesign->signature($data,'DECODE',$signV);
            if($signature['status']=='error'){
                return zy_json_echo(false,$signature['message'],'',-5);
            }
            $signature= '';
        }
        

        return zy_json_echo(true,'成功！',$signature,200);

    }





}

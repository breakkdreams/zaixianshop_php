<?php
namespace app\api\controller;
/**
 * 子系统访问权限控制器(app\api\controller)
 */
use think\Controller;
use think\Db;
use think\Cache;
use think\Config;
/**
 * 子系统访问权限控制器
 *
 *错误码:
 *
 * 10001    appid 不存在或为空
 * 10002    appsecret 不存在或为空
 * 10003    appsecret appid 值错误,信息校验失败
 *
 * 
 */
class ApiAccessAuthorityController extends Controller
{

    private $accessTokenURL = "";  // 获取access_token
    private $jsapiTicketURL = "";  // 获取临时票据
    private $signVerifyURL = "";    // 测试签名是否正确

    protected function _initialize()
    {
        parent::_initialize();
        $hostAddress = Config( 'admin_module_config.api_host' );
        $this->accessTokenURL = "$hostAddress/api/api_access_authority/accessToken?";    
        $this->jsapiTicketURL = "$hostAddress/api/api_access_authority/getToken?";    
        $this->signVerifyURL = "$hostAddress/api/api_access_authority/signVerifyTest?"; 

    }

    /**
     * 1、获取token access_token
     * @param appid 第三方应用唯一凭证
     * @param appsecret 第三方应用唯一凭证密钥，即appsecret
     * @return [type] [description]
     */
    public function accessToken(){
        $data = $this->request->get();

        if( !isset( $data [ 'appid' ] ) || empty( $data [ 'appid' ] )  ){
            return zy_json_echo( false, '参数缺失' ,null , 10001 );
        } 
        if( !isset( $data [ 'appsecret' ] ) || empty( $data [ 'appsecret' ] )  ){
            return zy_json_echo( false, '参数缺失' , null , 10002 );
        }

        // 获取key        
        $config = Config('sign_config');
        $key = $config['key'];

        $Jwt = new \sign\Jwt( $key );
        //获取token
        $arr = [
            'appid' => $data [ 'appid' ] ,
            'appsecret' => $data [ 'appsecret' ] ,
        ];
        $accessToken = $Jwt->accessToken($arr);
        if( $accessToken == false ){
            return zy_json_echo( false , '传入格式错误' , '' , 10004 );
        }
        return zy_json_echo(true,'成功！',array('access_token'=>$accessToken),200);
    }

    /**
     * 2、获取jwt token
     * @param appid 第三方应用唯一凭证
     * @param appsecret 第三方应用唯一凭证密钥，即appsecret
     * @return [type] [description]
     */
    public function getToken()
    {
        $data=$this->request->get();

        if( !isset( $data [ 'sub' ] ) || empty( $data [ 'sub' ] )  ){
            return zy_json_echo( false, '参数缺失' , null , 10005 );
        }

        if( !isset( $data [ 'jti' ] ) || empty( $data [ 'jti' ] )  ){
            return zy_json_echo( false, '参数缺失' , null , 10006 );
        }
        
        // 校验sub
        $sub = base64_decode( str_replace( ' ' , '+' , $data [ 'sub' ] ) );
        $sub = explode( ',' , $sub );
        $userLogin = reset($sub);
        $userPassword = end($sub);
        // dump($userLogin);dump($userPassword);exit();

        $company_data = Db::name('company')->where('super_login',$userLogin)->find();
        $user_data = Db::name('user')->where(['id'=>$company_data['super_admin'],'user_pass'=>$userPassword])->find();


        if(empty($company_data) || empty($user_data)){
            return zy_json_echo( false, '用于验证账户信息' , null , 10086 );
        }



        $config = Config('sign_config');
        $key = $config['key'];
        $Jwt = new \sign\Jwt( $key );


        // 验证jti 校验token
        $verifyaccessToken = $Jwt->verifyaccessToken($data['jti']);
        if($verifyaccessToken['status']=='error'){
            return zy_json_echo( false , $verifyaccessToken['message'],'', 10007 );
        }

        $data['exp']=time()+7200;
        //获取票据
        $jsapi_ticket = $Jwt->getToken($data);
        if($jsapi_ticket==false){
            return zy_json_echo(false,'传入格式错误','',10012);
        }
        return zy_json_echo(true,'成功！',$jsapi_ticket,200);

    }


     /**
     * 3、生成签名
     * @param appid 第三方应用唯一凭证
     * @param appsecret 第三方应用唯一凭证密钥，即appsecret
     * @return [type] [description]
     */
    public function signDe($data , $isApi = true )
    {
        if( !isset( $data [ 'appid' ] ) || empty( $data [ 'appid' ] )  ){
            return zy_json_echo( false, '参数缺失' ,null , 10001 );
        } 

        $config = Config('sign_config');
        $key = $config['key'];
        $appid = $config['appid'];        
        $appsecret = $config['appsecret'];
        $Jwt = new \sign\Jwt( $key );
        $Generatesign = new \sign\Generatesign();

        // dump($data['appid']);dump($appid);exit();

        if($appid!=$data['appid']){
            return zy_json_echo(false,'appid错误','',-1);
        }

        // 验证jsapi_token
        $verifyToken=$Jwt->verifyToken($data['jsapi_ticket']);
        if($verifyToken['status']=='error'){
            return zy_json_echo(false,$verifyToken['message'],'',10014);
        }
        $verifyaccessToken=$Jwt->verifyaccessToken($verifyToken['data']['jti']);

        if($verifyaccessToken['status']=='error'){
            return zy_json_echo(false,$verifyaccessToken['message'],'',10015);
        }
        if($verifyaccessToken['data']['appid']!=$appid || $verifyaccessToken['data']['appsecret']!=$appsecret){
            return zy_json_echo(false,'凭证不匹配','',-4);
        }

        //验证签名
        $signV = $data['signture'];
        unset($data['signture']);
        $signature = $Generatesign->signature($data,'DECODE',$signV);
        if($signature['status']=='error'){
            return zy_json_echo(false,$signature['message'],'',10017);
        }
		if( $isApi == true ){
            return true;
			// return zy_json_echo(true,'成功！','',200);
		}
        return true;
    }

     /**
      * 签名验证接口
      */
    public function signVerifyTest()
    {
        $data = $this->request->param();
		
        $this->signDe( $data );
    }
	
	/**
     * [signVerifyTest description]
     * @return [type] [description]
     */
    public static function signVerify( $data , $isApi = true )
    {
        $res = (new ApiAccessAuthorityController())->signDe( $data ,$isApi );
        $result = json_decode( $res , true );
        if( $result [ 'code' ] == 200 ){
            return true;
        }
        return $res;
    }
	





    // ============================= 加密传值 START
    

    /**
     * 1、获取access_token（加密）
     *
     * token需要进行存储
     */
    public function accessTokenEn()
    {
        // 判断 session 中是否存在access_token：如果存在那么就输出。如果没有那么就通过接口获取，然后进行存储
        if(session('?access_token')){
            $access_token = session('access_token');
            return $access_token;
        }else{
            $config = Config('sign_config');
            $appid = $config['appid'];
            $appsecret = $config['appsecret'];
            $urlData = cmf_curl_get($this->accessTokenURL."&appid=$appid&appsecret=$appsecret");
            $info = json_decode($urlData,true);
            if($info['status']=='error'){
                throw new \Exception('ERROR:'.$info['message'].',code:'.$info['code'], 1);
            }
            $result = $info['data']['access_token'];
            // 先写入到 session 中,在返回数据
            session('access_token',$result);
            return $result;            
        }
    }

    /**
     * 2、获取临时票据jwt token（加密）
     *
     * token需要进行存储
     */
    public function jsapiTicketEn()
    {
        // 获取帐号token
        $accessToken = $this->accessTokenEn();

        $jsapi_ticket = session('jsapi_ticket');    // jsapi_ticket
        $expires_in = session('expires_in');    // expires_in

        if( !empty($jsapi_ticket) && $expires_in>time() ){
            return $jsapi_ticket;
        }else{
            $company_data = Db::name('company')->where('id',1)->find();
            $user_data = Db::name('user')->where('id',$company_data['super_admin'])->find();
            // 配置个人信息 
            $user = base64_encode($company_data['super_login'].",".$user_data['user_pass']);

            $urlData = cmf_curl_get($this->jsapiTicketURL."&sub=".$user."&jti=".$accessToken);
            $info = json_decode($urlData,true);
            if($info['status']=='error'){
                throw new \Exception('ERROR:'.$info['message'], 1);
            }

            // 先获取，在写入
            $jsapi_ticket = $info['data']['jsapi_ticket'];
            $expires_in = $info['data']['expires_in'];

            // 先写入到 session 中,在返回数据
            session('jsapi_ticket',$jsapi_ticket);
            session('expires_in',$expires_in);

            return $jsapi_ticket;
        }
    }

    /**
     * 3、签名加解密（加密）
     *
     * @return 签名+数据
     */
    public function signCodeEn($data=array())
    {
        $config = Config('sign_config');
        $data['appid'] = $config['appid'];
        $data['timestamp'] = time();
        $data['noncestr'] = cmsrandom(8,'all');
        $data['jsapi_ticket'] = $this->jsapiTicketEn();
        // $data['isdebug'] = '100';
        $Generatesign = new \sign\Generatesign();
        $signature = $Generatesign->signature($data);
        if($signature['status']=='error'){
            throw new \Exception('ERROR:'.$signature['message'], 1);
        }
        $data['signture']=$signature['data']['sign'];
        // $paramString = http_build_query($data);
        return $data;
    }



    // ============================= 加密传值 END


}
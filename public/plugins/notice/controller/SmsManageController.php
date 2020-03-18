<?php
namespace plugins\notice\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;


class SmsManageController extends PluginAdminBaseController
{
    //分页数量
    private $pageNum = 20;

    //消息
    const aid = "fafd4cf8c648da1aa86338d4a5152294";
    const key = "86793af7c3bc64fd197001a8be4c6f9a";

    protected function _initialize()
    {
        parent::_initialize();
        $adminId = cmf_get_current_admin_id();
        //获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        }
    }




    /*
     * 网站配置信息
     */
    private function readConfig(){

        $symbol = 'notice';
        
        $id = 'readConfigInfo';

        $arr = ['isModule'=>true];

        //调用配置接口
        $web_config = getModuleApiData($symbol,$id,$arr);

        return $web_config['data'];
    }





     /**
     * @adminMenu(
     *     'name'   => '短信管理',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '短信管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $data = $this->request->param();

        $send = Db::name('notice_send')->where('type',2)->paginate($this->pageNum);

        $this->assign('send',$send);

        $this->assign('data',$data);

        $send->appends($data);

        $this->assign('page',$send->render());

        return $this->fetch();
    }





    /*
     * 添加短信页面
     */
    public function sendPage()
    {   
        
        return $this->fetch();
    }





    /*
     * 添加消息
     */
    public function addsms()
    {
        $data = $this->request->param();

        $module_info = getModuleConfig('notice','config','note_config.json');
        $module_info = json_decode($module_info,true);

        $da = [
            'aid' => self::aid,
            'key' => self::key,
            'mobile' => $data['mobile'],
            'model' => 'SMS_151997147',
            'content' => ['code'=>$data['content']],
            'keys' => $module_info['paccount'],
            'key_secret' => $module_info['ppassword'],
            'sign' => $module_info['sign'],
        ];

        $canshu = http_build_query($da);

        $url = 'http://gl.300c.cn/zylib/api_sms/sendSms?'.$canshu;

        $result = file_get_contents($url);

        $result = substr($result,0,strpos($result,"}")+1);

        $s = json_decode($result,true);

        if ($s['message']=='接口调用成功！') {
            $this->success('接口调用成功！');
        } else {
            $this->error($s['message']);
        }
    }








    public function test()
    {
        $aa = $this->addsms();

        dump(111);
    }


    




    // /**
    //  * 发送短信
    //  */
    // public function sendSms($data = null,$isModule=false){
    //     $request = $this->request->post();
    //     $request = zy_decodeData($request,$isModule);
    //     if(!empty($data)){
    //         $request = $data;
    //     }
    //     if(empty($request['mobile']) || empty($request['content'])){
    //         // return zy_json_echo(false,'传参错误',null,100);
    //         return zy_array (false,'传参错误',null,100 ,$isModule);
    //     }
    //     $module_info = getModuleConfig('message','config','config.json');
    //     $module_info = json_decode($module_info,true);
        
    //     $add['sendname'] = $module_info['id'];
    //     $add['key'] = $module_info['paccount'];
    //     $add['key_secret'] = $module_info['ppassword'];
    //     $add['model'] =  '234';
    //     $add['sign'] = $module_info['sign'];
    //     $add['mobile'] = $request['mobile'];
    //     $add['id_code']=cmsrandom();
    //     $add['content'] = $request['content'];
    //     $add['create_time'] = time();
    //     $add['ip'] = request()->ip();
    //     // dump($add);exit;
    //     // return zy_array (true,'添加成功',$add,200 ,$isModule);
    //     $re = Db::name('message_sms')->insert($add);
    //     if(empty($re)){
    //         // return zy_json_echo(false,'添加失败',null,101);
    //         return zy_array (false,'添加失败',null,101 ,$isModule);
    //     }
    //     // return zy_json_echo(true,'添加成功',$re,200);
    //     return zy_array (true,'添加成功',null,200 ,$isModule);
    // }








    public function sendSms()
    {
        //获取参数
        $data=$this->request->param();

        $data['content'] = ( isset($data['content']) && !empty($data['content']) )?$data['content']:[];

        //发送短信必要的参数检查  电话号码  模板号  参数内容
        if( (!isset($data['mobile']) && empty($data['mobile'])) || (!isset($data['model']) && empty($data['model'])) ){
            return zy_json(false,'所需的参数值缺失',null,101);
        }
        //检查产品AK等信息
        if(!isset($data['keys']) && empty($data['key'])){
            return zy_json(false,'所需的参数值缺失',null,102);
        }
        if(!isset($data['key_secret'])  && empty($data['key_secret'])){
            return zy_json(false,'所需的参数值缺失',null,103);
        }
        if(!isset($data['sign'])  && empty($data['sign'])){
            return zy_json(false,'所需的参数值缺失',null,104);
        }
        // 引入短信发送类
        Loader::import('aliyunsms', __DIR__."/../lib/zy_message/Aliyunsms", '.class.php');
        //实例化短信发送
        $sms=new \SmsDemo($data['keys'],$data['key_secret'],$data['sign']);

        //群发短信
        if(is_array($data['mobile'])){
            $res=$sms->sendBatchSms($data['mobile'],$data['model'],$data['content']);
        }else{

            //单发
            $res=$sms->sendSms($data['mobile'],$data['model'],$data['content']);
        }
        //dump($res);exit;
        if($res->Code=='OK'){
            return zy_json(true,'接口调用成功！',null,105);
        }else{
            return zy_json(true,$res->Message);
        }

        //$arr=['name'=>'短信接口测试','product'=>'产品','date'=>'000','day'=>'1'];
        //$res=$sms->sendSms("15858620686","SMS_154585568",$arr) ;
        //dump($res);
    }









}
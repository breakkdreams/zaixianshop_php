<?php
namespace plugins\notice\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginRestBaseController;
use plugins\Notice\Model\NoticeSendModel;
use think\Db;

class ApiIndexController extends PluginRestBaseController
{
    
    //消息
    const aid = "fafd4cf8c648da1aa86338d4a5152294";
    const key = "86793af7c3bc64fd197001a8be4c6f9a";




    /*
     * 站内消息发送
     */
    public function addmsg($da=null,$isModule=false)
    {   
        $model = new NoticeSendModel();
        $data = $this->request->post();
        $data = zy_decodeData($data,$isModule);
        if ($isModule==true) {
            $data = $da;
        }

        $result = $this->validate($data, "MessageApi");
        if ($result !== true) {
            return zy_array(false,'发送失败！',$result,-1 ,$isModule);
        }

        $symbol = 'notice';  
        if($data['status']==1){ //单发
            $num = $data['uid'];
            $id = 'member_one';
            $param = ['uid' =>$num,'field' =>'username,nickname,mobile',true];
            $res = getModuleApiData($symbol, $id, $param); //获取会员数据
            if ($res['status'] !== 'success') {
                return zy_array(false,'发送失败！uid错误！','',-2 ,$isModule);
            }
            $data['username'] = $res['data']['username'];
            $data['nickname'] = $res['data']['nickname'];
            $data['mobile'] = $res['data']['mobile'];
            $data['uid'] = $num;
            $data['u_read'] = ','.$num.',';

        } elseif($data['status']==2){ //群发

            $id = 'member_two';
            $param = ['isModule'=>true];
            $res = getModuleApiData($symbol, $id, $param); //uid获取所有的用户id，逗号拼接
            $data['u_read'] = $res['data'];
            $data['de_id'] = $res['data'];
            $data['uid'] = 1;
        }

        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['send_time'] = time();
        $data['type'] = 1; //站内信

        $data['is_success'] = 1; //发送状态

        $result=$model->allowField(true)->save($data);

        if ($result!==false) {
            if ($data['status'] == 1) {
                return zy_array(true,'单发消息成功！','',200 ,$isModule);
            }else if($data['status'] == 2){
                return zy_array(true,'群发消息成功！','',200 ,$isModule);
            }else{
                return zy_array(false,'发送失败！status参数错误！','',-3 ,$isModule);
            }
        } else {
            return zy_array(false,'发送失败！','',-4 ,$isModule);
        }
    }






    /*
     * 站内消息发送->江湖令
     */
    public function addmsgJhl($da=null)
    {   
        $data = $this->request->param();

        $model = new NoticeSendModel();

        $result = $this->validate($data, "MessageApi");

        if ($result !== true) {
            return zy_json_echo(false,'发送失败！',$result , -1);
        }

        $symbol = 'notice';
        if($data['status']==1){ //单发
            $num = $data['uid'];
            $id = 'member_one';
            $param = ['uid' =>$num,'field' =>'username,nickname,mobile',true];
            $res = getModuleApiData($symbol, $id, $param); //获取会员数据
            if ($res['status'] !== 'success') {
                return zy_json_echo(true,'发送失败！uid错误！',null , -1);
            }
            $data['username'] = $res['data']['username'];
            $data['nickname'] = $res['data']['nickname'];
            $data['mobile'] = $res['data']['mobile'];
            $data['uid'] = $num;
            $data['u_read'] = ','.$num.',';

        } elseif($data['status']==2){ //群发

            $id = 'member_two';
            $param = ['isModule'=>true];
            $res = getModuleApiData($symbol, $id, $param); //uid获取所有的用户id，逗号拼接
            $data['u_read'] = $res['data'];
            $data['de_id'] = $res['data'];
            $data['uid'] = 1;
        }

        $data['create_time'] = time();
        $data['update_time'] = time();
        $data['send_time'] = time();
        $data['type'] = 1; //站内信

        $data['is_success'] = 1; //发送状态

        $result=$model->allowField(true)->save($data);

        if ($result!==false) {
            if ($data['status'] == 1) {
                return zy_json_echo(true,'单发消息成功！',null , -1);
            }else if($data['status'] == 2){
                return zy_json_echo(true,'群发消息成功！',null , -1);
            }else{
                return zy_json_echo(false,'发送失败！status参数错误！',null , -1);
            }
        } else {
            return zy_json_echo(false,'发送失败！',null , -1);
        }
    }







    /**
     * 当前用户消息数量
     * @return [type] [description]
     */
    public function messageNum($isModule = false)
    {
        $request = $this->request->post();
        $request = zy_decodeData($request,$isModule);
        if(empty($request['uid'])){
            return zy_array (false,'用户id获取失败！','',-1 ,$isModule);
        }

        $where = "u_read like '%".','.$request['uid'].','."%'";

        $data = Db::name("notice_send")
            ->where($where)
            ->where('is_success',1)
            ->count();

        $arr['num']=$data;

        return zy_array(true,'操作成功',$arr,200 ,$isModule);
    }







    /**
     * 站内消息列表
     */
    public function message($uid = null ,$isModule = false)
    {
        $request = $this->request->post();
        $request = zy_decodeData($request,$isModule);

        if(empty($request['uid'])){
            return zy_array (false,'用户id获取失败！','',-1 ,$isModule);
        }
        $paginate=empty($request['paginate'])?20:$request['paginate'];

        $where = ('uid='.$request['uid']. " or de_id like '%".','.$request['uid'].','."%'");

        $field = 'id,sendname,thumb,content,title,u_read';
        $data = Db::name("notice_send")
            ->where($where)
            ->where('is_success',1)
            ->order("send_time DESC")
            ->field($field)
            ->paginate($paginate);

        $data = json_encode($data);
        $data = json_decode($data,true);

        /*if(empty($data['data'])){
            zy_json_echo(true,'暂无消息！','',201);
        }*/
        //如果当前用户id存在在这个字段里，那么就显示1
        $where = "u_read like '%".','.$request['uid'].','."%'";

        if($data){
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]['thumb']=ZY_APP_PATH.$data['data'][$key]['thumb'];

                //如果当前用户id存在在这个字段里，那么就显示1
                $data['data'][$key]['display'] = strstr($data['data'][$key]['u_read'], ','.$request['uid'].',') ? true : false;
                unset($data['data'][$key]['u_read']);
            }
        }


        return zy_array (true,'操作成功',$data,200 ,$isModule);
    }





    /**
     * 消息删除
     */
    public function messageDel($uid = null ,$id = null ,$status = null ,$isModule=false)
    {   
        $request = $this->request->param();
        $request = zy_decodeData($request,$isModule);

        if (empty($request['uid'])) {
            return zy_array (false,'用户id获取失败！','',-1 ,$isModule);
        }

        if (empty($request['id'])) {
            return zy_array (false,'id获取失败！','',-2 ,$isModule);
        }

        if (empty($request['status'])) {
            return zy_array (false,'status获取失败！','',-3 ,$isModule);
        }

        if ($request['status']==1) {
            $where = ['uid'=>$request['uid'],'id'=>$request['id'],'status'=>1];
            $result = Db::name('notice_send')->where($where)->delete();
            if ($result==true) {
                return zy_array (true,'操作成功','',200 ,$isModule);
            } else {
                return zy_array (false,'操作失败','',-10 ,$isModule);
            }
        }

        if ($request['status']==2) {

            $where = ['id'=>$request['id'],'status'=>2];            
            $data = Db::name("notice_send")->where($where)->field('de_id')->find();
            $str_replace = str_replace(','.$request['uid'].',',"",$data['u_read']);
            $result = Db::name('notice_send')->where($where)->setField('de_id',$str_replace);

            if($result==true){
                return zy_array (true,'操作成功','',200 ,$isModule);
            }else{
                return zy_array (false,'操作失败','',-10 ,$isModule);
            }
        }

        return zy_array (false,'status有误，请正确的传值！','',-4 ,$isModule);
    }






     /**
     * CURL方式的POST传值
     * @param  [type] $url  [POST传值的URL]
     * @param  [type] $data [POST传值的参数]
     * @return [type]       [description]
     */
    function _crul_post($url,$data){
        //初始化curl       
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        //post提交方式
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //运行curl
        $result = curl_exec($curl);

        //返回结果      
        if (curl_errno($curl)) {
           return 'Errno'.curl_error($curl);
        }
        curl_close($curl);
        return $result;
    }












    






    /**
     * 消息详情
     */
    public function messageDetail($uid = null ,$id = null ,$isModule=false)
    {
        $request = $this->request->post();
        $request = zy_decodeData($request,$isModule);

        //eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOjQwLCJleHAiOjE1NzU5NDY4MTUsImp0aSI6IklqRXlNelExTmlJLklqQXdNREF3TUNJLlgtWHpSbXpha1doSE5DMVlCOUNwUW1mc1VHVnh0dDNVa0RrME4wOGJPR0UifQ.KkPSj8f-BlqybK8NpDDhZ2ZaLq1olFbcBriXFltlsq4

        if(empty($request['uid'])){
            return zy_array (false,'用户id获取失败！','',-1 ,$isModule);
        }
        if(empty($request['id'])){
            return zy_array (false,'id获取失败！','',-2 ,$isModule);
        }

        $where= 'id='.$request['id'];

        $field = 'id,sendname,thumb,content,title,send_time,status,u_read';
        $data = Db::name("notice_send")
            ->where($where)
            ->field($field)
            ->find();
        if($data){
            $data['create_time']=date('Y-m-d H:i:s',$data['send_time']);
            $data['thumb']=ZY_APP_PATH.$data['thumb'];
        }

        //设置成已读
        if($data){
            if($data['status']==1){ //单发
                $result = Db::name('notice_send')->where($where)->setField('u_read','');
            }else{   //群发
                $str_replace = str_replace(','.$request['uid'].',',"",$data['u_read']);
                $result = Db::name('notice_send')->where($where)->setField('u_read',$str_replace);
            }
        }

        return zy_array (true,'操作成功',$data,200 ,$isModule);
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
    //     $module_info = getModuleConfig('notice','config','note_config.json');
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




    ////////////////////////////////////////////////////短信类/////////////////////////




    /**
     * 发送短信验证码
     * @return [type] [时间来不及了，先用这个凑凑数]
     */
    public function smsCode($mobile=null,$isModule=false){
        $request = $this->request->post();
        $request = zy_decodeData($request,$isModule);

        if (!empty($mobile)) {
            $request['mobile'] = $mobile;
        }

        if(empty($request['mobile'])){
            return zy_array (false,'参数不能为空','',-1 ,$isModule);
        }
        //短信配置信息
        $module_info = getModuleConfig('notice','config','note_config.json');
        $module_info = json_decode($module_info,true);

        $add['sendname'] = $module_info['id'];
        $add['key'] = $module_info['paccount'];
        $add['key_secret'] = $module_info['ppassword'];
        $add['sign'] = $module_info['sign'];
        $add['model'] =  'SMS_151997147';
        $add['mobile'] = $request['mobile'];
        $add['id_code']=cmsrandom();
        $add['content'] = '尊敬的用户您好，您的验证码为：'.$add['id_code'].'，验证码有效期为5分钟。'; //添加记录
        $add['create_time'] = time();
        $add['ip'] = request()->ip();

        $content = json_encode(['code'=>$add['id_code']],JSON_UNESCAPED_UNICODE);

        $url = 'http://www.300c.cn/api.php?op=aliyun_sms&uid='.$module_info['id'].'&user='.$module_info['paccount'].'&password='.$module_info['ppassword'].'&model='.$add['model'].'&mobile='.$add['mobile'].'&content='.$content;

        $retsult = json_decode(file_get_contents($url),true);

        if($retsult['status']!=1){
            return zy_array (false,$retsult['smg'],'',-100 ,$isModule);
        }
        $re = Db::name('notice_verify')->insert($add);

        if(empty($re)){
            return zy_array (false,'发送失败',null,101 ,$isModule);
        }
        return zy_array (true,'发送成功',null,200 ,$isModule);
    }










    /**
     * 验证短信验证码
     */
    public function  verifySms($data = null,$isModule=false)
    {
        $request = $this->request->param();
        $request = zy_decodeData($request,$isModule);

        if(!empty($data)){
            $request = $data;
        }
        if(empty($request['mobile']) || empty($request['code'])){
            return zy_array (false,'传参错误',null,100 ,$isModule);
        }
        $time = time()-300;
        $data = Db::name('notice_verify')->where('mobile',$request['mobile'])->where('create_time','>',$time)->order('create_time','desc')->find();

        if(empty($data)){
            return zy_array (false,'该电话号码无近期验证信息',null,101 ,$isModule);
        }
        if($request['code']!=$data['id_code']){
            return zy_array (false,'提交的验证密码不一致，验证失败',null,102 ,$isModule);
        }
        return zy_array (true,'验证通过',null,200 ,$isModule);

    }





    /////////////////////////////邮箱类//////////////////////////////////////////
    

    /*
     * 发送邮箱
     */
    public function fasongEmail($param=null,$isModule=false)
    {
        $data = $this->request->param();
        $data = zy_decodeData($data,$isModule);

        if (!empty($param)) {
            $data = $param;
        }


        if (empty($data['title']) || empty($data['content']) || empty($data['email_num'])) {
            return zy_array (false,'传参错误',null,101 ,$isModule);
        }

        $email_config = getModuleConfig('notice','config','email_config.json');
        $email_config = json_decode($email_config,true);

        if ($email_config['mail_off'] != 1) {
            return zy_array (false,'该接口已被禁用',null,102 ,$isModule);
        }

        //发送消息
        $da = [
            'aid'=>self::aid,
            'key'=>self::key,
            'host'=>$email_config['server'], //邮件服务器
            'port'=>$email_config['port'], //邮件发送端口
            'secure'=>$email_config['protocol'], //发送协议
            'username'=>$email_config['username'], //验证用户名
            'pwd'=>$email_config['password'], //验证密码
            'set_from'=>$email_config['mailbox'],//发件人

            'subject'=>$data['title'], //邮件标题  
            'address'=>$data['email_num'], //收件邮箱
            'content'=>$data['content'], //邮件内容
        ];

         //添加消息记录
        $add['title']=$data['title'];
        $add['content']=$data['content'];
        $add['recipients']=$data['email_num']; //接收人
        $add['create_time'] = time();
        $add['type'] = 3; //邮箱发送
        $add['status'] = 1;
        $add['send_time'] = time(); //发送时间

        $send = Db::name('notice_send');
        //发送
        $d=http_build_query($da);
        $url="https://oa.300c.cn/zyapi/sms/sendemail?".$d;
        $result = cmf_curl_get($url);
        $result = substr($result,0,strpos($result,"}")+1);
        $s = json_decode($result,true);
        
        if ($s['status']=='success'){
            $add['is_success'] = 1;
            $send->insert($add); //添加发送记录
            return zy_array(true,'发送成功',null,200,$isModule);
        } else {
            $add['is_success'] = 2;
            $send->insert($add); //添加发送记录
            return zy_array(false,'发送失败',null,104,$isModule);
        }
    }




    /**
     * 发送邮件验证码
     */
    public function sendEmaiil($data = null,$isModule=false){
        $request = $this->request->param();
        $request = zy_decodeData($request,$isModule);
        if(!empty($data)){
            $request = $data;
        }

        // $request['subject'] = '您有一个验证码，请查看';
        // // $request['send_userid'] = '1900227304@qq.com';
        // $request['email'] = 'lxh3734@163.com';

        if(empty($request['subject']) || empty($request['email'])){
            return zy_array (false,'传参错误',null,100 ,$isModule);
        }

        $module_info = getModuleConfig('notice','config','email_config.json');
        $module_info = json_decode($module_info,true);

        $yzm = cmsrandom(); //验证密码
        $add['msg']=$yzm; //邮件内容
        $add['subject']=$request['subject']; //邮件标题
        // $add['send_userid']=$request['send_userid']; //发件人
        $add['email']=$request['email']; //邮箱
        
        $add['posttime']=time();
        $add['id_code']= $yzm; //验证密码
        $add['status'] = 0; //默认0  1使用过
        $add['ip'] = request()->ip();

        $da = [
            'aid'=>self::aid,
            'key'=>self::key,
            'host'=>$module_info['server'],
            'port'=>$module_info['port'],
            'secure'=>$module_info['protocol'],
            'username'=>$module_info['username'],
            'pwd'=>$module_info['password'],
            'set_from'=>$module_info['mailbox'],//发件人
            'address'=>$request['email'], //收件邮箱
            'subject'=>$request['subject'], //邮件标题  

            'content'=>$yzm,//$request['msg'], //邮件内容
        ];

        $da=http_build_query($da);
        $url="https://oa.300c.cn/zyapi/sms/sendemail?".$da;
        $result = cmf_curl_get($url);
        $result = substr($result,0,strpos($result,"}")+1);
        $s = json_decode($result,true);

        if ($s['status']!='success'){
            return zy_array (false,'发送失败',null,101 ,$isModule);
        }

        $re = Db::name('notice_email')->insert($add);
        return zy_array (true,'发送成功',null,200 ,$isModule);
    }



    /**
     * 验证邮箱验证码
     */
    public function  verifyEmaiil($data = null,$isModule=false)
    {
        $request = $this->request->param();
        $request = zy_decodeData($request,$isModule);
        if(!empty($data)){
            $request = $data;
        }

        // $request['email'] = 'lxh3734@163.com';
        // $request['code'] = '091070';

        if(empty($request['email']) || empty($request['code'])){
            return zy_array (false,'传参错误',null,100 ,$isModule);
        }
        $time = time()-300;
        $data = Db::name('notice_email')->where('email',$request['email'])->where('posttime','>',$time)->order('posttime','desc')->find();

        if(empty($data)){
            return zy_array (false,'该邮箱无近期验证信息',null,101 ,$isModule);
        }

        if($request['code']!=$data['id_code']){
            return zy_array (false,'提交的验证密码不一致，验证失败',null,102 ,$isModule);
        }

        Db::name('notice_email')->where('id',$data['id'])->update(['status'=>1]);

        return zy_array (true,'验证通过',null,200 ,$isModule);

    }





    // /**
    //  * 发送邮件验证码
    //  */
    // public function sendEmaiil($data = null,$isModule=false){
    //     $request = $this->request->param();
    //     $request = zy_decodeData($request,$isModule);
    //     if(!empty($data)){
    //         $request = $data;
    //     }

    //     $request['msg'] = '内容';
    //     $request['subject'] = '您有一个验证码，请查看';
    //     $request['send_userid'] = '1900227304@qq.com';
    //     $request['email'] = 'lxh3734@163.com';


    //     if(empty($request['send_userid']) || empty($request['subject']) || empty($request['msg']) || empty($request['email'])){
    //         // return zy_json_echo(false,'传参错误',null,100);
    //         return zy_array (false,'传参错误',null,100 ,$isModule);
    //     }

        

    //     $module_info = getModuleConfig('notice','config','email_config.json');
    //     $module_info = json_decode($module_info,true);

    //     $add['msg']=$request['msg']; //邮件内容
    //     $add['subject']=$request['subject']; //邮件标题
    //     $add['send_userid']=$request['send_userid']; //发件人
    //     $add['email']=$request['email']; //邮箱
        
    //     $add['posttime']=time();
    //     $add['id_code']=cmsrandom(); //验证密码
    //     $add['status'] = 0; //默认0  1使用过
    //     $add['ip'] = request()->ip();

    //     $da = [
    //         'aid'=>self::aid,
    //         'key'=>self::key,
    //         'host'=>$module_info['server'],
    //         'port'=>$module_info['port'],
    //         'secure'=>$module_info['protocol'],
    //         'username'=>$module_info['username'],
    //         'pwd'=>$module_info['password'],
    //         'set_from'=>$module_info['mailbox'],//发件人
    //         'address'=>$request['email'], //收件邮箱
    //         'subject'=>$request['subject'], //邮件标题  

    //         'content'=>$add['id_code'],//$request['msg'], //邮件内容

    //     ];
    //     $da=http_build_query($da);
    //     $url="https://oa.300c.cn/zyapi/sms/sendemail?".$da;
    //     $result = json_decode(file_get_contents($url),true);
    //     if ($result['status']!='success'){
    //         return zy_array (false,'发送失败',null,101 ,$isModule);
    //         // return zy_json_echo(false,$result['message'],null,101);
    //     }
    //     $re = Db::name('notice_email')->insert($add);
    //     return zy_array (true,'发送成功',null,200 ,$isModule);
    //     // return zy_json_echo(true,'发送成功',$result,200);
    // }





    




    






    /**
     * 清空账号验证码
     */
    public function cleanCode($data = null,$isModule=false){
        $request = $this->request->param();
        $request = zy_decodeData($request,$isModule);
        if(!empty($data)){
            $request = $data;
        }
        //type 1.sms 2.email
        if(empty($request['account']) || empty($request['type'])){
            // return zy_json_echo(false,'传参错误',null,100);
            return zy_array (false,'传参错误',null,100 ,$isModule);
        }
        if($request['type']==1){
            $data = Db::name('notice_verify')->where('mobile',$request['account'])->delete();
        }elseif($request['type']==2){
            $data = Db::name('notice_email')->where('email',$request['account'])->delete();
        }else{
            // return zy_json_echo(false,'type值仅可为1,2',null,101);
            return zy_array (false,'传参错误',null,101 ,$isModule);

        }
        // return zy_json_echo(true,'清空成功',null,200);
        return zy_array (true,'清空成功',null,200 ,$isModule);
    }







/////////////////////////////////、、、、、公众号管理、、、、、、//////////////////////////////////////////////////////


    const APPID = 'wx5b74a5c289a4b2a5';
    const SECRET = '8c73b91c2c927df3a02795d74e04264f';

    //获取access_token的方法。
    public function getToken()
    {
        $appid = self::APPID;
        $appsecret = self::SECRET;
        $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $TOKEN_URL = $this->https_request($TOKEN_URL);
        $result=json_decode($TOKEN_URL,true);
        return $result['access_token'];
    }



    /**
     * request 请求
     */
    public function https_request($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


    /*
     * 订单支付状态通知
     */
    public function orderPayStatus($param=null,$isModule=false)
    {
        $data = $this->request->param();
        $data = zy_decodeData($data,$isModule);

        if (!empty($param)) {
            $data = $param;
        }

        // $data['uid'] = 30;
        // $data['ordersn'] = 123;
        // $data['shop_detail'] = '商品名称';
        // $data['money'] = '￥12.25';
        // $data['pay_status'] = '支付成功';
        // $data['url'] = '139.199.1.37';
        // $data['first'] = '您的订单已经支付成功';
        // $data['remark'] = '点击查看详情';

        if (empty($data['uid'])) {
            return zy_array (false,'uid不能为空',null,100 ,$isModule);
        }
        if (empty($data['ordersn'])) {
            return zy_array (false,'订单编号不能为空',null,100 ,$isModule);
        }
        if (empty($data['shop_detail'])) {
            return zy_array (false,'商品明细不能为空',null,100 ,$isModule);
        }
        if (empty($data['money'])) {
            return zy_array (false,'订单金额不能为空',null,100 ,$isModule);
        }
        if (empty($data['pay_status'])) {
            return zy_array (false,'支付状态不能为空',null,100 ,$isModule);
        }

        $symbol = 'notice';
        $id = 'member_one';
        $one_param = ['uid' =>$data['uid'],'field' =>'nickname,wechatpe_openid',true];
        $res = getModuleApiData($symbol, $id, $one_param); //获取会员数据

        if ($res['status']=='error') {
            return zy_array (false,$res['message'],null,100 ,$isModule);
        }

        if (empty($res['data']['wechatpe_openid'])) {
            return zy_array (false,'当前用户未绑定微信公众号',null,100 ,$isModule);
        }

        $token = $this->getToken();
        //模板消息发送接口
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$token;

        $fa = [
            'touser' => $res['data']['wechatpe_openid'],
            'template_id' => 'rfv9Vr8pR6nufxp3MTa7aZXv0zW7N4iFm60coch4ugg', //订单支付状态通知
            'url' => isset($data['url'])?$data['url']:'',
            'data' => [
                'first' => [
                    'value' => isset($data['first'])?$data['first']:'',
                    'color' => '#173177',
                ],
                'keyword1' => [
                    'value' => $data['ordersn'],
                    'color' => '#173177',
                ],
                'keyword2' => [
                    'value' => $data['shop_detail'],
                    'color' => '#173177',
                ],
                'keyword3' => [
                    'value' => $data['money'],
                    'color' => '#173177',
                ],
                'keyword4' => [
                    'value' => $data['pay_status'],
                    'color' => '#173177',
                ],
                'remark' => [
                    'value' => isset($data['remark'])?$data['remark']:'',
                    'color' => '#173177',
                ]
            ],
        ];

        //添加消息记录
        $add = [
            'uid' => $data['uid'],
            'nickname' => $res['data']['nickname'],
            'content' => '--',
            'recipients' => $res['data']['wechatpe_openid'],
            'create_time' => time(),
            'status' => 1,
            'type' => 6,
            'send_time' => time(),
            'is_success' => 1,
            'comm_type' => '订单支付状态通知',
            'url' => isset($data['url'])?$data['url']:null,
        ];

        $fa = json_encode($fa);

        $result = $this->https_request($url,$fa);

        $result = json_decode($result,true);

        if ($result['errmsg']=='ok') {
            Db::name('notice_send')->insert($add);
            return zy_array (true,'发送成功',null,100 ,$isModule);
        }
    }






    /*
     * 订单发货通知
     */
    public function orderFahuoStatus($param=null,$isModule=false)
    {
        $data = $this->request->param();
        $data = zy_decodeData($data,$isModule);

        if (!empty($param)) {
            $data = $param;
        }

        $token = $this->getToken();
        //模板列表
        // $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=".$token;
        // $mblist = $this->https_request($url);
        // $mblist = json_decode($mblist,true)['template_list'];
        // dump($mblist);exit;
        // $data['first'] = '您的订单已发货';
        // $data['uid'] = 1;
        // $data['name'] = '曹操';
        // $data['mobile'] = '14658258564';
        // $data['kd_name'] = '申通快递';
        // $data['kd_num'] = '222222';
        // $data['ordersn'] = '111111111111';
        // $data['remark'] = '点击查看详情';

        if (empty($data['uid'])) {
            return zy_array (false,'uid不能为空',null,100 ,$isModule);
        }
        if (empty($data['name'])) {
            return zy_array (false,'收货人姓名不能为空',null,100 ,$isModule);
        }
        if (empty($data['mobile'])) {
            return zy_array (false,'收货人手机号不能为空',null,100 ,$isModule);
        }
        if (empty($data['kd_name'])) {
            return zy_array (false,'快递公司不能为空',null,100 ,$isModule);
        }
        if (empty($data['kd_num'])) {
            return zy_array (false,'快递单号不能为空',null,100 ,$isModule);
        }
        if (empty($data['ordersn'])) {
            return zy_array (false,'订单号不能为空',null,100 ,$isModule);
        }

        $symbol = 'notice';
        $id = 'member_one';
        $one_param = ['uid' =>$data['uid'],'field' =>'nickname,wechatpe_openid',true];
        $res = getModuleApiData($symbol, $id, $one_param); //获取会员数据

        if ($res['status']=='error') {
            return zy_array (false,$res['message'],null,100 ,$isModule);
        }

        if (empty($res['data']['wechatpe_openid'])) {
            return zy_array (false,'当前用户未绑定微信公众号',null,100 ,$isModule);
        }

        $token = $this->getToken();
        //模板消息发送接口
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$token;

        $fa = [
            'touser' => $res['data']['wechatpe_openid'],
            'template_id' => '8wmmOCvpbIdp2c48qEVA6nBRgEfzJz91O_7-mvc3aKs', //订单支付状态通知
            'url' => isset($data['url'])?$data['url']:'',
            'data' => [
                'first' => [
                    'value' => isset($data['first'])?$data['first']:'',
                    'color' => '#173177',
                ],
                'keyword1' => [
                    'value' => $data['name'],
                    'color' => '#173177',
                ],
                'keyword2' => [
                    'value' => $data['mobile'],
                    'color' => '#173177',
                ],
                'keyword3' => [
                    'value' => $data['kd_name'],
                    'color' => '#173177',
                ],
                'keyword4' => [
                    'value' => $data['kd_num'],
                    'color' => '#173177',
                ],
                'keyword5' => [
                    'value' => $data['ordersn'],
                    'color' => '#173177',
                ],
                'remark' => [
                    'value' => isset($data['remark'])?$data['remark']:'',
                    'color' => '#173177',
                ]
            ],
        ];


        //添加消息记录
        $add = [
            'uid' => $data['uid'],
            'nickname' => $res['data']['nickname'],
            'content' => '--',
            'recipients' => $res['data']['wechatpe_openid'],
            'create_time' => time(),
            'status' => 1,
            'type' => 6,
            'send_time' => time(),
            'is_success' => 1,
            'comm_type' => '订单发货通知',
            'url' => isset($data['url'])?$data['url']:null,
        ];

        $fa = json_encode($fa);

        $result = $this->https_request($url,$fa);

        $result = json_decode($result,true);

        if ($result['errmsg']=='ok') {
            Db::name('notice_send')->insert($add);
            return zy_array (true,'发送成功',null,100 ,$isModule);
        }
    }









    ///////////////////////////////////////////////////小程序类/////////////////////////////////////////////
    



    ///////////////////////////////////////////////个推类///////////////////////////////////////////////
    
    











}

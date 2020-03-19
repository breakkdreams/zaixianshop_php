<?php
namespace plugins\member\controller;
/**
 * @Author: user
 * @Date:   2019-03-07 16:21:19
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-20 09:56:01
 */
use cmf\controller\PluginRestBaseController;//引用插件基类
use plugins\member\Model\PluginApiIndexModel;
use think\Db;

/**
 * api控制器
 */
class ApiIndexController extends PluginRestBaseController
{
	protected $apiMode=null;
	protected function _initialize()
    {
        $this->apiMode=new PluginApiIndexModel();
    }


    /**
     * 消息模块，返回所有的用户id，已逗号格式给他
     */
    public function messageUid( $isModule = false )
    {
        if($isModule!=true){
            return zy_array (false,'参数不能为空','',-1 ,$isModule);
        }
        $memberInfo = Db::name('member')->field('uid')->select()->toArray();
        $u_str='';
        foreach ($memberInfo as $key => $value) {
            $u_str .= ','.$memberInfo[$key]['uid'].',';
        }

        return zy_array (true,'操作成功',$u_str,200 ,$isModule);
    }

    /**
     * 添加余额/减少余额
     * @param  [type]  $uid      [用户id]
     * @param  integer $amount   [金额]
     * @param  integer $type     [类型，1为增加，2减少]
     * @param  boolean $isModule [模块标识]
     */
    public function amountOperation( $uid = null ,$amount=0 ,$type = 1,$isModule = false )
    {
        if($isModule!=true){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }
        if( empty($uid) || empty($amount) || empty($type) ){
            return zy_array (false,'参数不能为空','',-2 ,$isModule);
        }

        $data = Db::name('member_detail')->where('uid', $uid)->field('amount')->find();


        if($type==1){
            $result = Db::name('member_detail')
                ->where('uid', $uid)
                ->setInc('amount', $amount);
            return zy_array (true,'操作成功','',200 ,$isModule);    
        }else if($type==2){
            if($data['amount']<$amount){
                return zy_array (false,'余额不足','',-3 ,$isModule);
            }            

            $result = Db::name('member_detail')
                ->where('uid', $uid)
                ->setDec('amount', $amount);
            return zy_array (true,'操作成功','',200 ,$isModule);
        }else{
            return zy_array (false,'类型错误','',-4 ,$isModule);
        }

    }

    /**
     * 个人资料
     * @param  [type]  $uid      [用户id]
     * @param  [type]  $field    [需要查询的字段]
     * @param  boolean $isModule [是否是模块查询]
     * @return [type]            [description]
     */
    public function personalData( $uid = null ,$field=null ,$isModule = false )
    {

        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        
        $uid = isset( $data['uid'] ) ? $data['uid'] : $uid ;
        $field = isset( $data['field'] ) ? $data['field'] : $field ;



        if(empty($uid)){
            return zy_array (false,'uid不能为空','',-3 ,$isModule);
        }
        $result = Db::name('member')
            ->alias("a") //取一个别名
            ->join('member_detail b','a.uid = '.$uid.' and b.uid = '.$uid)
            ->field($field)
            ->find();

        if(!$result){
            return zy_array (false,'用户不存在','',-3 ,$isModule);
        }
        if(!empty($result['avatar'])){
            $result['avatar'] = 'http://'.$_SERVER['HTTP_HOST'].str_replace("/public/index.php","",$_SERVER['PHP_SELF']).$result['avatar'];
        }
        if(!empty($result['store_logo'])){
            $result['store_logo'] = 'http://'.$_SERVER['HTTP_HOST'].str_replace("/public/index.php","/public",$_SERVER['PHP_SELF']).$result['store_logo'];
        }

        return zy_array (true,'操作成功',$result,200 ,$isModule);
    }

    /**
     * 通过手机号个人资料
     * @param  [type]  $uid      [用户id]
     * @param  [type]  $field    [需要查询的字段]
     * @param  boolean $isModule [是否是模块查询]
     * @return [type]            [description]
     */
    public function mPersonalData( $mobile = null ,$field=null ,$isModule = false )
    {

        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);

        $mobile = isset( $data['mobile'] ) ? $data['mobile'] : $mobile ;
        $field = isset( $data['field'] ) ? $data['field'] : $field ;



        if(empty($mobile)){
            return zy_array (false,'手机号不能为空','',-3 ,$isModule);
        }
        $result = Db::name('member')
            ->where('mobile',$mobile)
            ->field($field)
            ->find();

        if(!$result){
            return zy_array (false,'用户不存在','',-3 ,$isModule);
        }

        return zy_array (true,'操作成功',$result,200 ,$isModule);
    }

    /**
     * 验证支付密码是否正确
     * @param  [type]  $uid      [用户id]
     * @param  [type]  $password    [密码]
     * @param  boolean $isModule [是否是模块查询]
     * @return [type]            [description]
     */
    public function isPaypassword( $uid = null ,$password=null ,$isModule = false )
    {

        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);

        $uid = isset( $data['uid'] ) ? $data['uid'] : $uid ;
        $password = isset( $data['password'] ) ? $data['password'] : $password ;

        if(empty($uid) || empty($password) ){
            return zy_array (false,'参数不能为空','',-3 ,$isModule);
        }
        $data2 = Db::name('member_detail')
                    ->where(['uid'=>$uid])
                    ->field('pay_password')
                    ->find();

        if(!$data2){
            return zy_array (false,'用户不存在','',-3 ,$isModule);
        }

        $validatePassword = cmf_compare_password($password,$data2['pay_password']);// 验证密码
        if($validatePassword==false){
            return zy_array (false,'密码错误！','',-3,$isModule);
        }

        return zy_array (true,'操作成功','',200 ,$isModule);
    }

    /**
     * 修改资料
     * @param  [type]  $uid      [用户id]
     * @param  [type]  $field    [需要更改的字段]
     * @param  [type]  $val    [需要更改的值]
     * @param  boolean $isModule [是否是模块查询]
     * @return [type]            [description]
     */
    public function editPersonal( $uid = null ,$field=null ,$val=null ,$isModule =  false )
    {

        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);

        $uid = isset( $data['uid'] ) ? $data['uid'] : $uid ;
        $field = isset( $data['field'] ) ? $data['field'] : $field ;
        $val = isset( $data['val'] ) ? $data['val'] : $val ;

        if(empty($uid) || empty($field) || empty($val)){
            return zy_array (false,'参数不能为空','',-1 ,$isModule);
        }
        $fieldArr = ['avatar','nickname','amount'];

        $isin = in_array($field,$fieldArr);
        if(!in_array($field,$fieldArr)){
            return zy_array (false,'操作失败','',-2 ,$isModule);
        }

        $result = Db::name('member')
            ->alias("a") //取一个别名
            ->join('member_detail b','a.uid = '.$uid.' and b.uid = '.$uid)
            ->where(['a.uid'=>$uid])
            ->setField($field, $val);

        if(!$result){
            return zy_array (false,'用户不存在','',-3 ,$isModule);
        }

        return zy_array (true,'操作成功',$result,200 ,$isModule);
    }


    /**
     * 手机号码登录
     * @param  [type]  $account      [手机号]
     * @param  [type]  $password    [密码]
     * @param  boolean $isModule [是否是模块查询]
     */
    public function mobileLogin($isModule=false)
    {

        if ($this->request->isPost()) {
            $data = $this->request->post();
            if($this->request->isPost()){
                $important = true;
            };
            $data = zy_decodeData($data,$isModule);

            $result = $this->validate($data,'Member.mobileLogin');
            if(true !== $result){
                // 验证失败 输出错误信息
                return zy_array (false,$result,'',-1,$isModule,$important);
            }
            $mobile = $data['mobile'];
            $code = $data['mobile_code'];


            $data2 = Db::name('member')
                        ->where(['mobile'=>$mobile])
                        ->field('uid,username,password,mobile,email,islock')
                        ->find();

            // 验证
            if(!$data2){
                return zy_array (false,'用户不存在！','',-2,$isModule,$important);
            }
            // 验证验证码
            $verifySms = array('mobile'=>$mobile,'code'=>$code);
            $message_verifySms = $this->notice_verifySms($verifySms);
            if($message_verifySms['status']=='error'){
                return zy_array (false,$message_verifySms['message'],'',-40,$isModule,$important);
            }
            // 清空短信验证码
            $cleanCode = array('account'=>$mobile,'type'=>1);
            $message_cleanCode = $this->notice_cleanCode($cleanCode);
            if($message_cleanCode['status']=='error'){
                return zy_array (false,$message_cleanCode['message'],'',-41,$isModule,$important);
            }
            if($this->verify_isLock($data2['uid'])==false){
                return zy_array (false,'账号被锁定！','',-4,$isModule,$important);
            }


            $result = zy_userid_jwt($data2['uid'])['data'];
            if(!empty($dataz['data'])){
                $result = ['uid'=>$data2['uid']];
            }
            Db::name('member')->where('uid', $data2['uid'])->update(['last_login_time' => time(),'last_login_ip' => get_client_ip()]);

            return zy_array (true,'操作成功',$result,200 ,$isModule,$important);
        } else {
            return zy_array (false,'请求错误!','',-100,$isModule,$important);
        }

    }


    /**
     * 账号密码登录
     * @param  [type]  $account      [账号]
     * @param  [type]  $password    [密码]
     * @param  boolean $isModule [是否是模块查询]
     */
    public function accountLogin($isModule=false)
    {

        if ($this->request->isPost()) {
            $data = $this->request->post();
            $dataz = $this->request->post();
            if($this->request->isPost()){
                $important = true;
            };

            $data = zy_decodeData($data,$isModule);

            $result = $this->validate($data,'Member.accountLogin');
            if(true !== $result){
                // 验证失败 输出错误信息
                return zy_array (false,$result,'',-1,$isModule,$important);
            }
            $account = $data['account'];
            $password = $data['password'];


            $data2 = Db::name('member')
                        ->where(['mobile'=>$account])
                        ->field('uid,username,password,mobile,email,islock')
                        ->find();

            // 验证
            if(!$data2){
                return zy_array (false,'用户不存在！','',-2,$isModule,$important);
            }
            $validatePassword = cmf_compare_password($password,$data2['password']);// 验证密码
            if($validatePassword==false){
                return zy_array (false,'密码错误！','',-3,$isModule,$important);
            }
            if($this->verify_isLock($data2['uid'])==false){
                return zy_array (false,'账号被锁定！','',-4,$isModule,$important);
            }


            $result = zy_userid_jwt($data2['uid'])['data'];
            if(!empty($dataz['data'])){
                $result = ['uid'=>$data2['uid']];
            }
            Db::name('member')->where('uid', $data2['uid'])->update(['last_login_time' => time(),'last_login_ip' => get_client_ip()]);

            return zy_array (true,'操作成功',$result,200 ,$isModule,$important);
        } else {
            return zy_array (false,'请求错误!','',-100,$isModule,$important);
        }

    }

    /**
     * 邮箱注册
     */
    public function emailRegister($isModule=false)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data = zy_decodeData($data,$isModule);

            // 验证 ============
            $result = $this->validate($data,'Member.emailRegister');
            if(true !== $result){
                // 验证失败 输出错误信息
                return zy_array (false,$result,'',-1,$isModule);
            }

            // 验证协议是否勾选
            if($data['agreement']!=true){
                return zy_array (false,'请勾选注册协议','',-3,$isModule);
            }

            // 验证验证码

            // 验证用户是否已存在
            if($this->verify_isEmail($data['email']) == false){
                // 验证失败 输出错误信息
                return zy_array (false,'用户已存在','',-2,$isModule);
            }
            // 验证 ============


            // 注册的数据-主表
            $info['email'] = $data['email'];
            $info['nickname'] = $data['nickname'];
            $info['password'] = cmf_password($data['password']);
            $info['username'] = cmf_random_string(8);
            $info['create_time'] = time();
            $info['last_login_time'] = time();
            $info['create_ip'] = get_client_ip();
            $info['last_login_ip'] = get_client_ip();
            $info['groupid'] = 1;


            $re = Db::name('member')->insertGetId($info);
            Db::name('member_detail')->insert(['uid'=>$re]);
            if(empty($re)){
                return zy_array (false,'注册失败!','',-4,$isModule);
            }

            //生成上下级关系
            if(!empty($fid)){
                $level['fid'] = $fid;
                $level['uid'] = $re;
                Db::name('member_level')->insert($level);
            }

            ///注册成功,获得原始股
            //获取配置表 register_integral
            $register_integral = 0;//原始股值
            $globalData = Db::name('global_config')->where('title','register_integral')->find();
            if(empty($globalData)){
                $register_integral = $globalData['register_integral'];
            }
            $result = Db::name('member')->where('uid',$re)->update(['thigh'=>$register_integral]);
            //添加记录
            if($result){
                $record['time'] = time();
                $record['uid'] = $re;
                $record['type'] = 1;
                $record['integral'] = $register_integral;
                $record['consumption'] = 0;
                $record['proportion'] = $globalData['register_integral'];
                Db::name('integral_record')->insert($record);
            }


            $result = zy_userid_jwt($re)['data'];

            return zy_array (true,'注册成功!',$result,200,$isModule);
        } else {
            return zy_array (false,'请求错误!','',-100,$isModule);
        }
    }


    /**
     * 手机注册
     */
    public function mobileRegister($isModule=false)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data = zy_decodeData($data,$isModule);

            $fid = $data['fid'];

            // 验证 ============
            $result = $this->validate($data,'Member.mobileRegister');
            if(true !== $result){
                // 验证失败 输出错误信息
                return zy_array (false,$result,'',-1,$isModule);
            }
            // 验证协议是否勾选
            if($data['agreement']!=true){
                return zy_array (false,'请勾选注册协议','',-3,$isModule);
            }

            // 验证用户是否已存在
            if($this->verify_isMobile($data['mobile']) == false){
                // 验证失败 输出错误信息
                return zy_array (false,'用户已存在','',-2,$isModule);
            }

            // 验证验证码
            $verifySms = array('mobile'=>$data['mobile'],'code'=>$data['mobile_code']);
            $message_verifySms = $this->notice_verifySms($verifySms);
            if($message_verifySms['status']=='error'){
                return zy_array (false,$message_verifySms['message'],'',-40,$isModule);
            }
            // 清空短信验证码
            $cleanCode = array('account'=>$data['mobile'],'type'=>1);
            $message_cleanCode = $this->notice_cleanCode($cleanCode);
            if($message_cleanCode['status']=='error'){
                return zy_array (false,$message_cleanCode['message'],'',-41,$isModule);
            }

            // 验证 ============


            // 注册的数据-主表
            $info['mobile'] = $data['mobile'];
            $info['nickname'] = $data['nickname'];
            $info['password'] = cmf_password($data['password']);
            $info['username'] = cmf_random_string(8);
            $info['create_time'] = time();
            $info['last_login_time'] = time();
            $info['create_ip'] = get_client_ip();
            $info['last_login_ip'] = get_client_ip();
            $info['groupid'] = 1;


            $re = Db::name('member')->insertGetId($info);
            Db::name('member_detail')->insert(['uid'=>$re]);

            if(empty($re)){
                return zy_array (false,'注册失败!','',-4,$isModule);
            }

            //生成上下级关系
            if(!empty($fid)){
                $level['fid'] = $fid;
                $level['uid'] = $re;
                Db::name('member_level')->insert($level);
            }

            ///注册成功,获得原始股
            //获取配置表 register_integral
            $register_integral = 0;//原始股值
            $globalData = Db::name('global_config')->where('title','register_integral')->find();
            if(empty($globalData)){
                $register_integral = $globalData['register_integral'];
            }
            $result = Db::name('member')->where('uid',$re)->update(['thigh'=>$register_integral]);
            //添加记录
            if($result){
                $record['time'] = time();
                $record['uid'] = $re;
                $record['type'] = 1;
                $record['integral'] = $register_integral;
                $record['consumption'] = 0;
                $record['proportion'] = $globalData['register_integral'];
                Db::name('integral_record')->insert($record);
            }


            $result = zy_userid_jwt($re)['data'];
            return zy_array (true,'注册成功!',$result,200,$isModule);
        } else {
            return zy_array (false,'请求错误!','',-100,$isModule);
        }
    }



    /**
     * 协议
     */
    public function agreement($isModule=false)
    {
        $module_info = getModuleConfig('member','config','config.json');
        $module_info = json_decode($module_info,true);
        return zy_json_echo (true,'操作成功!',$module_info['agreement'],200,$isModule);
    }


    /**
     * 个人中心
     * @return [type] [description]
     */
    public function gr_index($isModule=false)
    {
        $data = $this->request->post();
        $data = zy_decodeData($data,$isModule);

        $uid = $data['uid'];
        $data2 = Db::name('member')->where(['uid'=>$uid])->find();
        // 验证
        if(!$data2){
            return zy_array (false,'用户不存在！','',-2,$isModule);
        }

        $member_footprint = Db::name('member_footprint')->where(['uid'=>$uid])->count();
        $member_collection = Db::name('member_collection')->where(['uid'=>$uid])->count();

        $arr = [
            'footprint'=>$member_footprint,
            'collection'=>$member_collection,
        ];
        return zy_array (true,'操作成功',$arr,200 ,$isModule);
    }

    /**
     * 修改密码
     */
    public function editPassword($isModule=false)
    {
        $data = $this->request->post();
        $data = zy_decodeData($data,$isModule);

        if(empty($data['password']) || empty($data['new_password']) || empty($data['re_password']) || empty($data['uid'])){
            return zy_array (false,'参数不能为空','',-1,$isModule);
        }
        $password = $data['password'];
        $newPassword = $data['new_password'];
        $rePassword = $data['re_password'];
        $uid = $data['uid'];

        $data2 = Db::name('member')->where(['uid'=>$uid])->find();
        // 验证
        if(!$data2){
            return zy_array (false,'用户不存在！','',-2,$isModule);
        }
        $validatePassword = cmf_compare_password($password,$data2['password']);// 验证密码
        if($validatePassword==false){
            return zy_array (false,'原密码错误！','',-3,$isModule);
        }
        if($this->verify_isLock($data2['uid'])==false){
            return zy_array (false,'账号被锁定！','',-4,$isModule);
        }

        if($newPassword!=$rePassword){
            return zy_array (false,'重复密码不一致！','',-5,$isModule);
        }

        
        $info['password'] = cmf_password($newPassword);
        $result = Db::name('member')
            ->where('uid', $uid)
            ->setField('password', $info['password']);

        if(!$result){
            return zy_array (false,'操作失败','',-6 ,$isModule);
        }
        return zy_array (true,'操作成功','',200 ,$isModule);

    }




    /**
     * 修改支付密码_记得
     */
    public function editPayPassword($isModule=false)
    {
        $data = $this->request->post();
        $data = zy_decodeData($data,$isModule);

        if(empty($data['type'])){
            return zy_array (false,'类型不能为空','',-1,$isModule);
        }
        if(empty($data['uid'])){
            return zy_array (false,'用户id不能为空','',-1,$isModule);
        }
        $uid = $data['uid'];

        $data2 = Db::name('member_detail')->where(['uid'=>$uid])->find();

        if($data['type']==1){   //第一步
            if(empty($data['password'])){
                return zy_array (false,'原密码不能为空','',-1,$isModule);
            }
            $password = $data['password'];

            // 验证
            if(!$data2){
                return zy_array (false,'用户不存在！','',-2,$isModule);
            }
            $validatePassword = cmf_compare_password($password,$data2['pay_password']);// 验证密码
            if($validatePassword==false){
                return zy_array (false,'原密码错误！','',-3,$isModule);
            }
            return zy_array (true,'操作成功','',200 ,$isModule);

        }else{  //第二步
            if(empty($data['password']) || empty($data['new_password']) || empty($data['re_password']) || empty($data['uid'])){
                return zy_array (false,'参数不能为空','',-1,$isModule);
            }
            $password = $data['password'];
            $newPassword = $data['new_password'];
            $rePassword = $data['re_password'];
            $uid = $data['uid'];

            $data2 = Db::name('member_detail')->where(['uid'=>$uid])->find();
            // 验证
            if(!$data2){
                return zy_array (false,'用户不存在！','',-2,$isModule);
            }
            $validatePassword = cmf_compare_password($password,$data2['pay_password']);// 验证密码
            if($validatePassword==false){
                return zy_array (false,'原密码错误！','',-3,$isModule);
            }
            if($newPassword!=$rePassword){
                return zy_array (false,'重复密码不一致！','',-5,$isModule);
            }

            
            $info['password'] = cmf_password($newPassword);
            $result = Db::name('member_detail')
                ->where('uid', $uid)
                ->setField('pay_password', $info['password']);

            if(!$result){
                return zy_array (false,'操作失败','',-6 ,$isModule);
            }
            return zy_array (true,'操作成功','',200 ,$isModule);

        }
        return zy_array (false,'操作失败','',-100 ,$isModule);

    }


    /**
     * 忘记密码
     */
    public function forgetPassword($isModule=false)
    {
        $data = $this->request->post();
        $data = zy_decodeData($data,$isModule);

        if(empty($data['type'])){
            return zy_array (false,'类型不能为空','',-1,$isModule);
        }
        // 第一步：单纯验证短信验证码
        if($data['type']==1){
            if(empty($data['mobile']) || empty($data['mobile_code'])){
                return zy_array (false,'参数不能为空','',-2,$isModule);
            }
            //$uid = $data['uid'];
            $mobile = $data['mobile'];
            $mobileCode = $data['mobile_code'];

            // 验证此用户是否是此手机号码
            $data2 = Db::name('member')->where(['mobile'=>$mobile])->find();
            // 验证
            if(!$data2){
                return zy_array (false,'用户不存在！','',-3,$isModule);
            }

            // 验证验证码
            $verifySms = array('mobile'=>$data['mobile'],'code'=>$data['mobile_code']);
            $message_verifySms = $this->notice_verifySms($verifySms);
            if($message_verifySms['status']=='error'){
                return zy_array (false,$message_verifySms['message'],'',-40,$isModule);
            }

            return zy_array (true,'操作成功','',200 ,$isModule);

        }elseif ($data['type']==2) {   // 第二步：验证短信验证并且清空验证码
            if(empty($data['mobile']) || empty($data['mobile_code']) || empty($data['new_password']) || empty($data['re_password'])){
                return zy_array (false,'参数不能为空','',-2,$isModule);
            }
            $mobile = $data['mobile'];
            $mobileCode = $data['mobile_code'];
            $newPassword = $data['new_password'];
            $rePassword = $data['re_password'];

            if($newPassword!=$rePassword){
                return zy_array (false,'重复密码不一致！','',-5,$isModule);
            }
            // 验证此用户是否是此手机号码
            $data2 = Db::name('member')->where(['mobile'=>$mobile])->find();
            // 验证
            if(!$data2){
                return zy_array (false,'用户不存在！','',-3,$isModule);
            }

            // 验证验证码
            $verifySms = array('mobile'=>$data['mobile'],'code'=>$data['mobile_code']);
            $message_verifySms = $this->notice_verifySms($verifySms);
            if($message_verifySms['status']=='error'){
                return zy_array (false,$message_verifySms['message'],'',-40,$isModule);
            }
            // 清空短信验证码
            $cleanCode = array('account'=>$data['mobile'],'type'=>1);
            $message_cleanCode = $this->notice_cleanCode($cleanCode);
            if($message_cleanCode['status']=='error'){
                return zy_array (false,$message_cleanCode['message'],'',-41,$isModule);
            }
            

            // 修改密码
            $info['password'] = cmf_password($newPassword);
            $result = Db::name('member')
                ->where('mobile', $mobile)
                ->setField('password', $info['password']);

            if(!$result){
                return zy_array (false,'操作失败','',-6 ,$isModule);
            }
            return zy_array (true,'操作成功','',200 ,$isModule);
        }else{
            return zy_array (false,'类型错误','',-1,$isModule);
        }


    }





    /**
     * 工具菜单
     */
    public function toolsMenu($isModule=false)
    {
        $data = $this->request->get();
        if(empty($data['type'])){
            return zy_json_echo (false,'参数不能为空!','',-1,$isModule);
        }
        if($data['type']==1){
            $where = ['type'=>1,'status'=>1];
            $result = Db::name('member_app_config')->where($where)->select();
        }
        if($data['type']==2){
            $where = ['type'=>2,'status'=>1];
            $result = Db::name('member_app_config')->where($where)->select();
        }

        $result = json_encode($result);
        $result = json_decode($result,true);
        if($result){
            foreach ($result as $key => $value) {
                $result[$key]['icon'] = 'http://'.$_SERVER['HTTP_HOST'].str_replace("/public/index.php","",$_SERVER['PHP_SELF']).'/public/plugins/member/view/public/image/icon/'.$result[$key]['icon'];
            }
        }
        return zy_json_echo (true,'操作成功!',$result,200,$isModule);
    }


    /**
     * 修改支付密码_不记得
     */
    public function editNoPayPassword($isModule=false)
    {
        $data = $this->request->post();
        $data = zy_decodeData($data,$isModule);

        if(empty($data['type'])){
            return zy_array (false,'类型不能为空','',-1,$isModule);
        }
        // 第一步：单纯验证短信验证码
        if($data['type']==1){
            if(empty($data['mobile']) || empty($data['mobile_code']) || empty($data['uid'])){
                return zy_array (false,'参数不能为空','',-2,$isModule);
            }
            $uid = $data['uid'];
            $mobile = $data['mobile'];
            $mobileCode = $data['mobile_code'];

            // 验证此用户是否是此手机号码
            $data2 = Db::name('member')->where(['uid'=>$uid,'mobile'=>$mobile])->find();
            // 验证
            if(!$data2){
                return zy_array (false,'用户不存在！','',-3,$isModule);
            }

            // 验证验证码
            $verifySms = array('mobile'=>$data['mobile'],'code'=>$data['mobile_code']);
            $message_verifySms = $this->notice_verifySms($verifySms);
            if($message_verifySms['status']=='error'){
                return zy_array (false,$message_verifySms['message'],'',-40,$isModule);
            }
            

            return zy_array (true,'操作成功','',200 ,$isModule);

        }elseif ($data['type']==2) {   // 第二步：验证短信验证并且清空验证码
            if(empty($data['mobile']) || empty($data['mobile_code']) || empty($data['uid']) || empty($data['new_password']) || empty($data['re_password'])){
                return zy_array (false,'参数不能为空','',-2,$isModule);
            }
            $uid = $data['uid'];
            $mobile = $data['mobile'];
            $mobileCode = $data['mobile_code'];
            $newPassword = $data['new_password'];
            $rePassword = $data['re_password'];

            if($newPassword!=$rePassword){
                return zy_array (false,'重复密码不一致！','',-5,$isModule);
            }
            // 验证此用户是否是此手机号码
            $data2 = Db::name('member')->where(['uid'=>$uid,'mobile'=>$mobile])->find();
            // 验证
            if(!$data2){
                return zy_array (false,'用户不存在！','',-3,$isModule);
            }

            // 验证验证码
            $verifySms = array('mobile'=>$data['mobile'],'code'=>$data['mobile_code']);
            $message_verifySms = $this->notice_verifySms($verifySms);
            if($message_verifySms['status']=='error'){
                return zy_array (false,$message_verifySms['message'],'',-40,$isModule);
            }
            // 清空短信验证码
            $cleanCode = array('account'=>$data['mobile'],'type'=>1);
            $message_cleanCode = $this->notice_cleanCode($cleanCode);
            if($message_cleanCode['status']=='error'){
                return zy_array (false,$message_cleanCode['message'],'',-41,$isModule);
            }
            

            // 修改密码
            $info['password'] = cmf_password($newPassword);
            $result = Db::name('member_detail')
                ->where('uid', $uid)
                ->setField('pay_password', $info['password']);

            if(!$result){
                return zy_array (false,'操作失败','',-6 ,$isModule);
            }
            return zy_array (true,'操作成功','',200 ,$isModule);
        }else{
            return zy_array (false,'类型错误','',-1,$isModule);
        }


    }




    /**
     * 修改手机号码
     */
    public function editMobile($isModule=false)
    {
        $data = $this->request->post();
        $data = zy_decodeData($data,$isModule);
        if(empty($data['type'])){
            return zy_array (false,'类型不能为空','',-1,$isModule);
        }

        if($data['type']==1){
            if(empty($data['mobile']) || empty($data['mobile_code']) || empty($data['uid'])){
                return zy_array (false,'参数不能为空','',-2,$isModule);
            }
            $uid = $data['uid'];
            $mobile = $data['mobile'];
            $mobileCode = $data['mobile_code'];

            $data2 = Db::name('member')->where(['uid'=>$uid,'mobile'=>$mobile])->find();

            if(empty($data2)){
                return zy_array (false,'操作失败','',-3,$isModule);
            }

            // 验证验证码是否正确
            $verifySms = array('mobile'=>$data['mobile'],'code'=>$data['mobile_code']);
            $message_verifySms = $this->notice_verifySms($verifySms);
            if($message_verifySms['status']=='error'){
                return zy_array (false,$message_verifySms['message'],'',-40,$isModule);
            }

            return zy_array (true,'操作成功','',200 ,$isModule);
        }elseif ($data['type']==2) {
            if(empty($data['mobile']) || empty($data['mobile_code']) || empty($data['new_mobile']) || empty($data['new_mobile_code']) || empty($data['uid'])){
                return zy_array (false,'参数不能为空','',-2,$isModule);
            }

            $uid = $data['uid'];
            $mobile = $data['mobile'];
            $mobileCode = $data['mobile_code'];
            $newMobile = $data['new_mobile'];
            $newMobileCode = $data['new_mobile_code'];

            // 验证短信验证码，并清空验证码
            $data2 = Db::name('member')->where(['uid'=>$uid,'mobile'=>$mobile])->find();
            if(empty($data2)){
                return zy_array (false,'操作失败','',-3,$isModule);
            }

            // 验证验证码是否正确，并清空
            // 验证验证码
            $verifySms = array('mobile'=>$data['mobile'],'code'=>$data['mobile_code']);
            $message_verifySms = $this->notice_verifySms($verifySms);
            if($message_verifySms['status']=='error'){
                return zy_array (false,$message_verifySms['message'],'',-40,$isModule);
            }
            // 清空短信验证码
            $cleanCode = array('account'=>$data['mobile'],'type'=>1);
            $message_cleanCode = $this->notice_cleanCode($cleanCode);
            if($message_cleanCode['status']=='error'){
                return zy_array (false,$message_cleanCode['message'],'',-41,$isModule);
            }


            if($this->verify_isMobile($newMobile)==false){
                return zy_array (false,'该手机号已存在','',-2,$isModule);
            }

            // 验证此用户是否是此手机号码
            $data2 = Db::name('member')->where(['uid'=>$uid])->setField('mobile', $newMobile);
            return zy_array (true,'操作成功','',200 ,$isModule);
        }
        return zy_array (false,'类型错误','',-1,$isModule);

    }




    /**
     * 商品收藏
     */
    public function collection($isModule=false)
    {
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);


        if(empty($data['uid'])){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }
        $paginate=empty($data['paginate']) ? 20 : $data['paginate'];
        $search=empty($data['search']) ? '' : $data['search'];


        $where = 'uid='.$data['uid'];
        $where .= " and title like '%".$search."%'";
        $field = 'thumb,title,amount,goods_id,type';
        $data = Db::name("member_collection")
            ->where($where)
            ->order("create_time DESC")
            ->field($field)
            ->paginate($paginate);

        $data = json_encode($data);
        $data = json_decode($data,true);

        return zy_array (true,'操作成功',$data,200 ,$isModule);
    }



    /**
     * 收藏操作
     */
    public function collectionOperation($isModule=false)
    {
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);

        if(empty($data['uid']) || empty($data['goods_id'])){
            return zy_array (false,'参数不能为空','',-1 ,$isModule);
        }

        $uid = $data['uid'];
        $goods_id = $data['goods_id'];
        //先判断是否已经存在此用户/此商品了
        //不存在那么就增加
        $goods_goodsInfo = $this->goods_goodsInfo($goods_id);
        if($goods_goodsInfo['status']=='error'){
            return zy_array (false,$goods_goodsInfo['message'],'',-41,$isModule);
        }

        //存在那么久操作错误
        $data2 = Db::name('member_collection')->where(['uid'=>$uid,'goods_id'=>$goods_id])->find();

        if($data2){
            Db::name('member_collection')->where(['uid'=>$uid,'goods_id'=>$goods_id])->delete();
            return zy_array (true,'操作成功！','',200 ,$isModule);
        }

        $info = [
            'uid'=>$uid,
            'title'=>$goods_goodsInfo['data']['goodsname'],
            'thumb'=>$goods_goodsInfo['data']['goodsimg'],
            'amount'=>$goods_goodsInfo['data']['shopprice'],
            'type'=>1,
            'create_time'=>time(),
            'goods_id'=>$goods_id,
        ];

        $re = Db::name('member_collection')->insertGetId($info);
        if(!empty($re)){
            return zy_array (false,'操作失败!','',-100,$isModule);
        }
        return zy_array (true,'操作成功!','',200,$isModule);
    }


    /**
     * 验证改产品是否收藏过了
     */
    public function isCollect($uid,$gid,$isModule=false)
    {
        if(empty($uid) || empty($gid)){
            return zy_array (false,'参数不能为空','',-1 ,$isModule);
        }

        $data2 = Db::name('member_collection')->where(['uid'=>$uid,'goods_id'=>$gid])->find();
        if(empty($data2)){
            return zy_array (true,'未收藏',['is'=>1],200 ,$isModule);
        }else{
            return zy_array (true,'已收藏',['is'=>2],200 ,$isModule);
        }

    }




    /**
     * 足迹
     */
    public function footprint($isModule=false)
    {
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);


        if(empty($data['uid'])){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }

        $uid = $data['uid'];
        $date= strtotime("-1 months",time());       //前个月的时间戳
        $where = 'uid='.$uid.' AND create_time>='.$date;
        $field = 'id,footprint_time,create_time';
        $info = Db::name('member_footprint')->where($where)->field($field)->select();
        $info = json_encode($info,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        $info = json_decode($info,true);
        if(empty($info)){
            return zy_array (false,'用户没有数据','',-10 ,$isModule);
        }

        $item=array();
        $info_unique =$this->assoc_unique($info ,'footprint_time');

        foreach ($info_unique as $key => $value) {
            $item[$key]['create_time']=date('Y-m-d',$value['create_time']);
            $where1 = 'uid='.$uid.' AND create_time>='.$date.' and footprint_time='.$value['footprint_time'];

            $item[$key]['data'] = Db::name('member_footprint')->where($where1)->field('id,thumb,title,amount,goods_id')->select();
        }
        return zy_array (true,'操作成功!',$item,200,$isModule);
    }


    /**
     * 足迹操作
     */
    public function footprintOperation($uid = null ,$id = null ,$type = null ,$goods_id = null ,$isModule=false)
    {
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);

        $uid = isset( $data['uid'] ) ? $data['uid'] : $uid ;
        $id = isset( $data['id'] ) ? $data['id'] : $id ;
        $type = isset( $data['type'] ) ? $data['type'] : $type ;
        $goods_id = isset( $data['goods_id'] ) ? $data['goods_id'] : $goods_id ;


        if(empty($data['uid'])){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }

        if($type==1){    //添加操作

            $goods_goodsInfo = $this->goods_goodsInfo($goods_id);
            if($goods_goodsInfo['status']=='error'){
                return zy_array (false,$goods_goodsInfo['message'],'',-41,$isModule);
            }

            $time = time();
            //删除的是当天的
            $del_where = "goods_id = ".$goods_id." AND uid=".$uid;
            $start_addtime = strtotime(date("Y-m-d 00:00:00"));
            $end_addtime = strtotime(date("Y-m-d 23:59:59"));
            $del_where .= " and create_time >= '".$start_addtime."'";
            $del_where .= " and create_time <= '".$end_addtime."'";


            Db::name('member_footprint')->where($del_where)->delete();

            $footprint_time = strtotime(date('y-m-d 01:00:00',$time));
            $arr = [
                'uid'=>$uid,
                'goods_id'=>$goods_goodsInfo['data']['id'],
                'title'=>$goods_goodsInfo['data']['goodsname'],
                'thumb'=>$goods_goodsInfo['data']['goodsimg'],
                'amount'=>$goods_goodsInfo['data']['shopprice'],
                'create_time'=>$time,
                'footprint_time'=>$footprint_time,
            ];

            $re = Db::name('member_footprint')->insertGetId($arr);
            return zy_array (true,'添加成功!','',200,$isModule);

        }elseif($type==2){  //删除操作
            Db::name('member_footprint')->where(['uid'=>$uid,'id'=>$id])->delete();
            return zy_array (true,'删除成功！','',200 ,$isModule);
        }
        return zy_array (false,'操作失败！','',-100 ,$isModule);

    }

    /**
     * 图片上传
     */
    public function single_uploadFile($isModule=false){
        $data=$this->request->post();
        $data['uid'] = zy_userid_jwt($data['uid'],'de')['data'];
        
        if(empty($data['types']) || empty($data['types']) || empty($data['uid'])){
            return zy_array (false,'参数不能为空！','',-1 ,$isModule);
        }


        $types = $data['types'];
        $file = $data['file'];

        switch ($types) {
            case '1':   //身份证图片
                $imgurl2 = $this->base64_image_content('header',$file);
 
        
                if($imgurl2==false){
                    return zy_array (false,'操作失败！',$imgurl2,-2 ,$isModule);
                }

                Db::name('member')
                    ->where('uid', $data['uid'])
                    ->setField('avatar', $imgurl2);

                return zy_array (true,'操作成功！','http://'.$_SERVER['HTTP_HOST'].str_replace("/public/index.php","",$_SERVER['PHP_SELF']).$imgurl2,200 ,$isModule);

                break;

            case '2':   //工牌/名片2
                $imgurl2 = $this->ploadfile_user('manager_proof',$file);
                break;

            case '3':   //营业执照
                $imgurl2 = $this->uploadfile_user('agent_business',$file);
                break;

            default:
                //返回
                return zy_array (false,'操作失败！','',-200 ,$isModule);
                break;
        }


    }


    /**
     * 验证手机号是否已存在
     * @param  [type]  $mobile [用户手机]
     */
    private function verify_isMobile($mobile){
        if(empty($mobile)){
            return false;
        }
        $data = Db::name('member')
                    ->where(['mobile'=>$mobile])
                    ->field('uid')
                    ->find();

        return empty($data);
    }

    /**
     * 验证邮箱是否已存在
     * @param  [type]  $email [用户邮箱]
     */
    private function verify_isEmail($email){
        if(empty($email)){
            return false;
        }
        $data = Db::name('member')
                    ->where(['email'=>$email])
                    ->field('uid')
                    ->find();
        return empty($data);
    }

    /**
     * 验证是否锁定
     * @param  [type]  $uid [用户id]
     */
    private function verify_isLock($uid){
        if(empty($uid)){
            return false;
        }
        $data2 = Db::name('member')
                    ->where(['uid'=>$uid])
                    ->field('uid,islock')
                    ->find();

        if(empty($data2)){
            return false;
        }   
        if($data2['islock']==2){
            return false;
        }
        return true;
    }



    private function assoc_unique($arr, $key) {
        $tmp_arr = array();
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                unset($arr[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        sort($arr); //sort函数对数组进行排序
        return $arr;
    }





    /*  base64格式编码转换为图片并保存对应文件夹 */
    function base64_image_content($path,$base64_image_content){
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];

            $file_name1 = 'public/plugins/member/view/public/image/'.$path."/".date('Ymd',time())."/";
            $new_file = ROOT_PATH.$file_name1;

            if(!file_exists($new_file)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0777,true);
            }
            $wenjianNAME = time().cmf_random_string();
            $wenjian = $wenjianNAME.".{$type}";

            $new_file_path = $new_file.$wenjian;



            if (file_put_contents($new_file_path, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                // 生成或略图
                ini_set('memory_limit', '128M');
                $image = \think\Image::open($new_file_path);
                // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
                $image->thumb(150, 150)->save($new_file.$wenjianNAME.'_thumb_150'.".{$type}");

                $new_file_path2 = $file_name1.$wenjianNAME.'_thumb_150'.".{$type}";
                return '/'.$new_file_path2;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }




    // ====================== 调用其他模块操作

    /**
     * 消息模块-验证短信验证码是否正确
     * @param  [type]  $data     [description]
     */
    private function notice_verifySms($data){
        $symbol ='member';
        $id = 'notice_one';
        $param = ['data'=>$data,'isModule'=>true];
        $return = getModuleApiData( $symbol, $id, $param);
        return $return;
    }


    /**
     * 消息模块-清空验证码
     * @param  [type]  $data     [description]
     */
    private function notice_cleanCode($data){
        $symbol ='member';
        $id = 'notice_two';
        $param = ['data'=>$data,'isModule'=>true];
        $return = getModuleApiData( $symbol, $id, $param);
        return $return;
    }

    // ====================== 调用其他模块操作




}
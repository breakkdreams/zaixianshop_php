<?php
namespace plugins\fund\controller;

/**
 * @Author:项腾飞
 * @Date:   2019-10-14 09:03:19
 * @Last Modified by:   项腾飞
 * @Last Modified time: 2019-10-14 09:03:19
 */
use cmf\controller\PluginRestBaseController;//引用插件基类
use plugins\demo\Model\PluginApiIndexModel;
use plugins\fund\controller\bankList;
use think\Db;


/**
 * api控制器
 */
class ApiIndexController extends PluginRestBaseController
{   


    /**
    * 获取账号信息  我的账户
    * @param  String uid  用户id
    * @param  String 101 用户id不存在 
    * @return $da为返回的数据
    */
    public function index( $isModule = false ){
      $data = $this->request->param();
      $data = zy_decodeData($data,$isModule);      
      if(!isset($data['uid']) || empty($data['uid'])){
          return zy_array(false,'没有传值',null,101,$isModule);
      }
      //获取站点信息
      $res  = $this->onterface('geturl',['data'=>null,'isModule'=>true]);

      $basic   = $res['data'];
        
      $url_img = $basic['basic']['site_domain'];

      $da['content']  = Db::name("fund_bankcard")->where('del','1')->where('uid',$data['uid'])->where('tid',3)->order('is_first','asc')->select();

      foreach( $da['content'] as $key=>$value ){
          $bank  = Db::name("fund_bank")->where('card_bin',$value['bank_bin'])->find();
          if( empty($bank['thumb']) ){
            $value['logo'] =$url_img.'/plugins/fund/view/upload/zftype/银联.png';
          }else{
              $value['logo'] = $url_img.$bank['thumb'];
          }
          
          $value['bank'] = $bank['bank'];
          $value['card_type'] = $bank['card_type'];
          $value['desc']  = $bank['desc'];
          $da['content'][$key]=$value;
      }

      $weixin = Db::name("fund_bankcard")->where('del','1')->where('uid',$data['uid'])->where('tid',2)->find();

      if( $weixin ){
        $da['weixin']='2';
        $da['weixin_id'] = $weixin['id'];
        $da['weixin_name'] = $weixin['accountname'];
        $da['weixin_account'] = $weixin['account'];
      }else{
        $da['weixin']= '1';
        $da['weixin_id'] = intval('0');
        $da['weixin_name'] = '';
        $da['weixin_account']='';
      }
      
      $zhifubao = Db::name("fund_bankcard")->where('del','1')->where('uid',$data['uid'])->where('tid',1)->find();
      
      if( $zhifubao ){
        $da['zhifubao']='2';
        $da['zhifubao_id'] = $zhifubao['id'];
        $da['zhifubao_name'] = $zhifubao['accountname'];
        $da['zhifubao_account'] = $zhifubao['account'];
      }else{
        $da['zhifubao']='1';   
        $da['zhifubao_id'] = intval('0');
        $da['zhifubao_name'] = '';
        $da['zhifubao_account']='';
      }

      return zy_array(true,'获取成功',$da,200,$isModule);
    }



    
    /**
    * 添加我的账户
    * @param  String tid  1为支付宝  2为微信  3为银行卡
    * @param  String uid  用户id
    * @param  String phone 用户手机号
    * @param  String account  用户账号
    * @param  String accountname 用户账户名
    * @param  String bank_type 1为手动  2为自动  3为全部
    * @param  String 101 用户id不存在   108没有上传tid
    * @return $da为返回的数据
    */
    public function addaccount( $isModule = false ){
        $data = $this->request->param();
        $data = zy_decodeData($data,$isModule);        
        $banklist = new bankList();
        $bank = $banklist->bankList;
        if( !isset($data['tid']) || empty($data['tid']) ){
            return zy_array(false,'没有传类型',null,108,$isModule);
        }
        if(!isset($data['uid']) || empty($data['uid'])){
            return zy_array(false,'没有上传用户信息',null,101,$isModule);
        }
        ///获取用户信息
        $res  = $this->onterface('getUserinfo',['uid'=>$data['uid'],'field'=>'',true]);
        if( $res['code']!='200' ){
            return zy_array(false,'用户id不存在',null,103,$isModule); 
        }

        $da   = $res['data'];
        $con = ['username'=>$da['username'],'nickname'=>$da['nickname'],'tid'=>$data['tid'],'uid'=>$data['uid'],'phone'=>$da['mobile']];
        $con['account'] = $data['account'];
        $con['accountname'] = $data['accountname'];       
        $con['is_first']  = '2';
        $con['addtime']  = time();      
        if($data['tid']==3){
          $con['tphone'] = $data['tphone'];

          if(strlen($data['account'])>'19'  ||  strlen($data['account'])<'16'){
            return zy_array(false,'该卡号输入错误',null,109,$isModule);
          }
       
          $bin = $this->bankInfo($data['account'],$bank);

          if( $data['bank_type']=='2' ){
            //获取银行信息
            $res  = $this->onterface('getbankOk',[true]);
            $bankok = $res['data'];           
            $bankname = Db::name("fund_bank")->where('card_bin',$bin)->find();
            $cn ='0';
            foreach( $bankok as $ba ){
                if( $ba['name']==$bankname['bank'] ){
                    $cn+='1';
                }
            }            
            if( $cn=='0' ){
                return zy_array(false,'该银行卡微信暂未录入',null,178,$isModule);
            }
          }
     
          if($bin=='1'){
            return zy_array(false,'该卡号信息暂未录入',null,109,$isModule);
          }       
          $con['bank_bin'] = $bin;          
        }
        $insert = Db::name("fund_bankcard")->insertGetID($con);

        if($insert){
            return zy_array(true,'添加成功','',200,$isModule);
        }else{
            return zy_array(false,'添加失败',null,110,$isModule);
        }


        
    }

    

    /**
    * 修改我的账户
    * @param  String id  账户id
    * @param  String account  用户账号
    * @param  String accountname 用户账户名
    * @return $da为返回的数据
    */

    public function updateamount(  $isModule = false  ){
        $data = $this->request->param();

        $data = zy_decodeData($data,$isModule);

        if(!isset($data['id']) || empty($data['id'])){
            return zy_array(false,'账户id不存在',null,103,$isModule);
        }
        
        $da   = Db::name("fund_bankcard")->where('del','1')->where('id',$data['id'])->update(['account'=>$data['account'],'accountname'=>$data['accountname']]);

        if( $da ){
            return zy_array(true,'修改成功','',200,$isModule); 
        } else{
            return zy_array(false,'修改失败',null,105,$isModule);
        }
    }











    /**
    * 删除我的账户(银行卡)
    * @param  String id  账号id
    * @param  String 101 账号id无上传
    * @param  String 102 账号id不存在
    * @return 1
    */
    public function delaccount( $isModule = false ){
      $data = $this->request->param();

      $data = zy_decodeData($data,$isModule);
      
      if(!isset($data['id']) || empty($data['id'])){
        return zy_array(false,'没有传值',null,101,$isModule);
      }

      $da  = Db::name("fund_bankcard")->where('id',$data['id'])->update(['del'=>'2']);

      if($da){
        return zy_array(true,'删除成功','',200,$isModule); 
      }else{
        return zy_array(false,'删除失败',null,102,$isModule); 
      }
    }





    /**
    * 设为默认账户
    * @param  String uid  账号uid
    * @param  String id  账号id
    * @param  String 101 账号uid无上传
    * @param  String 102 账号uid不存在
    * @return 1
    */
    public function defaultaccount( $isModule = false ){
        $data = $this->request->param();

        $data = zy_decodeData($data,$isModule);

        if(empty($data['id']) || empty($data['uid'])){
            return zy_array(false,'没有传值',null,101,$isModule);
        }

        $da = Db::name("fund_bankcard")->where('del','1')->where('id',$data['id'])->update(['is_first'=>'1']);

        if($da){
            $up = Db::name("fund_bankcard")->where('uid',$data['uid'])->where('id','<>',$data['id'])->update(['is_first'=>'2']);
            return zy_array(true,'修改成功','',200,$isModule);
        }else{
            return zy_array(false,'修改失败',null,102,$isModule); 
        }
    }




    
   
    



    /**********************************  充值        ********************************************* */
    
    /**
    * 添加充值信息到数据库
    * @param  String tid  1为支付宝  2为微信  3为银行卡
    * @param  String uid  用户id
    * @param  String title  标题
    * @param  String amount 充值金额
    * @param  String bid 卡号id
    * @param  String 101 用户id不存在   108没有上传tid
    * @return $da为返回的数据
    */
    public function addamount( $isModule = false ){
        $data = $this->request->param();
        $data = zy_decodeData($data,$isModule);
        if(!isset($data['tid']) || empty($data['tid'])){
            return zy_array(false,'没有传类型',null,108,$isModule,true);
        }
        if(!isset($data['title']) || empty($data['title'])){
            return zy_array(false,'没有标题',null,109,$isModule,true);
        }
        if(!isset($data['uid']) || empty($data['uid'])){
            return zy_array(false,'没有传值',null,101,$isModule,true);
        }
        //获取用户信息
        $res  = $this->onterface('getUserinfo',['uid'=>$data['uid'],'field'=>'',true]);
        if( $res['code']!='200' ){
            return zy_array(false,'用户id不存在',null,103,$isModule,true); 
        }
        $user   = $res['data'];

        if( !empty($data['bid']) ){
            $bank  =  Db::name("fund_bankcard")->where('id',$data['bid'])->find();
        }else{
            $bank  =  Db::name("fund_bankcard")->where('uid',$data['uid'])->where('tid',$data['tid'])->find();
        }

        if( !isset($bank) || empty($bank) ){
            if( $data['tid']=='1' ){
                $vxzf = '支付宝';
            }else if( $data['tid']=='2' ){
                $vxzf = '微信'; 
            }else{
                $vxzf = '';
            }
            return zy_json_echo(false,$vxzf.'未绑定',null,109,$isModule,true);
        }

        $da = ['type'=>$data['tid'],'uid'=>$data['uid'],'username'=>$user['username'],'nickname'=>$user['nickname'],'phone'=>$user['mobile'],'status'=>'1','addtime'=>time(),'amount'=>$data['amount'],'title'=>$data['title']];

        $da['trade_sn']  = time().rand(10000,99999);
        $da['bid'] =    $bank['id'];
        $trade = $da['trade_sn'];
        $insert = Db::name("fund_pay_record")->insertGetID($da);
        
        //获取站点信息
        $res  = $this->onterface('geturl',['data'=>null,'isModule'=>true]);
        $basic   = $res['data'];       
        $url_img = $basic['basic']['site_domain'];
        
        if( $data['tid']=='1' ){
            $url =  $url_img."/plugin/ali_pay/api_index/wap?order_sn=".$trade."&money=".$data['amount']."&title=充值订单&module=fund&type=1&return_url=/Fund_wallet";
            dump($url);exit;
            return zy_array(true,'添加成功',$url,200,$isModule,true);
        }else if( $data['tid']=='2' ){
            //获取支付信息
            $res  = $this->onterface('wheachth5',['uid'=>$data['uid'],'order_sn'=>$trade,'money'=>$data['amount'],'title'=>'充值订单','module'=>'fund','type'=>'1','isModule'=>true]);

            if( $res['code']=='200' ){
                $url = $res['data']['mweb_url'];
                return zy_array(true,'添加成功',$url,200,$isModule,true);
            }else{
                return zy_array(false,$res['message'],null,113,$isModule,true);
            }
    
        }
        return zy_array(false,'添加失败',null,113,$isModule,true);
    }




        /***************************************   微信支付   支付宝支付   回调       ************************************ */






    /**
     * 微信支付（回调）
     * out_trade_no  商户单号
     * transaction_id  交易单号
     */
    public function wechat_notify($out_trade_no,$transaction_id,$type){
        switch($type){
            //添加余额
            case 1:$da = $this->addsuccess($out_trade_no,$transaction_id,true);break; 
            //添加支付商品
            case 2:$da = $this->paysuccess($out_trade_no,$transaction_id,true);break; 
            default:$da = ['code'=>'110'];break;
        }
              
        if($da['code']=='200'){
            return zy_array(true,'操作成功！','',200,true);
        }else{
            // 失败返回
            return zy_array(false,'失败',null,$da['message'],true); 
        }
   
    }



    /**
     * 支付宝支付（回调）
     * out_trade_no  商户单号
     * transaction_id  交易单号
     */
    public function ali_notify($out_trade_no,$transaction_id,$type){
        switch($type){
            //添加余额
            case 1:$da = $this->addsuccess($out_trade_no,$transaction_id,true);break; 
            //添加支付商品
            case 2:$da = $this->paysuccess($out_trade_no,$transaction_id,true);break; 
            default:$da = ['code'=>'110'];break;
        }  
        if($da['code']=='200'){
            return zy_array(true,'操作成功！','',200,true);
        }else{
            return zy_array(false,$da['message'],null,$da['code'],true); 
        }
            
   
    }


  






    /**
    *  充值余额成功
    * @param  String trade_sn  订单号
    * @param  String transaction_id  商户订单号
    * @return $da为返回的数据
    */
    public function addsuccess($trade_sn=null,$transaction_id=null,$isModule=false){
        if(empty($trade_sn)){
           return zy_array(false,'没有上传订单号',null,108,$isModule);
        }
       
        if(!isset($transaction_id) || empty($transaction_id)){
            return zy_array(false,'没有上传商户订单号',null,124,$isModule);
        }
       
        $type  = Db::name("fund_pay_record")->where('trade_sn',$trade_sn)->find();
        
        if( $type ){
            //修改用户余额
            $res  = $this->onterface('adopt',['module'=>'fund','uid'=>$type['uid'],'amount'=>$type['amount'],'type'=>'1',true]); 
            if($res['code']!='200'){
                return zy_array(false,'修改用户余额失败',null,122,$isModule);
            }
            $da  = Db::name("fund_pay_record")->where('trade_sn',$trade_sn)->update(['status'=>'2','uptime'=>time(),'transaction_id'=>$transaction_id]);
            if($da){
                return zy_array(true,'充值成功','',200,$isModule);
            }else{
                return zy_array(false,'充值记录修改失败',null,122,$isModule);
            }           
        }
        return zy_array(false,'订单号不存在',null,123,$isModule);       
    }




    /**
    *  充值余额失败
    * @param  String trade_sn  订单号
    * @return $da为返回的数据
    */
    public function adderror( $isModule = false ){
        $data = $this->request->param();

        $data = zy_decodeData($data,$isModule);

        if(empty($data['trade_sn'])){
           return zy_array(false,'没有上传订单号',null,108,$isModule);
        }

        $da  = Db::name("fund_pay_record")->where('trade_sn',$data['trade_sn'])->update(['status'=>'3','uptime'=>time()]);

        if($da){
            return zy_array(true,'充值失败','',200,$isModule);
        }else{
            return zy_array(false,'发生错误',null,123,$isModule);
        }
    }




      /**
     * 支付成功
     * @param  String uid  用户id
     * @param  String uid  用户id
     * @param  String uid  用户id 
     * @return $da为返回的数据
     */

    public function paysuccess($trade_sn=null,$transaction_id=null,$isModule=false){
        if(empty($trade_sn) || empty($transaction_id)){
            return zy_array(false,'没有上传必要参数',null,106,$isModule);
        }

        $da = Db::name("fund_zhifu_record")->where('trade_sn',$trade_sn)->find();

        if( empty($da) ){
            return zy_array(false,'参数上传错误',null,107,$isModule); 
        }
        
        $sid  =  explode(',',$da['sid']);  
        foreach($sid as $k=>$value){
        //修改订单  
        $symbol = 'fund';  
        $id     = 'uporder';
        $param = ['out_trade_no'=>$value,'transaction_id'=>$transaction_id,'pay_type'=>$da['type'],true];
        $res  = getModuleApiData($symbol,$id,$param);                
        }   
   
        $da = Db::name("fund_zhifu_record")->where('trade_sn',$trade_sn)->update(['status'=>'2','uptime'=>time(),'transaction_id'=>$transaction_id]);

        if($da){
            return zy_array(true,'修改成功','',200,true);
        }else{
            return zy_array(false,'修改失败',null,125,true);
        }
            

     }



    
    


/*************************************   提现       ************************************************** */

    /**
    * 添加提现信息到数据库
    * @param  String tid  1为支付宝  2为微信  3为银行卡
    * @param  String bid  银行卡id  没有卡号可不传
    * @param  String uid  用户id
    * @param  String amount 充值金额
    * @param  String 101 用户id不存在   108没有上传tid
    * @return $da为返回的数据
    */
    public function withdraw( $isModule = false ){
        $data = $this->request->param();       
        $data = zy_decodeData($data,$isModule);
        if(!isset($data['tid']) || empty($data['tid'])){
            return zy_array(false,'没有传类型',null,108,$isModule,true);
        }
        if(!isset($data['uid']) || empty($data['uid'])){
            return zy_array(false,'没有传值',null,101,$isModule,true);
        }
        //查看用户密码是否正确
        $res  = $this->onterface('getpassword',['uid'=>$data['uid'],'password'=>$data['password'],true]); 
        if($res['code']!='200'){          
            return zy_array(false,'密码错误',null,108,$isModule,true);
        }
        //获取用户信息
        $res  = $this->onterface('getUserinfo',['uid'=>$data['uid'],'field'=>'',true]); 
        if( $res['code']!='200' ){
            return zy_array(false,'用户id不存在',null,103,$isModule,true); 
        }
        $user   = $res['data'];
        if($data['amount']>$user['amount']){
            return zy_array(false,'您可提现金额已超标',null,135,$isModule,true);
        }       
        if( !empty($data['bid']) ){
            $bank  =  Db::name("fund_bankcard")->where('id',$data['bid'])->find();
        }else{
            $bank  =  Db::name("fund_bankcard")->where('uid',$data['uid'])->where('tid',$data['tid'])->find();
        }
        if(!isset($bank)  ||  empty($bank)){
            if( $data['tid']=='1' ){
                $vxzf = '支付宝';
            }else if( $data['tid']=='2' ){
                $vxzf = '微信'; 
            }else{
                $vxzf = '';
            }
            return zy_json_echo(false,$vxzf.'未绑定',null,109,$isModule,true);
        }
        $da = ['type'=>$data['tid'],'uid'=>$data['uid'],'username'=>$user['username'],'nickname'=>$user['nickname'],'phone'=>$user['mobile'],'addtime'=>time(),'amount'=>$data['amount']];
        $da['account'] = $bank['account'];
        $da['accountname'] = $bank['accountname'];
        $da['trade_sn']  = time().rand(10000,99999);
        $trade = $da['trade_sn'];
        //操作用户余额  并冻结
        $res  = $this->onterface('upusermoney',['module'=>'fund','uid'=>$data['uid'],'amount'=>$data['amount'],'type'=>1,true]); 
        if( $res['code']!='200' ){
            return zy_array(false,'余额操作失败',null,106,$isModule,true); 
        }       
        $insert = Db::name("fund_tx_record")->insertGetID($da);
        if($insert){
            return zy_array(true,'添加成功',$trade,200,$isModule,true);
        }else{
            //操作用户余额  并解冻
            $res  = $this->onterface('upusermoney',['module'=>'fund','uid'=>$data['uid'],'amount'=>$data['amount'],'type'=>'3',true]); 
            if( $res['code']!='200' ){
                return zy_array(false,'解冻操作失败',null,106,$isModule,true); 
            }
            return zy_array(false,'添加失败',null,113,$isModule,true);
        }


    }







 
    


 /***************************************   支付    ****************************************************** */   

    /**
     * 进行支付
     * @param  String uid  用户id
     * @param  String sid  订单号
     * @param  String title  标题

     * @param  String tid  1为支付宝  2为微信  3为银行卡  4为余额支付
     * @param  String password  余额支付时使用 密码
     * @return $da为返回的数据
     */
    public function addpayment( $isModule = false ){
        $data = $this->request->param();
        $data = zy_decodeData($data,$isModule);
        if(empty($data['tid'])  ||  empty($data['uid'])){
            return zy_array(false,'用户id没上传',null,101,$isModule,true);
        }
        if(empty($data['title'])  ||    empty($data['sid'])){
            return zy_array(false,'内容没上传',null,133,$isModule,true);
        }
        //获取订单信息
        $res  = $this->onterface('ordermoney',['ordersn'=>$data['sid'],'isModule'=>true]);
        $money = $res['data'];
        
        //获取用户信息
        $res  = $this->onterface('getUserinfo',['uid'=>$data['uid'],'field'=>'',true]); 
        if( $res['code']!='200' ){
            return zy_array(false,'用户id不存在',null,103,$isModule,true); 
        }
        $user   = $res['data'];    
        $da = ['uid'=>$data['uid'],'username'=>$user['username'],'nickname'=>$user['nickname'],'phone'=>$user['mobile'],'type'=>$data['tid'],'amount'=>$money,'addtime'=>time(),'status'=>'1','sid'=>$data['sid'],'title'=>$data['title']];
        $da['trade_sn']  = time().rand(10000,99999);
        if(!empty($data['bid'])){
            $da['bid'] = $data['bid'];
        }
        $trade = $da['trade_sn'];
        $insert = Db::name("fund_zhifu_record")->insertGetID($da);
        if(!$insert){
            return zy_array(false,'添加失败',null,108,$isModule,true);
        }
        //获取站点信息
        $res  = $this->onterface('geturl',['data'=>null,'isModule'=>true]);
        $basic   = $res['data'];       
        $url_img = $basic['basic']['site_domain'];
        if( $data['tid']=='1' ){
            $url =  $url_img."/plugin/ali_pay/api_index/wap?order_sn=".$trade."&money=".$money."&title=支付订单&module=fund&type=2&return_url=/Fund_wallet";
            return zy_array(true,'添加成功',$url,200,$isModule,true);
        }else if( $data['tid']=='2' ){
            //获取支付信息
            $res  = $this->onterface('wheachth5',['uid'=>$data['uid'],'order_sn'=>$trade,'money'=>$money,'title'=>'支付订单','module'=>'fund','type'=>'2','isModule'=>true]);
            if( $res['code']=='200' ){
                $url = $res['data']['mweb_url'];
                return zy_array(true,'添加成功',$url,200,$isModule,true);
            }else{
                return zy_array(false,$res['message'],null,113,$isModule,true);
            }
    
        }else if( $data['tid']=='4' ){
            $balance =  $this->balance($trade,$data['password'],true);
            if( $balance['code']=='200' ){
                return zy_array(true,'购买成功','',200,$isModule,true); 
            }else{
                return zy_array(false,$balance['message'],null,105,$isModule,true);
            }

        }

        return zy_array(false,'上传参数错误',null,107,$isModule,true);
    } 

     /************************************    余额支付    ******************************************** */

    /**
     * 余额支付
     * out_trade_no  商户单号
     * password      密码
     */
    public function balance($out_trade_no,$password,$isModule = false){
        $data = $this->request->param();
        if(empty($out_trade_no)){
            return zy_array(false,'没有上传单号或金额',null,105,$isModule);
        }
        $money = Db::name("fund_zhifu_record")->where('trade_sn',$out_trade_no)->find();
        //用户支付密码是否正确  
        $res  = $this->onterface('getpassword',['uid'=>$money['uid'],'password'=>$password,true]); 
        if($res['code']!='200'){ 
            return zy_array(false,'密码上传错误',null,108,$isModule);
        }
        //用户余额添加或减少
        $res  = $this->onterface('getUserinfo',['uid'=>$money['uid'],'field'=>'',true]); 
        if( $res['code']!='200' ){
            return zy_array(false,'用户id不存在',null,103,$isModule); 
        }
        $user   = $res['data']; 
        if($money['amount']>$user['amount']){
            return zy_array(false,'余额不足',null,135,$isModule);
        }       
        $transaction_id = time().rand(10000,99999);
        $da = $this->paysuccess($out_trade_no,$transaction_id,true);
        if($da['code']=='200'){
            //修改用户余额
            $res  = $this->onterface('adopt',['module'=>'fund','uid'=>$money['uid'],'amount'=>$money['amount'],'type'=>'2',true]);          
            if( $res['code']=='200' ){
                return zy_array(true,'修改成功','',200,$isModule);
            }else{
                return zy_array(false,'余额修改失败',null,106,$isModule);
            }
        }
        return zy_array(false,'支付失败',null,107,$isModule);
    }
    



    /**
     * 余额退款
     * order_number  商户单号
     */

    public function refund( $order_number = false,$money = false,$isModule = false ){
        $data = Db::name("fund_zhifu_record")->where('transaction_id',$order_number)->find();
        //用户余额添加或减少
        $res  = $this->onterface('adopt',['module'=>'fund','uid'=>$data['uid'],'amount'=>$money,'type'=>'1',true]);
        if($res['code']!='200'){   
            return zy_array(false,'余额添加失败',null,109,$isModule);           
        }
        
        $con = ['trade_sn'=>$order_number,'refund_uid'=>$data['uid'],'phone'=>$data['phone'],'status'=>'1','refund_money'=>$money,'type'=>'2','stat'=>'2','refund_time'=>time()];

        $refund = Db::name("fund_refund")->insertGetID($con);

        if( $refund ){
            return zy_array(true,'退款成功','',200,$isModule); 
        }else{
            return zy_array(false,'退款记录生成失败',null,124,$isModule);
        }
      
    }







    /**********************************   资金明细       ******************************** */

    


    /**
    *  获取信息明细
    * @param  String uid  用户id
    * @param  String type  类型  1为提现明细  2为充值明细  3为支付明细 4为退款明细
    * @return $da为返回的数据
    */
    public function withdrawminxi( $isModule = false ){
        $data = $this->request->param();
        $data = zy_decodeData($data,$isModule);        
        if(!isset($data['uid']) || empty($data['uid'])){
            return zy_array(false,'用户id没上传',null,101,$isModule);
        }    
        
        switch($data['type']){
            case 1:$da = Db::name("fund_tx_record")->where('del','1')->where('uid',$data['uid'])->order('addtime','desc')->select();break;
            case 2:$da = Db::name("fund_pay_record")->where('del','1')->where('uid',$data['uid'])->order('addtime','desc')->select();break;
            case 3:$da = Db::name("fund_zhifu_record")->where('del','1')->where('uid',$data['uid'])->order('addtime','desc')->select();break;
            case 4:$da = Db::name("fund_refund")->where('del','1')->where('uid',$data['uid'])->order('refund_time','desc')->select();break;
            default: return zy_array(false,'请正确上传类型',null,101,$isModule);break;
        }

        foreach($da as $d=>$value){
            if( $data['type']=='4' ){
              $value['addtime'] = date('Y-m-d H:i:s',$value['refund_time']); 
            }else{
              $value['addtime'] = date('Y-m-d H:i:s',$value['addtime']); 
            }
           
        }
        return zy_array(true,'获取成功',$da,200,$isModule);
    } 
  


    /**
     * 删除提现记录
    * @param  String id  记录id
    * @param  String type  类型  1为提现明细  2为充值明细  3为支付明细 4为退款明细
    * @return $da为返回的数据
     */
    public function delwithdrawal( $isModule = false ){
       $data = $this->request->param();
       $data = zy_decodeData($data,$isModule);
       if( empty($data['id']) ){
           return zy_array(false,'请上传删除记录',null,126,$isModule);
       }

       switch($data['type']){
           case 1: $da = Db::name("fund_tx_record")->where('id',$data['id'])->update(['del'=>'2']);break;
           case 2: $da = Db::name("fund_pay_record")->where('id',$data['id'])->update(['del'=>'2']);break;
           case 3: $da = Db::name("fund_zhifu_record")->where('id',$data['id'])->update(['del'=>'2']);break;
           case 4: $da = Db::name("fund_refund")->where('id',$data['id'])->update(['del'=>'2']);break;
           default: return zy_array(false,'请正确上传类型',null,101,$isModule);break;
       }

       if( $da ){
          return zy_array(true,'删除成功',$da,200,$isModule);
       }


    }







    // /**
    // *  充值明细
    // * @param  String uid  用户id
    // * @return $da为返回的数据
    // */
    // public function addminxi( $isModule = false ){
    //     $data = $this->request->param();
    //     $data = zy_decodeData($data,$isModule);        
    //     if(!isset($data['uid']) || empty($data['uid'])){
    //         return zy_array(false,'用户id没上传',null,101,$isModule);
    //     }
        
    //     $da = Db::name("fund_pay_record")->where('uid',$data['uid'])->order('addtime','desc')->select();

    //     foreach($da as $d=>$value){
    //         $value['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
    //     }

    //     return zy_array(true,'获取成功',$da,200,$isModule);
    // } 

    

    
    /**
    *  获取详情
    * @param  String id  账单id
    * @param  String xid  类型id 1为提现   2为充值 3为支付
    * @return $da为返回的数据
    */
    
    public function  xiangqing( $isModule = false ){
        $data  = $this->request->param();
        $data = zy_decodeData($data,$isModule);       
        if(!isset($data['id']) && empty($data['id'])){
            return zy_array(false,'账户id不存在',null,103,$isModule);
        }
        if(!isset($data['xid']) && empty($data['xid'])){
            return zy_array(false,'没有选择账户类型',null,104,$isModule);
        }
        //获取站点信息
        $res  = $this->onterface('geturl',['data'=>null,'isModule'=>true]);
        $basic   = $res['data'];        
        $url_img = $basic['basic']['site_domain'];

        if($data['xid']=='1'){
           $da = Db::name('fund_tx_record')->where('id',$data['id'])->find();
           $bankc = Db::name('fund_bankcard')->where('account',$da['account'])->find();          
           if($bankc['tid']=='3'){
                $bank  = Db::name("fund_bank")->where('card_bin',$bankc['bank_bin'])->find();
                if(empty($bank['thumb'])){
                      $value['logo'] =$url_img.'/plugins/fund/view/upload/zftype/银联.png';
                  }else{
                      $value['logo'] = $url_img.$bank['thumb'];
                  }                
                $da['bank'] = $bank['bank'];
           }
           $da['addtime'] = date('Y-m-d H:i:s',$da['addtime']);           
        }else if($data['xid']=='2'){
           $da = Db::name("fund_pay_record")->where('id',$data['id'])->find();          
           $bankc = Db::name("fund_bankcard")->where('id',$da['bid'])->find();           
           if($bankc['tid']=='3'){
                $bank  = Db::name("fund_bank")->where('card_bin',$bankc['bank_bin'])->find();               
                if(empty($bank['thumb'])){
                      $value['logo'] =$url_img.'/plugins/fund/view/upload/zftype/银联.png';
                  }else{
                      $value['logo'] = $url_img.$bank['thumb'];
                  }
                $da['bank'] = $bank['bank'];
           }
           $da['addtime'] = date('Y-m-d H:i:s',$da['addtime']);
        }else if($data['xid']=='3'){
           $da = Db::name("fund_zhifu_record")->where('id',$data['id'])->find();          
           $bankc = Db::name("fund_bankcard")->where('id',$da['bid'])->find();           
           if($bankc['tid']=='3'){
                $bank  = Db::name("fund_bank")->where('card_bin',$bankc['bank_bin'])->find();               
                if(empty($bank['thumb'])){
                      $value['logo'] =$url_img.'/plugins/fund/view/upload/zftype/银联.png';
                  }else{
                      $value['logo'] = $url_img.$bank['thumb'];
                  }
                $da['bank'] = $bank['bank'];
           }
           $da['addtime'] = date('Y-m-d H:i:s',$da['addtime']);
        }else{
           $da = '';
        }

        if($da){
            return zy_array(true,'获取成功',$da,200,$isModule); 
        }else{
            return zy_array(false,'没有该类型或id不存在',null,122,$isModule);
        }

    }  







    /*
    *查询银行
    */

    public function bankInfo($card,$bankList){
        $card_8 = substr($card,0,8);

        if (isset($bankList[$card_8])) {
            return $card_8;           
        }
        $card_6 = substr($card,0,6);
        if (isset($bankList[$card_6])){
            return $card_6;           
        }
        $card_5 = substr($card,0,5);
        if (isset($bankList[$card_5])){
            return $card_5;         
        }
        $card_4 = substr($card,0,4);
        if (isset($bankList[$card_4])){
            return $card_4;
        }
        $card_3 = substr($card,0,3);
        if (isset($bankList[$card_3])){
            return $card_3;
        }
        $card_2 = substr($card,0,2);
        if (isset($bankList[$card_2])){
            return $card_2;
        }
        return 1;
    }





    /**
     * 获取支付方式
     * @param string type  类型  1为获取支付方式  2为获取提现方式  3为充值余额方式
     */
   
    public function payMethod( $isModule=false ){
        $data = $this->request->param();     
        $data = zy_decodeData($data,$isModule);
        if( !isset($data['type']) || empty($data['type']) ){
           return zy_array(false,'类型没上传！',null,139,$isModule);
        }
        //获取站点信息
        $res  = $this->onterface('geturl',['data'=>null,'isModule'=>true]);
        $basic   = $res['data'];           
        $url_img = $basic['basic']['site_domain'];
        switch( $data['type'] ){
            case 1: $da = Db::name("fund_zhifu_type")->where('type',1)->select();
                     break;
            case 2: $da = Db::name("fund_type")->where('type',1)->select();
                     break;
            case 3: $da = Db::name("fund_zhifu_type")->where('type',1)->where('tid','<>','4')->select();
                     break;
            default: return zy_array(false,'类型上传错误！',null,140,$isModule);
                     break;
        }
  
        if( $data['type']=='2' ){ 
            $con = ['0'=>[],'1'=>[],'2'=>[]];
            $number1 = '0';
            $number2 = '0';
            $number3 = '0';
           foreach( $da as $key=>$value ){           
                switch($value['zid']){
                        case 1: $number1+=$value['number'];break;
                        case 2: $number2+=$value['number'];break;
                        case 3: $number3+=$value['number'];break;
                    }
            }

        foreach( $da as $key=>$value ){       
            if( $value['zid']=='1' ){              
                array_push($con['0'],$url_img.$value['photo'],'支付宝',$number1);
            }
            if( $value['zid']=='2' ){              
                array_push($con['1'],$url_img.$value['photo'],'微信',$number2);
            }
            if( $value['zid']=='3' ){
                array_push($con['2'],$url_img.$value['photo'],'银行卡',$number3);
            }
        }
            $con['0'] = array_unique($con['0']);
            $con['1'] = array_unique($con['1']);
            $con['2'] = array_unique($con['2']);
        }else{
            foreach( $da as $key=>$value ){
                $value['photo'] = $url_img.$value['photo'];
                $da[$key] = $value;
            }
            $con=$da;
        }   
        return zy_array(true,'获取成功',$con,200,$isModule);
    }



    /**
     * 模块调用
     */
    private function onterface( $id,$data ){
        $symbol = 'fund';  
        $id = $id;   
        $param = $data;    
        $res    = getModuleApiData($symbol,$id,$param);
        return $res;     
    }



}
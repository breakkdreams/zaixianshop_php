<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/7/9
 * Time: 9:24
 */
namespace app\api\controller;
use think\Controller;
use think\Url;
use think\Db;
class SmsController extends Controller
{
    public function birth(){
        $re=Db::name("receive")->find();
        $y=date("Y",time());
        $us=Db::name("user")->select();
        $con="";
        foreach ($us as $v){
            $br=$y.date("md",$v['birthday']);
            $br2=($y+1).date("md",$v['birthday']);

            $br=strtotime($br);
            $br2=strtotime($br2);

            if((($br-time())/86400>-1&&($br-time())/86400<=3)||(($br-time())/86400>-1&&($br2-time())/86400<=3)){
                $con.=$v['user_login'].":".date("m/d",$v['birthday'])."；";
            }
        }

        if($con!=""){
            $kait="生日提醒";
            $lea=Db::name('user')->whereIn("id",$re['user_id'])->select();
            $e_con="这些小伙伴就要生日了！".$con;
            $sms_con="code|".$con;
            foreach ($lea as $v){
                sms_send($v['mobile'],$sms_con,"SMS_142953112");

                cmf_send_email($v['user_email'], $kait, $e_con);
            }
        }
    }
    public function cs(){
        $mobile=$this->request->param()['mobile'];
       echo sms_yzm_send($mobile);
    }
}
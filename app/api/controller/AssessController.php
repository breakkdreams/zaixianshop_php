<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/7/9
 * Time: 9:24
 */
namespace app\api\controller;
use app\admin\model\RoleModel;
use think\Controller;
use think\Url;
use think\Db;
class AssessController extends Controller
{
    public function lists()
    {
        $name="";
        $xt=$this->request->param();
        $ass=Db::name('assess');
        $id=$xt['uid'];
        $model=new RoleModel();
        $rid=Db::name('role_user')->where('user_id',$id)->find();
        $time=time();
        $time=date("Y-m-01 00:00",$time);
        $time=strtotime($time);
        if(isset($xt['time']))
            $time=strtotime(date("Ym01 00:00",strtotime($this->request->param('time'))));
        if(isset($xt['userid'])){
            if($xt['userid']!=100){
                $ass->where('userid',$xt['userid']);
                $name=$this->request->param('userid');
            }}
        $ids=$model->where('parent_id',$rid['role_id'])->select();
        if(isset($ids)){
            $re=$model->children($ids);
            $userid=Db::name('role_user')->whereIn('role_id',$re)->select();
            foreach ($userid as $user){
                $infos[]=$user['user_id'];
            }
            if(isset($infos))
                $id=$id.','.implode(',',$infos);
        }
        $ass->whereIn('userid',$id)->order('time','desc')->where('time',$time);
        $ass=$ass->select();
        $time=date("Y-m-01 00:00",$time);
        $names=Db::name('user')->whereIn('id',$id)->select();
        $infos=[];
        foreach ($ass as $v){
            $val=$v;
            $val['count']=$v['personal_leave']+$v['late']+$v['abs']+$v['sick_leave']+$v['ear']+$v['fraud'];
            $val['sum']=$v['dota']+$v['ywsp']+$v['team']+$v['report']
                +$v['represe']+$v['wanc']+$v['zhil']+$v['zj']+$v['zjl']
                +$this->avg($v['cknl'])+$this->avg($v['wcgz'])+$this->avg($v['zxlh'])+$this->avg($v['btl']);
            $infos[]=$val;
        }
        return json($infos);
    }
    public function avg($x){
        $da=explode(',',$x);
        $sum=0;
        foreach ($da as $v){
            $sum+=$v;
        }
        $avg=$sum/count($da);
        return $avg;
    }
    public function xq(){
        $pa=$this->request->param();
        $ass=Db::name('assess')->where('assess_id',$pa['id'])->find();
        $ass['cknl']=explode(",",$ass['cknl']);
        $ass['wcgz']=explode(",",$ass['wcgz']);
        $ass['zxlh']=explode(",",$ass['zxlh']);
        $ass['btl']=explode(",",$ass['btl']);
        return json($ass);
    }
}
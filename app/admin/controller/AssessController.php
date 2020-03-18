<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/6/27
 * Time: 10:15
 */

namespace app\admin\controller;
use app\admin\model\RoleModel;
use cmf\controller\AdminBaseController;
use app\api\Model\AccessModel;//读取权限


use think\Db;
use FontLib\Table\Type\post;
/**
 * Class TaskController
 * @package app\admin\controller
 * @adminMenuRoot(
 *     'name'   =>'绩效考核',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'cogs',
 *     'remark' =>'绩效考核'
 * )
 */

class AssessController extends AdminBaseController
{
    public function _initialize()
    {
        parent::_initialize();
    }
    /**
     * 任务列表
     * @adminMenu(
     *     'name'   => '绩效考核',
     *     'parent' => 'default',
     *     'display'=> true,
     *	   'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '绩效考核',
     *     'param'  => ''
     * )
     */
    public function lists()
    {
        $name="";
        $ass=Db::name('assess');
        $id=cmf_get_current_admin_id();
        $model=new RoleModel();
        $rid=Db::name('role_user')->where('user_id',$id)->find();
        $time=time();
        $time=date("Y-m-01 00:00",$time);
        $time=strtotime($time);

            $xt=$this->request->param();
            if(isset($xt['time']))
                $time=strtotime(date("Ym01 00:00",strtotime($this->request->post('time'))));
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

        $ass=$ass->paginate(20);
        $ass->appends($this->request->param());
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
        $this->assign('name',$name);
        $this->assign('names',$names);
        $this->assign('time',$time);
        $this->assign('data',['key'=>'']);
        $this->assign('infos',$infos);
        $this->assign('page',$ass->render());
        return $this->fetch('lists');
    }
    /**
     * 任务列表
     * @adminMenu(
     *     'name'   => '绩效评分',
     *     'parent' => 'default',
     *     'display'=> true,
     *	   'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '绩效评分',
     *     'param'  => ''
     * )
     */
    public function gradelist()
    {
        $time=time();
        $time=date("Y-m-01 00:00",$time);
        $time=strtotime($time);
        if($this->request->isPost()){
            $xt=$this->request->post();
            if(isset($xt['time']))
            $time=strtotime(date("Ym01 00:00",strtotime($this->request->post('time'))));
        }
        $assess=Db::view('assess')->view('user','rz_time','user.id=assess.userid')->where('time',$time)->select();
        $time=date("Y-m-01 00:00",$time);
        $ids=[];
        $infos=[];
        foreach ($assess as $v){
            $val=$v;
            $val['count']=$v['personal_leave']+$v['late']+$v['abs']+$v['sick_leave']+$v['ear']+$v['fraud'];
            $val['sum']=$v['dota']+$v['ywsp']+$v['team']+$v['report']
                +$v['represe']+$v['wanc']+$v['zhil']+$v['zj']+$v['zjl']
                +$this->avg($v['cknl'])+$this->avg($v['wcgz'])+$this->avg($v['zxlh'])+$this->avg($v['btl']);
            $infos[]=$val;
            $ids[]=$v['userid'];
        }
        $wids=implode(',',$ids);
        if($wids!=""){
            $wids.=",1,81";
        }
        else{
            $wids="1.81";
        }
        $user2=Db::name('user')->whereNotIn('id',$wids)->select();
        $this->assign('time',$time);
        $this->assign('infos',$infos);
        $this->assign('user2',$user2);
        return $this->fetch('gradelist');
    }
    /**
     * 任务列表
     * @adminMenu(
     *     'name'   => '出勤记录',
     *     'parent' => 'default',
     *     'display'=> false,
     *	   'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '出勤记录',
     *     'param'  => ''
     * )
     */
    public function attendance()
    {
        $pa=$this->request->param();
        if(isset($pa['id'])){
            $this->assign('id',$pa['id']);
            $ass=Db::name('assess')->where('assess_id',$pa['id'])->find();
            $this->assign('ass',$ass);
        }
        if(isset($pa['userid'])){
            $this->assign('userid',$pa['userid']);
            $this->assign('time',$pa['time']);
        }
        return $this->fetch();
    }
    /**
     * 任务列表
     * @adminMenu(
     *     'name'   => '评分',
     *     'parent' => 'default',
     *     'display'=> false,
     *	   'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '评分',
     *     'param'  => ''
     * )
     */
    public function grade()
    {
        $pa=$this->request->param();
        if(isset($pa['id'])){
            $this->assign('id',$pa['id']);
            $ass=Db::name('assess')->where('assess_id',$pa['id'])->find();
            $ass['cknl']=explode(',',$ass['cknl']);
            $ass['wcgz']=explode(',',$ass['wcgz']);
            $ass['zxlh']=explode(',',$ass['zxlh']);
            $ass['btl']=explode(',',$ass['btl']);
            $this->assign('ass',$ass);
            $count=0;

        }
        if(isset($pa['userid'])){
            $this->assign('userid',$pa['userid']);
            $this->assign('time',$pa['time']);
        }
        return $this->fetch();
    }
    public function add(){
    $pa=$this->request->param();
    if(isset($pa['id'])){
        $up=$pa['post'];
        Db::name('assess')->where('assess_id',$pa['id'])->update($up);
    }else{
        $ins=$pa['post'];
        $ins['time']=strtotime($ins['time']);
        $ins['username']=Db::name('user')->where('id',$ins['userid'])->find()['user_login'];
        Db::name('assess')->insert($ins);
    }
    return $this->gradelist();

}
    public function add_gr(){
        $pa=$this->request->param();
        if(isset($pa['id'])){
            $up=$pa['post'];
            $up['cknl']=implode(',',$pa['cknl']);
            $up['wcgz']=implode(',',$pa['wcgz']);
            $up['zxlh']=implode(',',$pa['zxlh']);
            $up['btl']=implode(',',$pa['btl']);
            Db::name('assess')->where('assess_id',$pa['id'])->update($up);
        }else{
            $ins=$pa['post'];
            $ins['time']=strtotime($ins['time']);
            $ins['cknl']=implode(',',$pa['cknl']);
            $ins['wcgz']=implode(',',$pa['wcgz']);
            $ins['zxlh']=implode(',',$pa['zxlh']);
            $ins['btl']=implode(',',$pa['btl']);
            $ins['username']=Db::name('user')->where('id',$ins['userid'])->find()['user_login'];
            Db::name('assess')->insert($ins);
        }
        return $this->gradelist();

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

            $this->assign('id',$pa['id']);
            $ass=Db::name('assess')->where('assess_id',$pa['id'])->find();
            $ass['cknl']=explode(',',$ass['cknl']);
            $ass['wcgz']=explode(',',$ass['wcgz']);
            $ass['zxlh']=explode(',',$ass['zxlh']);
            $ass['btl']=explode(',',$ass['btl']);
            $this->assign('ass',$ass);
        return $this->fetch();
    }

    /**
     * 实习评分
     * @return [type] [description]
     */
    public function sx_grade()
    {
        $data=$this->request->param();
        $id=$data['userid'];//实习用户信息

       
        $m=new AccessModel();
        //读取最高权限列表
        $rootAdmins=$m->readRootAdmin(); 
        //读取部门领导
        $leaderid=$m->readDepartmentLeader($id);
        //获取用户信息
        $user=Db::view('user')->view('role_user','role_id','user.id=role_user.user_id')->view('role','name as roleName','role.id=role_user.role_id')->where('user.id='.$id)->find();
        //设置考核时间
        for($i=1;$i<=3;$i++){
            $user['assess_time'][]=strtotime('+'.$i.' month',$user['sx_time']);
        }
        

        //如果有传递考核时间 查询该考核时间评分，没有则查询最新  //读取实习评分表数据
        if(isset($data['assess_time'])){
            $pfdata=Db::name('sx_grade')->where('user_id='.$id)->where('assess_time='.$data['assess_time'])->find();
        }else{
            $data['assess_time']="";
             $pfdata=Db::table('cmf_sx_grade')->where('user_id='.$id)->order('assess_time')->limit(1)->find();
        }
       
        if(!empty($pfdata)){
            $pf=json_decode($pfdata['pf_content'],true);//评分
            $py=json_decode($pfdata['py_content'],true);//评语
            $this->assign('pf',$pf);
            $this->assign('py',$py);
        }
        $this->assign('assess_time',$data['assess_time']);
        $this->assign('rootAdmins',$rootAdmins);
        $this->assign('leaderid',$leaderid);
        $this->assign('pfdata',$pfdata);
        $this->assign('user',$user);
        
        return $this->fetch();
    }
    /**
     * 实习评分新增和修改
     * @return [type] [description]
     */
    public function sx_grade_add()
    {
        $data=$this->request->param();
        //dump($data);exit;
        
        if($data['submit']=="提交"){
            //新增
            unset($data['submit']);
            $in['pf_content']=json_encode($data['pf']);//评分

            $in['py_content']=json_encode($data['py']);//评语

            $in['pf_type']=0;//评分类型  0  实习评分  1正式员工评分

            $in['assess_time']=$data['assess_time'];//考核时间
            $in['sum']=$data['sum'];
            $in['user_id']=$data['user_id'];

            Db::name('sx_grade')->insert($in);

            return $this->success('评分成功！',url('assess/sx_grade',['userid'=>$data['user_id']]));
        }else{
            //更新
            
            $data['pf_content']=json_encode($data['pf']);//评分

            $data['py_content']=json_encode($data['py']);//评语
            unset($data['submit'],$data['py'],$data['pf']);
            Db::name('sx_grade')->where('id='.$data['id'])->update($data);
            return $this->success('评分修改成功！',url('assess/sx_grade',['userid'=>$data['user_id'],'assess_time'=>$data['assess_time']]));
        }
        
    }

    /**
     * 实习生列表
     * @return [type] [description]
     */
    public function sx_gradelist()
    {

        $user=Db::name('user')->where('rz_time>='.strtotime(date('Y-m-d',time())))->paginate(20);

        foreach ($user as $key=>$value) {
           $res=Db::table('cmf_sx_grade')->where('user_id='.$value['id'])->order('assess_time')->limit(1)->find();

           $value['sum']=$res['sum'];
           $value['assess_time']=$res['assess_time'];
           $value['assess_id']=$res['id'];
           $user[$key]=$value;
        }
        $sx_list=$user;
        $this->assign('sx_list',$sx_list);
        return $this->fetch();

    }






}
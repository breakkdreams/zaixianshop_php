<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/7/9
 * Time: 9:24
 */
namespace app\api\controller;
use app\admin\model\PlanModel;
use app\admin\model\ReportModel;
use app\admin\model\RoleModel;
use think\Controller;
use think\Url;
use think\Db;
class PlanController extends Controller
{

    public function index()
   {
        $id = $this->request->param('user_id');

        $model = new RoleModel();
        $rid = Db::name('role_user')->where('user_id', $id)->find();
        $ids = $model->where('parent_id', $rid['role_id'])->select();
        if (isset($ids)) {
            $re = $model->children($ids);
            $userid = Db::name('role_user')->whereIn('role_id', $re)->select();
            foreach ($userid as $user) {
                $infos[] = $user['user_id'];
            }
            if (isset($infos))
                $id = $id . ',' . implode(',', $infos);
        }


        $pl = new PlanModel();
        $da = $this->inwhere($pl);
        $xx = $da->whereIn('rela', $id)->select();
        if(!count($xx)){
            return json(['status'=>-1,'msg'=>'没有计划']);
        }
            return json($xx);
    }

    public function inwhere($pl){
        $pl->order('plan_id','desc');

                $data = $this->request->param();

            if ($data['start_time'] != '' && $data['end_time'] != '') {
                $start = strtotime($data['start_time']);
                $end = strtotime($data['end_time']);
                $pl->whereBetween('add_time', [$start, $end]);
            }
            if ($data['key'] != '100' && $data['val'] != '') {
                if ($data['key'] == 'plan_id' || $data['key'] == 'rela_name') {
                    $pl->where($data['key'], $data['val']);
                }
                if ($data['key'] == 'tit') {
                    $t = '%' . $data['val'] . '%';
                    $pl->whereLike($data['key'], $t);
                }
            }
            if ($data['type'] != '100') {
                $pl->where('type', $data['type']);
            }
            if ($data['rela'] != 'abc') {
                $pl->where('rela', $data['rela']);
            }

        return $pl;
    }
    public function plan_add(){
        $user= $this->request->param('user_id');
        $user=Db::name('user')->where('id',$user)->find();
        $doplan=$this->request->param('super_id');
        $doplan=Db::name('superclass')->where('super_id',$doplan)->find();
        $sa=$this->request->param();
        unset($sa['user_id']);
        $sa['add_time']=time();
        $sa['rela']=$user['id'];
        $sa['rela_name']=$user['user_login'];
        $sa['type']=$doplan['type'];
        $sa['month']=$doplan['month'];
        $sa['week']=$doplan['week'];
        Db::name('plan')->insert($sa);
        return 1;
    }
    public function planone(){
        $id=$this->request->param('id');
        $model=new PlanModel();
        $info=$model->where('plan_id',$id)->find();
        return json($info);
    }

    public function plan_edit(){

        $sa=$this->request->param();
        $id=$sa['id'];
        $doplan=$this->request->param('super_id');
        $doplan=Db::name('superclass')->where('super_id',$doplan)->find();
        unset($sa['id']);

        $sa['type']=$doplan['type'];
        $sa['month']=$doplan['month'];
        $week=explode(",",$doplan['week']);
        $sa['week']=$week[1]."-".$week[3]."周";
        $sa['week']=$doplan['week'];
        Db::name('plan')->where('plan_id',$id)->update($sa);
        return 1;
    }
    public function dele(){

//            $id=implode(',',$this->request->post()['ids']);
//            Db::name('plan')->whereIn('plan_id',$id)->delete();
//            $this->success("删除成功！", '');

            $id=$this->request->param('id');
            Db::name('plan')->where('plan_id',$id)->delete();
            return 1;

    }
    public function comp(){
        $model=new PlanModel();
        $da=$model->where('plan_id',$this->request->get('id'))->find();
        return json($da);
    }
    public function report(){
        $id=$this->request->param('user_id');
        $model=new RoleModel();
        $rid=Db::name('role_user')->where('user_id',$id)->find();
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
        if (isset($this->request->param()['search'])) {
            $param = $this->request->param()['search'];
        } else {
            $param = $this->request->param();
        }
        $names=Db::name('user')->select();
        $this->assign('names',$names);
        $re=new ReportModel();
        $da=$this->rewhere($re);
        $da=$da->whereIn('rela',$id)->select();
       // dump($da);

        //读取完成度
        foreach ($da as $key => $value) {
           $complete=Db::name('plan')->where('plan_id='.$value['plan_id'])->find()['complete'];
          $value['complete']=$complete;
           $da[$key]=$value;
        }
        return json($da);
    }
    public function rewhere($pl){

                $data = $this->request->param();
                $pl->order('report_id','desc');
            if ($data['start_time'] != '' && $data['end_time'] != '') {
                $start = strtotime($data['start_time']);
                $end = strtotime($data['end_time']);
                $pl->whereBetween('add_time', [$start, $end]);
            }
            if ($data['key'] != '100' && $data['val'] != '') {
                if ($data['key'] == 'plan_id' || $data['key'] == 'rela_name'||$data['key'] == 'report_id') {
                    $pl->where($data['key'], $data['val']);
                }
                if ($data['key'] == 'tit'||$data['key'] == 'plan_name') {
                    $t = '%' . $data['val'] . '%';
                    $pl->whereLike($data['key'], $t);
                }
            }
            if ($data['type'] != '100') {
                $pl->where('type', $data['type']);
            }
            if ($data['rela'] != 'abc') {
                $pl->where('rela', $data['rela']);
            }

        return $pl;
    }
    public function add_re(){
        $data=$this->request->param();

        return 1;
    }
    public function rdele(){

        $id=$this->request->param('id');
        Db::name('report')->where('report_id',$id)->delete();
        return 1;

    }
    public function re_add(){
        $user=$this->request->param('userid');
        $user=Db::name('user')->where('id',$user)->find();
        $sa=$this->request->param();
        unset($sa['userid']);
        $sa['add_time']=time();
        $sa['rela']=$user['id'];
        $sa['rela_name']=$user['user_login'];

        Db::name('report')->insert($sa);
        return 1;
    }

    //获取报告详情
    public function rcomp(){
        $model=new ReportModel();
        $da=$model->where('report_id',$this->request->param('id'))->find();
        //获取完成度
        $plan_complete=Db::name('plan')->where('plan_id='.$da->plan_id)->find()['complete'];
        $da['complete']=$plan_complete;
        return json($da);
    }
    public function report_edit(){
        $post=$this->request->param();
        $id=$this->request->param('id');
        
        $complete=$post['complete'];
        unset($post['id'],$post['complete']);
        Db::name('report')->where('report_id',$id)->update($post);
        //更新计划完成度
        Db::name('plan')->where('plan_id='.$post['plan_id'])->update(['complete'=>$complete]);
        return 1;
    }
    public function doplan(){
        $da=Db::name('superclass')->order('super_id','desc')->select();
        $infos=[];
        foreach ($da as $v){
            $val=$v;
            $week=explode(",",$v['week']);
            $val['week']=$week[1]."-".$week[3]."周";
            $infos[]=$val;
        }
        return json($infos);
    }
}
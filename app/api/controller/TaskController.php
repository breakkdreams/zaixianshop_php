<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/7/9
 * Time: 9:24
 */
namespace app\api\controller;
use app\admin\model\TaskModel;
use think\Controller;
use think\Url;
use think\Db;
class TaskController extends Controller
{

    public function index(){

        $model=new TaskModel();
        $da=$model->where("parent","0")->order(['task_id'=>'desc','order']);

        if(isset($this->request->param()['key'])){
                $data = $this->request->param();
                $ty=["已启动"=>"0","未启动"=>"1","已完成"=>"2","延迟"=>"3"];
            if ($data['key'] ==1) {
                $da->whereLike('tit',"%".$data['val']."%" );
            }
            if ($data['key'] ==2) {
                $da->whereLike('client_name',"%".$data['val']."%" );
            }
            if ($data['key'] ==3) {
                $da->whereLike('release_name',"%".$data['val']."%" );
            }
            if ($data['key'] ==4) {
                $da->whereLike('learder_name',"%".$data['val']."%" );
            }
            if ($data['key'] ==5) {
                $da->whereLike('deal_name',"%".$data['val']."%" );
            }
            if ($data['key'] ==6) {
                if ($data['val'] != "全部") {
                    if ($data['val'] != "关闭")
                        $da->where('status', $ty[$data['val']]);
                    else
                        $da->where('status', '>', '3');
                }
            }
        }

        $da=$da->select();
        $dojs=[];
        foreach ($da as $val){
            $val['time']=($val['deadline']-time())/86400;

            if($val['time']<0){
                $val['time']='逾期'.abs(round($val['time']));
            }else{
                $val['time']='倒数'.round($val['time']);
            }
            preg_match_all ("/>.*?</", $val['describe'], $a);
            $zh=[];
            foreach ($a[0] as $zhv){
                if($zhv!="><"){
                    $zhv=str_replace('<','',$zhv);
                    $zhv=str_replace('>','',$zhv);
                    $zh[]=$zhv;
                }
            }
            $val['con']=$zh;
            $dojs[]=$val;
        }
        $infos=$this->mangjs($dojs);
        if($infos==[])
            return json(['status'=>-1,'msg'=>'没有任务']);
        return json($infos);
    }
    public function zrw(){

        $model=new TaskModel();
        $id=$this->request->param('id');

        $da=$model->where("parent",$id)->order("task_id",'desc')->order('order')->select();

        $dojs=[];
        foreach ($da as $val){
            $val['time']=($val['deadline']-time())/86400;
            if($val['time']<0){
                $val['time']='逾期'.abs(round($val['time']));
            }else{
                $val['time']='倒数'.round($val['time']);
            }
            preg_match_all ("/>.*?</", $val['describe'], $a);
            $zh=[];
            foreach ($a[0] as $zhv){
                if($zhv!="><"){
                    $zhv=str_replace('<','',$zhv);
                    $zhv=str_replace('>','',$zhv);
                    $zh[]=$zhv;
                }
            }
            $val['con']=$zh;
            $dojs[]=$val;
        }
        $infos=$this->mangjs($dojs);
        if($infos==[])
            return json(['status'=>-1,'msg'=>'没有任务']);
        return json($infos);
    }
    /*递归*/
    public function mang($id){
        $da=Db::name("task")->where("task_id",$id)->find();
        $ids=explode(',',$da['children']);
        $re='';
        foreach ($ids as $v){
            $ix=Db::name("task")->where("task_id",$v)->find();
            if($ix['children']!=''){
                $re=$this->mang($v).$re;
            }
            $re=$v.','.$re;
        }
        return $re;
    }
    /*递归2*/
    public function mangjs($ids){
        $model=new TaskModel();
        $re=[];
        foreach ($ids as $v){
            $dojs=[];
            if($v['children']!=''){
                $da=$model->whereIn('task_id',$v['children'])->select();
                foreach ($da as $val){
                    $val['time']=($val['deadline']-time())/86400;
                    if($val['time']<0){
                        $val['time']='超'.abs(round($val['time']));
                    }else{
                        $val['time']='还'.round($val['time']);
                    }
                    $dojs[]=$val;
                }
                $v['count']=$this->mangjs($dojs);
            }
            $re[]=$v;
        }
        return $re;
    }


    /**
     *  我负责的参与的任务  字段 rela
     *@param $id  负责人的id   
     *  
     */
    public function lead($id)
    {

        $model=new TaskModel();
        $da=$model->order('status')->order('priority')->paginate(20);
        $a=$this->request->get('id');
        foreach ($da as $v){
            $us=explode(',',$v['learder']);
            foreach ($us as $val){
                if($val==$a){
                    $v['time']=($v['deadline']-time())/86400;
                    if($v['time']<0){
                        $v['time']='逾期'.abs(round($v['time']));
                    }else{
                        $v['time']='倒数'.round($v['time']);
                    }
                    $infos[]=$v;
                }}

        }
        if(!isset($infos))
            return json(['status'=>-1,'msg'=>'没有任务']);
        return json($infos);
    }



    /*日程页面我负责的任务*/
    public function my_lead($id){
        //提取出符合条件的前十条数据
        $model=new TaskModel(); 
        //$id=$this->request->get('id');
        $data=$model->where('rela','like',$id)->whereOr('learder','like',$id)->order('status')->order('priority')->limit(10)->select();
        foreach ($data as $v) {
            $v['time']=($v['deadline']-time())/86400;
            if($v['time']<0){
                $v['time']='逾期'.abs(round($v['time']));
            }else{
                $v['time']='倒数'.round($v['time']);
            }
        }
       
        return json($data);

    }

    /*我发布*/
    public function issue(){
        $model=new TaskModel();
        $a=$this->request->get('id');
        $da=$model->order('status')->where('release',$a)->select();
        foreach ($da as $v){
            $infos[]=$v;
            break;
        }
        if(!isset($infos))
            return json(['status'=>-1,'msg'=>'没有任务']);
        return json($da);
    }
    /*
     * 添加任务
     */
    public function task_add()
    {


        $post=$this->request->param();
        $user=Db::name('user')->where("id",$post['user_id'])->find();

        $post['deadline']=strtotime($post['deadline']);

        $post['release']=$user['id'];
        $post['release_name']=$user['user_login'];
        unset($post['user_id']);

        $task=Db::name('task');

        if(isset($post['parent'])){

            $pall=$task->where('task_id',$post['parent'])->find();
            if($pall['parent_all']!=''){
                $post['parent_all']=$post['parent'].",".$pall['parent_all'];
                $task->insert($post);
                $me=$task->getLastInsID();
                $pid=explode(',',$pall['parent_all']);

                foreach ($pid as $v){
                    $up=$task->where('task_id',$v)->find();
                    $up['children_all'].=",".$me;
                    $task->where('task_id',$v)->update($up);
                }
            }
            else{
                $post['parent_all']=$post['parent'];
                $task->insert($post);
                $me=$task->getLastInsID();
            }
            $up=$task->where('task_id',$post['parent'])->find();
            if($up['children']!=''){
                $up['children'].=",".$me;
                $up['children_all'].=",".$me;
            }else{
                $up['children']=$me;
                $up['children_all']=$me;
            }
            $task->where('task_id',$post['parent'])->update($up);
        }if(!isset($post['parent'])) {
        $task->insert($post);
    }
        return 1;
    }
    public function select()
    {
        $depart = Db::name("department")->select();

        foreach ($depart as $v) {
            $ids = Db::name("department_user")->where("department_id", $v['id'])->select();
            foreach ($ids as $dv) {
                $allid[] = $dv['user_id'];
            }


            $id = implode(',', $allid);
            $allid = [];
            $vn = $v['name'];
            $da[$vn]["title"] = $vn;
            $va = Db::name("user")->whereIn("id", $id)->select();

            foreach ($va as $k => $val) {
                $val['val'] = $val['user_login'];
                $val['id'] = $val['id'];
                $va[$k] = $val;
            }
            $da[$vn]["val"] = $va;

        }
        foreach ($da as $v){
            $infos[]=$v;
        }
        return json($infos);
    }
    /*
     * 客户选择弹框
     */
    public function kehu(){
        $da=Db::name('kehu');
        if($this->request->isPost()){
            $post=$this->request->post();
            if($post['name']!='') {
                $da->whereLike('kh_name', '%' . $post['name'] . '%');
            }
            if($post['id']!=''){
                $da->where('id',$post['id']);
            }
        }
        $da=$da->select();

        return json($da);
    }
    /*
     * 修改任务页面
     */
    public function task_find(){
        $model=new TaskModel();

        $id=$this->request->get('id');
        if(isset($id)) {
            $da = $model->where('task_id', $id)->find();
            if(!isset($da)){
                return json(['status'=>-1,'msg'=>'id不存在']);
            }
            if ($da['parent'] != 0) {
                $data = $model->where('task_id', $da['parent'])->find();
                $da['parent_tit'] = $data['tit'];
            }

            $da['log']=Db::name('log')->whereIn('task_id',$da['children_all'].','.$id)->select();
            return json($da);
        }else{
            return json(['status'=>-1,'msg'=>'链接没有附id']);
        }
    }
    /*
     * 修改任务方法
     */
    public function task_edit(){
        $up=$this->request->param();
        $id=$this->request->param()['task_id'];
        unset($up['task_id']);
        $up['deadline']=strtotime($up['deadline']);
        Db::name('task')->where('task_id',$id)->update($up);

        return 1;
    }
    /*
     * 开启/关闭
     */
    public function close(){
        $id=$this->request->get('id');
        if(!isset($id)){
            return 'id不存在';
        }
        $data=Db::name('task')->where('task_id',$id)->find();
        if($data['status']<=3){
            $data['status']+=4;
        }else{
            $data['status']-=4;
        }
        Db::name('task')->where('task_id',$id)->update($data);
        return 1;
    }
    /*
     * 查看日志
     */
    public function log_list(){
        $id=$this->request->get('id');

        $da=Db::name('task')->where('task_id',$id)->find();
        $data=Db::name('log')->whereIn('task_id',$da['children_all'].','.$id)->select();
        foreach($data as $key=>$value){
            $data[$key]=cmf_replace_content_file_url(htmlspecialchars_decode($value['con']));
        }
        return json($data);

        return $this->fetch();
    }



    /**
     * 售后列表
     */
    public function aftersale()
    {
        $model=new TaskModel();
        $da=$model->where("aftersale>0")->order(['task_id'=>'desc','order']);

        if(isset($this->request->param()['key'])){
                $data = $this->request->param();
                $ty=["已启动"=>"0","未启动"=>"1","已完成"=>"2","延迟"=>"3"];
            if ($data['key'] ==1) {
                $da->whereLike('tit',"%".$data['val']."%" );
            }
            if ($data['key'] ==2) {
                $da->whereLike('client_name',"%".$data['val']."%" );
            }
            if ($data['key'] ==3) {
                $da->whereLike('release_name',"%".$data['val']."%" );
            }
            if ($data['key'] ==4) {
                $da->whereLike('learder_name',"%".$data['val']."%" );
            }
            if ($data['key'] ==5) {
                $da->whereLike('deal_name',"%".$data['val']."%" );
            }
            if ($data['key'] ==6) {
                if ($data['val'] != "全部") {
                    if ($data['val'] != "关闭")
                        $da->where('status', $ty[$data['val']]);
                    else
                        $da->where('status', '>', '3');
                }
            }
        }

        $da=$da->select();
        $dojs=[];
        foreach ($da as $val){
            $val['time']=($val['deadline']-time())/86400;

            if($val['time']<0){
                $val['time']='逾期'.abs(round($val['time']));
            }else{
                $val['time']='倒数'.round($val['time']);
            }
            preg_match_all ("/>.*?</", $val['describe'], $a);
            $zh=[];
            foreach ($a[0] as $zhv){
                if($zhv!="><"){
                    $zhv=str_replace('<','',$zhv);
                    $zhv=str_replace('>','',$zhv);
                    $zh[]=$zhv;
                }
            }
            $val['con']=$zh;
            $dojs[]=$val;
        }
        $infos=$this->mangjs($dojs);
        if($infos==[])
            return json(['status'=>-1,'msg'=>'没有任务']);
        return json($infos);

    }


}
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
use app\api\Model\AccessModel;
class ClueController extends Controller
{
    public function clue(){
        $da=Db::name('clue')->order('clue_id','desc');
        $data = $this->request->param();

        //读取用户id权限所能看的线索
        $a=new AccessModel();
        $accessIds=$a->readAccess($data['id']);

        $da->where('leard','in',$accessIds);

        if(isset($data['key'])) {
            if ($data['key'] != '100' && $data['val'] != '') {
                if ($data['key'] == 'clue_id' || $data['key'] == 'name' || $data['key'] == 'tel') {
                    $da->where($data['key'], $data['val']);
                }
                if ($data['key'] == 'learder_name' || $data['key'] == 'tit') {
                    $t = '%' . $data['val'] . '%';
                    $da->whereLike($data['key'], $t);
                }
            }
        }

        $da=$da->paginate(20);

        $st=['','信息错误','沟通失败','无法接通','进一步联系'];
        $infos=[];
        foreach ($da as $v){
            if($v['next_time']!=''){
                $v['time']=($v['next_time']-time())/86400;
                if($v['time']<0){
                    $v['col']=1;
                    $v['time']='超'.abs(round($v['time']));
                }else{
                    $v['col']=0;
                    $v['time']='还'.round($v['time']);
                }
            }
            else{
                $v['time']='';
                $v['col']=0;
            }
            $val=$v;
            $val['status']=$st[$v['status']];

            $infos[]=$val;
        }
        return json($infos);

    }
    public function add(){
        $post=$this->request->param();
        $user=cmf_get_current_admin_id();
        $user=Db::name('user')->where("id",$user)->find();
        if($post['next_time'])
            $post['next_time']=strtotime($post['next_time']);
        $post['rela']=$user['id'];
        $post['rela_name']=$user['user_login'];
        $post['add_time']=time();

        if($post['addr'])
            $post['addr']=str_replace(' ','',$post['addr']);
        Db::name('clue')->insert($post);

        return 1;
    }
    public function xq(){
        $id=$this->request->param('id');
        $da=Db::name('clue')->where('clue_id',$id)->find();

        $st=['','信息错误','沟通失败','无法接通','进一步联系'];
        $sex=['','先生','女士'];
        $from=['','电话营销','网络营销','朋友介绍','猪八戒','一品威客'];

        $da['status']=$st[$da['status']];
        $da['sex']=$sex[$da['sex']];
        $da['from']=$from[$da['from']];

        return json($da);
    }
    public function edit(){
        $post=$this->request->param();
        $id=$this->request->param()['id'];
        unset($post['id']);
        if($post['next_time'])
            $post['next_time']=strtotime($post['next_time']);
        if($post['addr'])
            $post['addr']=str_replace(' ','',$post['addr']);
        Db::name('clue')->where('clue_id',$id)->update($post);
        return 1;
    }
}
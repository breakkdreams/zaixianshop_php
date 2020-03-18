<?php

/**
 * @Author: user
 * @Date:   2018-12-18 14:59:49
 * @Last Modified by:   user
 * @Last Modified time: 2019-01-07 09:34:43
 */

namespace app\api\controller;

use think\Controller;
use think\Db;
use app\api\model\ApiContractModel;
use app\api\model\AccessModel;//访问权限

/**
 * //线上合同api控制器
 */
class ApiContractController extends Controller
{
	/**
	 * 合同列表
	 * @return [type] [description]
	 */
	public function  contract_list()
	{
		if(!$this->request->isPost()){

			return json(['code'=>2,'msg'=>'请求类型错误！']);
		}

		$userid=$this->request->param()['user_id'];

        $contractModel=new ApiContractModel();
        //读取权限
        $accessModel=new AccessModel();

        $accessIds=$accessModel->readAccess($userid);

		$res=$contractModel->getContractList($accessIds);

		//数据处理
		$list=[];
		foreach ($res as $key => $value) {
			$it=[];
			$it['id']=$value['id'];
			$it['project_name']=$value['project_name'];
			$it['contract_date']=$value['contract_date'];
			$it['principal_name']=$value['principal_name'];
			$it['client_name']=$value['client_name'];
			//合同到期时间计算  工期到期时间加上售后
			$it['contract_end_date']=$value['end_date']+$value['aftersale_date']*86400;
			$list[]=$it;
		}

		return json($list);
	}


	/**
	 * 判断是否为时间戳
	 * @param  [type]  $timestamp [description]
	 * @return boolean            [description]
	 */
	public function is_timestamp($timestamp) {
		if(!ctype_digit($timestamp)){
			return false;
		}else if(strtotime(date('Y-m-d H:i:s',$timestamp))==$timestamp){
				return $timestamp;
		}else return false;
	}
	/**
	 * 添加合同
	 * @return [type] [description]
	 */
	public function contract_add()
	{
		//$vs="{'table':[{'stage':'','content':'','amount':'',time:''},{'stage':'','content':'','amount':'',time:''}]}";
		//dump($vs);exit;
		if(!$this->request->isPost()){
			
			return json(['code'=>2,'msg'=>'请求类型错误！']);
		}
		$data=$this->request->param();
		/*****************************contract表**********************************/

		//判断是否为时间戳格式
		
		if(false==$this->is_timestamp($data['contract_date']) || false==$this->is_timestamp($data['start_date'])|| false==$this->is_timestamp($data['end_date']))
		{
			return json(['code'=>2,'msg'=>'时间字段数据格式错误，请传递标准时间戳(秒数)！']);
		}
		
		//
		$data['contract_date']=strtotime(date('Y-m-d',$data['contract_date']));
		$data['start_date']=strtotime(date('Y-m-d',$data['start_date']));
		$date['end_date']=strtotime(date('Y-m-d',$data['end_date']));

		//处理{'stage':'','content':'','amount':'',time:''},{'stage':'','content':'','amount':'',time:''},
		//替换转义符号  去掉结尾一个逗号
		$data['stage_info']=str_replace('&quot;','"',$data['stage_info']);
		//$data['stage_info']=substr($data['stage_info'],0,strlen($data['stage_info'])-1);
		$data['stage_info']="{'table':".$data['stage_info']."}";//格式化
		//处理versions
		$data['versions']=array_filter(explode(',',$data['versions']));
		
		$data['versions'] = json_encode($data['versions'], JSON_UNESCAPED_UNICODE);
		//合同编号
		$data['num']=date('Y',time()).'-ZY'.strval(time()).strval(rand(1000,9999));

		//项目开发阶段信息
		$data['status']=1;


		/****************************hetong表*******************************************/

		//hetong 表数据
		$ht['contract_num']=$data['num'];
		$ht['contract_time']=$data['contract_date'];//签订时间
		//售后天数
		$ht['sh_time']=$data['aftersale_date'];
		//售后开始时间
		$ht['start_time']=$data['end_date'];
		//售后到期时间
		$ht['end_time']=$data['end_date']+$ht['sh_time']*86400;

		//合同金额
		$ht['contract_money']=$data['contract_amount'];
		$ht['sk_money']=$data['received_amount'];
		$ht['sy_money']=(float)$data['contract_amount']-(float)$data['received_amount'];
		//签订人
		$ht['user_id']=$data['principal_id'];
		$ht['user']=Db::name('user')->where('id='.$ht['user_id'])->find()['user_login'];
		//客户
		$kh=Db::view('lx')->view('kehu','company','kehu.id=lx.kh_id')->where('kehu.id','=',$data['client_id'])->find();
		$ht['username']=$kh['name'];
		$ht['mobile']=$kh['mobile'];
		$ht['kh_name']=$kh['company'];
		//标题
		$ht['title']=$data['project_name'];
		//内容
		$ht['content']=$data['detail'];
		$ht['status']=0;

		//存入数据
		$insert=['hetong'=>$ht,'contract'=>$data];
		$contractModel=new ApiContractModel();

		$res=$contractModel->saveContract($insert);

		if($res){
			return json(['code'=>1,'msg'=>'合同新增成功！']);
		}else{
			return json(['code'=>2,'msg'=>'合同新增失败，请稍后再试！']);
		}

	}

	/**
	 * 合同编辑
	 * @return [type] [description]
	 */
	public function contract_edit()
	{
		if(!$this->request->isPost()){
			
			return json(['code'=>2,'msg'=>'请求类型错误！']);
		}
		$data=$this->request->param();
		/*****************************contract表**********************************/

		//判断是否为时间戳格式
		if(false==$this->is_timestamp($data['contract_date']) || false==$this->is_timestamp($data['start_date'])|| false==$this->is_timestamp($data['end_date']))
		{
			return json(['code'=>2,'msg'=>'时间字段数据格式错误，请传递时间戳(秒)！']);
		}
		$data['contract_date']=strtotime(date('Y-m-d',$date['contract_date']));
		$data['start_date']=strtotime(date('Y-m-d',$date['start_date']));
		$data['end_date']=strtotime(date('Y-m-d',$date['end_date']));

		//处理{'stage':'','content':'','amount':'',time:''},{'stage':'','content':'','amount':'',time:''},
		//替换转义符号  去掉结尾一个逗号
		$data['stage_info']=str_replace('&quot;','"',$data['stage_info']);
		$data['stage_info']=substr($data['stage_info'],0,strlen($data['stage_info'])-1);
		$data['stage_info']="{'table':".$data['stage_info']."}";//格式化
		//处理versions
		$data['versions']=explode(',',$data['versions']);
		$data['versions'] = json_encode($data['versions'], JSON_UNESCAPED_UNICODE);
		//合同编号
		$data['num']=date('Y',time()).'-ZY'.strval(time()).strval(rand(1000,9999));

		//项目开发阶段信息
		$data['status']=1;




		/****************************hetong表*******************************************/

		//hetong 表数据
		$ht['id']=$data['hetong_id'];
		$ht['contract_num']=$data['num'];
		$ht['contract_time']=$data['contract_date'];//签订时间
		//售后天数
		$ht['sh_time']=$data['aftersale_date'];
		//售后开始时间
		$ht['start_time']=$data['end_date'];
		//售后到期时间
		$ht['end_time']=$data['end_date']+$ht['sh_time']*86400;

		//合同金额
		$ht['contract_money']=$data['contract_amount'];
		$ht['sk_money']=$data['received_amount'];
		$ht['sy_money']=(float)$data['contract_amount']-(float)$data['received_amount'];
		//签订人
		$ht['user_id']=$data['principal_id'];
		$ht['user']=Db::name('user')->where('id='.$ht['user_id'])->find()['user_login'];
		//客户
		$kh=Db::view('lx')->view('kehu','company','kehu.id=lx.kh_id')->where('kehu.id','=',$data['client_id'])->find();
		$ht['username']=$kh['name'];
		$ht['mobile']=$kh['mobile'];
		$ht['kh_name']=$kh['company'];
		//标题
		$ht['title']=$data['project_name'];
		//内容
		$ht['content']=$data['detail'];
		$ht['status']=0;

		//更新数据
		$insert=['hetong'=>$ht,'contract'=>$data];
		$contractModel=new ApiContractModel();

		$res=$contractModel->updateContract($insert);

		if($res){
			return json(['code'=>1,'msg'=>'合同更新成功！']);
		}else{
			return json(['code'=>2,'msg'=>'合同更新失败，请稍后再试！']);
		}

	}

	/**
	 * 合同删除
	 * @return [type] [description]
	 */
	public function contract_del()
	{
		if(!$this->request->isPost()){
			
			return json(['code'=>2,'msg'=>'请求类型错误！']);
		}
		$contractModel=new ApiContractModel();

		$contractId=$this->request->param();//合同id
		$res=$contractModel->deleteContract($contractId);

		if($res){

			return json(['code'=>1,'msg'=>'删除成功！']);

		}else {

			return json(['code'=>2,'msg'=>'删除失败！']);
		}

	}

	/**
	 * 获取客户信息
	 * @return [type] [description]
	 */
	public function getClientInfo()
	{

	}

	/**
	 * 合同详情
	 */
	public  function contract_particulars()
	{
		if(!$this->request->isPost()){
			
			return json(['code'=>2,'msg'=>'请求类型错误！']);
		}
		$data=$this->request->param();

		$contractModel=new ApiContractModel();

		$res=$contractModel->getContractById($data['id']);

		$res['stage_info']=json_decode($res['stage_info'])->table;

		//return json($res);

		/*
		$k='{"project_name":"","contract_amount":"","received_amount":"","contract_time":"2018-12-20","end_date":"2018-12-20","aftersale_date":"","start_date":"2018-12-20","stage_info":"{\"stage\":\"dsa\",\"content\":\"123\",\"amount\":\"123\",\"time\":\"123\"},"}';

		$k=json_decode($k,true);
		dump($k);
		dump($k['stage_info']);
		$st=substr($k['stage_info'],0,strlen($k['stage_info'])-1);
		dump($st);*/

	}


}

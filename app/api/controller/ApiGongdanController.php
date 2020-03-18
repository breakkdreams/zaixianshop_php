<?php

/**
 * @Author: user
 * @Date:   2018-11-01 14:23:06
 * @Last Modified by:   user
 * @Last Modified time: 2019-01-07 10:23:57
 */

//工单api控制器
/**
 * 
 */

namespace app\api\controller;
use app\api\model\ApiGongdanModel;
use app\api\model\ApiGongdanReplyModel;
use think\Controller;
use think\Url;
use think\Db;

/**
 * 工单api控制器类
 */
class ApiGongdanController extends Controller
{
	//获取所有工单
	public function get_all(){
		$gdModel=new ApiGongdanModel();
		$data=$gdModel->getAll();
		return json($data);
	}

	/**
	 * 根据userid获取工单列表
	 */
	public function get_list_by_userid()
	{

		$data=$this->request->get();

		$userid=intval($data['userid'],0);

		if($userid>0){

			//获取数据
			$gdModel=new ApiGongdanModel();
			$result=$gdModel->getListByUserid();
			return json($result);

		}else{

			return json(['code'=>2,'msg'=>'参数错误！']);

		}
		
	}

	/**
	 * 新增工单
	 */
	public function add_gd()
	{

		if(!$this->request->isPost()){return json(['code'=>2,'msg'=>'请求类型错误！']);}//请求类型错误
			//获取数据
			$data=$this->request->param();

			//return json($data);exit();
			$gdModel=new ApiGongdanModel();
	
			//根据用户id获取用户信息
			try {
				
			$user=Db::name('user')->where('id','=',$data['userid'])->select();
			$data['username']=$user[0]['user_login'];
			$data['mobile']=$user[0]['mobile'];

			$result=$gdModel->insertGongdan($data);
			} catch (Exception $e) {
				return json(['code'=>2,'msg'=>$e.getMessage()]);
				
			}

			if(false==$result){

				return json(['code'=>2,'msg'=>'提交失败稍后重试！']);
			}

			return json(['code'=>1,'msg'=>'工单提交成功！']);

	}

	/**
	 * 修改工单状态  开启1  关闭2
	 */

	public function modify_status()
	{
		$data=$this->request->get();
		$gdModel=new ApiGongdanModel();

		if(!in_array($data['status'],[1,2])){
			return json(['code'=>2,'msg'=>'非法数据无法提交！']);
		}


		$result=$gdModel->where('id='.$data['id'])->update(['status'=>$data['status']]);

		if($result){

			return json(['code'=>1,'msg'=>'操作成功！']);
		}else{

			return json(['code'=>2,'msg'=>'操作失败，请稍后重试！']);
		}

	}

	/**
	 * 新增回复
	 */

	public function add_reply()
	{
		if(!$this->request->isPost()){return json(['code'=>2,'msg'=>'请求类型错误！']);}//请求类型错误
		
			//保存数据数据
			$data=$this->request->param();
			$gdReplyModel=new ApiGongdanReplyModel();
			$result=$gdReplyModel->insertReply($data);

			if(false==$result){

				return json(['code'=>2,'msg'=>'操作失败，请稍后重试！']);
			}else{
				
				//通过工单id将zy_xiaoxi更新为未读消息
				Db::name('gongdan')->where('id='.$data['gd_id'])->update(['zy_xiaoxi'=>1,'state'=>2]);

				return json(['code'=>1,'msg'=>'回复成功！']);
			}

	}


	/**
	 * 通过工单id获取工单详细信息
	 */

	public function get_info_by_gd_id()
	{
		//if(!$this->request->isPost()){return json(['code'=>2,'msg'=>'请求类型错误！']);}//请求类型错误
		$id=$this->request->param()['id'];//工单id
		//获取工单信息
		$gdModel=new ApiGongdanModel();
		$data=$gdModel->getInfoByGdId($id);
		foreach ($data as $arr) {
			$arr['content']=cmf_replace_content_file_url(htmlspecialchars_decode($arr['content']));
			$data[]=$arr;
		}

		//根据工单id获取工单回复
		$gdReplyModel=new ApiGongdanReplyModel();
		$replyData=$gdReplyModel->getReplyByGdId($id);

		foreach ($replyData as $arr) {
			$arr['content']= cmf_replace_content_file_url(htmlspecialchars_decode($arr['content']));
		}

		//加入到数据中
		$data['reply_list']=$replyData;

		//通过工单id将zy_xiaoxi更新为已读消息
			Db::name('gongdan')->where('id','=',$id)->update(['kh_xiaoxi'=>1,'state'=>3]);		

		return json($data);


	}

	/**
	 * 获取工单类型 type
	 */
	public function get_gongdan_type()
	{
		return json(Db::name('gongdan_type')->select());
	}

}
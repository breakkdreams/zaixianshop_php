<?php 
namespace plugins\member\controller; 

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;
/**
 * 会员组管理控制器
 */
class MemberGroupController extends PluginAdminBaseController
{
	public function index()
	{	
		$where = 1;
		$data = Db::name('member_group')->select()->toArray();

		//TODO 此处循环中执行sql，会严重影响效率，稍后考虑在memebr_group表中加入会员数字段和统计会员总数功能解决。
		foreach ($data as $k=>$v) {
			$membernum = Db::name('member')->where(['groupid'=>$v['groupid']])->count();
			$data[$k]['membernum'] = $membernum;
		}


		// 签名加密解密
		/*$data = [
			'title'=>'我是标题',
			'content'=>'我是内容',
		];

		dump(zy_json_sign('en',$data));*/


		/*$data2 = [
			'title'=>'我是标题',
			'content'=>'我是内容',
			'appid'=>'123456',
			'jsapi_ticket'=>'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhZG1pbiIsInN1YiI6InVzZXIiLCJqdGkiOiJJakV5TXpRMU5pSS5JakF3TURBd01DSS5YLVh6Um16YWtXaEhOQzFZQjlDcFFtZnNVR1Z4dHQzVWtEazBOMDhiT0dFIiwiZXhwIjoxNTcwNTA4Nzc1fQ.zrf8msLmrdZAO8LZkEkg9fY1DSdY4ZiELDzSMaJ3e8Y',
			'timestamp'=>1570501631,
			'noncestr'=>'123456789',
		];
		$sign = '467E2B37163D782A58E710D63D665EDB';
		dump(zy_json_sign('de',$data2,$sign));
		exit();*/
		// 签名加密解密

		$this->assign('data',$data);
		return $this->fetch('/memberGroup/index');
	}



	public function addGroupPage(){
		return $this->fetch('/memberGroup/addGroupPage');
	}



	/**
	 * 添加会员组
	 */
	public function addGroup(){
		$param = $this->request->param();

		$data['name'] = $param['name'];
		$data['description'] = $param['description'];
		Db::name('member_group')->insert($data);


		return zy_json_echo(true,'添加成功！','',200);
	}





	public function editGroupPage(){
		$param = $this->request->param();
		if(empty($param['groupid'])){
			$this->error("传参错误,无id");
		}
		$data = Db::name('member_group')->where('groupid',$param['groupid'])->find();

		$this->assign('data',$data);
		return $this->fetch('/memberGroup/editGroupPage');

	}


	public function editGroup(){
		$param = $this->request->param();
		if(empty($param['groupid'])){
			$this->error("传参错误,无id");
		}
		$groupid = $param['groupid'];
		$data['name'] = $param['name'];
		$data['description'] = $param['description'];
		$results = Db::name('member_group')->where('groupid',$groupid)->update($data);

		if(empty($results)){
			return zy_json_echo(false,'添加失败！','',-1);
		}else{
			return zy_json_echo(true,'添加成功！','',200);
		}
	}



	/**
	 * 删除会员组
	 */
	public function deleteGroup(){
		$param = $this->request->param();
		if(empty($param['groupid'])){
			$this->error("传参错误，无id");
		}
		$groupid = $param['groupid'];
		Db::name('member_group')->delete($groupid);

		$this->success("删除成功");
		$this->error("删除失败");
	}



}
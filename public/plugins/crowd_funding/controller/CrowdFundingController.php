<?php 
namespace plugins\crowd_funding\controller;

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;


/**
 * 配置管理控制器
 */
class CrowdFundingController extends PluginAdminBaseController
{
	public function index(){
		$where = 1;
		$data = Db::name('crowd_funding')->where($where)->paginate(20);
		$this->assign('page', $data->render());//单独提取分页出来
		$this->assign('data',$data);
		return $this->fetch('/crowd_funding/index');
	}

    public function addGlobalConfigPage(){
        return $this->fetch('/global_config/addGlobalConfigPage');
    }

    public function editGlobalConfigPage(){
        $param = $this->request->param();
        if(empty($param['id'])){
            return json(['type'=>'error','msg'=>'无id']);
        }

        $id = $param['id'];
        $data = Db::name('global_config')->where('id',$id)->find();
        $this->assign('data',$data);
        return $this->fetch('/global_config/editGlobalConfigPage');
    }

    public function addGlobalConfig(){
        $param = $this->request->param();
        $add= [];
        $add['title'] = $param['title'];
        $add['content'] = $param['content'];
        $add['describe'] = $param['describe'];
        $id = Db::name('global_config')->insertGetId($add);
        if(empty($id)){
            return zy_array(false,'添加失败','',300,false);
        }
        return zy_array(true,'添加成功','',200,false);
    }

    public function editGlobalConfig(){
        $param = $this->request->param();
        $add= [];
        $add['content'] = $param['content'];
        $add['describe'] = $param['describe'];
        $id = Db::name('global_config')->where('id', $param['id'])->update($add);
        if(empty($id)){
            return zy_array(false,'添加失败','',300,false);
        }
        return zy_array(true,'添加成功','',200,false);
    }

}
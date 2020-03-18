<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;
use think\Db;
use cmf\controller\HomeBaseController;
use app\portal\model\GongdanModel;
use app\portal\model\Gongdan_xqModel;
use think\Request;
class IndexController extends HomeBaseController
{
	public function index()
	{
		$userid =cmf_get_current_user_id();
		$gd = new GongdanModel();

		$info = $gd->where(['userid'=>$userid])->paginate(10);

		$wd = $gd::where(['userid'=>$userid])->field("count(id) as  ids")->find()['ids'];

		$this->assign('info', $info);
		$this->assign('wd', $wd);
		return $this->fetch(':gongdan');
	}
	
	public function gongdan()
	{
		
		$userid =cmf_get_current_user_id();
		$gd = new GongdanModel();
		
		$info = $gd->where(['userid'=>$userid])->paginate(10);
		
		$wd = $gd::where(['userid'=>$userid])->field("count(id) as  ids")->find()['ids'];
		
		$this->assign('info', $info);
		$this->assign('wd', $wd);
		return $this->fetch(':gongdan');
	}
	
	public function khqr()
	{

		$info = Db::name('qrkh')->where(['id'=>$_GET['id']])->find();
		
		$this->assign('info', $info);	
		
		return $this->fetch(':khqr');
	}
	
	public function add_qr()
	{
		$request = Request::instance();
        $domain=$request->domain();
		$data = $this->request->param();
		$data['img'] = substr(strstr($data['img'],','),1);
		
		$img = base64_decode($data['img']);
		
		$time = time();
		$i =  file_put_contents(ROOT_PATH.'/public/upload/images/'.$time.".jpg",$img);		

		$imgaes =$domain.'/upload/images/'.$time.'.jpg';	
		
		$re = Db::name('qrkh')->where(['id'=>$data['id']])->update(['img'=>$imgaes,'status'=>1,'qr_time'=>$time]);
		if($re && $data['img']!=""){
			
			exit("1");
			
		}else{
			
			
			exit("2");
		}
		
		
		
		
	}
	
	
	public function gongdan_add()
	{
		
		$type = Db::name('gongdan_type')->select();
		
		$this->assign('type', $type);
		return $this->fetch(':gongdan_add');
	}
	public function gongdan_xq()
	{
		$id  = $this->request->param("id");
		
		$gd_xq = new Gongdan_xqModel();
		$gd = new GongdanModel();
		$gds = $gd_xq->where(['gd_id'=>$id])->paginate();

		$gd = $gd->where(['id'=>$id])->find();
		
		$gd->where(['id'=>$id])->update(['kh_xiaoxi'=>2]);
		
		$geshu = $gd_xq::where(['gd_id'=>$id])->field("count(id) as  ids")->find()['ids'];
		$this->assign('gd', $gd);
		$this->assign('gds', $gds);
		$this->assign('geshu', $geshu);
		return $this->fetch(':gongdan_xq');
	}
	
	public function add_hf()
	{
		$gd = new GongdanModel();
		$gd_xq = new Gongdan_xqModel();
		$data  = $this->request->param();
		
		$userid =cmf_get_current_user_id();
		
		if(!$userid){

			$this->error("请登录");
			exit();
		}

		if(!isset($data['post']['content'])){

			$this->error("请输入内容");
			exit();
		}
		$username = Db::name('user')->where('id','=',$userid)->find();
		
		$data['post']['username']=$username['user_login'];
		$data['post']['add_time']=time();
		$data['post']['userid']=$userid;

		
		$re = $gd_xq->save($data['post']);
		
		
		$gd->where(['id'=>$data['post']['gd_id']])->update(['zy_xiaoxi'=>1]);
		
		if($re){

			$this->success("提交成功");

		}else{


			$this->error("提交失败");
		}	
		
	}
	public function add_gd()
	{
		$data  = $this->request->param();

	$gd = new GongdanModel(); //实例化模型
	$userid =cmf_get_current_user_id();
	if(!$userid){
		
		$this->error("请登录");
		exit();
	}

	$username = Db::name('user')->where('id','=',$userid)->find();	
	$data['post']['num']=time().rand(1111,9999);
	$data['post']['userid']=$userid;
	$data['post']['status']=1;	
	$data['post']['add_time']=time();
	$data['post']['username']=$username['user_login'];
	$data['post']['mobile']=$username['mobile'];
	$data['post']['state']=1;	
	
	$re = $gd->save($data['post']);

	if($re){
		
		$this->success("提交成功",url('index/index'));
		
	}else{
		
		
		$this->error("提交失败");
	}	
}

public function gongdan_kai(){

	$data  = $this->request->param("id");	

	$re = Db::name('gongdan')->where(['id'=>$data])->update(['status'=>1,'modify_time'=>time()]);	
	
	if($re){
		
		$this->success("开启成功");
		
	}else{
		
		
		$this->error("开启失败");
	}		

}

public function gongdan_guan(){

	$data  = $this->request->param("id");	

	$re = Db::name('gongdan')->where(['id'=>$data])->update(['status'=>2,'modify_time'=>time()]);	
	
	if($re){
		
		$this->success("关闭成功");
		
	}else{
		
		
		$this->error("关闭失败");
	}		

}

	 /**
     * 用户签名提交
     * @return [type] [description]
     */
    public function yhqm(){
    	if($this->request->isPost()){
    		$request = Request::instance();
        	$domain=$request->domain();
	    	$data = $this->request->param();
			$data['img'] = substr(strstr($data['img'],','),1);
			
			$img = base64_decode($data['img']);
			
			$time = time();
			$i =  file_put_contents(ROOT_PATH.'/public/upload/admin/yhqm/'.$time.".jpg",$img);		

			$imgaes = $domain.'/upload/admin/yhqm/'.$time.'.jpg';	
			
			$re = Db::name('user')->where(['id'=>$data['id']])->update(['sign'=>$imgaes]);
			if($re && $data['img']!=""){
				
				exit("1");
				
			}else{
				
				
				exit("2");
			}
    	}else{
    		$id=$this->request->get('id');
    		$u=Db::name('user')->where('id='.$id)->find();
    		$this->assign('sign',$u['sign']);
    		return $this->fetch(":yhqm");
    	}
    	
    }


    //合同预览
    public function contract_preview()
    {

    	$id=$this->request->param('id');
		//获取合同关联的数据
		$res=Db::view('contract')->view('lx','name,mobile','lx.id=contract.client_id')->view('kehu','company,address','lx.kh_id=kehu.id')->view('user','user_login,sign as user_sign','user.id=contract.principal_id')->where('contract.id='.$id)->find();
				//所需工时
		$res['work_time']=($res['end_date']-$res['start_date'])/86400;
		//时间格式化：
		$res['contract_date']=date("Y年m月d日",$res['contract_date']);//签约时间
		$res['lose_date']=date("Y年m月d日",($res['end_date']+$res['aftersale_date']*86400));//合同失效时间
		$res['start_date']=date("Y年m月d日",$res['start_date']);//工期开始时间
		$res['end_date']=date("Y年m月d日",$res['end_date']);//工期结束时间
		//转换大小写金额
		
		$res['contract_amount']="￥".$res['contract_amount']."元 大写：( ".cny($res['contract_amount'])." )";

		//公章
		$res['user_seal']="__STATIC__/images/seal.jpg";//乙方公章
		//获取模板数据
		$mb=Db::name('htmodel')->where('id='.$this->request->param('mbid'))->field('content,content1')->find();
		//解码
		$content=htmlspecialchars_decode($mb['content']);
		$content1=htmlspecialchars_decode($mb['content1']);
		//替换模板中标记数据数据
		//模板填充参数处理
		$find=[];//查找的字符串
		$replace=[];//替换的字符串
		$param=htparamConfig();
		$i=0;
		foreach ($res as $key=>$value) {
			
			if(array_key_exists($key,$param)){
				if($param[$key]['type']=='string'){
					if($key=='versions'){//版本数据处理
						$value=implode("&nbsp;&nbsp;&nbsp;&nbsp;",json_decode($value));
					}
					$find[$i]=$param[$key]['data'];
					$replace[$i]=$value;
					$i++;
				}else{
					if($key=='detail'){//开发细则数据处理
						$value=htmlspecialchars_decode($value);
					}
					$param[$key]['data']=$value;
				}
			}
		}

		//替换模板的标记字符串
		$mb=str_replace($find, $replace, $content);
		$mb1=str_replace($find, $replace, $content1);

		//echo date('Y年m月d日',time());
		//dump($mb);
		$stage_info=$param['stage_info']['data'];//json_decode($param['stage_info']['data']);//开发表格数据

		$this->assign('mb',html_entity_decode($mb));
		$this->assign('mb1',html_entity_decode($mb1));
		$this->assign('stage_info',$stage_info);
		return $this->fetch(":contract_preview");
    }



}

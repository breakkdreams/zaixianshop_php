<?php
namespace plugins\member_address\controller;
use cmf\controller\PluginAdminBaseController;
use think\Db;


/**
    * 需求配置首页
    *@actionInfo(
    *  'name' => '地址管理',
    *  'symbol' => 'member_address',
    *  'list' => [
    *      'member_one' => [
    *          'demandName' => '会员模块' ,
    *          'demandSymbol' => 'member',
    *          'explain' => '获取测试接口数据AA'
    *      ],
    *      'site_configuration_one' => [
    *          'demandName' => '站点配置' ,
    *          'demandSymbol' => 'site_configuration',
    *          'explain' => '获取站点配置'
    *      ]
    *  ]
     *)
    */
   
class AdminIndexController extends PluginAdminBaseController
{
	protected function _initialize()
    {
        parent::_initialize();
        $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        }
    }

	/**
     * @adminMenu(
     *     'name'   => '地址管理',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '地址管理',
     *     'param'  => ''
     * )
     */
    
	public function index(){

		return $this->fetch();
	}


	/**
     * 地区主信息页面
     * @param  cri_superior_code   int   上级地区代码   
     * @return 首页的所有信息（各类数据）
     */
	public function subord(){
        $fa_id = "";
        $fa_code = "";
		$param = $this->request->param();
		if(empty($param['cri_superior_code'])){
			$aaaa = Db::name('memberaddress_cn_region')->where('cri_level',1)->find();
			$data = Db::name('memberaddress_cn_region')->where('cri_level',1)->order('cri_sort','asc')->paginate(20)->each(function($item,$key){
				$da = Db::name('memberaddress_cn_region')->where('cri_superior_code',$item['cri_code'])->find();
				if(!empty($da)){
					$item['subord'] = 1;
				}else{
					$item['subord'] = 2;
				}
				return $item;
			});
		}else{
			$cri_superior_code = $param['cri_superior_code'];
			$fa_data = Db::name('memberaddress_cn_region')->where('cri_code',$cri_superior_code)->find();
            if(!empty($fa_data)){
                $fa_code = $fa_data['cri_superior_code'];
                $fa_id = $fa_data['cri_id'];
            }
			$data = Db::name('memberaddress_cn_region')->where('cri_superior_code',$cri_superior_code)->order('cri_sort','asc')->paginate(20)->each(function($item,$key){
				$da = Db::name('memberaddress_cn_region')->where('cri_superior_code',$item['cri_code'])->find();
				if(!empty($da)){
					$item['subord'] = 1;
				}else{
					$item['subord'] = 2;
				}
				return $item;
			});
		}
		$page = $data->render();
		$this->assign('page',$page);//单独提取分页出来
		$this->assign('data',$data);
        $this->assign('fa_id',$fa_id);
        $this->assign('fa_code',$fa_code);
		return $this->fetch();
	}



	/**
     * 添加下级地区页面
     * @param  cri_id   int   当前地区代码   
     * @return 数据
     */
	public function addpage(){
		$param = $this->request->param();
		if(empty($param['cri_id'])){
			$this->error('未传递id');
		}
		$data = Db::name('memberaddress_cn_region')->where('cri_id',$param['cri_id'])->find();
		$superior = (empty($data)) ? '中国' : $data['cri_name'];
		$this->assign('data',$data);
		$this->assign('superior',$superior);
		return $this->fetch();

	}



	/**
     * 添加下级地区操作
     * @param  cri_id     		int   上级地区id   
     * @param  cri_name  		int   添加的地区名称   
     * @param  cri_code   		int   添加的地区代码   
     * @param  cri_short_name   int   添加的地区代码   
     * @return 添加成功与否
     */
	public function add(){
		$param = $this->request->param();
		if(empty($param['cri_id']) || empty($param['cri_name']) || empty($param['cri_code']) || empty($param['cri_short_name'])){
			return json(['type'=>'error','msg'=>'传参错误']);
		}
		$fa_data = Db::name('memberaddress_cn_region')->where('cri_id',intval($param['cri_id']))->find();
		if(empty($fa_data)){
			return json(['type'=>'error','msg'=>'查无上级信息']);
		}
		$add['cri_name'] = $param['cri_name'];//名称
		$add['cri_code'] = $param['cri_code'];//地区代码
		$add['cri_short_name'] = $param['cri_short_name'];//地区代码
		$add['cri_superior_code'] = $fa_data['cri_code']; //上级代码

		$re_data = Db::name('memberaddress_cn_region')->where('cri_code',$param['cri_code'])->find();
		if(!empty($re_data)){
			return json(['type'=>'error','msg'=>'该地区代码已被使用']);
		}
		$add['cri_memo'] = '';	//备注
		if(!empty($param['cri_memo'])){
			$add['cri_memo'] = $param['cri_memo'];
		}

		$add['cri_level'] = intval($fa_data['cri_level']) +1; //等级
		$add['cri_gmt_create'] = date('Y-m-d H:i:s',time());	//创建时间
		$sort = Db::name('memberaddress_cn_region')->where('cri_superior_code',$fa_data['cri_code'])->max('cri_sort');
		// $sort = Db::name('memberaddress_cn_region')->where('cri_superior_code',$fa_data['cri_code'])->find();
		$add['cri_sort'] = 0; //排序
		if(isset($sort)){
			$add['cri_sort'] = intval($sort) +1; //排序
		}
		$add['status'] = 1; //状态
		$re = Db::name('memberaddress_cn_region')->insert($add);
		if(empty($re)){
			return json(['type'=>'error','msg'=>'失败']);
		}
		return json(['type'=>'success','msg'=>'添加成功']);
	}


	/**
     * 修改信息页面
     * @param  cri_id     		int   当前地区id     
     * @return 添加成功与否
     */
	public function editpage(){
		$param = $this->request->param();
		if(empty($param['cri_id'])){
			$this->error('未传递cri_id');
		}
		$data = Db::name('memberaddress_cn_region')->where('cri_id',$param['cri_id'])->find();
		$superior_data = Db::name('memberaddress_cn_region')->where('cri_code',$data['cri_superior_code'])->find();
		$superior = (empty($superior_data)) ? '中国' : $superior_data['cri_name'];
		$this->assign('data',$data);
		$this->assign('superior',$superior);
		return $this->fetch();

	}




	/**
     * 修改信息操作
     * @param  cri_id     		int   当前地区id   
     * @param  cri_name  		int   修改后当前地区名称   
     * @param  cri_code   		int   修改后当前地区代码   
     * @param  cri_short_name   int   修改后当前地区代码   
     * @return 修改成功与否
     */
	public function edit(){
		$param = $this->request->param();
		if(empty($param['cri_id']) || empty($param['cri_name']) || empty($param['cri_code']) || empty($param['cri_short_name'])){
			return json(['type'=>'error','msg'=>'传参错误']);
		}
		$update['cri_id'] = $param['cri_id'];
		$update['cri_name'] = $param['cri_name'];
		$update['cri_code'] = $param['cri_code'];
		$update['cri_short_name'] = $param['cri_short_name'];
		$re_data = Db::name('memberaddress_cn_region')->where('cri_code',$param['cri_code'])->find();
		if($re_data){
			if($param['cri_id']!=$re_data['cri_id']){
				return json(['type'=>'error','msg'=>'该地区代码已被使用']);
			}
		}
		$re = Db::name('memberaddress_cn_region')->update($update);
		if(empty($re)){
			return json(['type'=>'error','msg'=>'编辑失败']);
		}
		return json(['type'=>'success','msg'=>'编辑成功']);
	}




	/**
     * 地区操作 --删除
     * @param  cri_id     		int   当前地区id   
     * @return 删除成功与否
     */
	public function del(){
		$param = $this->request->param();
		if(empty($param['cri_id'])){
			$this->error("传值错误");
		}
		$cri_id = $param['cri_id'];
		$re = Db::name('memberaddress_cn_region')->where('cri_id',$cri_id)->delete();
		if(empty($re)){
			$this->error("删除失败");
		}
		$this->success("删除成功");
	}




	/**
     * 地区操作--启用-显示
     * @param  cri_id     		int   当前地区id   
     * @return 操作成功与否
     */
	public function turn_on(){
		$param = $this->request->param();
		if(empty($param['cri_id'])){
			$this->error("传值错误");
		}
		$update['cri_id'] = $param['cri_id'];
		$data = Db::name('memberaddress_cn_region')->where('cri_id',$update['cri_id'])->find();
		if($data['status']==1){
			$this->error("无需多次显示");
		}
		$update['status'] = 1;
		$re = Db::name('memberaddress_cn_region')->update($update);
		if(empty($re)){
			$this->error("显示除失败");
		}
		$this->success("显示成功");
	}





	/**
     * 地区操作--禁用-隐藏
     * @param  cri_id     		int   当前地区id   
     * @return 操作成功与否
     */
	public function turn_off(){
		$param = $this->request->param();
		if(empty($param['cri_id'])){
			$this->error("传值错误");
		}
		$update['cri_id'] = $param['cri_id'];
		$data = Db::name('memberaddress_cn_region')->where('cri_id',$update['cri_id'])->find();
		if($data['status']==2){
			$this->error("无需多次隐藏");
		}
		$update['status'] = 2;
		$re = Db::name('memberaddress_cn_region')->update($update);
		if(empty($re)){
			$this->error("隐藏失败");
		}
		$this->success("隐藏成功");
	}





	/**
     * 地区操作--排序操作 （jquery传值）
     * @param  key     			array   当前页地区的cri_id数组
     * @param  value     		array   当前页地区的cri_sort数组  
     * @return 操作成功与否
     */	
	public function sort(){
		$param = $this->request->param();
		if(empty($param['key']) || empty($param['value'])){
			return json(['type'=>'error','msg'=>'无值']);
		}
		$keys = explode(",", $param['key']);
		$values = explode(",", $param['value']);
		foreach($keys as $k=>$v){
			$data[$v] = intval($values[$k]);
		}
		foreach($data as $key=>$value){
			$update = [];
			$re = '';
			$update = ['cri_id'=>$key,'cri_sort'=>$value];
			$re = Db::name('memberaddress_cn_region')->update($update);
		}
		return json(['type'=>'success','msg'=>'更新完成']);
	}


//---------------------------------------------------------------------------updateConfig 更新缓存


	public function updateConfig(){
		$time = time();

		$da = $this->getSubord();
		foreach($da as $key=>$value){
			$da1 = [];
			$data[$key]['label'] = $value['cri_name'];
			$data[$key]['value'] = $value['cri_code'];
			$da1 = $this->getSubord($value['cri_code']);
			if(!empty($da1)){
				foreach($da1 as $key1=>$value1){
					$da2 = [];
					$data[$key]['children'][$key1]['label'] = $value1['cri_name'];
					$data[$key]['children'][$key1]['value'] = $value1['cri_code'];
					$da2 = $this->getSubord($value1['cri_code']);
					if(!empty($da2)){
						foreach($da2 as $key2=>$value2){
							$data[$key]['children'][$key1]['children'][$key2]['label'] = $value2['cri_name'];
							$data[$key]['children'][$key1]['children'][$key2]['value'] = $value2['cri_code'];
						}
					}
				}
			}
		}
		$config = [
            'area'=>$data,
            'update_time'=>$time
        ];
        saveModuleConfigData('member_address','config','area.json',$config);
        $this->success("更新缓存成功");
	}




//********************************************************
	/**
     * 根据地区代码获取下级地区代码
     * @param    [str]   cri_code   [地区代码]    (可选)     
     * @return   [arr]              [下级地区信息] 
     */
    public function getSubord($cri_code='000000')
    {
        $da = Db::name('memberaddress_cn_region')->where(['cri_superior_code'=>$cri_code,'status'=>1])->order('cri_sort','asc')->select()->Toarray();
        if(empty($da)){
            return false;
        }
        foreach($da as $key=>$value){
            $data[$key]['cri_code'] = $value['cri_code'];
            $data[$key]['cri_name'] = $value['cri_name'];
            $data[$key]['cri_sort'] = $value['cri_sort'];
        }
        return $data;
    }










}
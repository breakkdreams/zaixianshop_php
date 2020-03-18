<?php 
namespace plugins\freight\controller;

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;


/**
 * 运费管理控制器
 */
class FreightController extends PluginAdminBaseController
{
	public function index(){
		$where = 1;
		$data = Db::name('freight')->where($where)->paginate(20);
		$this->assign('page', $data->render());//单独提取分页出来
		$this->assign('data',$data);
		return $this->fetch('/freight/index');
	}

	//////////////////////////////////////////配置 start/////////////////////////////////////////
    public function config_index(){
        $where = 1;
        $data = Db::name('freight_config')->where($where)->paginate(20);
        $this->assign('page', $data->render());//单独提取分页出来
        $this->assign('data',$data);
        return $this->fetch('/freight_config/index');
    }

    public function addFreightConfigPage()
    {
        return $this->fetch('/freight_config/addFreightConfigPage');
    }
    public function addFreightConfig(){
        $param = $this->request->param();

        $add= [];
        $add['symbol'] = $param['symbol'];
        $add['methods'] = $param['methods'];
//        $add['param'] = $param['param'];

        $first_id = Db::name('freight_config')->insertGetId($add);

        if(empty($first_id)){
            return zy_array(false,'添加失败','',300,false);
        }
        return zy_array(true,'添加成功','',200,false);
    }

    public function deleteFreightConfig(){
        $param = $this->request->param();
        if(empty($param['id'])){
            $this->error("传参错误");
        }
        $id = $param['id'];
        $re = Db::name('freight_config')->where('id',$id)->delete();
        if(empty($re)){
            $this->error("删除失败");
        }
        $this->success("删除成功");
    }

    //////////////////////////////////////////配置 end/////////////////////////////////////////

	/**
	 * 添加运费页面
	 */
	public function addFreightPage()
	{
		return $this->fetch('/freight/addFreightPage');
	}

    public function getSubord()
    {
        $da1 = Db::name('memberaddress_cn_region')->where(['cri_level'=>1,'status'=>1])->order('cri_sort','asc')->select()->Toarray();

        foreach($da1 as $key=>$value){
            $da1[$key]['value'] = $value['cri_name'];
            $da1[$key]['label'] = $value['cri_name'];
            $da1[$key]['disabled'] = false;
            $da2 = Db::name('memberaddress_cn_region')->where(['cri_level'=>2,'status'=>1,'cri_superior_code'=>$value['cri_code']])->order('cri_sort','asc')->select()->Toarray();
            foreach($da2 as $key2=>$value2){
//                $data['list']['children'] = $value2['CRI_NAME'];
                $da1[$key]['children'][$key2]['value'] = $value2['cri_name'];
                $da1[$key]['children'][$key2]['label'] = $value2['cri_name'];
                $da1[$key]['children'][$key2]['disabled'] = false;
            }
        }
        // return zy_json_echo(true,'查询成功',$data,200);
        return zy_array(true,'查询成功',$da1,200,false);
    }

	/**
	 * 添加运费操作
	 */
	public function addFreight(){
		$param = $this->request->param();

        $add= [];
		$add['title'] = $param['title'];
		$add['type'] = $param['type'];
		$add['default_num'] = $param['default_num'];
		$add['default_price'] = $param['default_price'];
		$add['continue_price'] = $param['continue_price'];
		$add['continue_num'] = $param['continue_num'];
		$add['free_shipping'] = $param['free_shipping'];
		$add['create_time'] = time();
        $first_id = Db::name('freight')->insertGetId($add);

		foreach ($param['freight_item'] as $item){
		    $area = '';
		    if(!empty($item['area'])){
                foreach ($item['area'] as $area_item){
                    if(!empty($area)){
                        $area.=',';
                    }
                    $area.=$area_item[1];
                }
                $add_item = [];
                $add_item['freight_id'] = $first_id;
                $add_item['area'] = $area;
                $add_item['first_num'] = $item['first_num'];
                $add_item['first_price'] = $item['first_price'];
                $add_item['continue_price'] = $item['continue_price'];
                $add_item['continue_num'] = $item['continue_num'];
                $add_item['create_time'] =time();
                Db::name('freight_item')->insert($add_item);
            }
        }

		if(empty($first_id)){
			return zy_array(false,'添加失败','',300,false);
		}
		return zy_array(true,'添加成功','',200,false);
	}

	/**
	 * 查看运费详情
	 */
	public function detailFreightPage(){
		$param = $this->request->param();
		if(empty($param['id'])){
			return json(['type'=>'error','msg'=>'无id']);
		}

		$id = $param['id'];
		$data = Db::name('freight')->where('id',$id)->find();
		$this->assign('data',$data);

        $list = Db::name('freight_item')->where(['freight_id'=>$id])->select();
        $this->assign('itemData',$list);
		return $this->fetch('/freight/detailFreightPage');
	}

	/**
	 * 删除运费
	 */
	public function deleteFreight(){
		$param = $this->request->param();
		if(empty($param['id'])){
			$this->error("传参错误");
		}
		$id = $param['id'];

//		//查询运费配置
//        $configList = Db::name('freight_config')->select();
//        foreach ($configList as $item){
//            $symbol =$item['symbol'];
//            $id = $item['methods'];
//            $param = ['freight_id'=>$id,'isModule'=>true];
//            getModuleApiData( $symbol, $id, $param);
//        }
//
		$re = Db::name('freight')->where('id',$id)->delete();
		$re = Db::name('freight_item')->where('freight_id',$id)->delete();
		if(empty($re)){
			$this->error("删除失败");
		}
		$this->success("删除成功");
	}



}
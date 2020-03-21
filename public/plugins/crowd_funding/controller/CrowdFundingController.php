<?php 
namespace plugins\crowd_funding\controller;

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;


/**
 * 配置管理控制器
 */
class CrowdFundingController extends PluginAdminBaseController
{
    /**
     * @var string
     */
    private $uploadPath;

    protected function _initialize()
    {
        parent::_initialize();
        $this->uploadPath = ROOT_PATH.'public'.DS.'upload'.DS;
        $this->uploadPathUrl = ZY_APP_PATH.'upload/goodsimg/';
    }
    
	public function index(){
		$where = 1;
		$data = Db::name('crowd_funding')->where($where)->paginate(20);
		$this->assign('page', $data->render());//单独提取分页出来
		$this->assign('data',$data);
		$this->assign('basePath',$this->uploadPathUrl);
		return $this->fetch('/crowd_funding/index');
	}

    public function add(){
        return $this->fetch('/crowd_funding/add');
    }

    /**
     *商品主图上传/商品详情图
     */
    public function uploadimg(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $outpath = $this->uploadPath.'goodsimg';
            $info = $file->move($outpath);
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                //echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg

                $data = str_replace("\\","/",$info->getSaveName());

                return zy_array(true,'连入成功',$data,200,false);
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getFilename();
            }else{
                // 上传失败获取错误信息
                return zy_array(false,'连入失败',$file->getError(),300,false);
            }
        }
    }


    /**
     *商品主图上传/商品详情图
     */
    public function uploadEditImg(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $outpath = $this->uploadPath.'goodsimg';
            $info = $file->move($outpath);
            if($info){
                $data = [
                    'code'=> 0,
                    'msg' => '',
                    'data' => [
                        'src'=> ZY_APP_PATH.'upload/goodsimg/'.str_replace("\\","/",$info->getSaveName())
                    ]
                ];
                return $data;
            }else{
                // 上传失败获取错误信息
                return zy_array(false,'连入失败',$file->getError(),300,false);
            }
        }
    }

    public function editCrowFundingPage(){
        $param = $this->request->param();
        if(empty($param['id'])){
            return json(['type'=>'error','msg'=>'无id']);
        }

        $id = $param['id'];
        $data = Db::name('crowd_funding')->where('id',$id)->find();
        if(!empty($data)){
            $data['value2'] = $data['start_time'].",".$data['end_time'];
            $data['img_path_old'] = $data['img_path'];
            $data['img_path'] = $this->uploadPathUrl.$data['img_path'];
            $data['img_path_list_old'] = $data['img_path_list'];
            $arr = explode(',', $data['img_path_list']);
            $new_list = '';
            foreach ($arr as $key=>$item) {
                if($new_list!=''){
                    $new_list .= ',';
                }
                $new_list .= $this->uploadPathUrl.$item;
            }
            $data['img_path_list'] = $new_list;
        }
        $this->assign('data',$data);
        return $this->fetch('/crowd_funding/edit');
    }

    public function addCrowFunding(){
        $param = $this->request->param();
        $add= [];
        $add['name'] = $param['name'];
        $add['img_path'] = $param['img_path'];
        $add['img_path_list'] = $param['img_path_list'];
        $add['describe'] = $param['describe'];
        $add['price'] = $param['price'];
        $add['num'] = $param['num'];
        $add['start_time'] = $param['start_time'];
        $add['end_time'] = $param['end_time'];
        $add['info'] = htmlspecialchars_decode($param['info']);
        $add['show_index'] = $param['show_index'];
        $add['create_time'] = time();
        $add['sale'] = $param['sale'];
        $add['state'] = 0;//0.未开始 1.进行中 2.已结束
        if(!empty($param['start_time']) && !empty($param['end_time'])){
            $st = $param['start_time'];
            $et = $param['end_time'];
            $nt = time()*1000;
            if($nt>$et){
                $add['state'] = 2;
            }elseif ($nt>$st && $nt<$et){
                $add['state'] = 1;
            }
        }

        $id = Db::name('crowd_funding')->insertGetId($add);
        if(empty($id)){
            return zy_array(false,'添加失败','',300,false);
        }
        return zy_array(true,'添加成功','',200,false);
    }

    public function editCrowFunding(){
        $param = $this->request->param();
        $add= [];
        $add['name'] = $param['name'];
        $add['img_path'] = $param['img_path'];
        $add['img_path_list'] = $param['img_path_list'];
        $add['describe'] = $param['describe'];
        $add['price'] = $param['price'];
        $add['num'] = $param['num'];
        $add['start_time'] = $param['start_time'];
        $add['end_time'] = $param['end_time'];
        $add['info'] = htmlspecialchars_decode($param['info']);
        $add['show_index'] = $param['show_index'];
        $add['sale'] = $param['sale'];
        $add['state'] = 0;//0.未开始 1.进行中 2.已结束
        if(!empty($param['start_time']) && !empty($param['end_time'])){
            $st = $param['start_time'];
            $et = $param['end_time'];
            $nt = time()*1000;
            if($nt>$et){
                $add['state'] = 2;
            }elseif ($nt>$st && $nt<$et){
                $add['state'] = 1;
            }
        }
        $id = Db::name('crowd_funding')->where('id', $param['id'])->update($add);
        if(empty($id)){
            return zy_array(false,'修改失败','',300,false);
        }
        return zy_array(true,'修改成功','',200,false);
    }

    public function deleteCrowFunding($isModule=false){
        $param = $this->request->param();
        if(empty($param['id'])){
            $this->error("传参错误");
        }
        $id = $param['id'];
        $re = Db::name('crowd_funding')->where('id',$id)->delete();
        if(empty($re)){
            return zy_array (false,'删除失败','删除失败',300 ,$isModule);
        }
        return zy_array (true,'删除成功','删除成功',200 ,$isModule);
    }

}
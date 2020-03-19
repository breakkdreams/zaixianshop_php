<?php
namespace plugins\home_configuration\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;
use think\Log;




class TypeController extends PluginAdminBaseController
{
	protected function _initialize()
    {
        parent::_initialize();
        $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        }
    }

    public function index()
    {   
        $param = $this->request->param();
        $keyword='';
        if(!empty($param['keyword'])){
            $keyword=$param['keyword'];
        }
        $data = Db::name('home_configuration_type')->where('type',1)->where('type_name','like','%'.$keyword.'%')->order('sort','asc')->paginate(10)->each(function($item,$key){
                $return = $this->getDomain();
                if(empty($item['img_url'])){
                    $item['img_url'] = $return."/plugins/home_configuration/view/public/default/hhh.png";
                }else{
                    $item['img_url'] = $return.$item['img_url'];
                }
                return $item;
            });
        $page = $data->render();
        $this->assign('page',$page);//单独提取分页出来
        $this->assign('data',$data);
        $this->assign('keyword',$keyword);
        return $this->fetch();
    }


    //网页端皮肤主页
    public function webType()
    {   
        $param = $this->request->param();
        $keyword='';
        if(!empty($param['keyword'])){
            $keyword=$param['keyword'];
        }
        $data = Db::name('home_configuration_type')->where('type',2)->where('type_name','like','%'.$keyword.'%')->order('sort','asc')->paginate(10)->each(function($item,$key){
                $return = $this->getDomain();
                if(empty($item['img_url'])){
                    $item['img_url'] = $return."/plugins/home_configuration/view/public/default/hhh.png";
                }else{
                    $item['img_url'] = $return.$item['img_url'];
                }
                return $item;
            });
        $page = $data->render();
        $this->assign('page',$page);//单独提取分页出来
        $this->assign('data',$data);
        $this->assign('keyword',$keyword);
        return $this->fetch();
    }


    //添加皮肤页面
    public function addTypePage()
    {   
    	$param = $this->request->param();
    	$type=$param['type'];
    	$this->assign('type',$type);
        return $this->fetch();
    }


    //添加皮肤操作
    public function addType()
    {   
        $param = $this->request->param();
        $add['type_name'] = $param['name'];
        $add['type'] = $param['type'];
        $add['img_url'] = $param['picture'];
        $add['jump_url'] = $param['jump'];
        $add['status'] = $param['status'];
        $best_sort = Db::name('home_configuration_type')->where('type',$param['type'])->order('sort','desc')->find();
        if(!empty($best_sort['sort'])){
            $add['sort'] = $best_sort['sort']+1;
        }else{
            $add['sort'] = 1;
        }
        $re_data = Db::name('home_configuration_type')->where('type_name',$add['type_name'])->find();
        if(!empty($re_data)){
            return zy_json_echo(false,'该名称已被使用，不可重复添加',null,100);
        }
        $re = Db::name('home_configuration_type')->insert($add);
        if(empty($re)){
            return zy_json_echo(false,'添加失败',null,101);
        }
        return zy_json_echo(true,'添加成功',$param,200);
    }




     public function editTypePage()
    {   
        $param = $this->request->param();
        $data = Db::name('home_configuration_type')->where('id',$param['id'])->find();
        if(!empty($data)){
            $return = $this->getDomain();
            if(empty($data['img_url'])){
                $data['img_url'] = $return."/plugins/home_configuration/view/public/default/hhh.png";
            }else{
                $data['img_url'] = $return.$data['img_url'];
            }
        }
        $this->assign('data',$data);
        return $this->fetch();
    }




     public function editType()
    {   
        $param = $this->request->param();
        $update['id'] = $param['id'];
        $update['type_name'] = $param['name'];
        $update['type'] = $param['type'];
        $update['jump_url'] = $param['jump'];
        $update['status'] = $param['status'];
        if(!empty($param['picture'])){
            $update['img_url'] = $param['picture'];
        }
        $re_data = Db::name('home_configuration_type')->where('type_name',$update['type_name'])->find();
        if(!empty($re_data)){
            if($re_data['id']!=$update['id']){
                return zy_json_echo(false,'该名称已被使用，不可重复添加',null,100);
            }
        }
        $re = Db::name('home_configuration_type')->update($update);
        if(empty($re)){
            return zy_json_echo(false,'修改失败',null,101);
        }
        return zy_json_echo(true,'修改成功',$param,200);
    }



    //删除操作
    public function delType()
    {   
        $param = $this->request->param();
        if(empty($param['id'])){
            return $this->error('缺少关键参数id');
        }
        $re = Db::name('home_configuration_type')->where('id',$param['id'])->delete();
        if(empty($re)){
            return $this->error('删除失败');
        }
        return $this->success('删除成功');
    }


    /**
     * 多条删除留言
     */
    public function allDelete(){
        $param = $this->request->param();
        if(empty($param['ids'])){
            return json(['type'=>'error','msg'=>'参数ids未传']);
        }
        $ids = explode(",", $param['ids']);
        foreach($ids as $key=>$value){
            $re ='';
            $re = Db::name('home_configuration_type')->where('id',$value)->delete();
            if(empty($re)){
                return json(['type'=>'error','msg'=>'删除失败']);
            }
        }
        return json(['type'=>'success','msg'=>'删除成功']);
    }







//----------------------------------------------------------------------------------------排序操作

    /**
     * 皮肤操作--排序操作 （jquery传值）
     * @param  key              array   当前页皮肤的id数组
     * @param  value            array   当前页皮肤的sort数组  
     * @return 操作成功与否
     */ 
    public function typeSort(){
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
            $update = ['id'=>$key,'sort'=>$value];
            $re = Db::name('home_configuration_type')->update($update);
        }
        return json(['type'=>'success','msg'=>'更新完成']);
    }





     //--------------------------------------------------------------------- ↓ 使用中

    /**
    * 上传任务图片
    *
    * @return json  返回json格式数据如下：
    * {error:110  : 没有上传内容与内容非法      120 :  图片不是通过HTTP POST 上传
    * 
    * 
    */
    public function uprwImage(){
        $data = $this->request->param();
        if(empty($_FILES)){
            return zy_json_echo(false,'非法上传内容！',null,110);
        }
        $file=$_FILES['file'];
        $upload_path = "./plugins/home_configuration/view/public/img/";
        // $upload_path = "./upload/home_imgurl/";

            

        //照片是否存在
            if(is_uploaded_file($file['tmp_name'])){
                if(!file_exists($upload_path)){
                        mkdir($upload_path,0777,true);
                    }
                $kz=substr($file['name'],strrpos($file['name'],'.'));

                $sui=mt_rand(1000,9999);
                $filename=date('YmdHis').$sui.$kz;
                $pic=$upload_path.$filename;
                // return zy_json_echo(false,$pic,null,110);

                if(move_uploaded_file($file['tmp_name'], $pic)){
                    // $this->compressedImage($pic,$pic);
                    $pic= explode('.',$pic,2)['1'];
                    // $pic="http://tp.300c.cn/zy_137/jianghuling/public".$pic;
                    return zy_json_echo(true,'获取成功',$pic,200);
                }else{
                    return zy_json_echo(false,$file['error'],null,103);
                 
                }

            }else{
                return zy_json_echo(false,"图片获取路径错误！",null,120);
            }


 
    }



    /**
   * desription 压缩图片
   * @param sting $imgsrc 图片路径
   * @param string $imgdst 压缩后保存路径
   */
  public function compressedImage($imgsrc, $imgdst) {
    list($width, $height, $type) = getimagesize($imgsrc);
     
    $new_width = $width;//压缩后的图片宽
    $new_height = $height;//压缩后的图片高
         
    if($width >= 600){
      $per = 600 / $width;//计算比例
      $new_width = $width * $per;
      $new_height = $height * $per;
    }
     
    switch ($type) {
      case 1:
        $giftype = check_gifcartoon($imgsrc);
        if ($giftype) {
          header('Content-Type:image/gif');
          $image_wp = imagecreatetruecolor($new_width, $new_height);
          $image = imagecreatefromgif($imgsrc);
          imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
          //90代表的是质量、压缩图片容量大小
          imagejpeg($image_wp, $imgdst, 90);
          imagedestroy($image_wp);
          imagedestroy($image);
        }
        break;
      case 2:
        header('Content-Type:image/jpeg');
        $image_wp = imagecreatetruecolor($new_width, $new_height);
        $image = imagecreatefromjpeg($imgsrc);
        imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        //90代表的是质量、压缩图片容量大小
        imagejpeg($image_wp, $imgdst, 90);
        imagedestroy($image_wp);
        imagedestroy($image);
        break;
      case 3:
        header('Content-Type:image/png');
        $image_wp = imagecreatetruecolor($new_width, $new_height);
        $image = imagecreatefrompng($imgsrc);
        imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        //90代表的是质量、压缩图片容量大小
        imagejpeg($image_wp, $imgdst, 90);
        imagedestroy($image_wp);
        imagedestroy($image);
        break;
    }
  }

//------------------------------------------------------------------------------------------↑↓模块配置调用

    public function getDomain(){
        $data = [];
        $symbol ='home_configuration';
        $id = 'one_site_configuration';
        $param = ['data'=>$data,'isModule'=>true];
        $return = getModuleApiData( $symbol, $id, $param);
        $da = $return['data']['basic']['site_domain'];
        return $da;
    }



}

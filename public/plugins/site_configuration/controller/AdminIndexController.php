<?php
namespace plugins\site_configuration\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
// use cmf\controller\ApiBaseController;//引入此类
use think\Db;
use think\Log;
// use Think\Log:record()
// use think\Request;


class AdminIndexController extends PluginAdminBaseController
// class AdminIndexController extends ApiBaseController
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
     *     'name'   => '站点配置',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '站点配置',
     *     'param'  => ''
     * )
     */
    
    public function index()//SEO配置主页
    {   
        $request = request();
        $module_info = getModuleConfig('site_configuration','config','config.json');
        $module_info = json_decode($module_info,true);

        $data['title'] = $module_info['title'];
        $data['keyword'] = $module_info['keyword'];
        $data['description'] = $module_info['description'];
        $data['img_url'] = $module_info['img_url'];
        $this->assign('data',$data);
        $this->assign('da',json_encode($data));
        return $this->fetch();

    }




    //修改SEO配置操作
    public function editSetting(){
        $param = $this->request->param();
        $module_info = getModuleConfig('site_configuration','config','config.json');
        $module_info = json_decode($module_info,true);
        $basicconfig = getModuleConfig('site_configuration','config','basicconfig.json');
        $basicconfig = json_decode($basicconfig,true); 
        if($module_info['img_url']==$param['picture']){
            $config['img_url'] = $module_info['img_url'];
        }else{
            $config['img_url'] = $basicconfig['site_domain'].$param['picture'];
        }
        $config['title'] = $param['title'];
        $config['keyword'] = $param['keyword'];
        $config['description'] = $param['description'];
        saveModuleConfigData('site_configuration','config','config.json',$config);
        $this->success("修改成功");
    }


    //basic基本配置页面
    public function basicInfo(){
        $module_info = getModuleConfig('site_configuration','config','basicconfig.json');
        $module_info = json_decode($module_info,true); 
        $data['site_name'] = $module_info['site_name'];
        $data['site_table'] = $module_info['site_table'];
        $data['site_domain'] = $module_info['site_domain'];
        $data['app_path'] = $module_info['app_path'];
        $this->assign('data',$data);
        return $this->fetch();
    }


    
    //basic修改基本配置操作
    public function editBasicSetting(){
        $param = $this->request->param();
        $config = [
            'site_name'=>$param['site_name'],
            'site_table'=>$param['site_table'],
            'site_domain'=>$param['site_domain'],
            'app_path'=>$param['app_path']
        ];
        saveModuleConfigData('site_configuration','config','basicconfig.json',$config);

        $this->success("修改成功");
    }



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
        // return $_FILES;
        $file=$_FILES['file'];

        // return zy_json_echo(true,$file['tmp_name'],null,110);
        // $upload_path = cmf_get_image_url("renwu/");
        // $upload_path = "./upload/site_imgurl/";
        $upload_path = "./plugins/site_configuration/view/public/img/";


            

        //照片是否存在
            if(is_uploaded_file($file['tmp_name'])){
                if(!file_exists($upload_path)){
                        mkdir($upload_path,0777,true);
                    }
                $kz=substr($file['name'],strrpos($file['name'],'.'));

                $sui=mt_rand(1000,9999);
                $filename=date('YmdHis').$sui.$kz;
                $pic=$upload_path.$filename;
                //取消原来的压缩图片流程 让透明图片正常显示
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

//------------------------------------------------------------------------------------------












}

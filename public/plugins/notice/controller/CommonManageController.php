<?php
namespace plugins\notice\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;


class CommonManageController extends PluginAdminBaseController
{
    //分页数量
    private $pageNum = 20;

    const APPID = 'wx5b74a5c289a4b2a5';
    const SECRET = '8c73b91c2c927df3a02795d74e04264f';

    protected function _initialize()
    {
        parent::_initialize();
        $adminId = cmf_get_current_admin_id();
        //获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        }
    }




    /*
     * 网站配置信息
     */
    private function readConfig(){

        $symbol = 'notice';
        
        $id = 'readConfigInfo';

        $arr = ['data'=>null,'isModule'=>true];

        //调用配置接口
        $web_config = getModuleApiData($symbol,$id,$arr);

        return $web_config['data'];
    }





     /**
     * @adminMenu(
     *     'name'   => '公众号管理',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '公众号管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $data = $this->request->param();

        $send = Db::name('notice_send')->where('type',6)->order('create_time','desc')->paginate($this->pageNum);

        $this->assign('send',$send);

        $this->assign('data',$data);

        $send->appends($data);

        $this->assign('page',$send->render());

        return $this->fetch();
    }


    /*
     * 客服消息发送页
     */
    public function kefuSms()
    {   
        $data = $this->request->param();

        $member = Db::name('member')->alias('m')
                    ->field('m.uid,m.nickname,mb.wechatpe_openid')
                    ->join('member_detail mb','m.uid=mb.uid')
                    ->where('mb.wechatpe_openid','<>','')->select();

        $this->assign('member',$member);

        return $this->fetch();
    }




    /**
    * 上传任务图片
    * @return json  返回json格式数据如下：
    * {error:110  : 没有上传内容与内容非法      120 :  图片不是通过HTTP POST 上传
    */
    public function pzUpload(){
        $data=$this->request->param();

        if(empty($_FILES)){
            return zy_json_echo(false,'非法上传内容！',null,110);
        }

        // dump($_FILES);exit;

        $file=$_FILES['file'];
        $upload_path = "./upload/xiaoxi/";
        //照片是否存在
        if($file['name'] <> ""){
            if(is_uploaded_file($file['tmp_name'])){
                if(preg_match('/\\.(gif|jpeg|png|bmp|jpg|tiff|)$/i', $file['name'])){
                    $kz=substr($file['name'],strrpos($file['name'],'.'));
                    $sui=mt_rand(1000,9999);
                    $filename=date('YmdHis').$sui.$kz;
                    $pic=$upload_path.$filename;
                    //判断pingzheng文件夹是否存在
                    if(!file_exists($upload_path)){
                        mkdir($upload_path,0777,true);
                    }
                    if(move_uploaded_file($file['tmp_name'], $pic)){
                        $this->compressedImage($pic,$pic);
                        $pic= explode('.',$pic,2)['1'];
                        return $this->success("上传成功",$pic);
                    }else{
                        $this->error("上传失败");
                    }
                }else{
                    return zy_json_echo(false,'文件不是图片格式！',null,105);
                }
            }else{
                return zy_json_echo(false,"图片获取路径错误！",null,120);
            }
        }else{
            return zy_json_echo(false,"没有上传图片！",null,108);
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






    //获取access_token的方法。
    public function getToken()
    {
        $appid = self::APPID;
        $appsecret = self::SECRET;

        $TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $TOKEN_URL = $this->https_request($TOKEN_URL);

        $result=json_decode($TOKEN_URL,true);

        return $result['access_token'];
    }








    /*
     * 客服消息单发
     */
    public function kfsms()
    {
        $data = $this->request->param();
        //获取Token
        $token = $this->getToken();
        //消息发送接口
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$token;
        //临时素材上传接口
        $upl_img = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$token."&type=image";

        $user = explode('|',$data['user']);

        $openid = $user[0]; //用户openid

        if($data['type']=='text') {
            //文本发送
            $da = [
                'touser' => $openid,
                'msgtype' => 'text',
                'text' => [
                    'content' =>$data['content'], //发送内容
                ],
            ];
        } else if ($data['type']=='image') {
            //图片发送
            $tu = substr($data['tupian'],1);
            $path = array('media'=>new \CURLFile(realpath($tu),'image/jpg') ); //上传
            $back = $this->https_request($upl_img,$path);

            $da = [
                'touser' => $openid,
                'msgtype' => 'image',
                'image' => [
                    'media_id' =>json_decode($back,true)['media_id'], //发送内容
                ],
            ];
        } 

        $da = json_encode($da,JSON_UNESCAPED_UNICODE);
        $back = $this->https_request($url,$da); //发送
        $arr = json_decode($back,true);

        $add = [
            'uid' => $user[1],
            'nickname' => $user[2],
            'content' => !empty($data['content'])?$data['content']:$data['tupian'],
            'recipients' => $user[0],
            'create_time' => time(),
            'status' => 1,
            'type' => 6,
            'send_time' => time(),
            'is_success' => 1,
            'comm_type' => '客服消息',
        ];

        if ($arr['errmsg']=='ok') {
            Db::name('notice_send')->insert($add);
            $this->success('发送成功',cmf_plugin_url('Notice://common_manage/index'));
        } else {
            $this->error('发送失败');
        }
    }





    /**
     * request 请求
     */
    public function https_request($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }













    /*
     * 模板消息发送页
     */
    public function tempSms()
    {
        $data = $this->request->param();

        return $this->fetch();
    }







}
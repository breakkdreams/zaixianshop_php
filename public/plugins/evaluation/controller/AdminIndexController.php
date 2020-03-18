<?php
namespace plugins\evaluation\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

/**
* 需求配置首页
*@actionInfo(
*  'name' => '评价管理',
*  'symbol' => 'evaluation',
*  'list' => [
*      'readConfigInfo' => [
*          'demandName' => '配置模块',
*          'demandSymbol' => 'site_configuration',
*          'explain' => '获取网站配置信息'
*      ],
*      'readgoodsinfo' => [
*          'demandName' => '商品模块',
*          'demandSymbol' => 'goods',
*          'explain' => '获取商品信息'
*      ],
*  ]
 *)
*/
class AdminIndexController extends PluginAdminBaseController
{
    //分页数量
    private $pageNum = 20;

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

        $symbol = 'evaluation';
        
        $id = 'readConfigInfo';

        $arr = ['data'=>null,'isModule'=>true];

        //调用配置接口
        $web_config = getModuleApiData($symbol,$id,$arr);

        return $web_config['data'];
    }





     /**
     * @adminMenu(
     *     'name'   => '评价管理列表',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '评价管理列表',
     *     'param'  => ''
     * )
     */
    public function index(){

        $data = $this->request->param();

        $where = 1;
        if (isset($data['productid']) && !empty($data['productid'])) {
            $where .= " and productid=".$data['productid'];
        }
        if (isset($data['is_pingtai']) && !empty($data['is_pingtai'])) {
            $where .= " and is_pingtai=".$data['is_pingtai'];
        }
        if (isset($data['start_addtime']) && !empty($data['start_addtime'])) {
            $where .= " and time >=".strtotime($data['start_addtime']);
        }
        if (isset($data['end_addtime']) && !empty($data['end_addtime'])) {
            $where .= " and time <=".strtotime($data['end_addtime']);
        }
        if (isset($data['score']) && !empty($data['score'])) {

            switch ($data['score']) {
                case 1: //好评
                    $where .= " and score = 5";
                    break;
                case 2: //中评
                    $where .= " and score in (2,3,4)";
                    break;
                case 3: //差评
                    $where .= " and score = 1";
                    break;
            }
        }


        $pingjia = Db::name('pingjia')->where($where)->order('time','desc')->paginate($this->pageNum);

        //读取回复数
        foreach ($pingjia as $key => $value) {
            $reply_num = Db::name('pingjia_reply')->where('comment_id',$value['id'])->count();
            $value['reply_num'] = $reply_num;
            $pingjia[$key] = $value;
        }

        $this->assign('data',$data);

        $this->assign('pingjia',$pingjia);
        //在分页前保存分页条件
        $pingjia->appends($data);

        $this->assign('page',$pingjia->render()); //分页

        $this->assign('total',$pingjia->total()); //数量

        return $this->fetch();
    }



    /**
    * 评价管理_删除
    */
    public function delPingjia(){
        $data = $this->request->param();
        //都没有选择删除什么
        if(empty($data['id'])){
            $this->error("请选择要删除的信息");
        }

        //批量删除
        Db::name('pingjia')->where('id','in',$data['id'])->delete();

        $this->success("删除成功"); 
    }





    /**
    * 评价管理_添加
    */
    public function addPingjia(){
        $data = $this->request->param();

        if (isset($data['comment'])) {

            if (empty($data['productid'])) {
                $this->error('请填写商品id~');
            }

            $symbol = 'evaluation';
        
            $id = 'readgoodsinfo';

            $arr = ['id'=>$data['productid'],'isModule'=>true];

            //获取商品信息
            $goodsinfo = getModuleApiData($symbol,$id,$arr);

            if ($goodsinfo['status'] == 'error') {
                $this->error($goodsinfo['message']);
            }
            if (empty($data['score'])) {
                $this->error('评分不能为空~');
            }
            if ($data['score'] > 5) {
                $this->error('评分最高为5分~');
            }
            if (empty($data['comment'])) {
                $this->error('内容不能为空~');
            }
            if (empty($data['time'])) {
                $this->error('请填写时间~');
            }
            if (empty($data['nickname'])) {
                $this->error('昵称不能为空~');
            }

            //图片处理
            if (isset($data['thumb']) && !empty($data['thumb'])) {
                $thumb = json_encode( explode(',' ,substr($data['thumb'] ,0 ,strlen($data['thumb'])-1) ) );
            } else {
                $thumb = json_encode(array());
            }

            $nickname = $data['nickname']; //昵称

            $config = $this->readConfig(); //读取网站配置信息

            if (!empty($data['avatar'])) { 
                $avatar = $config['basic']['site_domain'].$data['avatar']; 
            } else {
                $avatar = $config['basic']['site_domain'].'/plugins/evaluation/view/public/assets/images/nophoto.gif'; //默认头像
            }

            $da = array(
                'productid' => $data['productid'], //商品id
                'uid' => '', //用户id
                'score' => $data['score'], //评分
                'comment' => $data['comment'], //内容
                'status' => isset($data['status']) ? 1 : 0, //是否匿名
                'thumb' => $thumb, //评价图片
                'time' => strtotime($data['time']), //时间
                'nickname' => $nickname, //昵称
                'avatar' => $avatar, //头像
                'specid_name' => $data['specid_name'], //规格
                'is_pingtai' => 1, //是否平台添加 1.平台 2.用户

                'goods_name' => $goodsinfo['data']['goodsname'],
                'goods_num' => $goodsinfo['data']['goodsname'],
                'goods_img' => $goodsinfo['data']['goodsimg'],
                'goods_price' => $goodsinfo['data']['shopprice'],
            );

            Db::name('pingjia')->insert($da);

            $this->success('操作成功');
        }

        return $this->fetch();

    }





    /**
    * 评价管理_修改
    */
    public function editPingjia(){
        $data = $this->request->param();

        writeError(['code'=>111,'msg'=>'错误']);

        if (isset($data['comment'])) {
            if (empty($data['productid'])) {
                $this->error('请填写商品id~');
            }

            $symbol = 'evaluation';
        
            $id = 'readgoodsinfo';

            $arr = ['id'=>$data['productid'],'isModule'=>true];

            //获取商品信息
            $goodsinfo = getModuleApiData($symbol,$id,$arr);

            if ($goodsinfo['status'] == 'error') {
                $this->error($goodsinfo['message']);
            }
            if (empty($data['score'])) {
                $this->error('评分不能为空~');
            }
            if ($data['score'] > 5) {
                $this->error('评分最高为5分~');
            }
            if (empty($data['comment'])) {
                $this->error('内容不能为空~');
            }
            if (empty($data['time'])) {
                $this->error('请填写时间~');
            }
            if (empty($data['nickname'])) {
                $this->error('昵称不能为空~');
            }

            //图片处理
            if (isset($data['thumb']) && !empty($data['thumb'])) {
                $thumb = json_encode( explode(',' ,substr($data['thumb'] ,0 ,strlen($data['thumb'])-1) ) );
            } else {
                $thumb = json_encode(array());
            }

            $nickname = $data['nickname'];

            $config = $this->readConfig();

            if (!empty($data['avatar'])) { 
                //判断是否http开头
                $urls = substr($data['avatar'],0,4);
                if ($urls == 'http') {
                    $avatar = $data['avatar'];
                } else {
                    $avatar = $config['basic']['site_domain'].$data['avatar']; 
                }
            } else {
                $avatar = $config['basic']['site_domain'].'/plugins/evaluation/view/public/assets/images/nophoto.gif'; //默认头像
            }

            $da = array(
                'productid' => $data['productid'], //商品id
                'uid' => '', //用户id
                'score' => $data['score'], //评分
                'comment' => $data['comment'], //内容
                'status' => isset($data['status']) ? 1 : 0, //是否匿名
                'thumb' => $thumb, //评价图片
                'time' => strtotime($data['time']), //时间
                'nickname' => $nickname, //昵称
                'avatar' => $avatar, //头像
                'specid_name' => $data['specid_name'], //规格
                'is_pingtai' => 1, //是否平台添加 1.平台 2.用户

                'goods_name' => $goodsinfo['data']['goodsname'],
                'goods_num' => $goodsinfo['data']['goodsname'],
                'goods_img' => $goodsinfo['data']['goodsimg'],
                'goods_price' => $goodsinfo['data']['shopprice'],
            );

            Db::name('pingjia')->where('id',$data['id'])->update($da);

            $this->success('修改成功');

        }

        $pingjia = Db::name('pingjia')->where('id',$data['id'])->find();

        $pingjia['time'] = date('Y-m-d H:i:s',$pingjia['time']);

        $pingjia['thumb'] = json_decode($pingjia['thumb']); 

        $thumb = '';
        foreach ($pingjia['thumb'] as $key => $value) {
            $thumb .= $value.',';
        }

        $this->assign('thumb_num',count($pingjia['thumb']));

        $this->assign('thumb',$thumb); 

        $this->assign('pingjia',$pingjia);

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
        $upload_path = "./upload/pingjia/";
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










    //评价配置
    public function evaluateSet(){
        $param = $this->request->param();

        $wuliu = Db::name('evaluate_set')->order('id','asc')->paginate($this->pageNum);

        $this->assign('wuliu',$wuliu);

        $wuliu->appends($param);

        $this->assign('page',$wuliu->render());

        return $this->fetch();
    }










    /*
     * 评价回复
     */
    public function reply(){

        $data = $this->request->param();

        if (isset($data['comment'])) {

            $pingjia = Db::name('pingjia')->where('id',$data['id'])->find();

            //图片处理
            if (isset($data['thumb']) && !empty($data['thumb'])) {
                $thumb = json_encode( explode(',' ,substr($data['thumb'] ,0 ,strlen($data['thumb'])-1) ) );
            } else {
                $thumb = json_encode(array());
            }

            $config = $this->readConfig();

            if (!empty($data['avatar'])) { 
                //判断是否http开头
                $urls = substr($data['avatar'],0,4);
                if ($urls == 'http') {
                    $avatar = $data['avatar'];
                } else {
                    $avatar = $config['basic']['site_domain'].$data['avatar']; 
                }
            } else {
                $avatar = $config['basic']['site_domain'].'/plugins/evaluation/view/public/assets/images/nophoto.gif'; //默认头像
            }

            $da = array(
                'comment_id' => $data['id'], //主评价表id
                'reply_uid' => '', //回复者uid
                'reply_name' => $data['nickname'], //回复者昵称
                'reply_avatar' => $avatar, //回复者头像
                'to_uid' => $pingjia['uid'], //被回复者uid
                'to_name' => $pingjia['nickname'], //被回复者昵称
                'to_avatar' => $pingjia['avatar'], //被回复者头像
                'content' => $data['comment'], //回复内容
                'reply_thumb' => $thumb, //图片
                'create_time' => strtotime($data['time']), //时间
            );

            Db::name('pingjia_reply')->insert($da);

            $this->success('操作成功');

        }

        return $this->fetch();
    }







    /*
     * 查看回复
     */
    public function replyInfo(){

        $data = $this->request->param();

        $reply =  Db::name('pingjia_reply')->where('comment_id',$data['id'])->select();

        foreach ($reply as $key => $value) {
            $value['reply_thumb'] = json_decode($value['reply_thumb']);

            $reply[$key] = $value;

        }

        $this->assign('reply',$reply);

        return $this->fetch();
    }







    /*
     * 无限子回复
     */
    public function addzihuifu(){

        $data = $this->request->param();

        $ping = Db::name('pingjia_reply')->where('id',$data['id'])->find();

        //图片处理
        if (isset($data['thumb']) && !empty($data['thumb'])) {
            $thumb = json_encode( explode(',' ,substr($data['thumb'] ,0 ,strlen($data['thumb'])-1) ) );
        } else {
            $thumb = json_encode(array());
        }

        $config = $this->readConfig();

        if (!empty($data['avatar'])) { 
            //判断是否http开头
            $urls = substr($data['avatar'],0,4);
            if ($urls == 'http') {
                $avatar = $data['avatar'];
            } else {
                $avatar = $config['basic']['site_domain'].$data['avatar']; 
            }
        } else {
            $avatar = $config['basic']['site_domain'].'/plugins/evaluation/view/public/assets/images/nophoto.gif'; //默认头像
        }

        $da = array(
            'comment_id' => $ping['comment_id'], //主评价表id
            'reply_uid' => '', //回复者uid
            'reply_name' => $data['nickname'], //回复者昵称
            'reply_avatar' => $avatar, //回复者头像
            'to_uid' => $ping['reply_uid'], //被回复者uid
            'to_name' => $ping['reply_name'], //被回复者昵称
            'to_avatar' => $ping['reply_avatar'], //被回复者头像
            'content' => $data['comment'], //回复内容
            'reply_thumb' => $thumb, //图片
            'create_time' => strtotime($data['time']), //时间
        );

        Db::name('pingjia_reply')->insert($da);

        $this->success('操作成功');


    }













    /*
     * 评分项
     */
    public function scoreType()
    {
        $data = $this->request->param();
        
        $pingfen = Db::name('evaluate_set')->paginate($this->pageNum);

        $this->assign('pingfen',$pingfen);

        $this->assign('page',$pingfen->render());

        return $this->fetch();
    }




    /*
     * 批量删除评分项
     */
    public function evaluateSetDel()
    {
        $data = $this->request->param();

        if (empty($data['id'])) {
            $this->error('请选择要删除的信息');
        }

        Db::name('evaluate_set')->where('id','in',$data['id'])->delete();

        $this->success('删除成功');
    }




    /*
     * 添加评分项
     */
    public function addScoreType()
    {
        $data = $this->request->param();

        if (empty($data['name'])) {
            $this->error('评分项不能为空');
        }

        $da['name'] = $data['name'];

        $da['activate'] = isset($data['activate'])?1:0;

        Db::name('evaluate_set')->insert($da);

        $this->success('添加成功');
    }




    /*
     * 修改评分项
     */
    public function editScoreType()
    {
        $data = $this->request->param();

        if (empty($data['name'])) {
            $this->error('评分项不能为空');
        }

        $da['name'] = $data['name'];

        Db::name('evaluate_set')->where('id',$data['id'])->update($da);

        $this->success('修改成功');
    }




    /*
     * 修改状态
     */
    public function editScoreActivate()
    {
        $data = $this->request->param();

        switch ($data['activate']) {
            case 1:
                $da['activate'] = 0;
                break;
            case 0:
                $da['activate'] = 1;
                break;
        }

        Db::name('evaluate_set')->where('id',$data['id'])->update($da);

        $this->success('操作成功');
    }



}
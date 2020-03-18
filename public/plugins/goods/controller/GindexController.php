<?php
namespace plugins\goods\controller; 

use think\Db;
use cmf\controller\PluginBaseController;//引用插件基类
use plugins\goods\model\GoodsModel;
use plugins\goods\model\GoodscatsModel;

//AdminIndexController类和类的index()方法是必须存在的 index() 指向admin_index.html模板也就是模块后台首页
// 并且继承PluginAdminBaseController
class GindexController extends PluginBaseController
{
    public $uploadpath = '';
    public $uploadpaths = '';
    protected function _initialize()
    {
        parent::_initialize();
        $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        //dump($adminId);exit;
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        }
        $this->uploadpath = ROOT_PATH.'public'.DS.'plugins'.DS.'goods'.DS.'view'.DS.'public'.DS.'image'.DS;
        $this->uploadpaths = ZY_APP_PATH.'plugins/goods/view/public/image/';
    }

    

    public function add($isModule = false)
    {

        $data = $this->request->get();
        
        if ( isset($_GET['sign']) ) {
            $data['sign'] = urldecode($data['sign']);
            //dump($data);die();
        }
        $data = zy_decodeData($data,$isModule);
        if (!$data['uid']) {
            die();
        }
        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$data['uid'],'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        if ( empty($rs['data']) ) {
            return zy_array (false,'非法访问','',-2 ,$isModule);
        }

        if(request()->isPost()){
            //$specattrdata = json_decode($_POST['specattrdata'],true);
            //dump(input());dump($specattrdata);die();
            //dump(input());die();
            $goods = new GoodsModel();
            $res = $goods->add($type=2,$data['uid']);

            if ($res['code'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
            return;
        }

        

        $m = new GoodsModel();
        $object = $m->getEModel('goods');
        $this->assign(array(
            'object'=>$object,
            //'url'=>input('url')
        ));
        return $this->fetch();
        //include '/../view/gindex/add.html';
    }

    public function edit($isModule = false)
    {
        $data = $this->request->get();
        if ( isset($_GET['sign']) ) {
            $data['sign'] = urldecode($data['sign']);
            //dump($data);die();
        }
        
        $data = zy_decodeData($data,$isModule);
        if (!$data['uid']) {
            die();
        }
        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$data['uid'],'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        if ( empty($rs['data']) ) {
            return zy_array (false,'非法访问','',-2 ,$isModule);
        }


        $id = input('id');
        if(request()->isPost()){
            //dump(input());die();
            $goods = new GoodsModel();
            $res = $goods->edit($type=2,$data['uid']);

            if ($res['code'] == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
            }
            return;
        }

        $info = db('goods')->where(['id'=>$id,'shopid'=>$data['uid']])->find();
        $info['goodsinfo'] = htmlspecialchars_decode($info['goodsinfo']);
        if ( empty($info) ) {
            return zy_array (false,'状态异常','',-3 ,$isModule);
        } 

        if ( !empty($info['goodsinfo']) ) {
            $info['goodsinfo'] = explode(',', $info['goodsinfo']);
        }
        if ( !empty($info['goodsalbum']) ) {
            $info['goodsalbum'] = explode(',', $info['goodsalbum']);
        }

        $m = new GoodsModel();
        $object = $m->getedit(input('get.id'),$data['uid']);
        //dump($object);die();
        $catarr = explode("-",$info['gcatpath']);
        if (!isset($catarr[2])) {
            $catarr[2] = '';
        }
        $cat1 = db('goodscats')->where(['pid'=>0])->select();
        $returnarr = [
            'catarr'=>$catarr,
            'cat1'=>$cat1,
            'info'=>$info,
            'object'=>$object,
            'url'=>input('url')
        ];
        
        if ( $catarr[1] > 0 ) {
            $cat2 = db('goodscats')->where(['pid'=>$catarr[0]])->select();
            $returnarr['cat2'] = $cat2;
        }
        if ( $catarr[2] > 0 ) {
            $cat3 = db('goodscats')->where(['pid'=>$catarr[1]])->select();
            $returnarr['cat3'] = $cat3;
        }
        

        
        $this->assign($returnarr);
        return $this->fetch();
    }


    

    /**
     * 获取商品规格属性
     */
    public function getSpecAttrs(){
        $goodsType = (int)input('goodsType');
        $goodsCatId = Input('post.goodsCatId/d');
        
        $data = [];
        
        $specs = Db::name('goodscatspec')->where(['gcatid'=>$goodsCatId,'isshow'=>1])->field('id,specname,isallowimg')->order('isallowimg desc,sort asc,id asc')->select();
        $spec0 = null;
        $spec1 = [];
        foreach ($specs as $key => $v){
            if($v['isallowimg']==1){
                $spec0 = $v;
            }else{
                $spec1[] = $v;
            }
        }
        $data['spec0'] = $spec0;
        $data['spec1'] = $spec1;
        
        $data['attrs'] = Db::name('goodsattributes')->where(['gcatid'=>$goodsCatId,'isshow'=>1])->field('id,attrname,attrtype,attrval')->order('attrsort asc,id asc')->select();

        $datas = [
            'status' => 1,
            'data' =>$data
        ];
        return $datas;
    }


    /**
     *商品主图上传/商品详情图
     */
    public function uploadimg(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        //exit(dump($file));
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            if(input('type')) {
                $outpath = $this->uploadpath.'goodsinfo';
            }else {
                $outpath = $this->uploadpath.'goodsimg';
            }
            $info = $file->move($outpath);
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                //echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                
                $data = [
                    'code'=> 0,
                    'msg' => '',
                    'data' => [
                        'src'=> str_replace("\\","/",$info->getSaveName())
                    ]
                ];
                
                return $data;
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getFilename(); 
            }else{
                // 上传失败获取错误信息
                return $file->getError();
            }
        }
    }




    /**
     *商品相册图上传
     */
    public function uploadthumb(){
        $fileKey = key($_FILES);
        $file = request()->file($fileKey);
        //exit(dump($file));
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            
            $outpath = $this->uploadpath.'goodsthumb';

            $info = $file->move($outpath);
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                //echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                
                $data = [
                    'jsonrpc' => "2.0",
                    'result' => [
                        'src'=> str_replace("\\","/",$info->getSaveName())
                    ],
                    'name' => $info->getFilename(),
                ];
                
                exit(json_encode($data));
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getFilename(); 
            }else{
                // 上传失败获取错误信息
                $data = [
                    'jsonrpc' => "2.0",
                    'error' => [
                        'code'=> '102',
                        'message'=> $file->getError(),
                    ],
                ];
                exit(json_encode($data));
            }
        }
    }






    /**
     *商品规格图上传
     */
    public function upspecimg(){
        // 获取表单上传文件 例如上传了001.jpg
        $fileKey = key($_FILES);
        $file = request()->file($fileKey);
        $rule = [
            'type'=>'image/png,image/gif,image/jpeg,image/x-ms-bmp',
            'ext'=>'jpg,jpeg,gif,png,bmp',
            'size'=>'2097152'
        ];
        //exit(dump($file));
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){

            $info = $file->validate($rule)->move($this->uploadpath.'goodsspecimg');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                //echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $filePath = str_replace("\\","/",$info->getSaveName());
                $rdata = [
                    'status'=>1,
                    'savePath'=>ZY_APP_PATH.'plugins/goods/view/public/image/goodsspecimg/',
                    'name'=>'',
                    'thumb'=>$filePath
                ];    
                exit(json_encode($rdata));
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getFilename(); 
            }else{
                // 上传失败获取错误信息
                return json_encode(['msg'=>$file->getError(),'status'=>-1]);
            }
        }
    }





    public function getcats() {

        if (input('parentId') != 0 ) {
            $info = db('goodscats')->where('pid',input('parentId'))->field('catid,catname')->select();
        } else {
            $info = db('goodscats')->where('pid',0)->field('catid,catname')->select();
        }

        return $info;
    }





    /**
     *编辑器图片上传
     */
    public function layuploadimg(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        //exit(dump($file));
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){

            $outpath = $this->uploadpath.'goodsinfo';
           
            $info = $file->move($outpath);
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                //echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                
                $data = [
                    'code'=> 0,
                    'msg' => '',
                    'data' => [
                        'src'=> ZY_APP_PATH.'plugins/goods/view/public/image/goodsinfo/'.str_replace("\\","/",$info->getSaveName()),
                    ]
                ];
                
                return $data;
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getFilename(); 
            }else{
                // 上传失败获取错误信息
                $data = [
                    'code'=> 0,
                    'msg' => $file->getError(),
                    'data' => []
                ];
                return $data;
            }
        }
    }


}

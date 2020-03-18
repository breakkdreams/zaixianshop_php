<?php
namespace plugins\goods\controller; 

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;
use plugins\goods\model\GoodsModel;
use plugins\goods\model\GoodscatsModel;

//AdminIndexController类和类的index()方法是必须存在的 index() 指向admin_index.html模板也就是模块后台首页
// 并且继承PluginAdminBaseController
class GoodsController extends PluginAdminBaseController
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

    public function index()
    {
        //dump($_SERVER["SERVER_NAME"]);dump(ZY_APP_PATH);die();
        $map = [];
        $search = [
            'cat1' => '',
            'cat2' => '',
            'cat3' => '',
            'cat2arr' => [],
            'cat3arr' => [],
            'keyword' => '',
            'keywords' => ''
        ];
        if(request()->isPost()){
            
            $str = '';
            if ( input('cat1') ) {
                $str = input('cat1').'-';
                $search['cat1'] = input('cat1');
                $search['cat2arr'] = db('goodscats')->field("catid,catname")->where(['pid'=>input('cat1')])->order('catsort asc,catname asc')->select();
                if ( input('cat2') ) {
                    $str .= input('cat2').'-';
                    $search['cat2'] = input('cat2');
                    $search['cat3arr'] = db('goodscats')->field("catid,catname")->where(['pid'=>input('cat2')])->order('catsort asc,catname asc')->select();
                    if ( input('cat3') ) {
                        $str .= input('cat3').'-';
                        $search['cat3'] = input('cat3');
                    }
                }
                $map['gcatpath']  = ['like','%'.$str.'%'];
            }
            if ( input('keyword') ) {
                $map['goodsname'] = ['like','%'.input('keyword').'%'];
                $search['keyword'] = input('keyword');
            }

        } 

        //$map['issale']  = ['=',1];
        $map['goodsstatus'] = ['=',1];

        $info = db('goods')->where($map)->field('id,shopid,goodsname,gcatpath,goodsimg,marketprice,shopprice,issale,salenum,isspec')->order('id desc')->paginate(15);
        $infos = $info->toArray();
        
        if(count($infos['data'])>0){
            $idarr = [];
            $goods = new GoodscatsModel();
            $keyCats = $goods->listKeyAll();
            foreach ($infos['data'] as $key => $v){
                if (!in_array($v['shopid'],$idarr)) {
                    $idarr[] = $v['shopid'];
                }
                // $infos['data'][$key]['goodsimg'] = $this->uploadpaths.'goodsimg/'.$infos['data'][$key]['goodsimg'];
                // $goodsCatPath = $infos['data'][$key]['gcatpath'];
                // $infos['data'][$key]['gcatpath'] = $goods->getGoodsCatNames($goodsCatPath,$keyCats);
                // $infos['data'][$key]['shopid'] = $snamarr[$infos['data'][$key]['shopid']];
            }

            $ids = implode(',', $idarr);
            $symbol ='goods';
            $id = 'member_two';
            $param = ['uid'=>$ids,'isModule'=>true];
            $rs = getModuleApiData($symbol, $id, $param);
            
            $snamarr = [];

            if ( !empty($rs['data']) ) {
                foreach ($rs['data'] as $ks => $vs) {
                    $snamarr[$vs['uid']] = $vs['store_name'];
                }    
            }
            

            foreach ($infos['data'] as $key => $v){
                
                $infos['data'][$key]['goodsimg'] = $this->uploadpaths.'goodsimg/'.$infos['data'][$key]['goodsimg'];
                $goodsCatPath = $infos['data'][$key]['gcatpath'];
                $infos['data'][$key]['gcatpath'] = $goods->getGoodsCatNames($goodsCatPath,$keyCats);
                if ( !empty($rs['data']) ) {
                    $infos['data'][$key]['shopid'] = $snamarr[$infos['data'][$key]['shopid']];
                }
            }
        }

       


        $this->assign(array(
            'info'=> $infos['data'],
            'search'=> $search,
            'page'=> $info->render()
        ));
        return $this->fetch('goods/index');
    }


    public function add()
    {
        if(request()->isPost()){
            //$specattrdata = json_decode($_POST['specattrdata'],true);
            //dump(input());dump($specattrdata);die();
            //dump(input());die();
            $goods = new GoodsModel();
            $res = $goods->add();

            if ($res['code'] == 1) {
                $this->success($res['msg'],ZY_APP_PATH.'plugin/goods/goods/index');
            } else {
                $this->error($res['msg']);
            }
            return;
        }
        $m = new GoodsModel();
        $object = $m->getEModel('goods');

        // $symbol ='goods';
        // $ids = 'freight_one';
        // $param = ['isModule'=>true];
        // $return = getModuleApiData($symbol, $ids, $param);
        // //dump($return);die();
        // $freight = $return['data'];

        $freight = [
            [
                'id' => '',
                'title' => ''
            ]
        ];

        $this->assign(array(
            'object'=>$object,
            'freight'=>$freight
        ));
        return $this->fetch();
    }

    public function edit()
    {
        $id = input('id');
        if(request()->isPost()){
            //dump(input());die();
            $goods = new GoodsModel();
            $res = $goods->edit();

            if ($res['code'] == 1) {
                $this->success($res['msg'],ZY_APP_PATH.'plugin/goods/goods/index');
            } else {
                $this->error($res['msg']);
            }
            return;
        }

        $info = db('goods')->find(['id'=>$id]);
        $info['goodsinfo'] = htmlspecialchars_decode($info['goodsinfo']);
            //dump($info['goodsinfo']);die();
        // if ( !empty($info['goodsinfo']) ) {
        //     $info['goodsinfo'] = explode(',', $info['goodsinfo']);
        // }
        if ( !empty($info['goodsalbum']) ) {
            $info['goodsalbum'] = explode(',', $info['goodsalbum']);
        }

        $m = new GoodsModel();
        $object = $m->getedit(input('get.id'));
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



    public function check()
    {
        $id = input('id');
        
        $info = db('goods')->find(['id'=>$id]);
        if ( !empty($info['goodsinfo']) ) {
            $info['goodsinfo'] = explode(',', $info['goodsinfo']);
        }
        if ( !empty($info['goodsalbum']) ) {
            $info['goodsalbum'] = explode(',', $info['goodsalbum']);
        }

        $m = new GoodsModel();
        $object = $m->getedit(input('get.id'));
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


    public function auditgoods()
    {
        $map = [];
        $search = [
            'cat1' => '',
            'cat2' => '',
            'cat3' => '',
            'cat2arr' => [],
            'cat3arr' => [],
            'keyword' => '',
            'keywords' => ''
        ];
        if(request()->isPost()){
            
            $str = '';
            if ( input('cat1') ) {
                $str = input('cat1').'-';
                $search['cat1'] = input('cat1');
                $search['cat2arr'] = db('goodscats')->field("catid,catname")->where(['pid'=>input('cat1')])->order('catsort asc,catname asc')->select();
                if ( input('cat2') ) {
                    $str .= input('cat2').'-';
                    $search['cat2'] = input('cat2');
                    $search['cat3arr'] = db('goodscats')->field("catid,catname")->where(['pid'=>input('cat2')])->order('catsort asc,catname asc')->select();
                    if ( input('cat3') ) {
                        $str .= input('cat3').'-';
                        $search['cat3'] = input('cat3');
                    }
                }
                $map['gcatpath']  = ['like','%'.$str.'%'];
            }
            if ( input('keyword') ) {
                // $map['specname']  = ['like','%'.input('keyword').'%'];
                // $search['keyword'] = input('keyword');
            }

        }

        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>'','isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        
        $snamarr = [];
//        foreach ($rs['data'] as $ks => $vs) {
//            $snamarr[$vs['uid']] = $vs['store_name'];
//        }

        //$map['issale']  = ['=',1];
        $map['goodsstatus'] = ['=',2];

        $info = db('goods')->where($map)->field('id,shopid,goodsname,gcatpath,goodsimg,marketprice,shopprice,issale,salenum,isspec')->order('id desc')->paginate(15);
        $infos = $info->toArray();
        if(count($infos['data'])>0){
            $goods = new GoodscatsModel();
            $keyCats = $goods->listKeyAll();
            foreach ($infos['data'] as $key => $v){
                $infos['data'][$key]['goodsimg'] = $this->uploadpaths.'goodsimg/'.$infos['data'][$key]['goodsimg'];
                $goodsCatPath = $infos['data'][$key]['gcatpath'];
                $infos['data'][$key]['gcatpath'] = $goods->getGoodsCatNames($goodsCatPath,$keyCats);
                $infos['data'][$key]['shopid'] = $snamarr[$infos['data'][$key]['shopid']];
            }
        }

        $this->assign(array(
            'info'=> $infos['data'],
            'search'=> $search,
            'page'=> $info->render()
        ));
        return $this->fetch();
    }


    public function illegalgoods()
    {
        $map = [];
        $search = [
            'cat1' => '',
            'cat2' => '',
            'cat3' => '',
            'cat2arr' => [],
            'cat3arr' => [],
            'keyword' => '',
            'keywords' => ''
        ];
        if(request()->isPost()){
            
            $str = '';
            if ( input('cat1') ) {
                $str = input('cat1').'-';
                $search['cat1'] = input('cat1');
                $search['cat2arr'] = db('goodscats')->field("catid,catname")->where(['pid'=>input('cat1')])->order('catsort asc,catname asc')->select();
                if ( input('cat2') ) {
                    $str .= input('cat2').'-';
                    $search['cat2'] = input('cat2');
                    $search['cat3arr'] = db('goodscats')->field("catid,catname")->where(['pid'=>input('cat2')])->order('catsort asc,catname asc')->select();
                    if ( input('cat3') ) {
                        $str .= input('cat3').'-';
                        $search['cat3'] = input('cat3');
                    }
                }
                $map['gcatpath']  = ['like','%'.$str.'%'];
            }
            if ( input('keyword') ) {
                // $map['specname']  = ['like','%'.input('keyword').'%'];
                // $search['keyword'] = input('keyword');
            }

        }

        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>'','isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        
        $snamarr = [];
//        foreach ($rs['data'] as $ks => $vs) {
//            $snamarr[$vs['uid']] = $vs['store_name'];
//        }

        //$map['issale']  = ['=',1];
        $map['goodsstatus'] = ['=',4];

        $info = db('goods')->where($map)->field('id,shopid,goodsname,goodsimg,gcatpath,illegalremarks')->order('id desc')->paginate(15);
        $infos = $info->toArray();
        if(count($infos['data'])>0){
            $goods = new GoodscatsModel();
            $keyCats = $goods->listKeyAll();
            foreach ($infos['data'] as $key => $v){
                $infos['data'][$key]['goodsimg'] = $this->uploadpaths.'goodsimg/'.$infos['data'][$key]['goodsimg'];
                $goodsCatPath = $infos['data'][$key]['gcatpath'];
                $infos['data'][$key]['gcatpath'] = $goods->getGoodsCatNames($goodsCatPath,$keyCats);
                $infos['data'][$key]['shopid'] = $snamarr[$infos['data'][$key]['shopid']];
            }
        }

        $this->assign(array(
            'info'=> $infos['data'],
            'search'=> $search,
            'page'=> $info->render()
        ));
        return $this->fetch();
    }

    /**
     *上架
     */
    public function onsale()
    {
        $res = db('goods')->where(['id'=>input('id')])->update(['issale'=>1]);
        if( $res!== false ){
            $this->success('操作成功！');
        }else{
            $this->error('操作失败！');
        }  
    }

    /**
     *下架
     */
    public function unsale()
    {
        $res = db('goods')->where(['id'=>input('id')])->update(['issale'=>0]);
        if( $res!== false ){
            $this->success('操作成功！');
        }else{
            $this->error('操作失败！');
        }
    }


    /**
     *审核通过
     */
    public function ispass()
    {
        $res = db('goods')->where(['id'=>input('id')])->update(['goodsstatus'=>1]);
        if( $res!== false ){
            $this->success('操作成功！');
        }else{
            $this->error('操作失败！');
        }  
    }

    /**
     *审核不通过
     */
    public function nopass()
    {
        if(request()->isPost()){
            if ( !input('id') || !input('text') ) {
                return 0;
            }    
            $res = db('goods')->where(['id'=>input('id')])->update(['goodsstatus'=>3,'nopassremarks'=>input('text')]);
            if( $res!== false ){
                return 1;
            }else{
                return 0;
            }
        } else {
            return 0;
        }
    }


    /**
     *删除栏目
     */
    public function del()
    {
        
        if(db('goods')->delete(input('id'))){
            db('goods')->where(['id'=>input('id')])->delete();
            db('spec_items')->where(['goodsid'=>input('id')])->delete();
            db('goods_specs')->where(['goodsid'=>input('id')])->delete();
            db('goods_attritem')->where(['goodsid'=>input('id')])->delete();
            db('carts')->where(['gid'=>input('id')])->delete();
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }   
        
    }


    /**
     *违规
     */
    public function illegal()
    {
        if(request()->isPost()){
            if ( !input('id') || !input('text') ) {
                return 0;
            }    
            $res = db('goods')->where(['id'=>input('id')])->update(['goodsstatus'=>4,'illegalremarks'=>input('text')]);
            if( $res!== false ){
                return 1;
            }else{
                return 0;
            }
        } else {
            return 0;
        }
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

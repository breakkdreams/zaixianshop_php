<?php
namespace plugins\goods\controller; 

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;
use plugins\goods\model\GoodscatsModel;

//AdminIndexController类和类的index()方法是必须存在的 index() 指向admin_index.html模板也就是模块后台首页
// 并且继承PluginAdminBaseController
class GoodscatsController extends PluginAdminBaseController
{
    public $uploadpath = '';
    protected function _initialize()
    {
        parent::_initialize();
        $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        }
        $this->uploadpath = ROOT_PATH.'public'.DS.'plugins'.DS.'goods'.DS.'view'.DS.'public'.DS.'image'.DS.'catimg';
    }

    public function index()
    {
        //$map['pid']  = ['=',0];
        $info = db('goodscats')/*->where($map)*/->order('catsort asc')->select();
        $tree = new GoodscatsModel();
        $info = $tree->catetree($info);
        $this->assign(array(
            'info'=> $info
        ));
        return $this->fetch();
    }


    /**
     *排序
     */
    public function listorder(){

        if(isset($_POST['listorders'])) {
            foreach($_POST['listorders'] as $id => $listorder) {
                $save=db('goodscats')->where('catid',$id)->setField('catsort',$listorder);
            }
            return $this->success('操作成功！'/*,'index'*/);
        } else {
            return $this->error('操作失败！');
        }
    }


    /**
     *添加栏目
     */
    public function add()
    {
        if(request()->isPost()){
   
            $data = [
                'catname'=>input('cname'),
                'pid'=>input('cpid'),
                'isshow'=>input('cisshow'),
                'isfloor'=>input('cisfloor'),
                'catimg'=>input('cimage')
            ];
            
            if(db('goodscats')->insert($data)){

                $datas= [
                    'code'=> 1,
                    'msg' => '添加栏目成功！',
                    'data' => ''
                ];
                return $datas;
                //return $this->success('添加栏目成功！');
            }else{
                $datas = [
                    'code'=> 0,
                    'msg' => '添加栏目失败！',
                    'data' => ''
                ];
                return $datas;
                //return $this->error('添加栏目失败！');
            }
            return;
        }

        $_cateRes = db('goodscats')->select();
        // $cateRes = model('goodscats')->catetree($_cateRes);
        // dump($cateRes);die();
        $user = new GoodscatsModel();
        // 查询数据集
        $cateRes = $user->catetree($_cateRes);
        //dump($cateRes);die();
        $this->assign(array(
            'cateRes'=>$cateRes,
            ));
        return $this->fetch();
    }


    /**
     *编辑分类栏目
     */
    public function edit()
    {
        $id = input('id');
        $info = db('goodscats')->find(['catid'=>$id]);
        
        if(request()->isPost()){
            $data=[     
                'catname'=>input('cname'),
                'pid'=>input('cpid'),
                'isshow'=>input('cisshow'),
                'isfloor'=>input('cisfloor'),
                'catimg'=>input('cimage')
            ];
            
            $save = db('goodscats')->where('catid',input('cid'))->update($data);
            if($save !== false){
                $this->success('修改栏目信息成功！');
            }else{
                $this->error('修改栏目信息失败！');
            }
            return;
        }
        $_cateRes = db('goodscats')->select();
        $user = new GoodscatsModel();
        $cateRes = $user->catetree($_cateRes);
        $this->assign(array(
            'cateRes'=>$cateRes,
            'info'=>$info
        ));
        return $this->fetch();
    }


    /**
     *删除栏目
     */
    public function del()
    {
        
        if(db('goodscats')->delete(input('id'))){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }   
        
    }



    public function goodsattr()
    {
        $map = [];
        $search = [
            'cat1' => '',
            'cat2' => '',
            'cat3' => '',
            'cat2arr' => [],
            'cat3arr' => [],
            'keyword' => ''
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
                $map['specname']  = ['like','%'.input('keyword').'%'];
                $search['keyword'] = input('keyword');
            }

        }
        $info = db('goodsattributes')->where($map)->order('id asc')->paginate(15);
        //dump($info);die();
        $infos = $info->toArray();
        if(count($infos['data'])>0){
            
            $goods = new GoodscatsModel();
            $keyCats = $goods->listKeyAll();
            foreach ($infos['data'] as $key => $v){
                $goodsCatPath = $infos['data'][$key]['gcatpath'];
                $infos['data'][$key]['gcatpath'] = $goods->getGoodsCatNames($goodsCatPath,$keyCats);
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
     *添加属性
     */
    public function attradd()
    {
        if(request()->isPost()){
   
            $data = [
                'attrname'=>input('cname'),
                'gcatid'=>input('cid'),
                'gcatpath'=>input('cpath'),
                'isshow'=>input('cstaus'),
                'attrtype'=>input('atype'),
                'attrval'=>str_replace('，', ',', input('aname'))
            ];
            
            if(db('goodsattributes')->insert($data)){

                $datas= [
                    'code'=> 1,
                    'msg' => '添加属性成功！',
                    'data' => ''
                ];
                return $datas;
                //return $this->success('添加栏目成功！');
            }else{
                $datas = [
                    'code'=> 0,
                    'msg' => '添加属性失败！',
                    'data' => ''
                ];
                return $datas;
                //return $this->error('添加栏目失败！');
            }
            return;
        }

        return $this->fetch();
    }

    /**
     *编辑规格
     */
    public function attredit()
    {
        $id = input('id');
        $info = db('goodsattributes')->find(['id'=>$id]);
        
        if(request()->isPost()){
            $data=[     
                'attrname'=>input('cname'),
                'gcatid'=>input('cid'),
                'gcatpath'=>input('cpath'),
                'isshow'=>input('cstaus'),
                'attrtype'=>input('atype'),
                'attrval'=>str_replace('，', ',', input('aname'))
            ];
            
            $save = db('goodsattributes')->where('id',input('id'))->update($data);
            if($save !== false){
                $this->success('修改规格信息成功！');
            }else{
                $this->error('修改规格信息失败！');
            }
            return;
        }
        $catarr = explode("-",$info['gcatpath']);
        if (!isset($catarr[2])) {
            $catarr[2] = '';
        }
        $cat1 = db('goodscats')->where(['pid'=>0])->select();
        $returnarr = [
            'catarr'=>$catarr,
            'cat1'=>$cat1,
            'info'=>$info
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
     *删除
     */
    public function attrdel()
    {
        
        if(db('goodsattributes')->delete(input('id'))){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
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





    public function goodsspec()
    {
        $map = [];
        $search = [
            'cat1' => '',
            'cat2' => '',
            'cat3' => '',
            'cat2arr' => [],
            'cat3arr' => [],
            'keyword' => ''
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
                $map['specname']  = ['like','%'.input('keyword').'%'];
                $search['keyword'] = input('keyword');
            }

        }
        $info = db('goodscatspec')->where($map)->order('id asc')->paginate(15);
        //dump($info);die();
        $infos = $info->toArray();
        if(count($infos['data'])>0){
            
            $goods = new GoodscatsModel();
            $keyCats = $goods->listKeyAll();
            foreach ($infos['data'] as $key => $v){
                $goodsCatPath = $infos['data'][$key]['gcatpath'];
                $infos['data'][$key]['gcatpath'] = $goods->getGoodsCatNames($goodsCatPath,$keyCats);
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
     *添加规格
     */
    public function specadd()
    {
        if(request()->isPost()){
   
            $data = [
                'specname'=>input('cname'),
                'gcatid'=>input('cid'),
                'gcatpath'=>input('cpath'),
                'isshow'=>input('cstaus'),
                'isallowimg'=>input('ctype')
            ];
            
            if(db('goodscatspec')->insert($data)){

                $datas= [
                    'code'=> 1,
                    'msg' => '添加规格成功！',
                    'data' => ''
                ];
                return $datas;
                //return $this->success('添加栏目成功！');
            }else{
                $datas = [
                    'code'=> 0,
                    'msg' => '添加规格失败！',
                    'data' => ''
                ];
                return $datas;
                //return $this->error('添加栏目失败！');
            }
            return;
        }

        return $this->fetch();
    }

    /**
     *编辑规格
     */
    public function specedit()
    {
        $id = input('id');
        $info = db('goodscatspec')->find(['id'=>$id]);
        
        if(request()->isPost()){
            $data=[     
                'specname'=>input('cname'),
                'gcatid'=>input('cid'),
                'gcatpath'=>input('cpath'),
                'isshow'=>input('cstaus'),
                'isallowimg'=>input('ctype')
            ];
            
            $save = db('goodscatspec')->where('id',input('id'))->update($data);
            if($save !== false){
                $this->success('修改规格信息成功！');
            }else{
                $this->error('修改规格信息失败！');
            }
            return;
        }
        $catarr = explode("-",$info['gcatpath']);
        if (!isset($catarr[2])) {
            $catarr[2] = '';
        }
        $cat1 = db('goodscats')->where(['pid'=>0])->select();
        $returnarr = [
            'catarr'=>$catarr,
            'cat1'=>$cat1,
            'info'=>$info
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
     *规格检测
     */
    public function checkspec()
    {
        if(request()->isPost()){
   
            $info = db('goodscatspec')->where(['isallowimg'=>1])->where(['gcatid '=>input('cid')])->find();
            if ( count($info) == 0 ) {
                $datas= [
                    'code'=> 1,
                    'msg' => '',
                    'data' => ''
                ];
                return $datas;
            } else {
                $datas = [
                    'code'=> 0,
                    'msg' => '当前栏目下已经存在可上传图片的规格！',
                    'data' => ''
                ];
                return $datas;
            }
            return;
        }
        return;
    }


    /**
     *删除
     */
    public function specdel()
    {
        
        if(db('goodscatspec')->delete(input('id'))){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }   
        
    }






    /**
     *分类图上传
     */
    public function uploadimg(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        //exit(dump($file));
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){

            $info = $file->move($this->uploadpath);
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







}

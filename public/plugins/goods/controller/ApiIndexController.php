<?php
namespace plugins\goods\controller;
/**
 * @Author: user
 * @Date:   2019-03-07 16:21:19
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-20 09:56:01
 */
use cmf\controller\PluginRestBaseController;//引用插件基类
use plugins\goods\Model\PluginApiIndexModel;
use plugins\goods\model\GoodscatsModel;
use think\Db;

/**
 * api控制器
 */
class ApiIndexController extends PluginRestBaseController
{
  public $uploadpath = '';
	protected function _initialize()
    {
        // $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        // if (!empty($adminId)) {
        //     $this->assign("admin_id", $adminId);
        // }
        $this->uploadpath = ZY_APP_PATH.'plugins/goods/view/public/image/';
    }

    public function goodsinfo($id=0,$isModule = false){
        $uid = 0;
        $sid = 0;
        if ($isModule == false) {
            $data = $this->request->post();
            $data = zy_decodeData($data,$isModule);
            $uid = $data['uid'];
            $sid = $data['id'];
        } else {
            if (empty($id)){
                //return zy_json_echo(false,'非法访问！',null,-2);
                return zy_array (false,'非法访问！','',-2 ,$isModule);
            }
            $sid = $id;
        }
        
        //$uid = 30;
        
        $info = db('goods')->field('id,shopid,goodsname,goodsimg,marketprice,shopprice,issale,goodsstock,goodsinfo,goodsalbum,goodsstatus,salenum,isspec')->find(['id'=>$sid]);
        if ( count($info) == 0 || $info['issale'] == 0 || $info['goodsstatus'] != 1 ) {
            //return zy_json_echo(false,'该商品不存在或已下架',null,-2);
            return zy_array (false,'该商品不存在或已下架','',-2 ,$isModule);
        }
        $info['goodsimg'] = $this->uploadpath.'goodsimg/'.$info['goodsimg'];
        $info['goodsinfo'] = htmlspecialchars_decode($info['goodsinfo']);
        // $info['goodsinfo'] = explode(',', $info['goodsinfo']);
        // foreach ($info['goodsinfo'] as $k => $v) {
        //     $info['goodsinfo'][$k] = $this->uploadpath.'goodsinfo/'.$v;
        // }
        $info['goodsalbum'] = explode(',', $info['goodsalbum']);
        foreach ($info['goodsalbum'] as $k => $v) {
            $info['goodsalbum'][$k] = $this->uploadpath.'goodsthumb/'.$v;
        }

        if ($isModule == true) {
            db('goods')->where(['id'=>$sid])->setInc('viewnum', 1);
        }
        $info['issc'] = 0;
        if ( $uid ) {
            
            $symbol ='goods';
            $ids = 'member_one';
            $param = ['uid'=>$uid,'gid'=>$sid,'isModule'=>true];
            $return = getModuleApiData($symbol, $ids, $param);

            if ( $return['data']['is'] == 2 ) {
                $info['issc'] = 1;
            }
        }
        if ($isModule == false) {
            $sidinfo = $this->zy_userid_jwts($info['shopid']);
            $info['shopid'] = $sidinfo;
        }
        unset($info['issale']);unset($info['goodsstatus']);
        //return zy_json_echo(true,'成功！',$info);
        return zy_array (true,'成功！',$info,200,$isModule);  
    }




    public function getspec($id=0,$isModule = false){

        $sid = 0;
        if ($isModule == false) {
            $data = $this->request->post();
            $data = zy_decodeData($data,$isModule);
            $sid = $data['id'];
        } else {
            if (empty($id)){
                return zy_array (false,'非法访问！','',-2 ,$isModule);
            }
            $sid = $id;
        }
        
        $info = db('goods')->field('id,issale,gcatid,goodsname,goodsimg,isspec,shopprice,goodsstock')->find(['id'=>$sid]);
        if ( count($info) == 0 || $info['issale'] == 0 ) {
            return zy_array (false,'该商品不存在或已下架','',-2 ,$isModule);
        }
        $data = [
            'skuData'=>'',
            'initialSku'=>''
        ];
        $sku = [];
        $sku['tree'] = [];
        $default = [];
        if ( $info['isspec'] == 1 ) {
            $attrinfo = db('goodscatspec')->where(['gcatid'=>$info['gcatid']])->order('sort asc, id asc')->select();
            $allow = db('goodscatspec')->field('id')->where(['gcatid'=>$info['gcatid'],'isallowimg'=>1])->find();
            if ( count($allow) > 0 ) {
                $aid = $allow['id'];
            } else {
                $aid = 0;
            }
            $specinfo = db('goods_specs')->where(['goodsid'=>$sid])->order('id asc')->select();
            $iteminfo = db('spec_items')->where(['goodsid'=>$sid])->order('id asc')->select();
            $attrarr = [];
            foreach ($iteminfo as $k => $v) {
                if ( $v['catid'] == $aid ) {
                    $attrarr[$v['catid']][] = [
                        'id'=>$v['id'],
                        'name'=>$v['itemname'],
                        'imgUrl'=>/*$this->uploadpath.'goodsspecimg/'.*/$v['itemimg'] 
                    ];
                } else {
                    $attrarr[$v['catid']][] = [
                        'id'=>$v['id'],
                        'name'=>$v['itemname']
                    ];
                }
                
            }
            $tree = [];
            foreach ($attrinfo as $k => $v) {
                if (!isset($attrarr[$v['id']])) {
                    continue;
                }
                $tree[] = [
                    'k' => $v['specname'],
                    'v' => $attrarr[$v['id']],
                    'k_s' => 's'.($k+1)
                ];
            }

            $lists = [];
            foreach ($specinfo as $k => $v) {

                $sxarr = [
                    'id' => $v['id'],
                    'price' => $v['specprice']*100,
                    'stock_num' => intval($v['specstock']),
                ];
                $sparr = explode('_', $v['specids']);
                foreach ($sparr as $ks => $vs) {
                    if ($vs == '') {
                        continue;
                    }
                    $key = 's'.($ks+1);
                    $sxarr[$key] = $vs;
                }

                if ($v['isdefault'] == 1) {
                    //$default = $sxarr;
                    //dump($sparr);dump($default);die();
                    //unset($default['id'],$default['price'],$default['stock_num']);
                }

                $lists[] = $sxarr;
            }

            $sku['tree'] = $tree;
            $sku['list'] = $lists;
        }    
        
        $sku['price'] = $info['shopprice'];
        $sku['stock_num'] = intval($info['goodsstock']);
        if ( $info['isspec'] == 0 ) {
            $sku['none_sku'] = true;
        }else{
            $sku['none_sku'] = false;
        }
        $sku['hide_stock'] = false;

        $data['skuData']['sku'] = $sku;
        $data['skuData']['goods_id'] = $sid;
        $data['skuData']['goods_info'] = [
            'title' => $info['goodsname'],
            'picture' => $this->uploadpath.'goodsimg/'.$info['goodsimg'],
        ];
        $default['selectedNum'] = 1;
        $data['initialSku'] = $default;
        return zy_array (true,'成功！',$data,200,$isModule); 
    }

    //获取商品规格（不处理）
    public function getspecs($id=0,$isModule = false){
        if (empty($id)){
            return zy_array (false,'缺少参数！','',-1 ,$isModule);
        }
        $data = [
            'isspec'=>0,
            'spectypename'=>[],
            'specinfo'=>[]
        ];
        $info = db('goods')->field('id,gcatid,isspec')->find(['id'=>$id]);

        if ( $info['isspec'] == 1 ) {
            $data['isspec'] = 1;
            $attrinfo = db('goodscatspec')->where(['gcatid'=>$info['gcatid']])->order('sort asc, id asc')->select();
            foreach ($attrinfo as $k => $v) {
                $data['spectypename'][] = $v['specname'];
            }
            $specinfo = db('goods_specs')->where(['goodsid'=>$id])->order('id asc')->select();
            $iteminfo = db('spec_items')->where(['goodsid'=>$id])->order('id asc')->select();

            $attrarr = [];
            foreach ($iteminfo as $k => $v) {    
                $attrarr[$v['id']] = [
                    'id'=>$v['id'],
                    'name'=>$v['itemname']
                ];            
            }

            foreach ($specinfo as $k => $v) {    
                $sparr = explode('_', $v['specids']);
                $str = '';
                foreach ($sparr as $ks => $vs) {
                    if ($vs == '') {
                        continue;
                    }
                    if ( $ks == count($sparr)-2 ) {
                        $str .= $attrarr[$vs]['name'];
                    }else{
                        $str .= $attrarr[$vs]['name'].',';
                    }
                }

                $data['specinfo'][] = [
                    'id' => $v['id'],
                    'specids' => $v['specids'],
                    'specidsname' => $str
                ];

            }

        }
        
        return zy_array (true,'成功！',$data,200,$isModule); 

    }   

    

    public function addcarts($isModule = false){

        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        $uid = $data['uid'];
        if( empty($data['uid']) ){
            return zy_array (false,'参数不能为空','',-2 ,$isModule);
        }
        if (!$data['id']) { 
            return zy_array (false,'非法访问！','',-2 ,$isModule); 
        }

        if ($data['num'] <= 0) { 
            return zy_array (false,'购买数量必须大于0！','',-2 ,$isModule); 
        }
        $sid = $data['id'];
      
        $info = db('goods')->field('id,goodsname,goodsimg,marketprice,shopprice,issale,goodsinfo,goodsalbum,salenum,isspec')->find(['id'=>$sid]);
        if ( count($info) == 0 || $info['issale'] == 0 ) {
            //return zy_json_echo(false,'该商品不存在或已下架',null,-2);
            return zy_array (false,'该商品不存在或已下架','',-2 ,$isModule);
        }

        $spec = 0;
        if ( $info['isspec']==1 ) {
            if ( empty($data['sarr']) ) { 
                return zy_array (false,'缺少必要参数','',-2 ,$isModule); 
            }
            $spec = '';
            $data['sarr'] = explode(',', $data['sarr']);
            for ($i=0; $i < count($data['sarr']); $i++) { 
                if ($i == count($data['sarr'])-1) {
                    $spec .= $data['sarr'][$i];
                }else{
                    $spec .= $data['sarr'][$i].'_';
                }
            }

            $sinfo = db('goods_specs')->where(['goodsid'=>$sid,'specids'=>$spec])->find();

            if ( count($sinfo) == 0 ) {
                return zy_array (false,'参数异常！','',-2 ,$isModule); 
            }

        }
        $map = [
            'gid' => $sid,
            'uid' => $uid
        ];
        if ( empty($spec) ) {
            $cinfo = db('carts')->where($map)->find();
        } else {
            $map['gspecid'] = $spec;
            $cinfo = db('carts')->where($map)->find();
        }
        
        if ( count($cinfo) > 0 ) {
            db('carts')->where($map)->setInc('cartnum', $data['num']);
        } else {
            $idata = [
                'uid' => $data['uid'],
                'gid' => $data['id'],
                'gspecid' => $spec,
                'cartnum' => $data['num'],
            ];

            db('carts')->insert($idata);
        }
        

        return zy_array (true,'成功！','',200,$isModule);
    }




    public function getcarts($uid = null,$isModule=false)
    {
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);

        $uid = isset( $data['uid'] ) ? $data['uid'] : $uid ;
        if(empty($uid)){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }
        $prefix = config('database.prefix');
        $where['a.uid'] = $uid;
        $info = Db::table($prefix.'carts')
              ->alias([$prefix.'carts'=>'a',$prefix.'goods'=>'b',$prefix.'goods_specs'=>'c'])
              ->join($prefix.'goods','a.gid = b.id','inner')
              ->join($prefix.'goods_specs','a.gspecid = c.specids','left')
              ->field('a.id as cartid, a.gspecid, a.cartnum,b.id,b.shopid, b.goodsimg, b.goodsname, b.shopprice, b.goodsstock,c.specprice, c.specstock, c.specids')
              ->where($where)
              ->select();
        //dump($info);die();      
        $infos = Db::table($prefix.'carts')->alias('a')->field('b.shopid as sid')->join($prefix.'goods b','a.gid = b.id','left')/*->leftjoin('goods_specs c','a.gspecid = c.specids and c.shopid = '.$uid)*/->where($where)->group('b.shopid')->select();

        $idarr = '';
        foreach ($infos as $key => $value) {
            if ( empty($idarr) ) {
                $idarr = $value['sid'];
            }else{
                $idarr .= ','.$value['sid'];
            }
        }

        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$idarr,'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        
        $snamarr = [];
        foreach ($rs['data'] as $ks => $vs) {
            $snamarr[$vs['uid']] = $vs;
        }
        //$snamarr = ['1'=>['shopname'=>'自营店铺','shopid'=>1]];
        

        $narr = [];
        
        foreach ($info as $k => $v) {

            $cxsarr = db('spec_items')->where('goodsid','=',$v['id'])->select();
                
            $spcarr = [];
            foreach ($cxsarr as $ksp => $vsp) {
                $spcarr[$vsp['id']] = $vsp['itemname'];
            }

            if(!isset($narr[$v['shopid']])){
                $narr[$v['shopid']] = [
                    'shopid' => $v['shopid'],
                    'shopname' => $snamarr[$v['shopid']]['store_name'],
                    'shoplogo' => $snamarr[$v['shopid']]['store_logo'],
                    'this_all' => false
                ];
            }

            // if(!isset($narr[1])){
            //     $narr[1] = [
            //         'shopid' => 1,
            //         'shopname' => $snamarr[1]['shopname'],
            //         'this_all' => false
            //     ];
            // }
          
            if ( $v['gspecid'] != 0 ) {
                $jg = $v['specprice'];
            } else {
                $jg = $v['shopprice'];
            }

            $spnmae = '';
            if ($v['gspecid'] != 0) {
                $cxarr = explode('_', $v['specids']);
                // $cxarrs = implode(',', $cxarr);
                // $cxsarr = db('spec_items')->where('id','in',$cxarrs)->select();
                
                // $spcarr = [];
                // foreach ($cxsarr as $ksp => $vsp) {
                //     $spcarr[$vsp['id']] = $vsp['itemname'];
                // }

                foreach ($cxarr as $ksps => $vsps) {
                    if ( empty($spnmae) ) {
                        $spnmae = $spcarr[$vsps];
                    } else {
                        $spnmae .= ','.$spcarr[$vsps];
                    }
                }
            }

            $narr[$v['shopid']]['cartinfo'][] = [
                'cartid' => $v['cartid'],
                'goodsid' => $v['id'],
                'goodsname' => $v['goodsname'],
                'goodsimg' => $this->uploadpath.'goodsimg/'.$v['goodsimg'],
                'goodsspec' => $v['specids'],
                'goodsspecs' => $spnmae,
                'goodsprice' => $jg,
                'cartnum' => $v['cartnum'],
                'check_one' => false
            ];

        }
        //dump($narr);die();
        $result = [
            'status' => 'success',
            'code' => 1,
            'message' => 'OK',
            'data' => [
                'carts' => array_values($narr),
            ],
        ];

        return zy_array (true,'成功！',$result,200,$isModule);
        // $jg = json_encode($result,JSON_UNESCAPED_UNICODE);
        // $jg = stripslashes($jg);

        // exit($jg);

    }


    /**
     *购物车订单结算预览
     */
    public function settlement($isModule=false){


        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        if(empty($data['uid'])){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }
        $uid = $data['uid'];
        
        if ( empty($data['cids']) ) {
            return zy_array (false,'访问受限，缺少参数','',-1 ,$isModule);
        }
        $cids = $data['cids'];

        
        $arr = explode(',', $cids);
        $cwhere = ' uid = '.$uid.' and id in('.$cids.')'; 
        $cinfo = db('carts')->where($cwhere)->select();
        if ( count($cinfo) != count($arr) ) {
            return zy_array (false,'访问受限，参数无效','',-1 ,$isModule);
        } 
        
        $prefix = config('database.prefix');
        $where = "";
        $info = Db::table($prefix.'carts')
              ->alias([$prefix.'carts'=>'a',$prefix.'goods'=>'b',$prefix.'goods_specs'=>'c'])
              ->join($prefix.'goods','a.gid = b.id','inner')
              ->join($prefix.'goods_specs','a.gspecid = c.specids','left')
              ->field('a.id as cartid, a.gspecid, a.cartnum,b.id,b.shopid, b.goodsimg, b.goodsname, b.shopprice, b.goodsstock,c.specprice, c.specstock, c.specids')
              ->where('a.id','in',$cids)
              ->select();
        //dump($info);die();      
        $infos = Db::table($prefix.'carts')->alias('a')->field('b.shopid as sid')->join($prefix.'goods b','a.gid = b.id','left')/*->leftjoin('goods_specs c','a.gspecid = c.specids and c.shopid = '.$uid)*/->where('a.id','in',$cids)->group('b.shopid')->select();

        $idarr = '';
        foreach ($infos as $key => $value) {
            if ( empty($idarr) ) {
                $idarr = $value['sid'];
            }else{
                $idarr .= ','.$value['sid'];
            }
        }

        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$idarr,'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        
        $snamarr = [];
        foreach ($rs['data'] as $ks => $vs) {
            $snamarr[$vs['uid']] = $vs;
        }

        //$snamarr = ['1'=>['shopname'=>'自营店铺','shopid'=>1]];

        $narr = [];

        $total = 0;
        $tnum = 0;

        foreach ($info as $k => $v) {

            $cxsarr = db('spec_items')->where('goodsid','=',$v['id'])->select();
                
            $spcarr = [];
            foreach ($cxsarr as $ksp => $vsp) {
                $spcarr[$vsp['id']] = $vsp['itemname'];
            }

            if(!isset($narr[$v['shopid']])){
                $narr[$v['shopid']] = [
                    'shopid' => $v['shopid'],
                    'shopname' => $snamarr[$v['shopid']]['store_name'],
                    'shoplogo' => $snamarr[$v['shopid']]['store_logo'],
                    'stprice'=>0,
                    'stnum'=>0,
                ];
            }

            // if(!isset($narr[1])){
            //     $narr[1] = [
            //         'shopid' => $v['shopid'],
            //         'shopname' => $snamarr[1]['shopname'],
            //         'stprice'=>0,
            //         'stnum'=>0
            //     ];
            // }
            
            if ( $v['gspecid'] != 0 ) {
                $jg = $v['specprice'];
            } else {
                $jg = $v['shopprice'];
            }
            $spnmae = '';

            if ($v['gspecid'] != 0) {
                $cxarr = explode('_', $v['specids']);
                //$cxarrs = implode(',', $cxarr);

                foreach ($cxarr as $ksps => $vsps) {
                    if ( empty($spnmae) ) {
                        $spnmae = $spcarr[$vsps];
                    } else {
                        $spnmae .= ','.$spcarr[$vsps];
                    }
                }
            }
            

            $narr[$v['shopid']]['stprice'] += $jg*$v['cartnum'];
            $narr[$v['shopid']]['stnum'] += $v['cartnum'];
            $total += $jg*$v['cartnum'];
            $tnum += $v['cartnum'];
            $narr[$v['shopid']]['cartinfo'][] = [
                'cartid' => $v['cartid'],
                'goodsid' => $v['id'],
                'goodsname' => $v['goodsname'],
                'goodsimg' => $this->uploadpath.'goodsimg/'.$v['goodsimg'],
                'goodsspec' => $v['specids'],
                'goodsspecs' => $spnmae,
                'goodsprice' => $jg,
                'cartnum' => $v['cartnum'],
            ];

        }


        $result = [
            'status' => 'success',
            'code' => 1,
            'message' => 'OK',
            'data' => [
                'shops' => array_values($narr),
                'totalprice' => $total,
                'totalnum' => $tnum
            ],
        ];
        // $jg = json_encode($result,JSON_UNESCAPED_UNICODE);
        // $jg = stripslashes($jg);

        // exit($jg);
        return zy_array (true,'成功！',$result,200,$isModule);
      
    }





    /**
     *订单确认订单生成
     */
    public function sureMakeOrder($uid = '',$cids = '',$isModule=false){

        if(empty($uid)){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }

        if ( empty($cids) ) {
            return zy_array (false,'访问受限，缺少参数','',-1 ,$isModule);
        }

        $arr = explode(',', $cids);
        $cwhere = ' uid = '.$uid.' and id in('.$cids.')'; 
        $cinfo = db('carts')->where($cwhere)->select();
        if ( count($cinfo) != count($arr) ) {
            return zy_array (false,'访问受限，参数无效','',-1 ,$isModule);
        } 


        $prefix = config('database.prefix');
        $where = "";
        $info = Db::table($prefix.'carts')
              ->alias([$prefix.'carts'=>'a',$prefix.'goods'=>'b',$prefix.'goods_specs'=>'c'])
              ->join($prefix.'goods','a.gid = b.id','inner')
              ->join($prefix.'goods_specs','a.gspecid = c.specids','left')
              ->field('a.id as cartid, a.gspecid, a.cartnum,b.id,b.shopid, b.goodsimg, b.goodsname, b.shopprice, b.goodsstock,c.specprice, c.specstock, c.specids')
              ->where('a.id','in',$cids)
              ->select();
        //dump($info);die();      
        $infos = Db::table($prefix.'carts')->alias('a')->field('b.shopid as sid')->join($prefix.'goods b','a.gid = b.id','left')/*->leftjoin('goods_specs c','a.gspecid = c.specids and c.shopid = '.$uid)*/->where('a.id','in',$cids)->group('b.shopid')->select();

        $idarr = '';
        foreach ($infos as $key => $value) {
            if ( empty($idarr) ) {
                $idarr = $value['sid'];
            }else{
                $idarr .= ','.$value['sid'];
            }
        }

        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$idarr,'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        
        $snamarr = [];
        foreach ($rs['data'] as $ks => $vs) {
            $snamarr[$vs['uid']] = $vs;
        }

        //$snamarr = ['1'=>['shopname'=>'自营店铺','shopid'=>1]];

        $narr = [];

        $total = 0;
        $tnum = 0;

        foreach ($info as $k => $v) {

            $cxsarr = db('spec_items')->where('goodsid','=',$v['id'])->select();
                
            $spcarr = [];
            foreach ($cxsarr as $ksp => $vsp) {
                $spcarr[$vsp['id']] = $vsp['itemname'];
            }

            if(!isset($narr[$v['shopid']])){
                $narr[$v['shopid']] = [
                    'shopid' => $v['shopid'],
                    'shopname' => $snamarr[$v['shopid']]['store_name'],
                    'shoplogo' => $snamarr[$v['shopid']]['store_logo'],
                    'stprice'=>0,
                    'stnum'=>0,
                ];
            }

            // if(!isset($narr[1])){
            //     $narr[1] = [
            //         'shopid' => $v['shopid'],
            //         'shopname' => $snamarr[1]['shopname'],
            //         'stprice'=>0,
            //         'stnum'=>0
            //     ];
            // }
            
            if ( $v['gspecid'] != 0 ) {
                $jg = $v['specprice'];
            } else {
                $jg = $v['shopprice'];
            }
            $spnmae = '';

            if ($v['gspecid'] != 0) {
                $cxarr = explode('_', $v['specids']);
                //$cxarrs = implode(',', $cxarr);

                foreach ($cxarr as $ksps => $vsps) {
                    if ( empty($spnmae) ) {
                        $spnmae = $spcarr[$vsps];
                    } else {
                        $spnmae .= ','.$spcarr[$vsps];
                    }
                }
            }
            

            $narr[$v['shopid']]['stprice'] += $jg*$v['cartnum'];
            $narr[$v['shopid']]['stnum'] += $v['cartnum'];
            $total += $jg*$v['cartnum'];
            $tnum += $v['cartnum'];
            $narr[$v['shopid']]['cartinfo'][] = [
                'cartid' => $v['cartid'],
                'goodsid' => $v['id'],
                'goodsname' => $v['goodsname'],
                'goodsimg' => $this->uploadpath.'goodsimg/'.$v['goodsimg'],
                'goodsspec' => $v['specids'],
                'goodsspecs' => $spnmae,
                'goodsprice' => $jg,
                'cartnum' => $v['cartnum'],
            ];

        }


        $result = [
            'status' => 'success',
            'code' => 1,
            'message' => 'OK',
            'data' => [
                'shops' => array_values($narr),
                'totalprice' => $total,
                'totalnum' => $tnum
            ],
        ];

        db('carts')->where('id','in',$cids)->delete();

        return zy_array (true,'成功！',$result,200,$isModule);

    }  



  /**
    * 删除购物车里的商品
    */
    public function delcarts($isModule=false){

        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        if(empty($data['uid'])){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }
        $uid = $data['uid'];
        
        if ( empty($data['cids']) ) {
            return zy_array (false,'访问受限，缺少参数','',-1 ,$isModule);
        }
        $cids = $data['cids'];

        
        $arr = explode(',', $cids);
        $cwhere = ' uid = '.$uid.' and id in('.$cids.')'; 
        $cinfo = db('carts')->where($cwhere)->select();
        if ( count($cinfo) != count($arr) ) {
            return zy_array (false,'访问受限，参数无效','',-1 ,$isModule);
        } 

        db('carts')->where('id','in',$cids)->delete();
        return zy_array (true,'删除成功！','',200,$isModule);
    }



    /**
     *购物车修改操作
     */
    public function operacars($isModule=false){

        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        if(empty($data['uid'])){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }
        $uid = $data['uid'];
        
        if ( empty($data['cid']) ) {
            return zy_array (false,'访问受限，缺少必要参数','',-1 ,$isModule);
        }
        $cid = $data['cid'];
        $info = db('carts')->where(['id'=>$data['cid'],'uid'=>$data['uid']])->find();
        if(empty($info)){
            return zy_array (false,'非法访问','',-1 ,$isModule);
        }
        $ginfo = db('goods')->field('isspec')->where(['id'=>$info['gid']])->find();

        if ( empty($data['num'])) {
            return zy_array (false,'访问受限，缺少参数','',-1 ,$isModule);
        }
        $map = [
            'uid'=> $uid,
            'id' => $cid
        ];
        if ( $ginfo['isspec'] == 1 ) {
            if ( empty($data['specid']) ) {
                return zy_array (false,'访问受限，缺少参数S','',-1 ,$isModule);
            }
            $map['gspecid'] = $data['specid'];  
        }
        
        $info = db('carts')->where($map/*['uid'=>$uid,'id'=>$cid,'gspecid'=>$data['specid']]*/)->find();
        if ( count($info) == 0 ) {
            return zy_array (false,'访问受限，参数异常','',-1 ,$isModule);
        }


        db('carts')->where($map/*['uid'=>$uid,'id'=>$cid,'gspecid'=>$data['specid']]*/)->update(['cartnum'=>$data['num']]);
        $datas = [];
        $datas['cnum'] = $data['num'];
        return zy_array (true,'操作成功！',$datas,200,$isModule);
    }



    /**
     *获取商城栏目分类
     */
    public function getgoodscat($isModule=false){  

        $info = db('goodscats')->where(['isshow'=>1])->field('catid,pid,catname,catimg')->order('catid asc')->select()->toArray();
        $r = [];
        if ( count($info) > 0 ) {
            foreach ($info as $k => $v) {
                $info[$k]['catimg'] = $this->uploadpath.'catimg/'.$info[$k]['catimg'];
            }
            $tree = new GoodscatsModel();
            $data = $tree->catetree($info);

            require('classes/PHPTree.class.php');
            $r=\PHPTree::makeTree($data, array(

            ));
        }
        

        return zy_array (true,'操作成功！',$r,200,$isModule);
    }



    /**
     *商品搜索
     */
    public function goodslist($page=1,$gid='',$gname='',$isModule=false){
        $data = $this->request->post();
        $data = zy_decodeData($data,$isModule);

        // if(empty($data['uid'])){
        //     return zy_array (false,'uid不能为空','',-1 ,$isModule);
        // }
        //$uid = $data['uid'];
        // $data['uid'] = 30;
        // $data['sercon'] = '手机';
        $where = ' issale = 1 and goodsstatus = 1 ';
        $order = ' viewnum desc ';
        if( isset($data['order']) ){
            switch ($data['order']) {
                case '1':
                      $order =' viewnum desc ';
                      break;
                case '2':
                      $order =' salenum desc ';
                      break;
                case '3':
                      $order =' salenum asc ';
                      break;
                case '4':
                      $order =' shopprice asc ';
                      break;
                case '5':
                      $order =' shopprice desc ';
                      break;
                case '6':
                      $order =' addtime desc ';
                      break;          
                
                default:
                  
                    break;
            }
        }

        if ( isset($data['sercon']) && !empty(trim($data['sercon'])) ) {
            $where .= " and goodsname like '%".$data['sercon']."%' ";
            if ( isset($data['uid']) ) {

                $his = db('goods_sh')->where(['uid'=>$data['uid']])->find();
                if ( count($his) == 0 ) {
                    //$hisarr = [];
                    // $hisarr[] = $data['sercon'];
                    // $hiscon = implode('`', $hisarr); 
                    $hiscon = $data['sercon'];
                    db('goods_sh')->insert(['uid'=>$data['uid'],'searchhistory'=>$hiscon]);
                } elseif ( empty($his['searchhistory']) ) {
                    db('goods_sh')->where(['uid'=>$data['uid']])->update(['searchhistory'=>$data['sercon']]);
                } else {
                
                    $hisarr = explode('`', $his['searchhistory']);
                    foreach ($hisarr as $k => $v) {
                        if ( $data['sercon'] == $v ) {
                            unset($hisarr[$k]);
                            array_values($hisarr);
                            break;
                        }
                    }
                    if ( count($hisarr) < 10 ) {
                        //$hisarr[] = $_POST['sercon'];
                        array_unshift($hisarr,$data['sercon']);
                    } else {
                        unset($hisarr[9]);
                        array_unshift($hisarr,$data['sercon']);
                    }
                    $hiscon = implode('`', $hisarr);
                    db('goods_sh')->where(['uid'=>$data['uid']])->update(['searchhistory'=>$hiscon]);
                }
            }
        }

        if (isset($data['catid'])) {
            // $cinfo = db('goodscats')->where(['catid'=>$data['catid']])->find();
            // if ( empty($cinfo) ) {
            //     return zy_array (false,'参数异常','',-1 ,$isModule);
            // }
            // if ( !empty($cinfo['pid']) ) {
            //     $pinfo1 = db('goodscats')->where(['catid'=>$cinfo['pid']])->find();
            //     $pinfo2 = db('goodscats')->where(['catid'=>$pinfo1['pid']])->find();

            //     $gcatpath = $pinfo2['catid']."-".$pinfo1['catid']."-".$cinfo['catid']."-";
            // } else {
            //     $gcatpath = $cinfo['catid'].'-';
            // }

            // $where .= " and gcatpath = ".'"'.$gcatpath.'"';

            $cinfos = db('goodscats')->field('catid,pid')->select();
            $child = new GoodscatsModel();
            $rdata = $child->GetTeam($cinfos,$data['catid']);
            if (count($rdata) == 0) {
                $idstr = $data['catid'];
            } else {
                $idstr = implode(',', $rdata);
                $idstr .= ','.$data['catid'];
            }
            
            $where .= " and gcatid in (".$idstr.") ";
        }

        // $symbol ='goods';
        // $id = 'member_two';
        // $param = ['uid'=>'','isModule'=>true];
        // $rs = getModuleApiData($symbol, $id, $param);
        
        // $snamarr = [];
        // foreach ($rs['data'] as $ks => $vs) {
        //     $snamarr[$vs['uid']] = $vs['store_name'];
        // }      

        //dump($where);die();
        $paginate = empty($data['paginate'])?20:$data['paginate'];
        if ($isModule == true) {
            $where = ' goodsstatus = 1 ';
            if (!empty($gid)) {
                $where .= ' and id = '.$gid;
            }
            if (!empty($gname)) {
                $where .= " and goodsname like '%".$gname."%' ";;

            }

            $data = Db::name("goods")->where($where)->order($order)->field('id,shopid,goodsname,goodsimg,marketprice,shopprice,salenum')->paginate($paginate,true,[
                'page' => $page,
            ]);
            
        } else {
            $data = Db::name("goods")->where($where)->order($order)->field('id,shopid,goodsname,goodsimg,marketprice,shopprice,salenum')->paginate($paginate);
        }

        

        $data = json_encode($data);
        $data = json_decode($data,true);

        //dump($data);die();
        // foreach ($data['data'] as $k => $v) {
        //     $data['data'][$k]['goodsimg'] = $this->uploadpath.'goodsimg/'.$data['data'][$k]['goodsimg'];
        //     $data['data'][$k]['shopname'] = $snamarr[$v['shopid']];
        //     unset($data['data'][$k]['shopid']);
        // }


        if(count($data['data'])>0){
            $idarr = [];
            // $goods = new GoodscatsModel();
            // $keyCats = $goods->listKeyAll();
            foreach ($data['data'] as $k => $v){
                if (!in_array($v['shopid'],$idarr)) {
                    $idarr[] = $v['shopid'];
                }
            }

            $ids = implode(',', $idarr);

            $symbol ='goods';
            $id = 'member_two';
            $param = ['uid'=>$ids,'isModule'=>true];
            $rs = getModuleApiData($symbol, $id, $param);
            
            $snamarr = [];
            foreach ($rs['data'] as $ks => $vs) {
                $snamarr[$vs['uid']] = $vs['store_name'];
            }

            foreach ($data['data'] as $k => $v) {
                $data['data'][$k]['goodsimg'] = $this->uploadpath.'goodsimg/'.$data['data'][$k]['goodsimg'];
                $data['data'][$k]['shopname'] = $snamarr[$v['shopid']];
                unset($data['data'][$k]['shopid']);
                
            }
        }
        //dump($isModule);die();

        return zy_array (true,'操作成功',$data,200,$isModule);

    }





    /**
     *获取用户商品搜索记录
     */
    public function goods_sh($isModule=false){
      
        $data = $this->request->post();
        $data = zy_decodeData($data,$isModule);
        if(empty($data['uid'])){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }
        $uid = $data['uid'];

        $his = db('goods_sh')->where(['uid'=>$uid])->find();
        //dump($his);die();
        if ( !empty($his) ) {
            $hisarr = explode('`', $his['searchhistory']);
        } else {
            $hisarr = [];
        }
            
        return zy_array (true,'OK！',$hisarr,200,$isModule);
    } 



    /**
     *清空用户商品搜索记录
     */
    public function goods_shdel($isModule=false){
      
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        if(empty($data['uid'])){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }

        db('goods_sh')->where(['uid'=>$data['uid']])->update(['searchhistory'=>'']);
        return zy_array (true,'OK！','',200,$isModule);
    } 


    /**
     *立即购买订单结算预览
     */
    public function buynowsettlement($isModule=false){


        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        //$data['uid'] = 30;$data['gid'] = 2;$data['specid'] = '6,5,4';$data['num'] = 1;

        if(empty($data['uid'])){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }
        $uid = $data['uid'];
        
        if ( empty($data['gid']) || !isset($data['specid']) ) {
            return zy_array (false,'访问受限，缺少参数','',-1 ,$isModule);
        }
        if ( $data['num'] <= 0 ) {
            return zy_array (false,'参数类型异常','',-1 ,$isModule);
        }
        $ginfo = db('goods')->field('issale,goodsstatus,shopid,isspec')->where(['id'=>$data['gid']])->find();
        if ( count($ginfo) == 0 || $ginfo['issale'] == 0 || $ginfo['goodsstatus'] != 1 ) {
            return zy_array (false,'不存在的商品或商品已下架','',-1 ,$isModule);
        }
        $specid = 0;
        if ( $ginfo['isspec'] == 1 ) {
            $specid = explode(',', $data['specid']);
            $specid = implode('_', $specid);
            $gsinfo = db('goods_specs')->where(['goodsid'=>$data['gid'],'specids'=>$specid])->find();

            if ( !$gsinfo ) {
                return zy_array (false,'参数异常','',-1 ,$isModule);
            }
        }
        
        
        $gid = $data['gid'];
        $num = $data['num'];
        
        $prefix = config('database.prefix');
        $where = "";
        $info = Db::table($prefix.'goods')
              ->alias([$prefix.'goods'=>'b',$prefix.'goods_specs'=>'c'])
              ->join($prefix.'goods_specs','b.id = c.goodsid and c.specids = '.'"'.$specid.'"','left')
              ->field('b.id,b.shopid, b.goodsimg, b.goodsname, b.shopprice, b.goodsstock,c.specprice, c.specstock, b.isspec, c.specids')
              ->where('b.id',$gid)
              ->select();
        //dump($info);die();      
        

        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$ginfo['shopid'],'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        
        $snamarr = [];
        foreach ($rs['data'] as $ks => $vs) {
            $snamarr[$vs['uid']] = $vs;
        }      

        //$snamarr = ['1'=>['shopname'=>'自营店铺','shopid'=>1]];

        $narr = [];

        $total = 0;
        $tnum = 0;

        foreach ($info as $k => $v) {

            $cxsarr = db('spec_items')->where('goodsid','=',$v['id'])->select();
                
            $spcarr = [];
            foreach ($cxsarr as $ksp => $vsp) {
                $spcarr[$vsp['id']] = $vsp['itemname'];
            }

            if(!isset($narr[$ginfo['shopid']])){
                $narr[$ginfo['shopid']] = [
                    'shopid' => $v['shopid'],
                    'shopname' => $snamarr[$ginfo['shopid']]['store_name'],
                    'shoplogo' => $snamarr[$ginfo['shopid']]['store_logo'],
                    'stprice'=>0,
                    'stnum'=>0
                ];
            }
            
            if ( $v['isspec'] != 0 ) {
                $jg = $v['specprice'];
            } else {
                $jg = $v['shopprice'];
            }
            $spnmae = '';

            if ($v['isspec'] != 0) {
                $cxarr = explode('_', $v['specids']);
                //$cxarrs = implode(',', $cxarr);

                foreach ($cxarr as $ksps => $vsps) {
                    if ( empty($spnmae) ) {
                        $spnmae = $spcarr[$vsps];
                    } else {
                        $spnmae .= ','.$spcarr[$vsps];
                    }
                }
            }
            

            $narr[$ginfo['shopid']]['stprice'] += $jg*$num;
            $narr[$ginfo['shopid']]['stnum'] += $num;
            $total += $jg*$num;
            $tnum += $num;
            $narr[$ginfo['shopid']]['cartinfo'][] = [
                'goodsid' => $v['id'],
                'goodsname' => $v['goodsname'],
                'goodsimg' => $this->uploadpath.'goodsimg/'.$v['goodsimg'],
                'goodsspec' => $v['specids'],
                'goodsspecs' => $spnmae,
                'goodsprice' => $jg,
                'cartnum' => $num,
            ];

        }


        $result = [
            'status' => 'success',
            'code' => 1,
            'message' => 'OK',
            'data' => [
                'shops' => array_values($narr),
                'totalprice' => $total,
                'totalnum' => $tnum
            ],
        ];
        // $jg = json_encode($result,JSON_UNESCAPED_UNICODE);
        // $jg = stripslashes($jg);

        // exit($jg);
        return zy_array (true,'成功！',$result,200,$isModule);
      
    }


    /**
     *立即购买确认订单
     */
    public function buynowsureorder($uid = '',$gid = '',$specid=0,$num = '',$isModule=false){


        if(empty($uid)){
            return zy_array (false,'uid不能为空','',-1 ,$isModule);
        }
        
        if ( empty($gid) ) {
            return zy_array (false,'访问受限，缺少参数','',-1 ,$isModule);
        }
        if ( $num <= 0 ) {
            return zy_array (false,'参数类型异常','',-1 ,$isModule);
        }
        $ginfo = db('goods')->field('issale,goodsstatus,shopid,isspec')->where(['id'=>$gid])->find();
        if ( count($ginfo) == 0 || $ginfo['issale'] == 0 || $ginfo['goodsstatus'] != 1 ) {
            return zy_array (false,'不存在的商品或商品已下架','',-1 ,$isModule);
        }

        if ( $ginfo['isspec'] == 1 ) {
            if (empty($specid) ) {
              return zy_array (false,'访问受限，缺少参数S','',-1 ,$isModule);
          }
            $specid = explode(',', $specid);
            $specid = implode('_', $specid);
            $gsinfo = db('goods_specs')->where(['goodsid'=>$gid,'specids'=>$specid])->find();

            if ( !$gsinfo ) {
                return zy_array (false,'参数异常','',-1 ,$isModule);
            }
        }
        
        $prefix = config('database.prefix');
        $where = "";
        $info = Db::table($prefix.'goods')
              ->alias([$prefix.'goods'=>'b',$prefix.'goods_specs'=>'c'])
              ->join($prefix.'goods_specs','b.id = c.goodsid and c.specids = '.'"'.$specid.'"','left')
              ->field('b.id,b.shopid, b.goodsimg, b.goodsname, b.shopprice, b.goodsstock,b.isspec,c.specprice, c.specstock, c.specids')
              ->where('b.id',$gid)
              ->select();
        //dump($info);die();      
        

        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$ginfo['shopid'],'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        
        $snamarr = [];
        foreach ($rs['data'] as $ks => $vs) {
            $snamarr[$vs['uid']] = $vs;
        }            
        //$snamarr = ['1'=>['shopname'=>'自营店铺','shopid'=>1]];

        $narr = [];

        $total = 0;
        $tnum = 0;

        foreach ($info as $k => $v) {

            $cxsarr = db('spec_items')->where('goodsid','=',$v['id'])->select();
                
            $spcarr = [];
            foreach ($cxsarr as $ksp => $vsp) {
                $spcarr[$vsp['id']] = $vsp['itemname'];
            }

            // if(!isset($narr[1])){
            //     $narr[1] = [
            //         'shopid' => $v['shopid'],
            //         'shopname' => $snamarr[1]['shopname'],
            //         'stprice'=>0,
            //         'stnum'=>0
            //     ];
            // }
            if(!isset($narr[$ginfo['shopid']])){
                $narr[$ginfo['shopid']] = [
                    'shopid' => $v['shopid'],
                    'shopname' => $snamarr[$ginfo['shopid']]['store_name'],
                    'shoplogo' => $snamarr[$ginfo['shopid']]['store_logo'],
                    'stprice'=>0,
                    'stnum'=>0
                ];
            }
            
            if ( $v['isspec'] != 0 ) {
                $jg = $v['specprice'];
            } else {
                $jg = $v['shopprice'];
            }
            $spnmae = '';

            if ($v['isspec'] != 0) {
                $cxarr = explode('_', $v['specids']);
                //$cxarrs = implode(',', $cxarr);

                foreach ($cxarr as $ksps => $vsps) {
                    if ( empty($spnmae) ) {
                        $spnmae = $spcarr[$vsps];
                    } else {
                        $spnmae .= ','.$spcarr[$vsps];
                    }
                }
            }
            

            $narr[$ginfo['shopid']]['stprice'] += $jg*$num;
            $narr[$ginfo['shopid']]['stnum'] += $num;
            $total += $jg*$num;
            $tnum += $num;
            $narr[$ginfo['shopid']]['cartinfo'][] = [
                'goodsid' => $v['id'],
                'goodsname' => $v['goodsname'],
                'goodsimg' => $this->uploadpath.'goodsimg/'.$v['goodsimg'],
                'goodsspec' => $v['specids'],
                'goodsspecs' => $spnmae,
                'goodsprice' => $jg,
                'cartnum' => $num,
            ];

        }


        $result = [
            'status' => 'success',
            'code' => 1,
            'message' => 'OK',
            'data' => [
                'shops' => array_values($narr),
                'totalprice' => $total,
                'totalnum' => $tnum
            ],
        ];


        return zy_array (true,'成功！',$result,200,$isModule);
    }



    /**
     *支付订单回调计算销量库存
     */
    public function settleorder($datas=[],$isModule=false){

        if( $isModule==false || empty($datas) ){
            return zy_array (false,'非法访问','',-1 ,$isModule);
        }
        //dump($datas);die();
        $data = $datas;
        if ( count($data) == 1 ) {
            db('goods_specs')->where(['goodsid'=>$data[0]['goods_id'],'specids'=>$data[0]['specid']])->setInc('salenum', $data[0]['goods_num']);
            db('goods')->where(['id'=>$data[0]['goods_id']])->setInc('salenum', $data[0]['goods_num']);

            db('goods_specs')->where(['goodsid'=>$data[0]['goods_id'],'specids'=>$data[0]['specid']])->setDec('specstock', $data[0]['goods_num']);
            db('goods')->where(['id'=>$data[0]['goods_id']])->setDec('goodsstock', $data[0]['goods_num']);
        } else{
            $goodsinfo = [];
            foreach ($data as $k => $v) {
                $gkey = $v['goods_id'];
                if ( !isset($goodsinfo[$gkey]) ) {
                    $goodsinfo[$gkey] = [
                        'tnum' => $v['goods_num'],
                        'child' => [] 
                    ];
                    if ( $v['specid'] != 0 ) {
                        $goodsinfo[$gkey]['child'][$v['specid']] = [
                            //'specid' => $v['specid'],
                            'snum' => $v['goods_num']
                        ];
                    }
                    
                } else {
                    $goodsinfo[$gkey]['tnum'] += $v['goods_num'];
                    if ( $v['specid'] != 0 ) {
                        if ( !isset($goodsinfo[$gkey]['child'][$v['specid']]) ) {
                            $goodsinfo[$gkey]['child'][$v['specid']] = [
                                //'specid' => $v['specid'],
                                'snum' => $v['goods_num']
                            ];
                        } else {
                            $goodsinfo[$gkey]['child'][$v['specid']]['snum'] += $v['goods_num'];
                        }
                    }
                }
            }

            foreach ($goodsinfo as $k => $v) {
                db('goods')->where(['id'=>$k])->setInc('salenum', $v['tnum']);
                db('goods')->where(['id'=>$k])->setDec('goodsstock', $v['tnum']);

                if ( !empty($v['child']) ) {
                    foreach ($v['child'] as $ks => $vs) {
                        db('goods_specs')->where(['goodsid'=>$k,'specids'=>$ks])->setInc('salenum', $vs['snum']);
                        db('goods_specs')->where(['goodsid'=>$k,'specids'=>$ks])->setDec('specstock', $vs['snum']);
                    }
                }
                
            }


        }



        return zy_array (true,'OK','',200,$isModule);
    }









    /*店铺部分*/


    /**
     *获取店铺商品列表（正常上架）
     */
    public function shoponsalegoods($isModule=false){
      
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        if(empty($data['uid'])){
            return zy_array (false,'参数异常','',-1 ,$isModule);
        }

        $map = [];
        // $search = [
        //     'cat1' => '',
        //     'cat2' => '',
        //     'cat3' => '',
        //     'cat2arr' => [],
        //     'cat3arr' => [],
        //     'keyword' => '',
        //     'keywords' => ''
        // ];
        // if(request()->isPost()){
            
        //     $str = '';
        //     if ( input('cat1') ) {
        //         $str = input('cat1').'-';
        //         $search['cat1'] = input('cat1');
        //         $search['cat2arr'] = db('goodscats')->field("catid,catname")->where(['pid'=>input('cat1')])->order('catsort asc,catname asc')->select();
        //         if ( input('cat2') ) {
        //             $str .= input('cat2').'-';
        //             $search['cat2'] = input('cat2');
        //             $search['cat3arr'] = db('goodscats')->field("catid,catname")->where(['pid'=>input('cat2')])->order('catsort asc,catname asc')->select();
        //             if ( input('cat3') ) {
        //                 $str .= input('cat3').'-';
        //                 $search['cat3'] = input('cat3');
        //             }
        //         }
        //         $map['gcatpath']  = ['like','%'.$str.'%'];
        //     }
        //     if ( input('keyword') ) {
        //         // $map['specname']  = ['like','%'.input('keyword').'%'];
        //         // $search['keyword'] = input('keyword');
        //     }

        // }

        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$data['uid'],'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        if ( empty($rs['data']) ) {
            return zy_array (false,'非法访问','',-2 ,$isModule);
        }
        $snamarr = [];
        foreach ($rs['data'] as $ks => $vs) {
            $snamarr[$vs['uid']] = $vs['store_name'];
        }

        $map['issale']  = ['=',1];
        $map['goodsstatus'] = ['=',1];
        $map['shopid']  = ['=',$data['uid']];
        $paginate = empty($data['paginate'])?15:$data['paginate'];
        $info = db('goods')->where($map)->field('id,shopid,goodsname,gcatpath,goodsimg,goodsstatus,goodsstock,marketprice,shopprice,issale,salenum,isspec')->order('id desc')->paginate($paginate);
        $infos = $info->toArray();
        if(count($infos['data'])>0){
            $goods = new GoodscatsModel();
            $keyCats = $goods->listKeyAll();
            foreach ($infos['data'] as $key => $v){
                $infos['data'][$key]['goodsimg'] = $this->uploadpath.'goodsimg/'.$infos['data'][$key]['goodsimg'];
                $goodsCatPath = $infos['data'][$key]['gcatpath'];
                $infos['data'][$key]['gcatpath'] = $goods->getGoodsCatNames($goodsCatPath,$keyCats);
                $infos['data'][$key]['shopid'] = $snamarr[$infos['data'][$key]['shopid']];
            }
        }

        return zy_array (true,'OK',$infos,200,$isModule);

    } 




    /**
     *获取店铺商品列表（下架）
     */
    public function shopunsalegoods($isModule=false){
      
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        if(empty($data['uid'])){
            return zy_array (false,'参数异常','',-1 ,$isModule);
        }

        $map = [];
        
        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$data['uid'],'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        if ( empty($rs['data']) ) {
            return zy_array (false,'非法访问','',-2 ,$isModule);
        }
        $snamarr = [];
        foreach ($rs['data'] as $ks => $vs) {
            $snamarr[$vs['uid']] = $vs['store_name'];
        }

        $map['issale']  = ['=',0];
        $map['goodsstatus'] = ['=',1];
        $map['shopid']  = ['=',$data['uid']];
        $paginate = empty($data['paginate'])?15:$data['paginate'];
        $info = db('goods')->where($map)->field('id,shopid,goodsname,gcatpath,goodsimg,goodsstatus,goodsstock,marketprice,shopprice,issale,salenum,isspec')->order('id desc')->paginate($paginate);
        $infos = $info->toArray();
        if(count($infos['data'])>0){
            $goods = new GoodscatsModel();
            $keyCats = $goods->listKeyAll();
            foreach ($infos['data'] as $key => $v){
                $infos['data'][$key]['goodsimg'] = $this->uploadpath.'goodsimg/'.$infos['data'][$key]['goodsimg'];
                $goodsCatPath = $infos['data'][$key]['gcatpath'];
                $infos['data'][$key]['gcatpath'] = $goods->getGoodsCatNames($goodsCatPath,$keyCats);
                $infos['data'][$key]['shopid'] = $snamarr[$infos['data'][$key]['shopid']];
            }
        }

        return zy_array (true,'OK',$infos,200,$isModule);

    } 




    /**
     *获取店铺商品列表（审核）
     */
    public function shopauditgoods($isModule=false){
      
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        if(empty($data['uid'])){
            return zy_array (false,'参数异常','',-1 ,$isModule);
        }

        
        
        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$data['uid'],'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        if ( empty($rs['data']) ) {
            return zy_array (false,'非法访问','',-2 ,$isModule);
        }
        $snamarr = [];
        foreach ($rs['data'] as $ks => $vs) {
            $snamarr[$vs['uid']] = $vs['store_name'];
        }

        $where = ' shopid = '.$data['uid'].' and goodsstatus > 1 and goodsstatus < 4 ';
        $paginate = empty($data['paginate'])?15:$data['paginate'];
        $info = db('goods')->where($where)->field('id,shopid,goodsname,gcatpath,goodsimg,goodsstatus,goodsstock,marketprice,shopprice,issale,salenum,isspec,illegalremarks,nopassremarks')->order('id desc')->paginate($paginate);
        $infos = $info->toArray();
        if(count($infos['data'])>0){
            $goods = new GoodscatsModel();
            $keyCats = $goods->listKeyAll();
            foreach ($infos['data'] as $key => $v){
                $infos['data'][$key]['goodsimg'] = $this->uploadpath.'goodsimg/'.$infos['data'][$key]['goodsimg'];
                $goodsCatPath = $infos['data'][$key]['gcatpath'];
                $infos['data'][$key]['gcatpath'] = $goods->getGoodsCatNames($goodsCatPath,$keyCats);
                $infos['data'][$key]['shopid'] = $snamarr[$infos['data'][$key]['shopid']];
            }
        }

        return zy_array (true,'OK',$infos,200,$isModule);

    } 



    /**
     *获取店铺商品列表（违规）
     */
    public function shopillegalgoods($isModule=false){
      
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        if(empty($data['uid'])){
            return zy_array (false,'参数异常','',-1 ,$isModule);
        }

        
        
        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$data['uid'],'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        if ( empty($rs['data']) ) {
            return zy_array (false,'非法访问','',-2 ,$isModule);
        }
        $snamarr = [];
        foreach ($rs['data'] as $ks => $vs) {
            $snamarr[$vs['uid']] = $vs['store_name'];
        }

        $where = ' shopid = '.$data['uid'].' and goodsstatus = 4 ';
        $paginate = empty($data['paginate'])?15:$data['paginate'];
        $info = db('goods')->where($where)->field('id,shopid,goodsname,gcatpath,goodsimg,goodsstatus,goodsstock,marketprice,shopprice,issale,salenum,isspec,illegalremarks,nopassremarks')->order('id desc')->paginate($paginate);
        $infos = $info->toArray();
        if(count($infos['data'])>0){
            $goods = new GoodscatsModel();
            $keyCats = $goods->listKeyAll();
            foreach ($infos['data'] as $key => $v){
                $infos['data'][$key]['goodsimg'] = $this->uploadpath.'goodsimg/'.$infos['data'][$key]['goodsimg'];
                $goodsCatPath = $infos['data'][$key]['gcatpath'];
                $infos['data'][$key]['gcatpath'] = $goods->getGoodsCatNames($goodsCatPath,$keyCats);
                $infos['data'][$key]['shopid'] = $snamarr[$infos['data'][$key]['shopid']];
            }
        }

        return zy_array (true,'OK',$infos,200,$isModule);

    } 




    /**
     *上架
     */
    public function onsale($isModule=false)
    {   
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        if( empty($data['uid']) || empty($data['gid']) ){
            return zy_array (false,'参数异常','',-1 ,$isModule);
        }

        $ginfo = db('goods')->field('issale,goodsstatus,shopid,isspec')->where(['id'=>$data['gid'],'shopid'=>$data['uid']])->find();
        if ( count($ginfo) == 0 || $ginfo['goodsstatus'] != 1 ) {
            return zy_array (false,'操作异常，不合法的商品','',-2 ,$isModule);
        }

        $res = db('goods')->where(['id'=>$data['gid'],'shopid'=>$data['uid']])->update(['issale'=>1]);
        if( $res!== false ){
            return zy_array (true,'OK','',200,$isModule);
        }else{
            return zy_array (false,'操作失败，请重试','',-3 ,$isModule);
        }  
    }

    /**
     *下架
     */
    public function unsale($isModule=false)
    {
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        if( empty($data['uid']) || empty($data['gid']) ){
            return zy_array (false,'参数异常','',-1 ,$isModule);
        }

        $ginfo = db('goods')->field('issale,goodsstatus,shopid,isspec')->where(['id'=>$data['gid'],'shopid'=>$data['uid']])->find();
        if ( count($ginfo) == 0 || $ginfo['goodsstatus'] != 1 ) {
            return zy_array (false,'操作异常，不合法的商品','',-2 ,$isModule);
        }

        $res = db('goods')->where(['id'=>$data['gid'],'shopid'=>$data['uid']])->update(['issale'=>0]);
        if( $res!== false ){
            return zy_array (true,'OK','',200,$isModule);
        }else{
            return zy_array (false,'操作失败，请重试','',-3 ,$isModule);
        }  
    }



    /**
     *获取店铺上下架数量
     */
    public function getshopcount($isModule=false)
    {
        $data=$this->request->post();
        $data = zy_decodeData($data,$isModule);
        if( empty($data['uid']) ){
            return zy_array (false,'参数异常','',-1 ,$isModule);
        }

        $symbol ='goods';
        $id = 'member_two';
        $param = ['uid'=>$data['uid'],'isModule'=>true];
        $rs = getModuleApiData($symbol, $id, $param);
        if ( empty($rs['data']) ) {
            return zy_array (false,'非法访问','',-2 ,$isModule);
        }

        $num1 = db('goods')->where(['goodsstatus'=>1,'shopid'=>$data['uid'],'issale'=>1])->count();
        $num2 = db('goods')->where(['goodsstatus'=>1,'shopid'=>$data['uid'],'issale'=>0])->count();
        $res = [
            'up'=>$num1,
            'down'=>$num2
        ];
        return zy_array (true,'OK',$res,200,$isModule);

    }
















    /**
     * 加密数据encryption
     * @param  $strcon 需要加密的内容
     * @param  $type  en加密  de解密
     */
    function zy_userid_jwts ($strcon,$status='en') {
        if(is_integer($strcon)){
            $str = $strcon + 8 + date('Ymd');
            $str .= 'seed';
        }else{
            $str = 'seed'.mt_rand(10,99).$strcon;
        }

//        $str = strval($str);
//        var_dump($str);die();

        $key = md5('cy');
        if($status == 'en'){//默认加密
            return str_replace('=', '', base64_encode($str ^ $key));
        }elseif($status == 'de'){//解密
            $con = base64_decode($strcon);
            $con = $con ^ $key;
            if(strpos($con, 's') != 0){
                $parr = substr($con,0,strpos($con, 's'));
                $result = $parr - 8 - date('Ymd');
            }else{
                $result = substr($con,strpos($con, 'd')+3);
            }

            return $result;
        }
    }




}
<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace plugins\goods\model;//Demo插件英文名，改成你的插件英文就行了
use think\Model;
use think\Db;
use plugins\goods\model\Base;
//Demo插件英文名，改成你的插件英文就行了,插件数据表最好加个plugin前缀再加表名,这个类就是对应“表前缀+plugin_demo”表
class GoodsModel extends Base
{
	//protected $table="cmf_goodscats";

    public function add($type=1,$sid=0){
        $specattrdata = json_decode($_POST['specattrdata'],true);
        //dump($specattrdata);die();
        $data = input('post.');
        if($data['status']==1 && $data['image']==''){
            $rdata = [
                'code' => 0,
                'msg' => '上架商品必须有商品图片'
            ];
            return $rdata;
        }

        $datas = [
            'goodsname' => $data['gtitle'],
            'goodsimg' => $data['image'],
            'goodstype' => $data['gtype'],
            'marketprice' => $data['mprice'],
            'shopprice' => $data['sprice'],
            'issale' => $data['status'],
            'goodsstatus' => '1',
            'addtime'=>time()/*date('Y-m-d H:i:s')*/,
        ];

        if ($type == 2) {
            if ($sid > 0) {
                if ($sid == 1) {
                    $datas['shopid'] = $sid;
                } else {
                    $datas['shopid'] = $sid;
                    $datas['goodsstatus'] = 2;
                }
                
            } else {
                $rdata = [
                    'code' => 0,
                    'msg' => '店铺状态异常'
                ];
            return $rdata;
            }
        } else {
            $datas['shopid'] = 1;
        }
        

        if ( input('cat1') ) {
            $str = input('cat1').'-';
            $gcatid = input('cat1');
            if ( input('cat2') ) {
                $str .= input('cat2').'-';
                $gcatid = input('cat2');
                if ( input('cat3') ) {
                    $str .= input('cat3').'-';
                    $gcatid = input('cat3');
                }
            }
            $datas['gcatpath'] = $str;
            $datas['gcatid'] = $gcatid;
        }else{
            $rdata = [
                'code' => 0,
                'msg' => '请至少选择一个商品分类'
            ];
            return $rdata;
        }
        $datas['isspec'] = ($specattrdata['specsids']!='')?1:0;
        $datas['goodsinfo'] = $data['goodsinfo'];
        // if (isset($data['goodsinfoimg'])) {
        //     $datas['goodsinfo'] = implode(',',$data['goodsinfoimg']);
        // }
        if (isset($data['timg'])) {
            $datas['goodsalbum'] = implode(',',$data['timg']);
        }
        if ($datas['isspec'] == 0) {
            $datas['goodsstock'] = $data['stock'];
        }

        $result = $this/*->allowField(true)*/->save($datas);

        if(false !== $result){
            $goodsid = $this->id;

            //判断是否有规格值
            if ($specattrdata['specsids']!='') {
                $specsIds = explode(',',$specattrdata['specsids']);
                $specsArray = [];
                foreach ($specsIds as $v){
                    $vs = explode('-',$v);
                    foreach ($vs as $vv){
                       if(!in_array($vv,$specsArray))$specsArray[] = $vv;
                    }
                }

                //保存规格名称
                $specMap = [];
                foreach ($specsArray as $v){
                    $vv = explode('_',$v);
                    $sitem = [];
                    $sitem['shopid'] = 1;
                    $sitem['catid'] = (int)$vv[0];
                    $sitem['goodsid'] = $goodsid;
                    $sitem['itemname'] = $specattrdata['specName_'.$vv[0]."_".$vv[1]];
                    $sitem['itemimg'] = $specattrdata['specImg_'.$vv[0]."_".$vv[1]];
                    $itemId = Db::name('spec_items')->insertGetId($sitem);
                    //if($sitem['itemImg']!='')WSTUseImages(0, $itemId, $sitem['itemImg']);
                    $specMap[$v] = $itemId;
                }


                //保存销售规格
                $defaultPrice = 0;//最低价
                $totalStock = 0;//总库存
                $gspecArray = [];
                $isFindDefaultSpec = false;
                $defaultSpec = $specattrdata['defaultSpec'];
                foreach ($specsIds as $v){
                    $vs = explode('-',$v);
                    $goodsSpecIds = [];
                    foreach ($vs as $gvs){
                        $goodsSpecIds[] = $specMap[$gvs];
                    }
                    $gspec = [];
                    $gspec['specids'] = implode('_',$goodsSpecIds);
                    $gspec['shopid'] = 1;
                    $gspec['goodsid'] = $goodsid;
                    $gspec['marketprice'] = (float)$specattrdata['marketPrice_'.$v];
                    $gspec['specprice'] = (float)$specattrdata['specPrice_'.$v];
                    $gspec['specstock'] = (int)$specattrdata['specStock_'.$v];
                    //设置默认规格
                    if($defaultSpec==$v){
                        $isFindDefaultSpec = true;
                        $defaultPrice = $gspec['specprice'];
                        $gspec['isdefault'] = 1;
                    }else{
                        $gspec['isdefault'] = 0;
                    }
                    $gspecArray[] = $gspec;
                    //获取总库存
                    $totalStock = $totalStock + $gspec['specstock'];
                }

                if(count($gspecArray)>0){
                    Db::name('goods_specs')->insertAll($gspecArray);
                    //更新默认价格和总库存
                    $this->where('id',$goodsid)->update(['isspec'=>1,'shopprice'=>$defaultPrice,'goodsstock'=>$totalStock]);
                }
            }



            //保存商品属性
            $attrsArray = [];
            $attrRs = Db::name('goodsattributes')->where(['gcatid'=>$gcatid,'isshow'=>1])
                            ->field('id')->select();
            foreach ($attrRs as $key =>$v){
                $attrs = [];
                $attrs['attrval'] = $specattrdata['attr_'.$v['id']];
                if($attrs['attrval']=='')continue;
                $attrs['shopid'] = 1;
                $attrs['goodsid'] = $goodsid;
                $attrs['attrid'] = $v['id'];
                $attrsArray[] = $attrs;
            }
            if(count($attrsArray)>0)Db::name('goods_attritem')->insertAll($attrsArray);
            $rdata = [
                'code' => 1,
                'msg' => '添加商品成功'
            ];
            return $rdata;
        }else{
            $rdata = [
                'code' => 0,
                'msg' => $this->getError()
            ];
            return $rdata;
        }
        
    }





    /**
     * 编辑商品资料
     */
    public function edit($type=1,$sid=0){


        $specattrdata = json_decode($_POST['specattrdata'],true);
        //dump(input('post.'));dump($specattrdata);die();
        $data = input('post.');
        if($data['status']==1 && $data['image']==''){
            $rdata = [
                'code' => 0,
                'msg' => '上架商品必须有商品图片'
            ];
            return $rdata;
        }

        $datas = [
            'goodsname' => $data['gtitle'],
            'goodsimg' => $data['image'],
            'goodstype' => $data['gtype'],
            'marketprice' => $data['mprice'],
            'shopprice' => $data['sprice'],
            'issale' => $data['status'],
            'goodsstatus' => '1',
            'addtime'=>date('Y-m-d H:i:s'),
        ];
        //$datas['shopid'] = 1;

        if ($type == 2) {
            if ($sid > 0) {
                if ($sid == 1) {
                    $datas['shopid'] = $sid;
                } else {
                    $datas['shopid'] = $sid;
                    /*$datas['goodsstatus'] = 2;*/
                }
            } else {
                $rdata = [
                    'code' => 0,
                    'msg' => '店铺状态异常'
                ];
            return $rdata;
            }
        } else {
            $datas['shopid'] = 1;
        }


        if ( input('cat1') ) {
            $str = input('cat1').'-';
            $gcatid = input('cat1');
            if ( input('cat2') ) {
                $str .= input('cat2').'-';
                $gcatid = input('cat2');
                if ( input('cat3') ) {
                    $str .= input('cat3').'-';
                    $gcatid = input('cat3');
                }
            }
            $datas['gcatpath'] = $str;
            $datas['gcatid'] = $gcatid;
        }else{
            $rdata = [
                'code' => 0,
                'msg' => '请至少选择一个商品分类'
            ];
            return $rdata;
        }
        $datas['isspec'] = ($specattrdata['specsids']!='')?1:0;
        if (isset($data['goodsinfoimg'])) {
            $datas['goodsinfo'] = implode(',',$data['goodsinfoimg']);
        }
        if (isset($data['timg'])) {
            $datas['goodsalbum'] = implode(',',$data['timg']);
        }
        if ($datas['isspec'] == 0) {
            $datas['goodsstock'] = $data['stock'];
        }
        //dump('123');die();
        $result = $this->save($datas,['id'=>$data['gid']]);

        if(false !== $result){

            $specNameMapTmp = explode(',',$specattrdata['specmap']);
            $specIdMapTmp = explode(',',$specattrdata['specidsmap']);
            $specNameMap = [];//规格值对应关系
            $specIdMap = [];//规格和表对应关系
            foreach ($specNameMapTmp as $key =>$v){
                if($v=='')continue;
                $v = explode(':',$v);
                $specNameMap[$v[1]] = $v[0];   //array('页面上的规则值ID'=>数据库里规则值的ID)
            }
            foreach ($specIdMapTmp as $key =>$v){
                if($v=='')continue;
                $v = explode(':',$v);
                $specIdMap[$v[1]] = $v[0];     //array('页面上的销售规则ID'=>数据库里销售规格ID)
            }
            $sc1 = 0;
            $sc2 = 0;
            //如果是实物商品并且有销售规格则保存销售和规格值
            if(/*$data['goodstype'] ==1 &&*/ $specattrdata['specsids']!=''){
                //把之前之前的销售规格
                $specsIds = explode(',',$specattrdata['specsids']);
                $specsArray = [];
                foreach ($specsIds as $v){
                    $vs = explode('-',$v);
                    foreach ($vs as $vv){
                       if(!in_array($vv,$specsArray))$specsArray[] = $vv;//过滤出不重复的规格值
                    }
                }
                //先标记作废之前的规格值
                Db::name('spec_items')->where(['shopid'=>$data['spid'],'goodsid'=>$data['gid']])->update(['isok'=>0]);
                //保存规格名称
                $specMap = [];
                foreach ($specsArray as $v){
                    $vv = explode('_',$v);
                    $specNumId = $vv[0]."_".$vv[1];
                    $update = [];
                    $update['itemname'] = $specattrdata['specName_'.$specNumId];
                    $update['itemimg'] = $specattrdata['specImg_'.$specNumId];
                    //如果已经存在的规格值则修改，否则新增
                    if(isset($specNameMap[$specNumId]) && (int)$specNameMap[$specNumId]!=0){
                        $update['isok'] = 1;
                        Db::name('spec_items')->where(['shopid'=>$data['spid'],'id'=>(int)$specNameMap[$specNumId]])->update($update);
                        $specMap[$v] = (int)$specNameMap[$specNumId];
                    }else{
                        $update['goodsid'] = $data['gid'];
                        $update['shopid'] = $data['spid'];
                        $update['catid'] = (int)$vv[0];
                        $update['isok'] = 1;
                        $itemid = Db::name('spec_items')->insertGetId($update);

                        $specMap[$v] = $itemid;
                    }
                }
                //删除已经作废的规格值
                $sc1 = Db::name('spec_items')->where(['shopid'=>$data['spid'],'goodsid'=>$data['gid'],'isok'=>0])->delete();

                //保存销售规格
                $defaultPrice = 0;//默认价格
                $totalStock = 0;//总库存
                $gspecArray = [];
                //把之前的销售规格值标记删除
                Db::name('goods_specs')->where(['shopid'=>$data['spid'],'goodsid'=>$data['gid']])->update(['isok'=>0,'isdefault'=>0]);
                $isFindDefaultSpec = false;
                $defaultSpec = $specattrdata['defaultSpec'];
                foreach ($specsIds as $v){
                    $vs = explode('-',$v);
                    $goodsSpecIds = [];
                    foreach ($vs as $gvs){
                        $goodsSpecIds[] = $specMap[$gvs];
                    }
                    $gspec = [];
                    $gspec['specids'] = implode('_',$goodsSpecIds);
                    $gspec['marketprice'] = (float)$specattrdata['marketPrice_'.$v];
                    $gspec['specprice'] = (float)$specattrdata['specPrice_'.$v];
                    $gspec['specstock'] = (int)$specattrdata['specStock_'.$v];

                    //设置默认规格
                    if($defaultSpec==$v){
                        $gspec['isdefault'] = 1;
                        $isFindDefaultSpec = true;
                        $defaultPrice = $gspec['specprice'];
                    }else{
                        $gspec['isdefault'] = 0;
                    }
                    //如果是已经存在的值就修改内容，否则新增
                    if(isset($specIdMap[$v]) && $specIdMap[$v]!=''){
                        $gspec['isok'] = 1;
                        Db::name('goods_specs')->where(['shopid'=>$data['spid'],'id'=>(int)$specIdMap[$v]])->update($gspec);
                    }else{
                        $gspec['shopid'] = $data['spid'];
                        $gspec['goodsid'] = $data['gid'];
                        $gspecArray[] = $gspec;
                    }
                    //获取总库存
                    $totalStock = $totalStock + $gspec['specstock'];
                }

                //删除作废的销售规格值
                $sc2 = Db::name('goods_specs')->where(['shopid'=>$data['spid'],'goodsid'=>$data['gid'],'isok'=>0])->delete();

                if(count($gspecArray)>0){
                    Db::name('goods_specs')->insertAll($gspecArray);
                }

                //更新推荐规格和总库存
                $this->where('id',$data['gid'])->update(['isspec'=>1,'shopprice'=>$defaultPrice,'goodsstock'=>$totalStock]);

            }else if($specattrdata['specsids']==''){
                Db::name('spec_items')->where(['shopid'=>$data['spid'],'goodsid'=>$data['gid']])->delete();
                Db::name('goods_specs')->where(['shopid'=>$data['spid'],'goodsid'=>$data['gid']])->delete();
            }




            //保存商品属性
            //删除之前的商品属性
            Db::name('goods_attritem')->where(['shopid'=>$data['spid'],'goodsid'=>$data['gid']])->delete();
            //新增商品属性
            $attrsArray = [];
            $attrRs = Db::name('goodsattributes')->where(['gcatpath'=>$str,'isshow'=>1,'isok'=>1])
                            ->field('id')->select();
            foreach ($attrRs as $key =>$v){
                $attrs = [];
                $attrs['attrval'] = $specattrdata['attr_'.$v['id']];
                if($attrs['attrval']=='')continue;
                $attrs['shopid'] = $data['spid'];
                $attrs['goodsid'] = $data['gid'];
                $attrs['attrid'] = $v['id'];

                $attrsArray[] = $attrs;
            }
            if(count($attrsArray)>0)Db::name('goods_attritem')->insertAll($attrsArray);
            
            if ( $sc1 > 0 || $sc2 > 0 ) {
                $this->delillcarts($data['gid']);
            }
            

            $rdata = [
                'code' => 1,
                'msg' => '修改商品信息成功'
            ];
            return $rdata;


        }else{
            $rdata = [
                'code' => 0,
                'msg' => $this->getError()
            ];
            return $rdata;
        }

    }



    /**
     * 编辑商品删除购物车实效商品
     */
    public function delillcarts($gid){
        if(is_array($gid)){
            Db::name('carts')->where([['gid','in',$gid]])->delete();
        }else{
            Db::name('carts')->where('gid',$gid)->delete();
        }
    }








    /**
     * 获取商品编辑资料
     */
    public function getedit($gid,$sid=0){
        $shopid = ($sid==0)?/*(int)session('WST_USER.shopId')*/1:$sid;
        $rs = $this->where(['shopid'=>$shopid,'id'=>$gid])->find();


        if(!empty($rs)){
            if($rs['goodsinfo']!='')$rs['goodsinfo'] = explode(',',$rs['goodsinfo']);
            if($rs['goodsalbum']!='')$rs['goodsalbum'] = explode(',',$rs['goodsalbum']);
            //$rs['goodsinfo'] = htmlspecialchars_decode($rs['goodsinfo']);
            //获取规格值
            $specs = Db::name('goodscatspec')
                    ->alias('a')
                    ->join('spec_items b','a.id=b.catid','inner')
                    ->where(['b.goodsid'=>$gid,'a.isshow'=>1])
                    ->field('a.isallowimg,b.catid,b.id,b.itemname,b.itemimg')
                    ->order('a.isallowimg desc,a.sort asc,a.gcatid asc')
                    ->select();
            $spec0 = [];
            $spec1 = [];                    
            foreach ($specs as $key =>$v){
                if($v['isallowimg']==1){
                    $spec0[] = $v;
                }else{
                    $spec1[] = $v;
                }
            }
            $rs['spec0'] = $spec0;
            $rs['spec1'] = $spec1;
            //获取销售规格
            $rs['saleSpec'] = Db::name('goods_specs')
                              ->where('goodsid',$gid)
                              ->field('id,isdefault,specids,marketprice,specprice,specstock,salenum')
                              ->select();
            //获取属性值
            $rs['attrs'] = Db::name('goods_attritem')
                           ->alias('a')
                           ->join('goodsattributes b','a.attrid=b.id','inner')
                           ->where('goodsid',$gid)
                           ->field('a.attrid,b.attrtype,a.attrval')
                           ->select();
        }
        return $rs;
    }



}
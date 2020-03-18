<?php
namespace plugins\goods\controller; 
/**
 * @Author: user
 * @Date:   2019-03-07 16:21:19
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-20 09:56:01
 */
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;
use plugins\goods\model\GoodsModel;
/**
 * api控制器
 */
class GoodsapiController extends PluginAdminBaseController
{
    public $uploadpath = '';
    protected function _initialize()
    {
        $this->uploadpath = ZY_APP_PATH.'plugins/goods/view/public/image/';
    } 

    public function goodsinfo($id,$isModule = false){
        if (!input('id')) { 
            if (empty($id)){
                //return zy_json_echo(false,'非法访问！',null,-2);
                return zy_array(false,'非法访问！','',-2 ,$isModule);
            };
            $sid = $id;  
        }else{
            $sid = input('id');
        }
        
        $info = db('goods')->field('id,goodsname,goodsimg,marketprice,shopprice,issale,goodsinfo,goodsalbum,salenum,isspec')->find(['id'=>$sid]);
        if ( count($info) == 0 || $info['issale'] == 0 ) {
            //return zy_json_echo(false,'该商品不存在或已下架',null,-2);
            return zy_array(false,'该商品不存在或已下架','',-2 ,$isModule);
        }
        $info['goodsimg'] = $this->uploadpath.'goodsimg/'.$info['goodsimg'];
        $info['goodsinfo'] = explode(',', $info['goodsinfo']);
        foreach ($info['goodsinfo'] as $k => $v) {
            $info['goodsinfo'][$k] = $this->uploadpath.'goodsinfo/'.$v;
        }
        $info['goodsalbum'] = explode(',', $info['goodsalbum']);
        foreach ($info['goodsalbum'] as $k => $v) {
            $info['goodsalbum'][$k] = $this->uploadpath.'goodsthumb/'.$v;
        }
        unset($info['issale']);
        //return zy_json_echo(true,'成功！',$info);
        return zy_array(true,'成功！',$info,200,$isModule);  
    }




    public function getspec($id,$isModule = false){

        if (!input('id')) { 
            if (empty($id)){
                return zy_array(false,'非法访问！','',-2 ,$isModule);
            };
            $sid = $id;  
        }else{
            $sid = input('id');
        }
        
        $info = db('goods')->field('id,issale,isspec')->find(['id'=>$sid]);
        if ( count($info) == 0 || $info['issale'] == 0 ) {
            return zy_array(false,'该商品不存在或已下架','',-2 ,$isModule);
        }
        if ( $info['isspec'] == 0 ) {
            return zy_array(false,'该商品无规格数据，无需调用','',-2 ,$isModule);
        }

        


        return zy_array(true,'成功！',$info,200,$isModule);  
    }

    








}
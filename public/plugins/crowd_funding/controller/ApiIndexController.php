<?php
namespace plugins\global_config\controller;
/**
 * @Author: user
 * @Date:   2019-03-07 16:21:19
 * @Last Modified by:   user
 * @Last Modified time: 2019-03-20 09:56:01
 */
use cmf\controller\PluginRestBaseController;//引用插件基类
use plugins\freight\Model\PluginApiIndexModel;
use think\Db;

/**
 * api控制器
 */
class ApiIndexController extends PluginRestBaseController
{
    public function index($isModule=false)//index(命名规范)
    {
        $param=$this->request->post();
        $param = zy_decodeData($param,$isModule);

        return zy_array(true,'连入成功',$param,200,$isModule);
    }

    /**
     * 店铺添加运费
     */
    public function addFreightShop($isModule=false){
        $param = $this->request->param();
        $param = zy_decodeData($param,$isModule);

        $add= [];
        $add['title'] = $param['title'];
        $add['type'] = $param['type'];
        $add['uid'] = $param['uid'];
        $add['default_num'] = $param['default_num'];
        $add['default_price'] = $param['default_price'];
        $add['continue_price'] = $param['continue_price'];
        $add['continue_num'] = $param['continue_num'];
        $add['free_shipping'] = $param['free_shipping'];
        $add['create_time'] = time();
        $first_id = Db::name('freight')->insertGetId($add);

        foreach ($param['freight_item'] as $item){
            $area = '';
            if(!empty($item['area'])){
                foreach ($item['area'] as $area_item){
                    if(!empty($area)){
                        $area.=',';
                    }
                    $area.=$area_item[1];
                }
                $add_item = [];
                $add_item['freight_id'] = $first_id;
                $add_item['area'] = $area;
                $add_item['first_num'] = $item['first_num'];
                $add_item['first_price'] = $item['first_price'];
                $add_item['continue_price'] = $item['continue_price'];
                $add_item['continue_num'] = $item['continue_num'];
                $add_item['create_time'] =time();
                Db::name('freight_item')->insert($add_item);
            }
        }
        if(empty($first_id)){
            return zy_array(false,'添加失败','',300,$isModule);
        }
        return zy_array(true,'添加成功','',200,$isModule);
    }

    public function getSubord()
    {
        $da1 = Db::name('memberaddress_cn_region')->where(['cri_level'=>1,'status'=>1])->order('cri_sort','asc')->select()->Toarray();

        foreach($da1 as $key=>$value){
            $da1[$key]['value'] = $value['cri_name'];
            $da1[$key]['label'] = $value['cri_name'];
            $da1[$key]['disabled'] = false;
            $da2 = Db::name('memberaddress_cn_region')->where(['cri_level'=>2,'status'=>1,'cri_superior_code'=>$value['cri_code']])->order('cri_sort','asc')->select()->Toarray();
            foreach($da2 as $key2=>$value2){
//                $data['list']['children'] = $value2['CRI_NAME'];
                $da1[$key]['children'][$key2]['value'] = $value2['cri_name'];
                $da1[$key]['children'][$key2]['label'] = $value2['cri_name'];
                $da1[$key]['children'][$key2]['disabled'] = false;
            }
        }
        // return zy_json_echo(true,'查询成功',$data,200);
        return zy_array(true,'查询成功',$da1,200,false);
    }


    /**
     * 获取所有运费模板列表
     * @param bool $isModule
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllFreight($isModule=false,$uesrid=''){
        $param = $this->request->param();
        
        if ($isModule==true) {
            if(empty($uesrid)){
                return zy_array(false,'请传入uesrid','',-1,$isModule);
            }
            $param['uid'] = $uesrid;

        } else {
            if(empty($param['uid'])){
                return zy_array(false,'请传入uid','',300,$isModule);
            }
            $param = zy_decodeData($param,$isModule);
        }
        
        $uid = $param['uid'];//店铺id
        if ( isset($param['pageNum']) && !empty($param['pageNum']) ){
            $pageNum = $param['pageNum'];
        } else {
            $pageNum = 10; //默认为每页十条
        }
        $where = 'uid='.$uid;
        $datas = Db::name('freight')->where($where)->paginate($pageNum);

        $currentPage = $datas->currentPage();
        $lastPage = $datas->lastPage();
        $listRows = $datas->listRows();
        $total = $datas->total();


        $newData= '';
        foreach ($datas as  $key => $item){

            $item['title'] = $item['title'];
            $item['type'] = $item['type'];
            $item['default_num'] = $item['default_num'];
            $item['default_price'] = $item['default_price'];
            $item['continue_num'] = $item['continue_num'];
            $item['continue_price'] = $item['continue_price'];

            $list = Db::name('freight_item')->where(['freight_id'=>$item['id']])->select();
            $item['item'] = $list;

            $newData['data'][$key] = $item;
        }
        $newData['currentPage'] = $currentPage; //当前页
        $newData['lastPage'] = $lastPage; //总页数
        $newData['listRows'] = $listRows; //每页数量
        $newData['total'] = $total; //总条数

        return zy_array (true,'操作成功',$newData,200 ,$isModule);
    }

    /**
     * 删除运费
     */
    public function deleteFreight($isModule=false){
        $param = $this->request->param();
        if(empty($param['id'])){
            $this->error("传参错误");
        }
        $id = $param['id'];
        $re = Db::name('freight')->where('id',$id)->delete();
        Db::name('freight_item')->where('freight_id',$id)->delete();
        if(empty($re)){
            return zy_array (false,'删除失败','删除失败',300 ,$isModule);
        }
        return zy_array (true,'删除成功','删除成功',200 ,$isModule);
    }

    /**
     * 查询运费价格
     * @param bool $isModule
     * @return \返回json字符串
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getFreight($freight_id = '',$province='',$goods_num=0, $isModule=false){

        if ( $isModule==true ) {
            if(empty($freight_id) || empty($province) || $goods_num<=0 ){
                return zy_array (false,'参数异常','',-1 ,$isModule);
            }
            $province = $province;//省
            $freight_price = 0;//最后运费
            $id = $freight_id;//运费id
            $goods_num = $goods_num;//购买数量

        } else {
            $param = $this->request->param();
            $province = $param['province'];//省
            $freight_price = 0;//最后运费
            $id = $param['freight_id'];//运费id
            $goods_num = $param['goods_num'];//购买数量
        }
        
        //获取运费模板的详情
        $freight = Db::name('freight')->where('id',$id)->find();
        if($freight == null){
            return zy_array(false,'未找到记录','',300,false);
        }
        //查询运费模板详情是否有匹配的省
        $list = Db::name('freight_item')->where(['freight_id'=>$id])->select();
        $freight_price = $this->getPrice($freight,$goods_num,$freight_price);
        if(sizeof($list)>0){
            foreach ($list as $freight_item){
                if(strpos($freight_item['area'],$province) !== false){
                    $cut_price = $goods_num-$freight_item['first_num'];
                    if($cut_price<0){
                        $freight_price = $freight['default_price'];
                    }elseif($cut_price==0 || $cut_price<$freight_item['continue_num']){
                        $freight_price = $freight_item['first_price'];
                    }elseif ($cut_price>$freight_item['continue_num'] || $cut_price==$freight_item['continue_num']){
                        $freight_price1 = $freight_item['continue_price'] * floor($cut_price/$freight_item['continue_num']) + $freight_item['first_price'];
                        if($freight_price1>$freight_price){
                            $freight_price = $freight_price1;
                        }
                    }
                    if($freight['type'] == 1){//包邮
                        if($goods_num == $freight['free_shipping'] || $goods_num > $freight['free_shipping']){
                            $freight_price = 0;
                        }
                    }
                }
            }
        }
        $res = array(
            'freight_price'=>$freight_price
        );
        return zy_array(true,'查询成功',$res,200,isModule);
    }


    function getPrice($freight,$goods_num,$freight_price){
        $cut_price = $goods_num-$freight['default_num'];
        if($cut_price<$freight['continue_num']){
            $freight_price = $freight['default_price'];
        }elseif ($cut_price>$freight['continue_num'] || $cut_price==$freight['continue_num']){
            $freight_price1 = $freight['continue_price'] * floor($cut_price/$freight['continue_num']) + $freight['default_price'];
            if($freight_price1>$freight_price){
                $freight_price = $freight_price1;
            }
        }
        if($freight['type'] == 1){//包邮
            if($goods_num == $freight['free_shipping'] || $goods_num > $freight['free_shipping']){
                $freight_price = 0;
            }
        }
        return $freight_price;
    }
}
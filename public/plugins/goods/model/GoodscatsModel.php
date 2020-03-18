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

//Demo插件英文名，改成你的插件英文就行了,插件数据表最好加个plugin前缀再加表名,这个类就是对应“表前缀+plugin_demo”表
class GoodscatsModel extends Model
{
	//protected $table="cmf_goodscats";
	
    public function catetree($cateRes){
        return $this->sort($cateRes);
    }

    public function sort($cateRes,$pid=0,$level=0){

        static $arr=array();
        foreach ($cateRes as $k => $v) {
            if($v['pid']==$pid){
                $v['level']=$level;
                $arr[]=$v;
                $this->sort($cateRes,$v['catid'],$level+1);
            }
        }

        return $arr;
    }




    /**
     *获取规格名称
     */
    public function getGoodsCatNames($goodsCatPath, $keyCats){

        $catIds = explode("-",$goodsCatPath);
        $catNames = array();
        for($i=0,$k=count($catIds);$i<$k;$i++){
            if($catIds[$i]=='')continue;
            if(isset($keyCats[$catIds[$i]]))$catNames[] = $keyCats[$catIds[$i]];
        }
        $data = implode(" -- ",$catNames);
        //dump($catIds);dump($keyCats);dump($data);die();
        return $data;
    }

    /**
     *获取商品分类名值对
     */
    public function listKeyAll(){
        $rs = db('goodscats')->field("catid,catname")->order('catsort asc,catname asc')->select()->toArray();
        $data = array();
        foreach ($rs as $key => $cat) {
            $data[$cat["catid"]] = $cat["catname"];
        }
        return $data;
    }




    public function GetTeam($arrs, $mid/*, $time = 10*/) {

        $Teams=array();//最终结果
        $mids=array($mid);//第一次执行时候的用户id
        do {
            $othermids=array();
            $state=false;
            foreach ($mids as $valueone) {
                foreach ($arrs as $key => $valuetwo) {
                    if($valuetwo['pid']==$valueone){
                        $Teams[]=$valuetwo['catid'];//找到我的下级立即添加到最终结果中
                        $othermids[]=$valuetwo['catid'];//将我的下级id保存起来用来下轮循环他的下级
                        //array_splice($arrs,$key,1);//从所有会员中删除他
                        unset($arrs[$key]);
                        $state=true;   
                    }
                }          
            }
            $mids=$othermids;//foreach中找到的我的下级集合,用来下次循环
            //$time --; 
            // if ($time == 0) {
            //     $state=false;
            // }
        } while ($state==true);
     
        return $Teams;
    }


}
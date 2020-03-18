<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------

namespace plugins\qrcodes\controller;


//Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;
use think\Db;
use plugins\qrcodes\model\PluginCodeQrcodeModel;
class QrcodeController extends PluginAdminBaseController
{
    protected $config = array(
        'coupon_status' => array(
            '0'=>'未使用',
            '1'=>'已使用',
            '2'=>'已过期',
            '3'=>'已收回'
        )
    );

    /**二维码页
     *
     */
    public function index()
    {
        $qrcodeM=new PluginCodeQrcodeModel;
        $data=$this->request->param();
        $where=[];
        if(!empty($data['code_name'])){
            $where['code_name'] = $data['code_name'];
        }
        if(isset($data['create_status'])){
            if(is_numeric($data['create_status'])){
                $where['create_status'] = $data['create_status'];
            }
        }
        if(isset($data['qrcode_type'])){
            if(is_numeric($data['qrcode_type'])){
                $where['qrcode_type'] = $data['qrcode_type'];
            }
        }

        if(!empty($data['start_time'])&&!empty($data['end_time'])){
            $where['create_time'] = ['between time',[$data['start_time'],$data['end_time']]];
        }
        $list=$qrcodeM->where($where)->order('id desc')->paginate(10, false, ['query' => request()->param()]);
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch('/qrcode/index');
    }

    /**
     * 添加二维码
     */
    public function addQrcode()
    {
        $data=$this->request->param();
        Vendor('phpqrcode.phpqrcode');
        $QRcode=new \QRcode();
        $qr_url = 'plugins/qrcode/upload/qrcode/'.$this->getfour_str(8).'.png';
        $dir = iconv("UTF-8", "GBK", "plugins/qrcode/upload/qrcode");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        if($data['qrcode_type']==1){
            $text="{\"status\":\"success\",\"data\":".$data['text'].",\"code\":200,}";
            $QRcode::png($text,$qr_url , 3, 5, false);
        }else{
            $QRcode::png($data['text'],$qr_url , 3, 5, false);
        }

        if(!file_exists($qr_url)){
            $this->error('生成二维码失败！');
            die;
        }
        $qrcodeM=new PluginCodeQrcodeModel;
        $is_name=$qrcodeM->where('code_name',$data['name'])->find();
        if(!empty($is_name)){
            $this->error('名称已存在！');
        }
        $is_in=$qrcodeM->insert([
            'img_url'=>$qr_url,
            'text'=>$data['text'],
            'create_status'=>0,
            'create_time'=>time(),
            'qrcode_type'=>$data['qrcode_type'],
            'code_name'=>$data['name']
        ]);
        if(!$is_in){
            $this->error('添加失败，请重试！');
        }
        $this->success("添加成功", '');
    }

    /**
     * 删除一条二维码
     */
    public function delete()
    {
        $data=$this->request->param();
        $qrcodeM=new PluginCodeQrcodeModel;
        try{
            $old_img=$qrcodeM->where('id',$data['id'])->value('img_url');
            @unlink($old_img);
            $qrcodeM->where('id',$data['id'])->delete();
        } catch (\Exception $e) {
            $this->error('删除失败，请重试！');
        }
        $this->success("删除成功", '');
    }
    /**
     * 二维码批量删除
     *
     */
    public function deleteArr()
    {
        $data=$this->request->param();
        $qrcodeM=new PluginCodeQrcodeModel;
        if(empty($data['id'])){
            $this->error('id不存在！');
        }
        foreach ($data['id'] as $k=>$v){
            $old_img=$qrcodeM->where('id',$v)->value('img_url');
            @unlink($old_img);
            $qrcodeM->where('id',$v)->delete();
        }
        $this->success("删除成功", '');
    }

    /**
     * 取随机数方式2
     *
     */
    private function getfour_str($len)
    {
        $chars_array = array(
            "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z",
        );
        $charsLen = count($chars_array) - 1;
        $outputstr = "";
        for ($i=0; $i<$len; $i++)
        {
            $outputstr .= $chars_array[mt_rand(0, $charsLen)];
        }
        return $outputstr;
    }
}
<?php
namespace plugins\qrcodes\controller;
// use cmf\controller\ApiBaseController;//引用插件基类
use cmf\controller\PluginRestBaseController;//引用插件基类
use Exception;
use think\Db;
use think\Config;
use think\Request;
use vendor\phpqrcode\QRcode;
use plugins\qrcodes\model\PluginCodeQrcodeModel;
use plugins\qrcodes\model\PluginCodeBarccodeModel;
/**
 * api控制器
 */

// class ApiIndexController extends ApiBaseController
class ApiIndexController extends PluginRestBaseController
{
    protected $config = array(
        'coupon_status' => array(
            '0'=>'正常',
            '1'=>'已使用',
            '2'=>'已过期',
            '3'=>'已收回'
        )
    );
    /**
     * 执行构造
     */
    function __construct()
    {
        header('Content-Type:application/json; charset=utf-8');
        Config('APP_DEBUG',false);
        parent::__construct();
    }

    /**
     * 生成二维码
     */
    public function getQrcode($isModule=false)
    {
        $param=$this->request->param();
        if(empty($param['name'])){
            return zy_json_echo(false,'请输入name参数','',-1);
        }
        if(empty($param['text'])){
            return zy_json_echo(false,'请输入text参数','',-1);
        }
        if(empty($param['type'])){
            $param['type']=0;
        }
        Vendor('phpqrcode.phpqrcode');
        $QRcode=new \QRcode();//实例化对象
        $qr_url = 'plugins/qrcode/upload/qrcode/'.$this->getfour_str(8).'.png';
        $dir = iconv("UTF-8", "GBK", "plugins/qrcode/upload/qrcode");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        if($param['type']==1){
            $text="{\"status\":\"success\",\"data\":".$param['text'].",\"code\":200,}";
            $QRcode::png($text,$qr_url , 3, 5, false);
        }else{
            $QRcode::png($param['text'],$qr_url , 3, 5, false);
        }
        $test_url= $qr_url;//临时测试用
        if(!file_exists($qr_url)){
            return zy_json_echo(false,'生成失败','',0);
            die;
        }
        $qrcodeM=new PluginCodeQrcodeModel;
        $is_name=$qrcodeM->where('code_name',$param['name'])->find();
        if(!empty($is_name)){
            $this->error('名称已存在！');
        }
        $is_in=$qrcodeM->insert([
            'img_url'=>$qr_url,
            'text'=>$param['text'],
            'create_status'=>1,
            'qrcode_type'=>$param['type'],
            'create_time'=>time(),
            'code_name'=>$param['name']
        ]);
        if(!$is_in){
            return zy_json_echo(false,'保存失败','',0);
        }
        return zy_json(true,'添加成功',$test_url,200);
    }

    /**
     * 生成条形码
     */
    public function getBarccode($isModule=false)
    {
        $param=$this->request->param();
        if(empty($param['name'])){
            return zy_json_echo(false,'请输入name参数','',-1);
        }
        if(empty($param['text'])){
            return zy_json_echo(false,'请输入text参数','',-1);
        }
        if(!preg_match('/^[0-9a-zA-Z]*$/',$param['text'])){
            return zy_json_echo(false,'请输入字母或者数字','',-1);
        }
        $dir = dirname(dirname(__FILE__));
        $tiao_dir = str_replace('/', '\\', $dir.'/extend/barcodegen/');
        require($tiao_dir.'barccode.php');
        $bar=new \Barccode();
        $br_url = 'plugins/barccode/upload/barccode/'.$this->getfour_str(8).'.png';
        $dir = iconv("UTF-8", "GBK", "plugins/barccode/upload/barccode");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $bar->createBarccode($param['text'],$br_url);
        if(!file_exists($br_url)){
            return zy_json_echo(false,'生成失败','',-1);
            die;
        }
        $test_url= $br_url;
        $brcodeM=new PluginCodeBarccodeModel;
        $is_name=$brcodeM->where('code_name',$param['name'])->find();
        if(!empty($is_name)){
            return zy_json(false,'名称已存在！',-1);
        }
        $is_in=$brcodeM->insert([
            'img_url'=>$br_url,
            'text'=>$param['text'],
            'create_status'=>1,
            'create_time'=>time(),
            'code_name'=>$param['name']
        ]);
        if(!$is_in){
            return zy_json_echo(false,'保存失败','',-1);
        }
        return zy_json(true,'添加成功',$test_url,200);

    }
    public function test()
    {
        echo '扫描成功！';
    }

    /**返回json
     *
     */
    private function render_json($code = 0,$msg = '',$data = [])
    {
        $resdata['code'] = $code;
        $resdata['msg'] = $msg;
        $resdata['data'] = $data;
        return json_encode($resdata);
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
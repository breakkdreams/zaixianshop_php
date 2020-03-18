<?php
namespace plugins\express_kdniao\controller; 

use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;
use plugins\express_kdniao\controller\ApiIndexController;
//AdminIndexController类和类的index()方法是必须存在的 index() 指向admin_index.html模板也就是模块后台首页
// 并且继承PluginAdminBaseController
//
class AdminIndexController extends PluginAdminBaseController
{   

    //分页数量
    private $pageNum = 20;

    protected function _initialize()
    {
        parent::_initialize();
        $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        }
    }


    /**
     * @adminMenu(
     *     'name'   => '快递配置',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '快递配置',
     *     'param'  => ''
     * )
     */
    public function index()
    {      
     
        $param = $this->request->param();

        //读取配置文件
        $EXinfo = getModuleConfig('express_kdniao','config','zysystem.json');
        $EXinfo = json_decode($EXinfo,true);

        if (isset($param['EBusinessID'])) {

            $config = [
                'EBusinessID'=>$param['EBusinessID'],//1305814
                'AppKey'=>$param['AppKey'],//77593c6f-1f02-4523-ae43-f56ee86c452c
                'ReqURL' => $param['ReqURL']//http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx
            ];

            //保存配置文件
            saveModuleConfigData('express_kdniao','config','zysystem.json',$config);

            $this->success('修改成功');
        }
        $this->assign('EXinfo',$EXinfo);

        return $this->fetch();
    }



    /**
     * 查询快递单号
     */
    public function kdSearch(){
        $param = $this->request->param();

        //读取配置文件
        $EXinfo = getModuleConfig('express_kdniao','config','zysystem.json');
        $EXinfo = json_decode($EXinfo,true);

        if (isset($param['ShipperCode']) && !empty($param['ShipperCode'])) {

            $orderInfo['ShipperCode']=$param['ShipperCode'];
            $orderInfo['LogisticCode']=$param['LogisticCode'];
            $orderInfo['OrderCode']=empty($param['OrderCode'])?'':intval($param['OrderCode']);

            //实例化物流接口
            $KdApi  = new ApiIndexController();
            //获取物流信息
            $data = $KdApi->getOrderTracesByJson($orderInfo['ShipperCode'],$orderInfo["LogisticCode"],$orderInfo['OrderCode'],true);

            $msg = $data['data'];

            $msg = json_decode($msg,true);

            $code = Db::name('zyexpress')->where('code',$msg['ShipperCode'])->find();

            $msg['company']=$code['company'];

            //按照时间排序
            if ($msg['State'] != 0) {

                $ctime_str = array();
                foreach($msg['Traces'] as $key=>$v){
                    $msg['Traces'][$key]['AcceptTime'] = strtotime($v['AcceptTime']);
                    $ctime_str[] = $msg['Traces'][$key]['AcceptTime'];
                }

                array_multisort($ctime_str,SORT_DESC,$msg['Traces']);

                foreach ($msg['Traces'] as $key => $value) {
                    $value['AcceptTime'] = date('Y-m-d H:i:s',$value['AcceptTime']);
                    $msg['Traces'][$key] = $value;
                }
            }




            
            /* 
            物 流 状 态 ：
            0-无 轨 迹
            1-已揽收
            2-在途中
            3-签收 */
            if($msg['State']=='0'){
                $msg['State']='无轨迹';
            }else if($msg['State']=='1'){
                $msg['State']='已揽收';
            }else if($msg['State']=='2'){
                $msg['State']='在途中';
            }else if($msg['State']=='3'){
                $msg['State']='签收';
            }
        }
        $info = Db::name('zyexpress')->select();

        $this->assign('info',$info);

        $this->assign('msg',isset($msg)?$msg:false);

        return $this->fetch();
    }






    /**
     * 快递公司管理 设置快递公司
     */
    public function kdCode(){

        $kuaidi = Db::name('zyexpress')->order('id','asc')->paginate($this->pageNum);

        $this->assign('kuaidi',$kuaidi);

        $this->assign('page',$kuaidi->render());

        return $this->fetch();
    }


    /*
     * 添加快递
     * */
     public function add()
    {   
        $param = $this->request->param();
        if (isset($param['company'])) {

            $data['company'] = $param['company'];

            $data['code'] = $param['code'];

            $zy = Db::name('zyexpress')->insert($data);

            if ($zy) {
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }else{
            return $this->fetch();
        }
    }


    /**
      * 删除
      */ 
    public function del(){
        $param = $this->request->param();

        if (empty($param['id'])) {

            $this->error('请选择要删除的信息');
        }

        //删除单个
        if (!is_array($param['id'])) {

            Db::name('zyexpress')->where('id',$param['id'])->delete();

            $this->success('删除成功');
        }

        //批量删除
        if (!empty($param['id'])) {

            foreach ($param['id'] as $id) {
                Db::name('zyexpress')->where('id',$id)->delete();
            }

            $this->success('删除成功');
        }

    }


    /*
     * 编辑快递
     * */
    public function edit()
    {       
        $param = $this->request->param();

        if (isset($param['company'])) {
            $data['company'] = $param['company'];
            $data['code'] = $param['code'];
                
            if (Db::name('zyexpress')->where('id',$param['id'])->update($data)) {
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }else{

            $info = Db::name('zyexpress')->where('id',$param['id'])->find();

            $this->assign('info',$info);

            return $this->fetch();
        }
    }




}

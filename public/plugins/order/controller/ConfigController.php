<?php
namespace plugins\order\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

class ConfigController extends PluginAdminBaseController
{
    protected function _initialize()
    {
    	parent::_initialize();
    	$adminId = cmf_get_current_admin_id();
    	//获取后台管理员id，可判断是否登录
    	if (!empty($adminId)) {
    		$this->assign("admin_id", $adminId);
    	}
    }



    /*
     * 配置页
     */
    public function index()
    {
    	$data = $this->request->param();

        //订单配置
        $order_config = getModuleConfig('order','config','order_config.json');

        $order_config = json_decode($order_config,true);


    	//订单时间配置
        $time_config = getModuleConfig('order','config','time_config.json');

        $time_config = json_decode($time_config,true);

        //延时配置
        $order_delayed = Db::name('order_delayed')->select();

        $this->assign('order_config',$order_config);
        
        $this->assign('time_config',$time_config);

        $this->assign('order_delayed',$order_delayed);

        return $this->fetch();
    }





    /*
     * 保存订单配置
     */
    public function saveConfig()
    {
        $data = $this->request->param();

        $da = [
            'type' => $data['type'], //配置 1.单用户 2.多用户
        ];

        saveModuleConfigData('order','config','order_config.json',$da);

        $this->success('操作成功');

    }



















    /*
     * 保存时间配置
     */
    public function savePjtime()
    {
        $data = $this->request->param();

        $da = [
            'cuifu' => $data['cuifu'], //支付时间
            'pingjia' => $data['pingjia'], //收货后评价时间
            'shouhuo' => $data['shouhuo'], //自动确认收货时间
            'fahuo' =>$data['fahuo'], //订单发货时间
        ];

        saveModuleConfigData('order','config','time_config.json',$da);

        $this->success('操作成功');
    }



    /*
     * 添加延时项
     */
    public function saveDelayed()
    {
        $data = $this->request->param();

        $da = [
            'ys_name' => $data['ys_name'],
            'ys_status' => isset($data['ys_status'])?1:2,
        ];

        Db::name('order_delayed')->insert($da);

        $this->success('添加成功');
    }



    /*
     * 修改延时配置状态
     */
    public function editysstatus()
    {
        $data = $this->request->param();

        switch ($data['ys_status']) {
            case 1:
                $da['ys_status'] = 2;
                break;
            case 2:
                $da['ys_status'] = 1;
                break;
        }
        
        Db::name('order_delayed')->where('id',$data['id'])->update($da);

        $this->success('操作成功');
    }



    /*
     * 修改延时项名称
     */
    public function editysName()
    {
        $data = $this->request->param();

        $da = [
            'ys_name' => $data['ys_name'],
        ];

        Db::name('order_delayed')->where('id',$data['id'])->update($da);

        $this->success('修改成功');
    }




   /*
    * 延时配置项删除
    */ 
    public function delys()
    {
        $data = $this->request->param();

        Db::name('order_delayed')->where('id',$data['id'])->delete();

        $this->success('操作成功');
    }



}
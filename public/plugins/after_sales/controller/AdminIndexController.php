<?php
namespace plugins\after_sales\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;




/**
* 需求配置首页
*@actionInfo(
*  'name' => '售后模块',
*  'symbol' => 'after_sales',
*  'list' => [
*      'afterSalesRefuse' => [
*          'demandName' => '订单模块' ,
*          'demandSymbol' => 'order',
*          'explain' => '售后拒绝'
*      ],
*      'readaftersalesinfo' => [
*          'demandName' => '订单模块' ,
*          'demandSymbol' => 'order',
*          'explain' => '获取售后商品信息'
*      ],
*      'readconfiginfo' => [
*          'demandName' => '站点配置' ,
*          'demandSymbol' => 'site_configuration',
*          'explain' => '获取站点配置信息'
*      ],
*      'lookLogistics_one' => [
*          'demandName' => '快递鸟' ,
*          'demandSymbol' => 'express_kdniao',
*          'explain' => '查询物流轨迹'
*      ],
*      'wechatrefund' => [
*          'demandName' => '微信支付模块',
*          'demandSymbol' => 'wechat_pay',
*          'explain' => '微信支付订单退款'
*      ],
*      'balancerefund' => [
*          'demandName' => '资金模块',
*          'demandSymbol' => 'fund',
*          'explain' => '余额支付订单退款'
*      ],
*      'zhifubaorefund' => [
*          'demandName' => '支付宝支付模块',
*          'demandSymbol' => 'ali_pay',
*          'explain' => '支付宝支付订单退款'
*      ],
*      'kdInfo' => [
*          'demandName' => '快递鸟',
*          'demandSymbol' => 'express_kdniao',
*          'explain' => '根据快递公司编号获取信息'
*      ],
*  ]
 *)
*/
class AdminIndexController extends PluginAdminBaseController
{
    //分页数量
    private $pageNum = 20;

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
     * 网站配置信息
     */
    private function readConfig(){

        $symbol = 'after_sales';
        
        $id = 'readconfiginfo';

        $arr = ['data'=>null,'isModule'=>true];

        //调用快递接口
        $web_config = getModuleApiData($symbol,$id,$arr);

        return $web_config['data'];
    }




    /**
     * @adminMenu(
     *     'name'   => '售后列表',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '售后列表',
     *     'param'  => ''
     * )
     */
    /**
     * [index 售后列表]
     * @return [type] [description]
     */
    public function index(){
        $data = $this->request->param();

        $where = 1;

        //订单编号搜索
        if (isset($data['refund_ordersn']) && !empty($data['refund_ordersn'])) {
            $where .= " and refund_ordersn = ".$data['refund_ordersn'];
        }
        //状态搜索
        if (isset($data['status']) && !empty($data['status'])) {
            if ($data['status']==4) {
                $where .= " and audit_status = 1 and status = ".$data['status'];
            } else {
                $where .= " and status = ".$data['status'];
            }
        }
        //售后时间
        if (isset($data['start_addtime']) && !empty($data['start_addtime'])) {
            $where .= " and time > ".strtotime($data['start_addtime']);
        }
        if (isset($data['end_addtime']) && !empty($data['end_addtime'])) {
            $where .= " and time < ".strtotime($data['end_addtime']);
        }

        $after = Db::name('after_sales')->where($where)->order('time','desc')->paginate($this->pageNum);

        //读取网站配置信息
        $config = $this->readConfig();

        $this->assign('site_domain',$config['basic']['site_domain']);

        $this->assign('data',$data);

        $this->assign('after',$after);
        //在分页前保存分页条件
        $after->appends($data);
        //分页
        $this->assign('page',$after->render());

        return $this->fetch();
    }





    /*
     * 售后订单删除
     */
    public function afterDel(){
        $data = $this->request->param();
    
        if(empty($data['id'])){
            $this->error('请选择要删除的信息');
        }

        foreach($data['id'] as $id){
            Db::name('after_sales')->where('id',$id)->delete();
        }

        $this->success('删除成功');
    }




    /*
     * 通过售后申请
     */
    public function tongguo(){
        $data = $this->request->param();

        Db::name('after_sales')->where('order_id',$data['orderid'])->update(['status'=>2,'audit_status'=>1]);

        return $this->success('操作成功');
    }





    /*
     * 拒绝售后申请
     */
    public function jujue(){

        $data = $this->request->param();

        Db::name('order')->where('id',$data['orderid'])->update(['shstatus'=>0,'is_shouhou'=>0]);

        Db::name('after_sales')->where('order_id',$data['orderid'])->update(['status'=>4,'audit_status'=>2,'refuse_cause'=>$data['yuanyin']]);

        return $this->success('操作成功');
    }






    /*
     * 物流信息
     */
    public function afterWlxx(){

        $data = $this->request->param();

        $after = Db::name('after_sales')->where('id',$data['id'])->find();

        $symbol = 'after_sales';
        
        $id = 'lookLogistics_one';

        $arr = ['ShipperCode'=>$after['shipper_code'],'LogisticCode'=>$after['logistics_order'],'OrderCode'=>$after['refund_ordersn'],'isModule'=>true];

        //调用快递接口
        $logisticResult = getModuleApiData($symbol,$id,$arr);

        $logisticResult = json_decode($logisticResult['data'] ,true);

        if ($logisticResult['Success'] == 'true') {
            $logisticResult = $logisticResult;
        } else {
            $logisticResult = false;
        }

        $this->assign('after',$after);

        $this->assign('logisticResult',$logisticResult);

        return $this->fetch();
    }




    /*
     * 确认收货
     */
    public function querenShouhuo(){

        $data = $this->request->param();

        if (isset($data['money'])) {

            //调用支付
            switch ($data['pay_type']) {
                case 1: //支付宝
                    $symbol = 'after_sales';

                    $id = 'zhifubaorefund';
                    
                    $arr = ['transaction_id'=>$data['transaction_id'],'out_trade_no'=>'','refund_amount'=>$data['refund_money'],'refund_reason'=>'订单退款','out_request_no'=>$data['refund_ordersn'],'isModule'=>true];

                    $refund = getModuleApiData($symbol,$id,$arr);

                    if ($refund['status'] == 'error') {
                        $this->error($refund['data']['sub_msg']);
                    }

                    break;
                case 2: //微信
                    $symbol = 'after_sales';

                    $id = 'wechatrefund';

                    $arr = ['transaction_id'=>$data['transaction_id'],'out_trade_no'=>'','total_fee'=>$data['money'],'refund_fee'=>$data['refund_money'],'isModule'=>true];

                    $refund = getModuleApiData($symbol,$id,$arr);

                    if ($refund['status']=='error') {
                        $this->error($refund['data']['err_code_des']);
                    }
                    
                    break;
                case 3: //银行卡
                        
                    break;
                case 4: //余额

                    $symbol = 'after_sales';

                    $id = 'balancerefund';
                    
                    $arr = ['order_number'=>$data['transaction_id'],'money'=>$data['refund_money'],'isModule'=>true];

                    $refund = getModuleApiData($symbol,$id,$arr);

                    if ($refund['status']=='error') {
                        $this->error($refund['message']);
                    }
                    break;
            }


            Db::name('after_sales')->where('order_id',$data['orderid'])->update(['status'=>4,'tksucc_time'=>time()]);

            Db::name('order')->where('id',$data['orderid'])->update(['is_shouhou'=>2]);

            return json(['status'=>'success','mssage'=>'退款成功']);

        }

        $afterinfo = Db::name('after_sales')->where('order_id',$data['orderid'])->find();

        $this->assign('afterinfo',$afterinfo);

        return $this->fetch();
    }






    /**
    * 售后管理_商品信息
    */
    public function afterShopinfo(){
        $data = $this->request->param();

        $after = Db::name('after_sales')->where('id',$data['id'])->find();

        // $ids = explode(',',$after['goods_id']);

        //根据订单id和售后商品id获取商品信息

        $symbol = 'after_sales';

        $id = 'readaftersalesinfo';
        
        $arr = ['order_id'=>$after['order_id'],'goods_id'=>$after['goods_id'],'isModule'=>true];

        $info = getModuleApiData($symbol,$id,$arr);

        // $shopinfo = Db::name('order_goods')->where('order_id',$after['order_id'])->where('goods_id','in',$ids)->select();

        if ($info['status']=='success' && isset($info['data'])){
            $shopinfo = $info['data'];
        } else {
            $shopinfo = array();
        }


        $this->assign('shopinfo',$shopinfo);

        return $this->fetch();
    }

}
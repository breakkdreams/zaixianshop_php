<?php
namespace plugins\order\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;
use app\admin\model\AccessModel;




/**
* 需求配置首页
*@actionInfo(
*  'name' => '订单模块',
*  'symbol' => 'order',
*  'list' => [
*      'readAddress_one' => [
*          'demandName' => '地址管理' ,
*          'demandSymbol' => 'member_address',
*          'explain' => '获取用户收货地址'
*      ],
*      'lookLogistics_one' => [
*          'demandName' => '快递' ,
*          'demandSymbol' => 'express_kdniao',
*          'explain' => '查询订单物流轨迹'
*      ],
*      'lookShopInfo_one' => [
*          'demandName' => '商品模块',
*          'demandSymbol' => 'goods',
*          'explain' => '获取商品详情'
*      ],
*      'readConfigInfo_one' => [
*          'demandName' => '配置模块',
*          'demandSymbol' => 'site_configuration',
*          'explain' => '获取网站配置信息'
*      ],
*      'lijigoumai_one' => [
*          'demandName' => '商品模块',
*          'demandSymbol' => 'goods',
*          'explain' => '立即购买确认订单'
*      ],
*      'goodspaytongji' => [
*          'demandName' => '商品模块',
*          'demandSymbol' => 'goods',
*          'explain' => '商品购买统计'
*      ],
*      'readmemberinfo' => [
*          'demandName' => '会员管理',
*          'demandSymbol' => 'member',
*          'explain' => '获取会员个人信息'
*      ],
*      'addPingjia' => [
*          'demandName' => '评价管理',
*          'demandSymbol' => 'evaluation',
*          'explain' => '添加评价'
*      ],
*      'addShouhou' => [
*          'demandName' => '售后管理',
*          'demandSymbol' => 'after_sales',
*          'explain' => '添加售后'
*      ],
*      'newApply' => [
*          'demandName' => '售后管理',
*          'demandSymbol' => 'after_sales',
*          'explain' => '重新申请售后'
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


     /**
     * @adminMenu(
     *     'name'   => '订单管理列表',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '订单管理列表',
     *     'param'  => ''
     * )
     */
    public function index(){
        $param = $this->request->param();

        $where = 1;
        //订单编号
        if(isset($param['ordersn']) && !empty($param['ordersn'])){
            $where .= " and ordersn ='".$param['ordersn']."'";
        }
        //支付方式
        if(isset($param['pay_type']) && !empty($param['pay_type'])){
            //1 .余额 2 .微信  3 .支付宝
            $where .= " and pay_type ='".$param['pay_type']."'";
        }
        //状态
        if(isset($param['status']) && !empty($param['status'])){
            $where .=" and `status` =".$param['status'];
        }
        //下单时间
        if(isset($param['start_addtime']) && !empty($param['start_addtime'])){
            $where .= " and addtime >= '".strtotime($param['start_addtime'])."'";
        }
        if(isset($param['end_addtime']) && !empty($param['end_addtime'])){
            $where .= " and addtime <= '".strtotime($param['end_addtime'])."'";
        }

        //订单配置
        $order_config = getModuleConfig('order','config','order_config.json');

        $order_config = json_decode($order_config,true);

        if ($order_config['type']==2) {
            //读取权限（多用户）
            $accessModels=new AccessModel();
            $ids=$accessModels->getAllUserTheCurrentRole(cmf_get_current_admin_id());
            $ids = !empty($ids)?implode(',',$ids):0;
            $where .= " and storeid in ({$ids})";
        }


        

        $order = Db::name('order')->where($where)
                ->order('addtime','desc')->paginate($this->pageNum);

        $this->assign('param',$param);

        $this->assign('order',$order);
        //在分页前保存分页条件
        $order->appends($param);
        //分页
        $this->assign('page',$order->render());

        return $this->fetch();
    }



    /**
    * 订单管理_删除
    */
    public function orderManageDel(){
        $param = $this->request->param();
        //都没有选择删除什么
        if(empty($param['id'])){
            $this->error("请选择要删除的信息");
        }

        //批量删除；
        Db::name('order')->where('id','in',$param['id'])->delete();
        $this->success("删除成功");
    }








    /*
     * 订单详情
     */
    public function orderDetail(){
        $data = $this->request->param();

        $order = Db::name('order')->where('id',$data['id'])->find();

        $order['paytime'] = $order['paytime']?date('Y-m-d H:i:s',$order['paytime']):'';
        $order['fhtime'] = $order['fhtime']?date('Y-m-d H:i:s',$order['fhtime']):'';
        $order['shouhuo_time'] = $order['shouhuo_time']?date('Y-m-d H:i:s',$order['shouhuo_time']):'';

        $this->assign('order',$order);

        return $this->fetch();

    }










    /*
     * 查询订单物流轨迹
     */
    public function orderManageWlxx(){    
        $param = $this->request->param();

        $order = Db::name('order')->where('id',$param['id'])->find();

        $symbol = 'order';
        
        $id = 'lookLogistics_one';

        $arr = ['ShipperCode'=>$order['shipper_code'],'LogisticCode'=>$order['logistics_order'],'OrderCode'=>$order['ordersn'],'isModule'=>true];

        //调用快递接口
        $logisticResult = getModuleApiData($symbol,$id,$arr);

        $logisticResult = json_decode($logisticResult['data'] ,true);

        if ($logisticResult['Success'] == 'true') {
            $logisticResult = $logisticResult;
        } else {
            $logisticResult = false;
        }

        $this->assign('order',$order);

        $this->assign('logisticResult',$logisticResult);

        return $this->fetch();
    }





    /**
    * 订单管理_订单发货_添加物流
    */
    public function orderManageDdfh(){  
        $param = $this->request->param();

        if(isset($param['logistics_order'])){

            $wuliu = Db::name('zyexpress')->where('code',$param['shippercode'])->find();

            $info = Db::name('order')->where('id',$param['id'])->update(array('shipper_name' =>$wuliu['company'],'shipper_code' =>$wuliu['code'],'logistics_order' =>$param['logistics_order'],'fhtime'=>time(),'status'=>'3','remind'=>''));
            
            if($info){
                $this->success("操作成功");
            }else{
                $this->error('操作失败');
            }

        }else{
            $info = Db::name('zyexpress')->where('id',$param['id'])->find();

            $infok = Db::name('zyexpress')->select();

            $this->assign('orderid',$param['id']);

            $this->assign('infok',$infok);

            return $this->fetch();
        }
    }




    /**
    * 订单管理_商品信息
    */
    public function orderManageShopinfo(){
        $param = $this->request->param();

        $shopinfo = Db::name('order_goods')->where('order_id',$param['id'])->select();

        $this->assign('shopinfo',$shopinfo);

        return $this->fetch();
    }



    /*
     * 修改收货地址
     */
    public function editcity()
    {
        $data = $this->request->param();

        $order = Db::name('order')->where('id',$data['id']);

        if (isset($data['lx_name'])) {
            unset($data['_plugin'],$data['_controller'],$data['_action'],$data['id']);

            if (empty($data['lx_name'])) {
                $this->error('姓名不能为空');
            }
            if (empty($data['lx_mobile'])) {
                $this->error('手机号不能为空');
            }
            if (empty($data['province'])) {
                $this->error('省不能为空');
            }
            if (empty($data['city'])) {
                $this->error('市不能为空');
            }
            if (empty($data['area'])) {
                $this->error('区、县不能为空');
            }
            if (empty($data['address'])) {
                $this->error('详细地址不能为空');
            }

            $upd = $order->update($data);

            $this->success('操作成功');
        }

        $this->assign('order',$order->find());

        return $this->fetch();
    }





    /*
     * 延长收货
     */
    public function prolongShouhuo()
    {
        $data = $this->request->param();

        if (isset($data['sh_delayed'])) {

            Db::name('order')->where('id',$data['id'])->update(['sh_delayed'=>$data['sh_delayed']]);

            $this->success('操作成功');
        }

        $order = Db::name('order')->where('id',$data['id'])->find();

        $delayed = Db::name('order_delayed')->where('ys_status',1)->select();

        $this->assign('order',$order);

        $this->assign('delayed',$delayed);

        return $this->fetch();
    }






    /*
     * 修改价格
     */
    public function editprice()
    {
        $data = $this->request->param();

        //修改
        if (isset($data['new_price'])) {

            $jiage = 0; //重新计算总价
            foreach ($data['good_id'] as $key => $value) {

                if ($data['new_price'][$key]!=0) {
                    $goods = Db::name('order_goods')->where('id',$value);

                    $upd = $goods->update(['goods_price'=>$data['new_price'][$key] ]); //更新

                    $goods_find = $goods->find();

                    $jiage += $data['new_price'][$key] * $goods_find['goods_num'];
                }
            }

            $da = [
                'totalprice' => $jiage,
                'freeship' => isset($data['freeship']) ? 1 : 2,
                'freight' => $data['freight'],
            ];

            Db::name('order')->where('id',$data['id'])->update($da);

            $this->success('操作成功');
        }

        $shopinfo = Db::name('order_goods')->where('order_id',$data['id'])->select();

        $order = Db::name('order')->where('id',$data['id'])->find();

        $this->assign('shopinfo',$shopinfo);

        $this->assign('order',$order);

        return $this->fetch();
    }












}
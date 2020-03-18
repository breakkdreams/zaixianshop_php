<?php
namespace plugins\fund\controller; //Demo插件英文名，改成你的插件英文就行了

use cmf\controller\PluginAdminBaseController;//引入此类
use plugins\fund\controller\bankList;
use think\Db;


/**
    * 需求会员信息配置
    *@actionInfo(
    *  'name' => '资金模块',
    *  'symbol' => 'fund',
    *  'list' => [
    *      'getUserinfo' => [
    *          'demandName' => '会员模块' ,
    *          'demandSymbol' => 'member',
    *          'explain' => '获取会员信息'
    *      ],
    *      'adopt'  =>  [
    *          'demandName' => '会员模块' ,
    *          'demandSymbol' => 'member',
    *          'explain' => '操作会员金额 '
    *      ],
    *      'geturl'  =>  [
    *          'demandName' => '站点配置' ,
    *          'demandSymbol' => 'SiteConfiguration',
    *          'explain' => '获取站点配置 '
    *      ],
    *      'getgoods'  =>  [
    *          'demandName' => '订单模块' ,
    *          'demandSymbol' => 'Order',
    *          'explain' => '获取商品详情 '
    *      ], 
    *      'uporder'  =>  [
    *          'demandName' => '订单模块' ,
    *          'demandSymbol' => 'Order',
    *          'explain' => '修改订单状态'
    *      ], 
    *      'upusermoney'  =>  [
    *          'demandName' => '会员模块' ,
    *          'demandSymbol' => 'member',
    *          'explain' => '修改用户余额'
    *      ], 
    *      'getpassword'  =>  [
    *          'demandName' => '会员模块' ,
    *          'demandSymbol' => 'member',
    *          'explain' => '获取用户密码'
    *      ], 
    *      'zhifubao'  =>  [
    *          'demandName' => '支付模块' ,
    *          'demandSymbol' => 'AliPay',
    *          'explain' => '支付宝自动支付'
    *      ], 
    *      'weixin'  =>  [
    *          'demandName' => '微信支付模块' ,
    *          'demandSymbol' => 'WechatPay',
    *          'explain' => '微信自动支付'
    *      ], 
    *      'bankcard'  =>  [
    *          'demandName' => '微信支付模块' ,
    *          'demandSymbol' => 'WechatPay',
    *          'explain' => '银行卡自动支付'
    *      ], 
    *      'getbankOk'  =>  [
    *          'demandName' => '微信支付模块' ,
    *          'demandSymbol' => 'WechatPay',
    *          'explain' => '银行卡类型判断'
    *      ], 
    *      'wheachth5'  =>  [
    *          'demandName' => '微信支付模块' ,
    *          'demandSymbol' => 'WechatPay',
    *          'explain' => '微信h5支付'
    *      ], 
    *      'alipayh5'  =>  [
    *          'demandName' => '支付模块' ,
    *          'demandSymbol' => 'AliPay',
    *          'explain' => '支付宝h5支付'
    *      ], 
    *      'ordermoney'  =>  [
    *          'demandName' => '订单模块' ,
    *          'demandSymbol' => 'Order',
    *          'explain' => '更具订单编号获取总金额'
    *      ]
    *  ]
    *)
    */



class AdminIndexController extends PluginAdminBaseController{
  
   /**
    * 初始化
    */
   private  $pag = "20";

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
   * 更爱地址(临时)
   */
   public function uotyoiandizhi(){
       $data = $this->request->param();

       $da = Db::name("fund_bank")->select();

       foreach($da as $key=>$value){
           $bank = Db::name("fund_bank")->where('id',$value['id'])->update(['thumb'=>'/plugins/fund/view'.$value['thumb']]);
       }
   
   }






    /**
     * @adminMenu(
     *     'name'   => '提现列表',
     *     'parent' => 'admin/Plugin/fund',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '提现列表',
     *     'param'  => ''
     * )
     */

     
    
    public function index(){
        $data = $this->request->param();       
        $where=1;
        if(!empty($data['types'])  &&  !empty($data['category'])){
            $types = $data['types'];
            $category = "'%".$data['category']."%'";
            if($types=='1'){
              $where .= " and `trade_sn` like".$category;
            }
            if($types=='2'){
              $where .= " and `phone` like".$category;  
            }
        }
        if(isset($data['type']) && !empty($data['type'])){
            $where .=" and `type` =".$data['type'];
        }
        if(isset($data['addmoney'])  &&  !empty($data['addmoney'])){
            $where .=" and `amount` >=".$data['addmoney'];
        }
        if(isset($data['endmoney'])  &&  !empty($data['endmoney'])){
            $where .=" and `amount` <=".$data['endmoney'];
        } 
        if(isset($data['status'])  &&  !empty($data['status'])){
            $where .=" and `status` =".$data['status'];
        }      
        $da = Db::name("fund_tx_record")->where($where)->order('status','asc')->paginate($this->pag);
        foreach( $da as $key=>$value ){
            $type  = Db::name("fund_type")->where('type',1)->where('zid',$value['type'])->select();
            $count = 0;
            foreach( $type as $t){
                 $count+=$t['number'];
            }
            $value['number'] = $count;
            $da[$key] =$value;
        }
        $contype = ['1'=>'支付宝','2'=>'微信','3'=>'银行卡'];
        $this->assign('data',$da);
        // 在 render 前，使用appends方法保持分页条件
        $da->appends($data);
        $this->assign('page', $da->render());//单独提取分页出来
        $this->assign('search',$data);
        $this->assign('contype',$contype);
        return $this->fetch('/presentation');
    }



    

    /**
     * 提现批量删除
     */
    public function per_delall(){
        $data = $this->request->param();
        
        if(!isset($data['id']) || empty($data['id'])){
            return  $this->error("请选择删除目标");
        }
        $ids  = $data['id'];
        foreach($ids as $id){
          $da = Db::name("fund_tx_record")->where("id",$id)->find();
          if($da['status']!='1'){
             $da = Db::name("fund_tx_record")->where("id",$id)->delete();
          }else{
            $up = $this->pre_upht($id);
            if( $up=='1' ){
             $da = Db::name("fund_tx_record")->where("id",$id)->delete();
            }
          }
        }
        return $this->success("批量删除成功");
     
    }


        /**
     * 拒绝提现需求
     */
    private function pre_upht($id){
        $da = Db::name("fund_tx_record")->where('id',$id)->find();
        //操作用户余额（解冻）
        $res  = $this->onterface('upusermoney',['module'=>'fund','uid'=>$da['uid'],'amount'=>$da['amount'],'type'=>3,true]);
        if( $res['code']!='200' ){
            return $this->error("用户余额操作失败");
        }
        $up = Db::name("fund_tx_record")->where('id',$da['id'])->update(['status'=>'3','uptime'=>time()]);
        if($up){
            return 1;
        }else{
            //操作用户余额（冻结）
            $res  = $this->onterface('upusermoney',['module'=>'fund','uid'=>$da['uid'],'amount'=>$da['amount'],'type'=>1,true]);
            if( $res['code']!='200' ){
                return $this->error("用户余额冻结失败");
            }
            return $this->error("拒绝失败");
        }

    }



    /**
     * 拒绝提现需求
     */
    public function pre_up(){
        $data = $this->request->param();
        $da = Db::name("fund_tx_record")->where('id',$data['id'])->find();
        //操作用户余额（解冻）
        $res  = $this->onterface('upusermoney',['module'=>'fund','uid'=>$da['uid'],'amount'=>$da['amount'],'type'=>3,true]);
        if( $res['code']!='200' ){
            return $this->error("用户余额操作失败");
        }
        $da = Db::name("fund_tx_record")->where('id',$data['id'])->update(['reason'=>$data['jujue'],'status'=>'3','uptime'=>time()]);
        if($da){
            return $this->success("拒绝成功");
        }else{
            //操作用户余额（冻结）
            $res  = $this->onterface('upusermoney',['module'=>'fund','uid'=>$da['uid'],'amount'=>$da['amount'],'type'=>1,true]);
            if( $res['code']!='200' ){
                return $this->error("用户余额冻结失败");
            }
            return $this->error("拒绝失败");
        }


    }

 
    




    /**
     * 通过提现需求
     */
    public function adopt(){
        $data = $this->request->param();  
        $da = Db::name("fund_tx_record")->where('id',$data['id'])->find();
        //获取用户信息
        $res  = $this->onterface('getUserinfo',['uid'=>$da['uid'],'field'=>'',true]);       
        $user = $res['data'];
        //操作用户余额解冻
        $res  = $this->onterface('upusermoney',['module'=>'fund','uid'=>$da['uid'],'amount'=>$da['amount'],'type'=>2,true]);  
        if( $res['code']!='200' ){
            return $this->error($res['message']);
        }
        if( isset($data['status']) && !empty($data['status']) ){
        switch($data['status']){
            case 1://操作打款到支付宝
                    $res  = $this->onterface('zhifubao',['module'=>'fund','totalFee'=>$da['amount'],'outTradeNo'=>$da['trade_sn'],'account'=>$da['account'],'realName'=>$da['accountname'],'remark'=>'无',true]);   
                    break;
            case 2: //操作打款到微信
                    $res  = $this->onterface('weixin',['module'=>'fund','amount'=>$da['amount'],'re_openid'=>$user['wechatpe_openid'],'desc'=>'无','check_name'=>$da['accountname'],true]);               
                    break;
            case 3: $banklist = new bankList();
                    $bank = $banklist->bankList;
                    $bin = $this->bankInfo($da['account'],$bank);
                    $bankname = Db::name("fund_bank")->where('card_bin',$bin)->find()['bank']; 
                    //操作打款到银行卡
                    $res  = $this->onterface('bankcard',['module'=>'fund','out_trade_no'=>time().rand(10000,99999),'money'=>$da['amount'],'enc_bank_no'=>$da['account'],'enc_true_name'=>$da['accountname'],'bank_name'=>$bankname,'desc'=>'无',true]);   break;
        }
        if( $res['code']!='200' ){
            //修改用户余额
            $adopt  = $this->onterface('adopt',['module'=>'fund','uid'=>$da['uid'],'amount'=>$da['amount'],'type'=>'1',true]);  
            if($adopt['code']!='200'){
                return $this->error($adopt['message']);  
            }
            //操作用户余额解冻
            $upusermoney  = $this->onterface('upusermoney',['module'=>'fund','uid'=>$da['uid'],'amount'=>$da['amount'],'type'=>1,true]);  
            if( $upusermoney['code']!='200' ){
                return $this->error($upusermoney['message']);
            }
            return $this->error($res['message']);
        }
            $da = Db::name("fund_tx_record")->where('id',$data['id'])->update(['status'=>'2','uptime'=>time()]);
        }else{
            $da = Db::name("fund_tx_record")->where('id',$data['id'])->update(['status'=>'2','uptime'=>time(),'make'=>$data['make']]);
        }

        if($da){
            return $this->success('提现通过');
        }else{
            return $this->error("提现失败");
        }
     
    }




    /**
     * 获取原因数据
     */
    public function fundcon(){
        $data = $this->request->param();
        $da = Db::name("fund_tx_record")->where('id',$data['id'])->find()['reason'];
        return $da;
    }





    /**
     * 获取提现备注
     */
    public function makekan(){
        $data = $this->request->param();
        $da = Db::name("fund_tx_record")->where('id',$data['id'])->find()['make'];
        return $da;
    }


 
    




    /**********************************************   充值列表            ******************************************************************* */

    /**
     * @adminMenu(
     *     'name'   => '充值列表',
     *     'parent' => 'admin/Plugin/fund',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '充值列表',
     *     'param'  => ''
     * )
     */


    public function recharge(){
        $data = $this->request->param();        
        $where=1;
        if(!empty($data['type'])  &&  !empty($data['category'])){
            $type = $data['type'];
            $category = "'%".$data['category']."%'";
            if($type==1){
                $where .=" and `trade_sn` like".$category;
            }
            if($type==2){
                $where .=" and `phone` like".$category;
            }
        }
        if(isset($data['add_time'])  &&  !empty($data['add_time'])){
            $where .=" and `addtime` >=".strtotime($data['add_time']);
        }
        if(isset($data['end_time'])  &&  !empty($data['end_time'])){
            $where .=" and `addtime` <=".strtotime($data['end_time']);
        }
        $da = Db::name("fund_pay_record")->where($where)->paginate($this->pag);
        $contype = ['1'=>'支付宝','2'=>'微信','3'=>'银行卡'];
        $this->assign('data',$da);
        $da->appends($data);
        $this->assign('page',$da->render());
        $this->assign('search',$data);
        $this->assign('contype',$contype);
        return $this->fetch();
    }


    

    /**
     * 删除充值信息
     */
    public function re_del(){
        $data = $this->request->param();
        $da  = Db::name("fund_pay_record")->where('id',$data['id'])->delete();      
        if($da){
            return $this->success("删除成功");
        }
    } 




    

    /**
     * 批量删除充值信息
     */
    public function re_delall(){
        $data = $this->request->param();       
        if(!isset($data['id']) || empty($data['id'])){
            return  $this->error("请选择删除目标");
        }
        $ids  = $data['id'];
        foreach($ids as $id){
          $da = Db::name("fund_pay_record")->where("id",$id)->delete();
        }
        return $this->success("批量删除成功");
     
    }
    







     /********************************cc  支付列表   ************************************* */



    /**
     * @adminMenu(
     *     'name'   => '支付列表',
     *     'parent' => 'admin/Plugin/fund',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '支付列表',
     *     'param'  => ''
     * )
     */

     public function payment(){
         $data = $this->request->param();
         $where=1;
         if(!empty($data['type'])  &&  !empty($data['category'])){
             $type = $data['type'];
             $category = "'%".$data['category']."%'";
             if($type==1){
                 $where .=" and `trade_sn` like".$category;
             }
             if($type==2){
                 $where .=" and `phone` like".$category;
             }
         }
         if(isset($data['add_time'])  &&  !empty($data['add_time'])){
             $where .=" and `addtime` >=".strtotime($data['add_time']);
         }
         if(isset($data['end_time'])  &&  !empty($data['end_time'])){
             $where .=" and `addtime` <=".strtotime($data['end_time']);
         }       
         $da  = Db::name("fund_zhifu_record")->where($where)->where('status',2)->order('id','desc')->paginate($this->pag);    
         foreach($da as $d=>$value){
            $value['type'] = Db::name("fund_zhifu_type")->where('tid',$value['type'])->find()['name'];
            $da[$d]=$value;
         }
        //支付统计
        $money = [];  $type0=0;  $type1=0;  $type2=0;  $type3=0;  $type4=0;
        $info = Db::name("fund_zhifu_record")->where($where)->where('status',2)->select();
        foreach ($info as $key => $value) {
            $type0 += $value['amount'];
            if ($value['type'] == 1 ) {
                $type1+=$value['amount'];
            }
            if ($value['type'] == 2 ) {
                $type2+=$value['amount'];
            }
            if ($value['type'] == 3 ) {
                $type3+=$value['amount'];
            }
            if ($value['type'] == 4 ) {
                $type4+=$value['amount'];
            }
        }
        $money[]=$type0; $money[]=$type1; $money[]=$type2; $money[]=$type3; $money[]=$type4;
        $moneys = [];  $types0=0;  $types1=0;  $types2=0;  $types3=0;  $types4=0;
        $infos = Db::name("fund_zhifu_record")->where('status',2)->select();
        foreach ($infos as $key => $value) {
            $types0 += $value['amount'];
            if ($value['type'] == 1 ) {
                $types1+=$value['amount'];
            }
            if ($value['type'] == 2 ) {
                $types2+=$value['amount'];
            }
            if ($value['type'] == 3 ) {
                $types3+=$value['amount'];
            }
            if ($value['type'] == 4 ) {
                $types4+=$value['amount'];
            }
        }
        $moneys[]=$types0; $moneys[]=$types1; $moneys[]=$types2; $moneys[]=$types3; $moneys[]=$types4;            
        $da->appends($data);
        $this->assign('page',$da->render());
        $this->assign('da',$da);
        $this->assign('search',$data);
        $this->assign('count',$money);
        $this->assign('counts',$moneys);
        return $this->fetch();
     }
     







     /**
      * 删除支付订单
      */
     
     public function  delpay(){
         $data = $this->request->param();
         if( !isset($data['id'])  ||  empty($data['id'])){
             return $this->error("没有上传id");
         }
         $da = Db::name("fund_zhifu_record")->where('id',$data['id'])->delete();
         if($da){
             return $this->success("删除成功");
         }
     }       


    


      /**
      * 批量删除支付订单
      */
     
      public function  delpayall(){
        $data = $this->request->param();       
        if(!isset($data['id']) || empty($data['id'])){
            return  $this->error("请选择删除目标");
        }
        $ids  = $data['id'];
        foreach($ids as $id){
          $da = Db::name("fund_zhifu_record")->where("id",$id)->delete();
        }
        return $this->success("批量删除成功");     
    }       

    




    /**
     * @adminMenu(
     *     'name'   => '支付类型',
     *     'parent' => 'admin/Plugin/fund',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '支付类型',
     *     'param'  => ''
     * )
     */

    public  function zftype(){
        $data = $this->request->param();      
        $da = Db::name("fund_zhifu_type")->select();
        $this->assign('da',$da);
        return $this->fetch();
    }
    



    /**
     * 增加支付类型
     */
    public function addtype(){
        $data = $this->request->param();
        $da = ['name'=>$data['name'],'photo'=>$data['zhumb'],'type'=>$data['type']];      
        $da = Db::name("fund_zhifu_type")->insertGetID($da);
        if( $da ) {
            return $this->success("添加成功");
        }else{
            return $this->error("添加失败");
        }
    }


   /**
    * 查询类型
    */
    public function fundtype(){
        $data = $this->request->param();
        $da = Db::name("fund_zhifu_type")->where('tid',$data['id'])->find()['type'];
        return $da;
    }
    


    /**
     * 修改类型
     */
    public function uptype(){
        $data = $this->request->param();
        if($data['type']=='1'){
            $da = Db::name("fund_zhifu_type")->where('tid',$data['id'])->update(['type'=>'2']);
        }
        if($data['type']=='2'){
            $da = Db::name("fund_zhifu_type")->where('tid',$data['id'])->update(['type'=>'1']);
        }
        if($da) {
            return $this->success("修改成功");
        }
    }








    /**
     * 删除任务类型
     */
    public function typedel(){
        $data = $this->request->param(); 
        $da = Db::name("fund_zhifu_type")->where('tid',$data['id'])->delete();
        if($da){
            return $this->success("删除成功");
        }
    }
    




   
/********************************cc  退款列表   ************************************* */





    /**
     * @adminMenu(
     *     'name'   => '支付列表',
     *     'parent' => 'admin/Plugin/index',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '支付列表',
     *     'param'  => ''
     * )
     */
    public function refundmManagement(){
        $data = $this->request->param();
        $where=1;
        if(!empty($data['type'])  &&  !empty($data['category'])){
            $type = $data['type'];
            $category = "'%".$data['category']."%'";
            if($type==1){
                $where .=" and `trade_sn` like".$category;
            }
            if($type==2){
                $where .=" and `phone` like".$category;
            }
        }
        if(isset($data['add_time'])  &&  !empty($data['add_time'])){
            $where .=" and `refund_time` >=".strtotime($data['add_time']);
        }
        if(isset($data['end_time'])  &&  !empty($data['end_time'])){
            $where .=" and `refund_time` <=".strtotime($data['end_time']);
        }       
        $da = Db::name("fund_refund")->where($where)->paginate($this->pag);
        foreach( $da as $key=>$value ){
            //获取用户信息
            $res  = $this->onterface('getUserinfo',['uid'=>$value['refund_uid'],'field'=>'',true]);       
            $user = $res['data'];
            $value['userName'] = $user['username'];
            $da[$key] = $value;
        }
        $da->appends($data);
        $this->assign('page',$da->render());
        $this->assign('da',$da);
        $this->assign('search',$data);
        return $this->fetch();
    }



    






    /***********************************************  用户账户列表   ********************************************************************** */
    
    /**
     * @adminMenu(
     *     'name'   => '账户列表',
     *     'parent' => 'admin/Plugin/index',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '账户列表',
     *     'param'  => ''
     * )
     */

    public function accountList(){
        $data = $this->request->param();
        $da   = Db::name("fund_bankcard")->paginate($this->pag);
        $da->appends($data);
        $this->assign('page',$da->render());      
        $this->assign('da',$da);
        $this->assign('search',$data);
        return $this->fetch();
    } 






    /**
     * 批量删除账号
     */
    public function bankcarddelall(){
        $data = $this->request->param();
        if(!isset($data['id']) || empty($data['id'])){
            return  $this->error("请选择删除目标");
        }
        $ids = $data['id'];
        foreach($ids as $id){
           $da = Db::name("fund_bankcard")->where("id",$id)->delete();
        }
        if( $da ){
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }

    }



    /**
     * 删除单条账号
     */
    public function bankcarddelone(){
        $data = $this->request->param();
        $da  = Db::name("fund_bankcard")->where("id",$data['id'])->delete();
        if($da){
           return $this->success("删除成功");
        }
    }




    




    /********************************cc  银行列表   ************************************* */

    /**
     * @adminMenu(
     *     'name'   => '银行信息',
     *     'parent' => 'admin/Plugin/fund',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,
     *     'icon'   => '',
     *     'remark' => '银行信息',
     *     'param'  => ''
     * )
     */

    public function banklist(){
        $data = $this->request->param();
        $where = 1;
        if(!empty($data['types']) && !empty($data['category'])){
            $types = $data['types'];
            $category = "'%".$data['category']."%'";
            if($types==1){
                $where .=" and `bank` like".$category;
            }
            if($types==2){
                $where .=" and `card_bin` like".$category;
            }
            if($types==3){
                $where .=" and `desc` like".$category;
            }
        }
        $da = Db::name("fund_bank")->where($where)->paginate($this->pag);
        $da->appends($data);
        $this->assign('page',$da->render());
        $this->assign('data',$da);
        $this->assign('search',$data);
        return $this->fetch(); 
    }



    /**
     * 批量删除银行信息
     */
    
    public function bank_delall(){
        $data = $this->request->param();      
        if(!isset($data['id']) || empty($data['id'])){
            return  $this->error("请选择删除目标");
        }
        $ids  = $data['id'];
        foreach($ids as $id){
          $da = Db::name("fund_bank")->where("id",$id)->delete();
        }
        return $this->success("批量删除成功");
    }


    /**
     * 删除
     */
    public function bank_del(){
        $data = $this->request->param();
        $da  = Db::name("fund_bank")->where("id",$data['id'])->delete();
        if($da){
           return $this->success("删除成功");
        }
    }




    /*
     * 添加银行
     */
    public function add_bank(){
        $data = $this->request->param();
        $da['desc'] = $data['desc'];
        $da['thumb'] = $data['thumb'];
        $da['bank'] = $data['bank'];
        $da['status'] = $data['powerExp'];
        $data = Db::name("fund_bank")->insert($da);
        if ( $data ) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');   
        }   
    }


    /**
     * 获取银行数据
     */
    public function fund_bank(){
        $data = $this->request->param();
        $da = Db::name("fund_bank")->where('id',$data['id'])->find();
        return $da;
    }




    /**
     * 更该银行数据
     */
    public function up_bank(){
        $data = $this->request->param();
        $da = Db::name("fund_bank")->where('id',$data['id'])->update(['desc'=>$data['desc'],'thumb'=>$data['thumb'],'bank'=>$data['bank'],'status'=>$data['powerExp']]);
        if ( $da ) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');   
        }
    }
    



    
    /**
     * 添加图片
     */
    public function add_photo(){
        $dara = $this->request->param();
        if(empty($_FILES)){
            return zy_json_echo(false,'非法上传内容！',null,110);
        }
        $file=$_FILES;
        $file=$_FILES['photo'];
        $upload_path = "./plugins/task/view/public/upload/banklogo/";
        //照片是否存在
        if($file['name'] <> ""){   
            if(is_uploaded_file($file['tmp_name'])){     
              if(preg_match('/\\.(gif|jpeg|png|bmp|jpg|tiff|)$/i', $file['name'])){
                $kz=substr($file['name'],strrpos($file['name'],'.'));
                $sui=mt_rand(1000,9999);
                $filename=date('YmdHis').$sui.$kz;
                $pic=$upload_path.$filename;
                //判断pingzheng文件夹是否存在
                if(!file_exists($upload_path)){
                    mkdir($upload_path,0777,true);
                }
                if(move_uploaded_file($file['tmp_name'], $pic)){
                    $this->compressedImage($pic,$pic);
                    $pic= explode('.',$pic,2)['1'];
                    return zy_json_echo(true,'获取成功',$pic,200);
                }else{
                    return zy_json_echo(false,$file['error'],null,103);              
                }
            }else{
                return zy_json_echo(false,'文件不是图片格式！',null,105);
            }
            }else{
                return zy_json_echo(false,"图片获取路径错误！",null,120);
            }
        }else{     
            return zy_json_echo(false,"没有上传图片！",null,108);
        }
    }


    /**
    * desription 压缩图片
    * @param sting $imgsrc 图片路径
    * @param string $imgdst 压缩后保存路径
    */
    public function compressedImage($imgsrc, $imgdst) {
        list($width, $height, $type) = getimagesize($imgsrc);
        $new_width = $width;//压缩后的图片宽
        $new_height = $height;//压缩后的图片高
        if($width >= 600){
            $per = 600 / $width;//计算比例
            $new_width = $width * $per;
            $new_height = $height * $per;
        }
        switch ($type) {
            case 1:
                $giftype = check_gifcartoon($imgsrc);
                if ($giftype) {
                    header('Content-Type:image/gif');
                    $image_wp = imagecreatetruecolor($new_width, $new_height);
                    $image = imagecreatefromgif($imgsrc);
                    imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    //90代表的是质量、压缩图片容量大小
                    imagejpeg($image_wp, $imgdst, 90);
                    imagedestroy($image_wp);
                    imagedestroy($image);
                }
                break;
            case 2:
                header('Content-Type:image/jpeg');
                $image_wp = imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefromjpeg($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                //90代表的是质量、压缩图片容量大小
                imagejpeg($image_wp, $imgdst, 90);
                imagedestroy($image_wp);
                imagedestroy($image);
                break;
            case 3:
                header('Content-Type:image/png');
                $image_wp = imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefrompng($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                //90代表的是质量、压缩图片容量大小
                imagejpeg($image_wp, $imgdst, 90);
                imagedestroy($image_wp);
                imagedestroy($image);
                break;
        }
    }





    /**
     * 模块调用
     */
    private function onterface( $id,$data ){
        $symbol = 'fund';  
        $id = $id;   
        $param = $data;    
        $res    = getModuleApiData($symbol,$id,$param);
        return $res;     
    }






    
    /*
    *查询银行
    */

    public function bankInfo($card,$bankList){
        $card_8 = substr($card,0,8);
        if (isset($bankList[$card_8])) {
            return $card_8;        
        }
        $card_6 = substr($card,0,6);
        if (isset($bankList[$card_6])){
            return $card_6;        
        }
        $card_5 = substr($card,0,5);
        if (isset($bankList[$card_5])){
            return $card_5;   
        }
        $card_4 = substr($card,0,4);
        if (isset($bankList[$card_4])){
            return $card_4;
        }
        $card_3 = substr($card,0,3);
        if (isset($bankList[$card_3])){
            return $card_3;
        }
        $card_2 = substr($card,0,2);
        if (isset($bankList[$card_2])){
            return $card_2;
        }
        return 1;
    }



    
}
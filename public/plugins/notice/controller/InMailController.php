<?php
namespace plugins\notice\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;


class InMailController extends PluginAdminBaseController
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

        $symbol = 'notice';
        
        $id = 'readConfigInfo';

        $arr = ['data'=>null,'isModule'=>true];

        //调用配置接口
        $web_config = getModuleApiData($symbol,$id,$arr);

        return $web_config['data'];
    }





     /**
     * @adminMenu(
     *     'name'   => '站内信管理',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '站内信管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $data = $this->request->param();

        $where = 1;

        if (!empty($data['nickname'])) {
            $where .= " and nickname like '%{$data['nickname']}%'";
        }

        if (!empty($data['status'])) {
            $where .= " and status = ".$data['status'];
        }

        // dump(ZY_APP_PATH);

        $send = Db::name('notice_send')->where('type',1)->where($where)->order('create_time','desc')->paginate($this->pageNum);

        $this->assign('send',$send);

        $send->appends($data);

        $this->assign('data',$data);

        $this->assign('page',$send->render());

        return $this->fetch();
    }




    /*
     * 发送消息页面
     */
    public function sendPage()
    {
        $data = $this->request->param();

        return $this->fetch();
    }






    /*
     * 发送消息
     */
    public function addsms()
    {
        $data = $this->request->param();

        if (empty($data['title'])) {
            $this->error('标题不能为空');
        }
        if (empty($data['content'])) {
            $this->error('内容不能为空');
        }

        $symbol = 'notice';

        //判断是否单发群发
        switch ($data['leixing']) {
            case 1: //单发
                $num = $data['uid'];

                if($data['type']==1){
                    $id = 'member_one';
                    $param = ['uid' =>$num,'field' =>'username,nickname,mobile',true];
                }elseif($data['type']==2){
                    $id = 'member_three';
                    $param = ['mobile' =>$num,'field' =>'username,nickname,mobile,uid',true];
                }else{
                    $this->error("参数type传递错误");
                }
                $res = getModuleApiData($symbol, $id, $param);

                if($res['code']!=200){
                    $this->error($res['message']);
                }

                if($data['type']==2){
                    $num = $res['data']['uid'];
                }

                $da['username'] = $res['data']['username'];
                $da['nickname'] = $res['data']['nickname'];
                $da['mobile'] = $res['data']['mobile'];
                $da['uid'] = $num;
                $da['u_read'] = ','.$num.',';
                $da['status'] = 1;
                break;
            case 2: //群发
                $id = 'member_two';
                $param = ['isModule'=>true];
                $res = getModuleApiData($symbol, $id, $param); //uid获取所有的用户id，逗号拼接
                $da['u_read'] = $res['data'];
                $da['de_id'] = $res['data'];
                $da['uid'] = 1;
                $da['status'] = 2;
                break;
        }

        $da['title'] = $data['title'];
        $da['content'] = $data['content'];
        $da['create_time'] = time();
        $da['type'] = 1; //站内信

        //保存并发送
        if ($data['ty']==1) {
            $da['is_success'] = 1;
            $da['send_time'] = time();
            Db::name('notice_send')->insert($da);
            $this->success('发送成功',cmf_plugin_url('Notice://in_mail/index'));
        }

        //保存
        if ($data['ty']==2) {
            Db::name('notice_send')->insert($da);
            $this->success('保存成功',cmf_plugin_url('Notice://in_mail/index'));
        }
    }



    /*
     * 发送
     */
    public function fasong()
    {
        $data = $this->request->param();

        Db::name('notice_send')->where('id',$data['id'])->update(['is_success'=>1,'send_time'=>time()]);

        $this->success('发送成功');
    }


    /*
     * 修改站内信
     */
    public function editsms()
    {
        $data = $this->request->param();

        $send = Db::name('notice_send')->where('id',$data['id'])->find();

        if (isset($data['leixing'])) {

            if (empty($data['title'])) {
                $this->error('标题不能为空');
            }
            if (empty($data['content'])) {
                $this->error('内容不能为空');
            }

            $symbol = 'notice';
            //判断是否单发群发
            switch ($data['leixing']) {
                case 1: //单发
                    $num = $data['uid'];

                    if($data['type']==1){
                        $id = 'member_one';
                        $param = ['uid' =>$num,'field' =>'username,nickname,mobile',true];
                    }elseif($data['type']==2){
                        $id = 'member_three';
                        $param = ['mobile' =>$num,'field' =>'username,nickname,mobile,uid',true];
                    }else{
                        $this->error("参数type传递错误");
                    }
                    $res = getModuleApiData($symbol, $id, $param);

                    if($res['code']!=200){
                        $this->error($res['message']);
                    }

                    if($data['type']==2){
                        $num = $res['data']['uid'];
                    }

                    $da['username'] = $res['data']['username'];
                    $da['nickname'] = $res['data']['nickname'];
                    $da['mobile'] = $res['data']['mobile'];
                    $da['uid'] = $num;
                    $da['u_read'] = ','.$num.',';
                    $da['status'] = 1;
                    $da['de_id'] = '';
                    break;
                case 2: //群发
                    $id = 'member_two';
                    $param = ['isModule'=>true];
                    $res = getModuleApiData($symbol, $id, $param); //uid获取所有的用户id，逗号拼接
                    $da['username'] = '';
                    $da['nickname'] = '';
                    $da['mobile'] = '';
                    $da['u_read'] = $res['data'];
                    $da['de_id'] = $res['data'];
                    $da['uid'] = 1;
                    $da['status'] = 2;
                    break;
            }

            $da['title'] = $data['title'];
            $da['content'] = $data['content'];
            $da['update_time'] = time();
            $da['type'] = 1; //站内信

            Db::name('notice_send')->where('id',$data['id'])->update($da);

            $this->success('保存成功');
        }

        

        $this->assign('send',$send);

        return $this->fetch();
    }
















}
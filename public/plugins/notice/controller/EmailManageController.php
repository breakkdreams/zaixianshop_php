<?php
namespace plugins\notice\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;


class EmailManageController extends PluginAdminBaseController
{
    //分页数量
    private $pageNum = 20;

    //邮箱消息
    const aid = "fafd4cf8c648da1aa86338d4a5152294";
    const key = "86793af7c3bc64fd197001a8be4c6f9a";

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
     *     'name'   => '邮箱管理',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '邮箱管理',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $data = $this->request->param();

        $send = Db::name('notice_send')->where('type',3)->order("field(status,0,2,1)")->paginate($this->pageNum);

        $this->assign('send',$send);

        $send->appends($data);

        $this->assign('page',$send->render());

        return $this->fetch();
    }




    /*
     * 添加邮箱消息
     */
    public function addsms()
    {
        $data = $this->request->param();
        //执行发送
        if (isset($data['title'])) {
            //邮箱配置
            $email_config = getModuleConfig('notice','config','email_config.json');
            $email_config = json_decode($email_config,true);

            if ($email_config['mail_off'] != 1) {
                $this->error('该接口已被禁用');
            }

            //发送消息
            $da = [
                'aid'=>self::aid,
                'key'=>self::key,
                'host'=>$email_config['server'], //邮件服务器
                'port'=>$email_config['port'], //邮件发送端口
                'secure'=>$email_config['protocol'], //发送协议
                'username'=>$email_config['username'], //验证用户名
                'pwd'=>$email_config['password'], //验证密码
                'set_from'=>$email_config['mailbox'],//发件人
                'subject'=>$data['title'], //邮件标题  
                'address'=>$data['email'], //收件邮箱
                'content'=>$data['content'], //邮件内容
            ];

            //添加消息记录
            $add['title']=$data['title'];
            $add['content']=$data['content'];
            $add['recipients']=$data['email']; //接收人
            $add['create_time'] = time();
            $add['type'] = 3; //邮箱发送
            $add['status'] = 1;

            $send = Db::name('notice_send');

            switch ($data['ty']) {
                case 1: //保存并发送
                    //发送
                    $d=http_build_query($da);
                    $url="https://oa.300c.cn/zyapi/sms/sendemail?".$d;

                    $result = cmf_curl_get($url);

                    $result = substr($result,0,strpos($result,"}")+1);

                    $s = json_decode($result,true);

                    $add['send_time'] = time(); //发送时间
                    
                    if ($s['status']=='success'){
                        $add['is_success'] = 1;
                        $send->insert($add); //添加发送记录
                        $this->success('发送成功',cmf_plugin_url('Notice://email_manage/index'));
                    } else {
                        $add['is_success'] = 2;
                        $send->insert($add); //添加发送记录
                        $this->error('发送失败');
                    }
                    break;
                case 2: //仅保存

                    $send->insert($add); //添加发送记录
                    $this->success('保存成功',cmf_plugin_url('Notice://email_manage/index'));
                    break;
            }     
        }

        return $this->fetch();
    }






    /*
     * 批量删除
     */
    public function delsms()
    {
        $data = $this->request->param();

        if (empty($data['id'])) {
            $this->error('请选择要删除的数据');
        }

        Db::name('notice_send')->where('id','in',$data['id'])->delete();

        $this->success('操作成功');
    }





    /*
     * 发送
     */
    public function fasong()
    {
        $data = $this->request->param();
        //邮箱配置
        $email_config = getModuleConfig('notice','config','email_config.json');
        $email_config = json_decode($email_config,true);

        if ($email_config['mail_off'] != 1) {
            $this->error('该接口已被禁用');
        }

        $send = Db::name('notice_send')->where('id',$data['id']);

        $s = $send->find();

        //发送消息
        $da = [
            'aid'=>self::aid,
            'key'=>self::key,
            'host'=>$email_config['server'], //邮件服务器
            'port'=>$email_config['port'], //邮件发送端口
            'secure'=>$email_config['protocol'], //发送协议
            'username'=>$email_config['username'], //验证用户名
            'pwd'=>$email_config['password'], //验证密码
            'set_from'=>$email_config['mailbox'],//发件人
            'subject'=>$s['title'], //邮件标题  
            'address'=>$s['recipients'], //收件邮箱
            'content'=>$s['content'], //邮件内容
        ];

        // $da=http_build_query($da);
        // $url="https://oa.300c.cn/zyapi/sms/sendemail?".$da;
        // $result = json_decode(file_get_contents($url),true);

        //发送
        $d=http_build_query($da);
        $url="https://oa.300c.cn/zyapi/sms/sendemail?".$d;

        $result = cmf_curl_get($url);

        $result = substr($result,0,strpos($result,"}")+1);

        $s = json_decode($result,true);



        $edit['send_time'] = time();

        if ($s['status']=='success'){
            $edit['is_success'] = 1;
            Db::name('notice_send')->where('id',$data['id'])->update($edit); 
            $this->success('发送成功');
        } else {

            $edit['is_success'] = 2;
            Db::name('notice_send')->where('id',$data['id'])->update($edit); 
            $this->error('发送失败');
        } 
    }







    /*
     * 修改消息
     */
    public function editsms()
    {
        $data = $this->request->param();

        if (isset($data['save'])) {
            $da = [
                'title' => $data['title'],
                'content' => $data['content'],
                'recipients' => $data['email'],
                'update_time' => time(),
            ];


            Db::name('notice_send')->where('id',$data['id'])->update($da);

            $this->success('修改成功',cmf_plugin_url('Notice://email_manage/index'));
        }

        $send = Db::name('notice_send')->where('id',$data['id'])->find();

        $this->assign('send',$send);

        return $this->fetch();
    }










}
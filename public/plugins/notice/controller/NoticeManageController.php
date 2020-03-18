<?php
namespace plugins\notice\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;



class NoticeManageController extends PluginAdminBaseController
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
     *     'name'   => '公告管理列表',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '公告管理列表',
     *     'param'  => ''
     * )
     */
    public function index()
    {
    	$data = $this->request->param();

        $where = 1;
        //标题
        if (isset($data['title']) && !empty($data['title'])) {
            $where .= " and title like '%".$data['title']."%'";
        }
        //开始时间
        if (isset($data['start_addtime']) && !empty($data['start_addtime'])) {
            $where .= " and add_time >=".strtotime($data['start_addtime']);
        }
        //截止时间
        if (isset($data['end_addtime']) && !empty($data['end_addtime'])) {
            $where .= " and add_time <=".strtotime($data['end_addtime']);
        }
        //公告状态
        if (isset($data['status']) && !empty($data['status'])) {
            $where .= " and status = ".$data['status'];
        }

        $notice = Db::name('notice')->where($where)->order('add_time','desc')->paginate($this->pageNum);

        $this->assign('data',$data);

        $this->assign('notice',$notice);
        //在分页前保存分页条件
        $notice->appends($data);

        $this->assign('page',$notice->render()); //分页

        $this->assign('total',$notice->total()); //数量

        return $this->fetch('notice_manage/index');
    }




    /**
    * 公告删除
    */
    public function delNotice(){
        $data = $this->request->param();
        //都没有选择删除什么
        if(empty($data['id'])){
            $this->error("请选择要删除的信息");
        }

        //批量删除
        Db::name('notice')->where('id','in',$data['id'])->delete();

        $this->success("删除成功");
    }





    /**
    * 公告添加
    */
    public function addNotice(){
        $data = $this->request->param();

        if (isset($data['type'])) {

            if (empty($data['title'])) {
                $this->error('标题不能为空');
            }
            if (empty($data['content'])) {
                $this->error('内容不能为空');
            }

            switch ($data['type']) {
                case 1: //保存并发布
                    $da['status'] = 2;
                    $da['release_time'] = time();
                    break;
                case 2: //只保存
                    $da['status'] = 1;
                    break;
            }

            $da['title'] = $data['title'];

            $da['content'] = $data['content'];

            $da['add_time'] = time();

            $da['release_name'] = session('name');

            $da['is_jinyong'] = 1; //是否禁用 1.启用 2.禁用

            Db::name('notice')->insert($da);

            $this->success('操作成功',cmf_plugin_url('Notice://admin_index/index'));
        }

        return $this->fetch();
    }





    /**
    * 评价管理_修改
    */
    public function editNotice(){
        $data = $this->request->param();


        if (isset($data['title'])) {

            if (empty($data['title'])) {
                $this->error('标题不能为空');
            }
            if (empty($data['content'])) {
                $this->error('内容不能为空');
            }

            $da['title'] = $data['title'];

            $da['content'] = $data['content'];

            Db::name('notice')->where('id',$data['id'])->update($da);

            $this->success('修改成功');
        }

        $notice = Db::name('notice')->where('id',$data['id'])->find();

        $notice = autoHtmlspecialcharsDecode($notice); //对数据解码

        $this->assign('notice',$notice);

        return $this->fetch();
    }








    /*
     * 开始发布
     */
    public function release(){
        $data=$this->request->param();
        $da['release_time'] = time();
        $da['status'] = 2;
        Db::name('notice')->where('id',$data['id'])->update($da);
        $this->success('操作成功');
    }







    /*
     * 公告审核
     */
    public function audit()
    {
        $data = $this->request->param();

        $where = 1;
        if (isset($data['title']) && !empty($data['title'])) {
            $where .= " and title like '%".$data['title']."%'";
        }
        if (isset($data['start_addtime']) && !empty($data['start_addtime'])) {
            $where .= " and add_time >=".strtotime($data['start_addtime']);
        }
        if (isset($data['end_addtime']) && !empty($data['end_addtime'])) {
            $where .= " and add_time <=".strtotime($data['end_addtime']);
        }

        $notice = Db::name('notice')->where('status',2)->order('add_time','desc')->where($where)->paginate($this->pageNum);

        $notice = autoHtmlspecialcharsDecode($notice); //对数据解码

        $this->assign('notice',$notice);

        $this->assign('data',$data);

        $notice->appends($data);

        $this->assign('page',$notice->render()); //分页

        $this->assign('total',$notice->total()); //数量

        return $this->fetch();

    }





    /*
     * 公告通过
     */
    public function tongguo()
    {
        $data = $this->request->param();

        Db::name('notice')->where('id',$data['id'])->update(['status'=>3]);

        $this->success('操作成功');
    }


    /*
     * 公告拒绝
     */
    public function jujue()
    {
        $data = $this->request->param();

        $da['status'] = 4;
        $da['refuse_reason'] = $data['refuse_reason']; //拒绝原因

        Db::name('notice')->where('id',$data['id'])->update($da);

        $this->success('操作成功');
    }










}
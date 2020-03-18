<?php
namespace plugins\after_sales\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;


class AfterConfigController extends PluginAdminBaseController
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
     *     'name'   => '售后配置',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 1000,  //此处为排序，请使用1000
     *     'icon'   => '',
     *     'remark' => '售后配置',
     *     'param'  => ''
     * )
     */
    /**
     * [index 售后配置]
     * @return [type] [description]
     */
    public function index()
    {
        $data = $this->request->param();

        $where = 1;
        if (isset($data['name']) && !empty($data['name'])) {
            $where .= " and name like '%".$data['name']."%'";
        }

        $after = Db::name('after_set')->where($where)->paginate($this->pageNum);

        $this->assign('data',$data);

        $this->assign('after',$after);
        //在分页前保存分页条件
        $after->appends($data);
        //分页
        $this->assign('page',$after->render());

        return $this->fetch();
    }



    /*
     * 修改配置的状态
     */
    public function editStatus()
    {
        $data = $this->request->param();

        switch ($data['status']) {
            case 1:
                $da['status'] = 2;
                break;
            case 2:
                $da['status'] = 1;
                break;
        }

        Db::name('after_set')->where('id',$data['id'])->update($da);

        $this->success('操作成功');
    }



    /*
     * 添加售后配置
     */
    public function addConfig()
    {
        $data = $this->request->param();

        if (empty($data['name'])) {
            $this->error('配置名称不能为空');
        }

        $da['name'] = $data['name'];

        $da['status'] = isset($data['status'])?1:2;

        Db::name('after_set')->insert($da);

        $this->success('操作成功');
    }




    /*
     * 批量删除配置项
     */
    public function delConfig()
    {
        $data = $this->request->param();

        Db::name('after_set')->where('id','in',$data['id'])->delete();

        $this->success('删除成功');
    }











}
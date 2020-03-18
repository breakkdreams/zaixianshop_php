<?php
namespace plugins\evaluation\controller; //Demo插件英文名，改成你的插件英文就行了
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

use think\Log;


class EvaluateSetController extends PluginAdminBaseController
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
     * 评价配置
     */
    public function index()
    {
        $data = $this->request->param();

        $peizhi = getModuleConfig('evaluation','config','evaluateconfig.json');

        $peizhi = json_decode($peizhi,true);

        $this->assign('peizhi',$peizhi);

        return $this->fetch();
    }



    /*
     * 保存配置
     */
    public function evaluateSetSave()
    {
        $data = $this->request->param();

        $config = [
            'is_anonymity' => isset($data['is_anonymity']) ? 1 : 2, //是否允许匿名 1.是 2.否
            'is_comment' => isset($data['is_comment']) ? 1 : 2, //是否允许评论 1.是 2.否
            'jifen' => $data['jifen'], //评论奖励积分
            'delkoufen' => $data['delkoufen'], //评论被删除扣除积分
            'is_audit' => isset($data['is_audit']) ? 1 : 2, //是否需要审核 1.是 2.否
            'kai_yanzheng' => isset($data['kai_yanzheng']) ? 1 : 2, //是否开启验证 1.是 2.否
        ];

        saveModuleConfigData('evaluation','config','evaluateconfig.json',$config);

        $this->success('操作成功');
    }

}
<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\Model;
use think\Cache;
use think\Db;
use app\admin\model\AccessModel;//权限查询
use app\admin\model\AuthorizationModel;
use app\admin\model\AuthAccessModel;

class AdminMenuModel extends Model
{
    //验证菜单是否超出三级
    public function checkParentId($parentId)
    {
       // $find = $this->where(["id" => $parentId])->getField("parent_id");
       $find=$this->getField($parentId);
        if ($find) {
           //$find2 = $this->where(["id" => $find])->getField("parent_id");
           $find2=$this->getField($find);
            if ($find2) {
                //$find3 = $this->where(["id" => $find2])->getField("parent_id");
                $find3=$this->getField($find2);
                if ($find3) {
                    return false;
                }
            }
        }
        return true;
    }

    //验证action是否重复添加
    public function checkAction($data)
    {
        //检查是否重复添加
        $find = $this->where($data)->find();
        if ($find) {
            return false;
        }
        return true;
    }

    //验证action是否重复添加
    public function checkActionUpdate($data)
    {
        //检查是否重复添加
        $id = $data['id'];
        unset($data['id']);
        $find = $this->field('id')->where($data)->find();
        if (isset($find['id']) && $find['id'] != $id) {
            return false;
        }
        return true;
    }


    /**
     * 按父ID查找菜单子项
     * @param int $parentId 父菜单ID
     * @param boolean $withSelf 是否包括他自己
     * @return mixed
     */
    public function adminMenu($parentId, $withSelf = false)
    {
        //父节点ID
        $parentId = (int)$parentId;
        //$result   = $this->where(['parent_id' => $parentId, 'status' => 1])->order("list_order", "ASC")->select();
        $result=$this->getMenuByParentId($parentId,$status=1);
        $result=$this->listOrder($result,'ASC');

        //排序
        if ($withSelf) {
           // $result2[] = $this->where(['id' => $parentId])->find();
            $result2[]=$this->getMenuByParentId_one($parentId);
            $result    = array_merge($result2, $result);
        }

        $array = [];

        $auth_access=$this->getCacheAccessMenu();
        foreach ($result as $v) {
            if( $v['id'] == 152 ){ continue;}
            //方法
            $action = $v['action'];
            //public开头的通过
            if (preg_match('/^public_/', $action)) {
                $array[] = $v;
            } else {
                if (preg_match('/^ajax_([a-z]+)_/', $action, $_match)) {

                    $action = $_match[1];
                }
                $rule_name = strtolower($v['app'] . "/" . $v['controller'] . "/" . $action);
                if (cmf_auth_check(cmf_get_current_admin_id(), $rule_name)) {
                    //添加权限 检查auth_access中是否存在菜单
                    $rule_names=$v['app'] . "/" . $v['controller'] . "/" . $action;
                    if(in_array($rule_names,$auth_access)){
                        $array[] = $v;
                    }
                }

            }
        }

        return $array;
    }

    /**
     * 获取菜单 头部菜单导航
     * @param string $parentId 菜单id
     * @return mixed|string
     */
    public function subMenu($parentId = '', $bigMenu = false)
    {
        $array   = $this->adminMenu($parentId, 1);
        $numbers = count($array);
        if ($numbers == 1 && !$bigMenu) {
            return '';
        }
        return $array;
    }

    /**
     * 菜单树状结构集合
     */
    public function menuTree()
    {
        $data = $this->getTree(0);
        return $data;
    }

    /**
     * 取得树形结构的菜单
     * @param $myId
     * @param string $parent
     * @param int $Level
     * @return bool|null
     */
    public function getTree($myId, $parent = "", $Level = 1)
    {
        $data = $this->adminMenu($myId);
        $Level++;
        if (count($data) > 0) {
            $ret = NULL;
            foreach ($data as $a) {
                if($a['status']==0 ) continue;
                $id         = $a['id'];
                $name       = $a['app'];
                $controller = ucwords($a['controller']);
                $action     = $a['action'];
                //附带参数
                $params = "";
                if ($a['param']) {
                    $params = "?" . htmlspecialchars_decode($a['param']);
                }
                 if (strpos($name, 'plugin/') === 0) {
                    $pluginName = str_replace('plugin/', '', $name);
                    $url        = cmf_plugin_url($pluginName . "://{$controller}/{$action}{$params}");
                } else {
                    $url =url("{$name}/{$controller}/{$action}{$params}");
                }
                $name = str_replace('/', '_', $name);
                $array = [
                    "icon"   => $a['icon'],
                    "id"     => $id . $name,
                    "name"   => $a['name'],
                    "parent" => $parent,
                    "url"    => $url,
                    'lang'   => strtoupper($name . '_' . $controller . '_' . $action)
                ];
                $ret[$id . $name] = $array;
                $child            = $this->getTree($a['id'], $id, $Level);
                //由于后台管理界面只支持三层，超出的不层级的不显示
                if ($child && $Level <=3) {
                    $ret[$id . $name]['items'] = $child;
                }
            }
            return $ret;
        }
        return false;
    }

    /**
     * 更新缓存
     * @param  $data
     * @return array
     */
    public function menuCache($data = null)
    {
        if (empty($data)) {
            $data=$this->getServerMenu();
        } else {
            Cache::set('Menu', $data, 60);
        }
        return $data;
    }
    /**
     * 后台有更新/编辑则删除缓存
     * @param type $data
     */
    public function _before_write(&$data)
    {
        parent::_before_write($data);
        F("Menu", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options)
    {
        parent::_after_delete($data, $options);
        $this->_before_write($data);
    }

    public function menu($parentId, $with_self = false)
    {
        //父节点ID
        $parentId = (int)$parentId;
        //$result   = $this->where(['parent_id' => $parentId])->select();
        $result=$this->getMenuByParentId($parentId);
        if ($with_self) {
            $result2[] = $this->where(['id' => $parentId])->find();
            $result    = array_merge($result2, $result);
        }
        return $result;
    }

    /**
     * 得到某父级菜单所有子菜单，包括自己
     * @param number $parentId
     */
    public function get_menu_tree($parentId = 0)
    {
        //$menus = $this->where(["parent_id" => $parentId])->order(["list_order" => "ASC"])->select();
        $menus=$this->getMenuByParentId($parentId);
        $menus=$this->listOrder($menus,'ASC');
        if ($menus) {
            foreach ($menus as $key => $menu) {
                $children = $this->get_menu_tree($menu['id']);
                if (!empty($children)) {
                    $menus[$key]['children'] = $children;
                }
                unset($menus[$key]['id']);
                unset($menus[$key]['parent_id']);
            }
            return $menus;
        } else {
            return $menus;
        }
    }

    protected $auth_access=null;
    public function getCacheAccessMenu()
    {
        if(empty($this->auth_access)){
            //获取当前登录用户所再角色的授权菜单 2019-3-4
            $accessModel=new AccessModel();
            $role=$accessModel->get_user_role_info(cmf_get_current_admin_id());
            $this->auth_access=Db::name('auth_access')->where('role_id',$role['id'])->column('rule_name');
        }
        return $this->auth_access;
    }
/***********************************************************************************/
    /**
     * 获取菜单20190401
     */
    protected function getServerMenu()
    {
		$menus = Cache::get('Menu');
        if( !Cache::has('Menu') || empty( $menus ) ){
            $author=new AuthorizationModel();
            $data=$author->getServerMenu();
            if($data['status']=='error'){
                Cache::set('Menu',[],60);
            }else{
                if(!isset($data['data'])){
                    $data['data']=[];
                }
                //更新数据
                $authAccess=new AuthAccessModel();
                $authAccess->updateAccessMenu($data['data']);
                //缓存数据
                Cache::set('Menu',$data['data'],60);
            }
        }
        return Cache::get('Menu');
    }

    /**
     * 清空缓存菜单
     */
    public function  clearCacheMenu()
    {
        Cache::clear();
    }

    /**
     * 根据parentId获取菜单列表20190401
     * @param  [type] $parentId [description]
     * @param  int  是否获取显示状态的菜单  $status [<description>] 
     * @return [type]           [description] 
     */
    public function getMenuByParentId($parentId,$staus=null)
    {
        //获取缓存
        $menuList=$this->getServerMenu();
        $result=[];
        foreach ($menuList as $key => $value) {
            if($value['parent_id']==$parentId){
                if(empty($status)){
                    $result[]=$value;  
                    continue;
                }
                if(!empty($status) && $status==1 && $value['status']== 1 ){
                    $result[]=$value;  
                }else if(!empty($status) && $status==0 && $value['status']== 0 ){
                    $result[]=$value;  
                }       
            }
        }
        return $result;
    }

        /**
     * 根据parentId获取菜单列表2019521
     * @param  [type] $parentId [description]
     * @param  int  是否获取显示状态的菜单  $status [<description>] 
     * @return [type]           [description] 
     */
    public function getMenuByParentId_one($parentId)
    {
        //获取缓存
        $menuList=$this->getServerMenu();
        $result=null;
        foreach ($menuList as $key => $value) {
            if($value['id']==$parentId){
                $result=$value;
                break;
            }
        }
        return $result;
    }

    /**
     * 20190401
     */
    protected function getField($parentId)
    {
        $menuList=$this->getServerMenu();
        $result=null;
        foreach ($menuList as $key => $value) {
            if($value['id']==$parentId){
                $result=$value['prent_id'];
                break;
            }
        }
        return $result;

    }

    /**
     * 菜单排序  20190521
     */
    protected function listOrder($list,$ruler)
    {
        for($i=0;$i<count($list);$i++){
            for($j=$i+1;$j<count($list);$j++){
                if( $ruler=='ASC' && $list[$i]['list_order']>$list[$j]['list_order']  || $ruler=='DESC' && $list[$i]['list_order']<$list[$j]['list_order']  ){
                    $k = $list[$i];
                    $list[$i] = $list[$j];
                    $list[$j] = $k;
                }
            }
        }
        return $list;
    }

}

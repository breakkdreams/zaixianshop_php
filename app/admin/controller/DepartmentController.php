<?php
namespace app\admin\controller;

use app\admin\model\AdminMenuModel;
use cmf\controller\AdminBaseController;
use think\Db;
use tree\Tree;
use app\admin\validate;
use app\admin\model\AccessModel;//读取权限模型类

/**
* 部门管理
*/
class DepartmentController extends AdminBaseController
{
	
	/**
	 * 树状展示模块
	 * @return [type] [description]
	 */
	public function index()
	{	
		//读取公司信息
		$company=get_current_company_info();
		$this->assign('company',$company);
		//where('company_id',$company['id'])
		if($company['id']==8){
			$result = Db::name('department')->column('');
		}else{
			$result = Db::name('department')->where('company_id',$company['id'])->column('');
		}
		
		$tree       = new Tree();
        $tree->icon = ['│ ', '├─ ', '└─ '];
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $m) {
            $newMenus[$m['id']] = $m;
        }

        foreach ($result as $n => $t) {
            $result[$n]['level']        = $this->_getLevel($t['id'], $newMenus);
            $result[$n]['style']        = empty($t['parent_id']) ? '' : 'display:none;';
            $result[$n]['parentIdNode'] = ($t['parent_id']) ? ' class="child-of-node-' . $t['parent_id'] . '"' : '';
        }

        $str = "<tr id='node-\$id' \$parentIdNode  style='\$style'>
                   <td style='padding-left:30px;'>\$spacer
	                   <span class= 'department_name'>\$name</span> &nbsp;&nbsp;<span class='badge'></span>

	                   <a title = '添加部门' class='department-tree' data-toggle='modal' data-target='#myModal' id = '\$id'>
                   			<span class='icon glyphicon glyphicon-plus-sign'></span></a>
                        <a title = '编辑部门' class='ajax-edit' data-toggle='modal' data-target='#myModal' id = '\$id' url='ajaxEdit/id/\$id'><span class='icon glyphicon glyphicon-edit'></span></a>
                        <a title = '删除部门' class='icon js-ajax-delete' href='ajaxDelete/id/\$id'><span class='glyphicon glyphicon-trash'></span></a>
						<div class = 'rolelist'></div>
                   </td>
    			</tr>";
    	$str2 = "<option value = \$id>
        		<tr id='node-\$id'\$parentIdNode  style='\$style'>
                   <td style='padding-left:30px;'>\$spacer\$name</td>
    			</tr>
    			</option>";
        $tree->init($result);
        $category = $tree->getTree(0, $str);
        $category2 = $tree->getTree(0, $str2);
        $this->assign('category2' ,$category2);
		$this->assign('category',$category);
		return $this->fetch();
	}

	//ajax方式添加部门
	public function ajaxAdd()
	{
		if ($this->request->isAjax()) {
			$data = $this->request->param();
			$validate = $this->validate($data,'department');
			if ($validate !== true) {
				$this->error($validate);
			}else{
				$company=get_current_company_info();
				$data['company_id']=$company['id'];//公司信息id
				$res = db::name('department')->insert($data);
				if ($res) {
					$this->success('添加成功');
				}else{
					$this->error('添加失败');
				}
			}
		}
	}
	//ajax方式修改
	public function ajaxEdit()
	{
		if ($this->request->isAjax()) {
			$id = $this->request->param('id',0 ,'intval');
			$res = Db::name('department')->where(['id' => $id])->find();
			echo json_encode($res,JSON_UNESCAPED_UNICODE);
		}
	}
	//ajax更新提交
	public function ajaxEditPost()
	{
		if ($this->request->isAjax()) {
			$data = $this->request->param();
			$validate = $this->validate($data,'department');
			if ($validate !== true) {
				$this->error($validate);
			}else{
				$res = Db::name('department')->update($data);
				if ($res) {
					$this->success('修改成功');
				}else{
					$this->error('修改失败');
				}
			}
		}
	}
	//删除方法
	public function ajaxDelete()
	{
		if ($this->request->isAjax()) {
			$id = $this->request->param("id", 0, 'intval');
			if ($id == 1) {
				$name = Db::name('department')->where(['id'=>$id])->find();
				$this->error($name['name'].'不能被删除');
			}
			$count = Db::name('department')->where(['parent_id' => $id])->count();
			$count1 = Db::name('role')->where(['department_id' => $id])->count();
			if ($count > 0) {
				$this->error('该部门存在下属部门');
			}
			if ($count1 > 0) {
				$this->error('该部门存在角色');
			}
			$res = Db::name('department')->delete($id);
			if (!empty($res)) {
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}
	}

	/**
	 * 获取层级
	 * @param  [type]  $id    [description]
	 * @param  array   $array [description]
	 * @param  integer $i     [description]
	 * @return [type]         [description]
	 */
	protected function _getLevel($id, $array = [], $i = 0)
    {
        if ($array[$id]['parent_id'] == 0 || empty($array[$array[$id]['parent_id']]) || $array[$id]['parent_id'] == $id) {
            return $i;
        } else {
            $i++;
            return $this->_getLevel($array[$id]['parent_id'], $array, $i);
        }
    }

    //通过ajax方式获取角色
    public function getRole()
    {
    	if ($this->request->isAjax()) {
    		$id = $this->request->param('id',0,'intval');
    		$count = Db::name('role')->where(['department_id'=>$id])->count();
    		$user_count = Db::name('DepartmentUser')->where(['department_id'=>$id])->count();
    		$name = Db::name('role')->where(['department_id'=>$id])->column('name');
    		$data = [];
    		$data['count'] = $count;
    		$data['user_count'] = $user_count;
    		$data['name'] = $name;
    		echo json_encode($data,JSON_UNESCAPED_UNICODE);
    	}
    }
/**
 * 检查部门编号
 * @return [type] [description]
 */
    public function ajaxCheckNo()
    {
    	$no=$this->request->param()['no'];
    	if(mb_strlen($no,'UTF8')!=2){
    		return 1;
    	}else{
    		$company=get_current_company_info();//获取公司信息
    		$res=Db::name('department')->where('department_NO='.$no)->where('company_id',$company['id'])->find();
    	}
    	
    	return $res?1:0;//存在返回1 否则0
    }


    /**
     * 部门添加页面
     */
    public function add()
    {

    	return $this->fetch();
    }

    /**
     * 获取部门编号
     */
    public function getDpNO()
    {
    	$NO=Db::name('department')->column('department_NO');
    	echo (max($NO)+5);
    }



}
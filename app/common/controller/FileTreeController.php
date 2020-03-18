<?php
namespace app\common\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use think\Cache;
use think\Request;
use tree\Tree;

/**
 *系统更新控制器201911081503
 */
class FileTreeController extends HomeBaseController
{

    /**
     *获取目录树
     */
    private $id = 1;
    private function getFileTree( $path ,$parent = 0, $parent_title = 'root' , $list = []  )
    {
        if( $parent == 0 ){
            $item = [
                'id' => $this->id,
                'parent_id' => $parent,
                'parent_title' => $parent_title,
                'name' => $parent_title,
                'path' => '',
                'type'  => 0
            ];
            $list [] = $item;
            $this->id ++ ;
            $parent ++ ;
        }
        $dir = scandir( $path );
        foreach ($dir as $key => $value) {
            if( $value == '.' || $value == '..' ) continue;
            $file = $path.DS.$value;
            $item = [
                'id' => $this->id,
                'parent_id' => $parent,
                'parent_title' => $parent_title,
                'name' => $value,
                'path' => ($parent_title == 'root')? $value : $parent_title.'/'.$value,
                'type' => is_file( $file ) ? 0 : 1 //0目录 / 1文件
            ];
            $this->id++ ;
            $list [] = $item;
            
            if( is_dir( $file ) ){
               $list  = $this->getFileTree( $file , $item [ 'id' ]  , $item [ 'path' ] , $list );
            }
        }
        return $list;
    }


    /**
     * 获取文件列表
     */
    public function dirList()
    {
        if( !Cache::has('DIR_LIST') || empty( Cache::get( 'DIR_LIST' ) ) ){
            //dump(ROOT_PATH);exit;
            $result = $this->getFileTree( ROOT_PATH );
            $tree       = new Tree();
            $tree->icon = ['&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ '];
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            foreach ($result as $key => $value) {
                $result[$key]['parent_id_node'] = ($value['parent_id']) ? ' class="child-of-node-' . $value['parent_id'] . '"' : '';
                $result[$key]['style']          = empty($value['parent_id']) ? '' : 'display:none;';
                $result[$key]['checkbox'] = $value['id'] == 1 ? "" : '<input type="checkbox" value='.$value['path'].' />';
                $result[$key]['icon'] = !empty($value['type']) ? '<span class="glyphicon glyphicon-folder-close"></span>' : '<span class="glyphicon glyphicon-menu-hamburger"></span>';
            }

            $tree->init($result);
            $str      = "<tr id='node-\$id'  \$parent_id_node style='\$style ; cursor:pointer;'>
            <td style='padding-left:20px;'><label>\$checkbox &nbsp;&nbsp; \$icon \$name</label></td>
            <td>\$path </td>
            </tr>";
            $list = $tree->getTree(0, $str);
            Cache::set( 'DIR_LIST' , $list , 3600 );
        }else{
            $list = Cache::get( 'DIR_LIST' );
        }
        return $list;
    }



}

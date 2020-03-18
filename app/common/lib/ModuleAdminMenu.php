<?php 
namespace app\common\lib;
use mindplay\annotations\Annotations;
use app\admin\controller\MenuController;

/**
 * 模块注释菜单
 */
class ModuleAdminMenu 
{
	/**
	 * 获取当前安装模块的模块菜单信息
	 * @return [type] [description]
	 */
	public function getModuleMenuList( $symbol )
    {	
    	$controllerPath = ROOT_PATH.'/public/plugins/'.cmf_parse_name( $symbol ).'/controller';
    	$directorys = scandir( $controllerPath );
    	$controllers = array_diff( $directorys , [ '.' , '..' ] );

    	Annotations::$config['cache']                 = false;
        $annotationManager                            = Annotations::getManager();
        $annotationManager->registry['adminMenu']     = 'app\admin\annotation\AdminMenuAnnotation';
        $annotationManager->registry['adminMenuRoot'] = 'app\admin\annotation\AdminMenuRootAnnotation';

        //module menu list
		$menuList = [];

		$module = cmf_parse_name( $symbol );
		//规定主菜单必须是 AdminIndexController index()

    	foreach ( $controllers as  $controller ) {
    		//读取模块主菜单
    		$controller      = preg_replace('/\.php$/', '', $controller);
            $controllerName  = preg_replace('/\Controller$/', '', $controller);
            $controllerClass = "\\plugins\\$module\\controller\\$controller";
            $class = $controllerClass;        
            if( !class_exists($class) ){ continue; }
	        	$reflect = new \ReflectionClass($class);
	        	$methods = $reflect -> getMethods();
	        	if( !empty($methods) ){
	        		foreach ($methods as $mv) {
	        			$currentClassName = '\\' . $mv->class;
	        			if( $currentClassName != $class ){
	        				continue;
	        			}
	        			$infoList = Annotations::ofMethod($class, $mv->name, '@adminMenu');
			        	if( !empty($infoList) ){
			        		$menu =  $infoList[0];
			        		$newMenu = [] ;
		        			if( $controllerName == 'AdminIndex' && $mv->name == 'index' ){
		        				//模块主菜单
		        				$newMenu['parent'] = 'admin/Plugin/default';
		        			}else{
		        				//这是子菜单
		        				$parent = explode('/', $menu->parent);
                                $countParent = count($parent);
                                if ($countParent > 2) {
                                   continue;
                                }
                                $keys = "plugin/";
                                switch ( $countParent ) {
                                	case 1:
                                		$keys = $keys . "$module/$controllerName/" . $parent[0];
                                		break;
                                	case 2:
                                		$keys = $keys . "$module/" .$parent[0].'/'. $parent[1];
                                		break;
                                }
                                $newMenu['parent'] = $keys;
		        			}
	        				$newMenu['type'] = $menu->display;
	        				$newMenu['status'] = $menu->hasView ? 1 : 2;
	        				$newMenu['list_order'] = $menu->order;
	        				$newMenu['app'] = 'plugin/'. cmf_parse_name( $module , 1 );
	        				$newMenu['controller'] = $controllerName;
	        				$newMenu['action'] = $mv->name;
	        				$newMenu['param'] = $menu->param;
	        				$newMenu['name'] = $menu->name;
	        				$newMenu['icon'] = $menu->icon;
	        				$newMenu['remark'] = $menu->remark;
	        				$menuList [ "plugin/$module/$controllerName/".$mv->name ] =  $newMenu;
			        	}
	        		}
	        	}
    	}
    	if( empty( $menuList ) ){
    		return [];
    	}
    	//子菜单分层
    	$menuList = $this->hierarchy( $menuList );
    	return $menuList;
   	//方法结束
    }
    /**
     * 	分层级
     */
    public function hierarchy( $list ,$parent = 'admin/Plugin/default' )
    {
    	$newMenu = [] ;
    	foreach ( $list as $key => $value ) {
    		if( $parent == $value['parent'] ){
    			$value ['node'] = $this->hierarchy( $list , $key ); 
    			if( empty( $value['node'] ) ){
    				unset( $value['node'] );
    			}
    			$newMenu[] = $value; 
    		}
    	}
    	return $newMenu;
    }

}
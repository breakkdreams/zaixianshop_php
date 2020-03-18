<?php 
namespace app\admin\model;

use think\Model;
use think\Db;
use app\common\lib\SqlEasy;
use mindplay\annotations\Annotations;

/**
 * 插件数据库操作
 */
class PluginDbTableModel extends Model
{

	/**
	 * 读取安装模块的表信息
	 * @param  $[symbol] [模块标识]
	 * return true 安装成功  false 安装失败
	 */
	public function installDataTable( $symbol )
	{
		$result = true;
		$directorys = $this->getDataPathFile( $symbol );
		if( false === $directorys ){
			return ['error'=> false, 'message'=> '目录不存在，模块：'.$symbol."，检查模块目录下是否存在目录：data" ];
		}else if( is_array( $directorys ) && empty( $directorys ) ){
			return true;
		}
		$flag = true;
		$sqlEasy = new SqlEasy();
		foreach ($directorys as $key => $value) {
			$file = file_get_contents($value);
			$result = $sqlEasy->sqlExecute($file);
			if( isset($result['error'] )){
				break;
			}
		}
		return $result;
	}	

	/**
	 * 数据表卸载
	 */
	public function uninstallDataTable( $symbol )
	{
		$directorys = $this->getDataPathFile( $symbol ,'sql' , true );
		if( !empty( $directorys ) ) {
			$dropTableSql = " DROP TABLE IF EXISTS ";
			$dropTableSql = $dropTableSql . implode( ',' , $directorys );
			Db::execute( $dropTableSql );
		}
		return true;
	}

	/**
	 * 检测需求表，需求模块是否存在
	 * retutn  true / 1 存在   0不存在
	 * 
	 */
	public function chechModuleAndTableIsExists( $symbol )
	{
		$config = getModuleConfig($symbol , "data" , "config.php");
		if( empty( $config ) || !is_array( $config )  ){
			return true ;
			//配置不存在
		}
		$flag = 1;
		foreach ($config as $key => $value) {
			$tableNames = array_keys( $value ); 
			$res = $this->checkConfig( $key , $tableNames);
			if( !$res ) {
				$flag = 0;
				break;
			}
		}
		return  $flag;
	}

	/**
	 * 检查配置
	 */
	private function checkConfig( $moduleSymbol , $tableNames )
	{
		$status = 1;
		foreach ($tableNames as $key => $value) {
			$tableFile = PLUGINS_PATH . cmf_parse_name( $moduleSymbol ) . '/data' . '/' . $value . '.sql';
			if( !file_exists( $tableFile ) ){
				$status = 0;
				break;
				//模块不存在
			}
		}
		return $status;
	}

	/**
	 * 获取文件
	 * @param  	$symbol string 模块标识
	 * @param   $suffix string 文件后缀 
	 * @param  	$getFileName  true 获取文件名，不带路径不带文件后缀    false 获取带路径 带后缀的文件名
	 * @return [type]       [description]
	 */
	public function getDataPathFile( $symbol = '',$suffix = 'sql' ,$getFileName = false ) 
	{
		$sqlFileDirectory = PLUGINS_PATH . cmf_parse_name( $symbol ) . '/data';
		if( !file_exists( $sqlFileDirectory ) ){
			return false;
		}
		$directorys = scandir( $sqlFileDirectory );

		$directorys = array_diff($directorys, ['.','..'] );
		if( empty($directorys) ){
			return [];
		}
		$pattern = "/^(.*?)\.".$suffix."$/i";

		$fileNames = [];
		foreach ($directorys as $key => $value) {
			if( preg_match( $pattern ,$value ,$result) && isset( $result[0] ) ){
				if( $getFileName ){
					$fileNames[] = preg_replace( "/\.".$suffix."$/", '', $result[0] ) ;
				}else{
					$fileNames[] = $sqlFileDirectory . "/" . $result[0] ;
				}
			}
		}
		return $fileNames;
	}


	 /**
	 * 检查所有模块名字
	 */
	public function checkModuleInfo( $dir )
	{
		$dir = cmf_parse_name( $dir );
		$result = true;
		$moduleInfoList = [];
		Annotations::$config['cache']                 = false;
        $annotationManager                            = Annotations::getManager();
        $annotationManager->registry['pluginInfo']     = 'app\admin\annotation\PluginInfoAnnotation';

        	$moduleName = cmf_parse_name($dir,1);

        	$class = "\plugins\\$dir\\$moduleName"."Plugin";

        	if( !class_exists($class) ){ 
        		return [ 'error' =>false ,'message' => '类不存在：'.$class ];
        	}

        	$infoList = Annotations::ofClass($class,'@pluginInfo');

        	if( !empty($infoList) ){
        		$list = $infoList[0];
        		if( is_object($list) ){
        			$list = (array)$list;
        		}
        		if( !isset( $list ['symbol'] ) || cmf_parse_name( $dir ) != cmf_parse_name( $list ['symbol'] ) ){
        			$result = [ 'error' =>false ,'message' => '@pluginInfo信息配置错误，模块名称：'.$dir ];
        		}
        	}
        return $result;
	} 

	/**
	 * 检查所需模块是否存在 针对 config/demand.json中个需求配置
	 */
	public function checkDemandModuleIsExists( $pluginName )
	{
		//读取需求配置
		$config = getModuleConfig( $pluginName , "config" , "demand.json");
		$config = json_decode( $config , true );
		if( empty( $config ) || !is_array( $config )  ){
			//配置不存在
			return true ;
		}
		foreach ($config as $key => $value) {
			$demandSymbol = $value ['demandSymbol']; 
			$demandName = $value [ 'demandName' ];
			$demandModule = PLUGINS_PATH.cmf_parse_name( $demandSymbol ).DS.cmf_parse_name( $demandSymbol , 1 ).'Plugin.php';
			if( !file_exists( $demandModule) ){
				return [ 'status' => false , 'message'=>'此模块正常运行所需模块:'.$demandName.'('.$demandSymbol.')不存在,请检查!' ];
			}
		}
		return true;
	}

}
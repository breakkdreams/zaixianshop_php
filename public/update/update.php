<?php
//去掉警告信息
/*error_reporting(E_ERROR); 
ini_set("display_errors","Off");*/
//定义路径
define( 'ROOT' , __dir__.'/../../' );
define( 'ZY_UPDATE' , ROOT.'simplewind/zhuoyuan/update');
include_once ROOT.'public/update/FileOperate.php';
use app\common\lib\FileOperate;



sleep(2);
//检查版本信息
$oldVersion = getOldVersion();//旧版本信息

$updateVersion = getUpdateVersion();//新版本信息

//如果是更新文件就不检测版本不更新版本信息
if(  $updateVersion ['type'] != 1  ){

	if( false == $oldVersion || false == $updateVersion || empty( $oldVersion ) || empty( $updateVersion ) ){
		return json( [ 'status' => false, 'message' => '校验版本信息失败,无法操作!请确认更新包是否正确?' ] );
	}
	$res = check( $oldVersion , $updateVersion );
	if( isset( $res ['status'] ) &&  $res ['status'] == false ){
		return json ( $res );
	}
}

//执行更新操作
$res = update( ZY_UPDATE.'/app' , ROOT );

//如果是更新文件就不检测版本不更新版本信息
if(  $updateVersion ['type'] != 1  ){
	$updateVersion['update_date'] = date( 'Y-m-d H:i:s');
	//替换框架版本信息
	FileOperate::save( ZY_UPDATE.'/../version','version.json' ,json_encode($updateVersion,JSON_UNESCAPED_UNICODE) );
}
return json( $res );




/**
 *	 读取文件目录 
 *	 $current 当前目录
 *	 $webPath 网站根目录
 */
function update( $current =  ZY_UPDATE , $webPath = ROOT )
{
	
		$directory = scandir( $current );//打开目录
		foreach ( $directory as $key => $value) {
			if( $value == '.' || $value == '..' ) continue;
			try{
				//防止中文目录乱码
				$value = iconv( 'GBK' , 'utf-8' , $value );
				//更新文件源路径
				$file = $current.'/'.$value;
				$file = str_replace('//', '/', $file);
				$webFile = $webPath.'/'.$value;
				$webFile = str_replace('//', '/', $webFile);
				if ( is_dir(  $file ) ){
					if( !file_exists( $webFile ) ){
						mkdir($webFile , 0777 , true );
					}
					update( $file , $webFile );
				}else if( is_file( $file )  ){
					//将更新文件拷贝到相应目录
					if( file_exists( $webFile ) ){
						//如果文件存在执行删除文件再复制
						unlink( $webFile );
					}	
					//复制文件到指定框架目录
					copy( $file, $webFile );
				}
			} catch (\Exception $e) {
				return [ 'status' => false , 'message' => $e->getMessage() ];
			}
		}
	return  [ 'status' => true , 'message' => '系统更新完成!'];
}

	/**
	* 检查版本信息
	*/
	function check( $oldVersion  , $currentVersion )
	{
		$res = true;
		if( !isset( $currentVersion ['version'] ) ||  !isset( $oldVersion ['version'] ) ){
		    return  [ 'status' => false, 'message' => '版本信息校验失败!' ];
		}

		if(  false == versionCompare( $oldVersion [ 'version' ] , $currentVersion [ 'version' ]  )  ){
		    return  [ 'status' => false, 'message' => '当前更新版本低于旧版本,无法更新!' ];
		}
		return $res;
	}
/**
     * 版本比较
     */
    function versionCompare( $last , $curr )
    {
        $lastArr = explode( '.', $last );
        $currArr = explode( '.' , $curr );

        $isOk = true ; 

        for( $i = 0 ; $i < count( $lastArr ) ; $i ++ ){
            if( !isset( $currArr [ $i ] ) ){
                $isOk = false;
                break;
            }
            if( $currArr [ $i ] == $lastArr [ $i ] ){
                if( !isset( $currArr [ $i +1 ] ) ) {
                    $isOk = false;
                    break;
                }
                continue;
            }
            if( $currArr [ $i ] > $lastArr [ $i ]  ){
                break;
            }
            if( $currArr [ $i ] < $lastArr [ $i ] ) {
                $isOk = false;
                break;
            }

        }
        return $isOk;
    }

/**
 * 返回json数据
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
function json( $data ){
	echo json_encode( $data , JSON_UNESCAPED_UNICODE );die();
};

/**
 * 返回信息
 */
function zy_json_echo( $statsu = false , $message = '' , $data = '' , $code = 200 )
{
	$result = [
		'status' => $status ,
		'message' => $message,
		'code'	=> ( $statsu == false ) ? $code : 200
	];
	if( !empty( $data ) ) $result [ 'data' ] = $data;
	echo json_encode( $result , JSON_UNESCAPED_UNICODE );exit();	
}

/**
 * 获取就版本信息
 * @return [type] [description]
 */
function getOldVersion( )
{
	$path = ZY_UPDATE.'/../version/version.json';
	if( !file_exists( $path ) ){
		return  false;
	}
	$res = FileOperate::get( $path );
	return  json_decode( $res , true );
}


/**
 * 获取新版本信息
 */
function getUpdateVersion( )
{
	$path = ZY_UPDATE.'/app/updateInfo.json';
	if( !file_exists( $path ) ){
		return  false;
	}
	$res = FileOperate::get( $path );
	return  json_decode( $res , true );
}


/**
 * 获取版本信息
 */

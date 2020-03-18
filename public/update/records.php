<?php
//去掉警告信息
error_reporting(E_ERROR); 
ini_set("display_errors","Off");
//定义路径
define( 'ROOT' , __dir__.'/../../' );
define( "DS" , DIRECTORY_SEPARATOR );

include_once ROOT.'public/update/FileOperate.php';
use app\common\lib\FileOperate;


$data = $_POST['data'] ;
$version = isset($_POST['version'])?$_POST['version'] : '' ;
$lastVersion = $_POST[ 'lastVersion' ];
$versionName = $_POST['versionName'] ;
$updateType = $_POST['type'];
$content = $_POST[ 'content' ];

//如果是版本更新验证版本
if( $updateType == 2 ){
	if(preg_match('/[^\d|\.]/', $version, $matches)){
		return json( [ 'status'=> false, 'message'=>'版本中包含未支持的字符格式!' ]);
	}
	if( $lastVersion !=0 ){
		$compareRes = versionCompare( $lastVersion , $version );
		if( !$compareRes ){
			return json( [ 'status'=> false, 'message'=>'更新版本不能低于已有版本!']);
		}
	}
	$notCopy = [
		'/public/update',
		'/public/upload',
		'/RECORDSUZIPCOPYUPLOAD',
		'/RECORDSUZIPCOPYUPLOAD.zip'
	];
	//复制框架所有文件
	ini_set('max_execution_time','0');
	$res = xcopyAllFile( $notCopy );
	if( isset( $res ['status'] )  &&  $res [ 'status' ] == false ){
		return json($res);
	}
}else if( $updateType == 1 ) {
	//更新文件
	//复制更新记录
	$res = xcopy( $data );
	if( $res [ 'status' ] == false ){
		return json( $res );
	}

}

//压缩文件
if( !file_exists( ROOT.'RECORDSUZIPCOPYUPLOAD' ) ){
	return  json([ 'status' => false , 'message' => '压缩源文件不存在!']);
}

//保存版本信息
$versionData = [
	'pageName' => $versionName.(empty($version)?"":"_".$version),
	'version'	=> $version,
	'type' => $updateType,
	'release_date'	=> date( 'Y-m-d' , time() ), 
	'copyright' => '台州市卓远网络科技有限公司'
];
$typeName = ( $updateType == 2 ) ? 'system':'update';
$versionData [ 'pageName' ] = $typeName.'_'.$versionData['pageName']; 

$detail = $content;

//var_dump( $versionData );exit;
$path = ROOT.'RECORDSUZIPCOPYUPLOAD';
$zipPageName = 'RECORDSUZIPCOPYUPLOAD.zip';
$fileOperate = new FileOperate();
$res = $fileOperate ->save( $path , 'updateInfo.json' , json_encode( $versionData , JSON_UNESCAPED_UNICODE ) );


if( $res != true ){
	return json( $res );
}

$zip = new ZipArchive();
$zipName = ROOT.$zipPageName;
unlink($zipName);
try {
	if($zip->open( $zipName , ZipArchive::OVERWRITE)=== TRUE){
	    if(is_dir($path)){  //给出文件夹，打包文件夹
	        addFileToZip($path, $zip);
	    }else{      //只给出一个文件
	        $zip->addFile($path);
	        $zip->renameName($path);
	    }
	    $zip->close(); //关闭处理的zip文件
	   	//压缩完成后删除原文件夹
		delDirAndFile( $path ) ;
	    return json([ 'status' => true , 'message' => '导出成功!' , 'data'=> [ "packageName" => $versionData['pageName'].'.zip' , "content" => $detail ] ] );
	}else{
		return  json([ 'status' => false , 'message' => '文件压缩失败!']);
	}
} catch (\Exception $e) {
	return  json([ 'status' => false , 'message' => '异常:'.$e->getMessage()]);
}
/**
 * 压缩文件
 */
function addFileToZip($path,$zip,$subDir = ''){
    $handler = opendir($path); //打开当前文件夹由$path指定。
    $isEmpty = true;
    while( ($filename=readdir($handler))!==false ){
        if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
            if( is_dir($path.'/'.$filename) ){// 如果读取的某个对象是文件夹，则递归
                addFileToZip($path.'/'.$filename, $zip , $subDir.$filename.'/');
            }else{ //将文件加入zip对象
                $zip->addFile($path."/".$filename);
                $zip->renameName($path."/".$filename,$subDir.$filename);
            }
            $isEmpty = false;
        }
    }
    if( $isEmpty ){
    	//添加空目录
		$zip->addEmptyDir($subDir);
    }
    @closedir($path);
}




//遍历记录
/**
 * 复制及记录文件到 根目录下
 * RECORDSUZIPCOPYUPLOAD
 * 
 * */
function xcopy( $data )
{
	$path = ROOT.'RECORDSUZIPCOPYUPLOAD';
	if( file_exists( $path ) ){
		delDirAndFile($path);
	}
	mkdir( $path , 0777 );
	
	foreach ( $data as $key => $value) {
		//防止中文目录乱码
		$value['path'] = iconv( 'GBK' , 'utf-8' , $value[ 'path' ] );
		//文件源路径
		$sourcePath = ROOT.$value[ 'path' ];
		try {
			if( file_exists( $sourcePath ) ){
				$copyPath = $path.DS.$value['path'];
				if( is_dir($sourcePath) && !file_exists( $copyPath ) ){
					mkdir( $copyPath , 0777 , true );
				}
				if( is_file( $sourcePath ) ){
					$baseName = basename( $sourcePath );
					$d = str_replace( $baseName, '', $copyPath );
					trim($d,'/');
					trim($d,'\\');
					mkdir($d , 0777 , true);
					//如果是文件就复制好了
					//createDir( $path , $value["path"] );
					copy( $sourcePath, $copyPath );
				}
			}
		} catch (\Exception $e) {
			return [ 'status' => false , 'message' => $e->getMessage() ];
		}
	}
	
	return  [ 'status' => true , 'message' => '复制完成!'];
}


function createDir( $rootPath ,  $pathStr )
{
	$pathStr = str_replace("\\", '/', $pathStr );
	$dirArr = explode( "/" , $pathStr );
	if( count( $dirArr ) != 0 ) {
		for( $i = 0 ; $i < count( $dirArr )-1 ; $i ++ ) {
			mkdir( $rootPath.DS.$dirArr[$i] , 0777 );
		}
	}
}


/**
 * 返回json数据
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
function json( $data ){
	$data [ 'status' ] = ( $data [ 'status' ] == true ) ?  'success' : 'error'; 
	echo json_encode( $data , JSON_UNESCAPED_UNICODE );exit;
};


//循环删除目录和文件函数 
function delDirAndFile( $dirName ) 
{ 
	if ( $handle = opendir( "$dirName" ) ) { 
		while ( false !== ( $item = readdir( $handle ) ) ) { 
			if ( $item != "." && $item != ".." ) { 
				if ( is_dir( "$dirName/$item" ) ) { 
					delDirAndFile( "$dirName/$item" ); 
				} else { 
					if( unlink( "$dirName/$item" ) );
				} 
			} 
		} 
		closedir( $handle ); 
		if( rmdir( $dirName ) ) return true; 
	} 
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
	 * 复制框架文件包
	 */
	function xcopyAllFile( $notCopy = [] )
	{
		//递归复制文件recursion 
		$backupDir = ROOT ;
		$targetDir =  ROOT.'RECORDSUZIPCOPYUPLOAD'; 
		if( file_exists( $targetDir ) ){
			delDirAndFile( $targetDir );
		}
		try {
			recursionDir( $backupDir , $targetDir , $notCopy );
		} catch (\Exception $e) {
			return [ 'status' => false, 'message'=> $e->getMessage() ];
		}
		return true;		
	}

	/**
     * 递归目录
     *$backupDir 需要备份的目录
     *$targetDir 目标路径
     *$unBackupDir 不需要备份的目录 一维数组 目录分割符号都是被处理为  '/dir/ddd/dfd/' 格式
     *$path 当前目录相对路径
     */
    function  recursionDir( $backupDir , $targetDir , $unBackupDir = []  , $path = ''  )
    {   
        $dirList = scandir( $backupDir );
        //遍历目录
        foreach ($dirList as $key => $value) {
            if( $value ==  '.' ||  $value == '..') continue;
            //资源文件路径
            $sourcePath = $backupDir.DS.$value ; 
            //根据系统处理不同的目录分割符号
            $findPath = str_replace( DS , '/' , $path.DS.$value );
            //排除不需要备份的目录
            if( in_array( $findPath , $unBackupDir)) continue;

            /*****************复制文件开始********************/
            //防止中文目录乱码
            $sourcePath = iconv( 'GBK' , 'utf-8' , $sourcePath );
            if( is_file( $sourcePath ) ){
                try {
                    //如果是文件 复制到新的地址
                    //目标目录是否存在
                    if( !file_exists($targetDir) ){
                        //创建多级目录
                        mkdir( iconv( "UTF-8", "GBK", $targetDir ) , 0777 , true );
                    }
                    copy( $sourcePath, $targetDir.DS.$value );
                } catch (\Exception $e) {
                    // return ['status'=>false , 'message'=>'复制文件异常:'.$e->getMessage()];
                    throw new \Exception($e->getMessage(), 1);   
                }
                
            }else if( is_dir( $sourcePath ) ){
                //如果是目录
                recursionDir( $sourcePath , $targetDir.DS.$value , $unBackupDir , $findPath );
            }       
        }


    }
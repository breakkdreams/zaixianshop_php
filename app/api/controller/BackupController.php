<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\api\controller;

use think\Controller;
use think\Db;
use app\api\controller\ApiAccessAuthorityController;
// 导出数据库时，当内容过于庞大。则会报错，所以升级为256M内容
ini_set('memory_limit','256M'); //升级为256M内存

class BackupController extends Controller
{
    public function _initialize()
    {
        //$adminId = cmf_get_current_admin_id();
        //$userId  = cmf_get_current_user_id();
        /*if (empty($adminId) && empty($userId)) {
            exit("非法操作！");
        }*/
    }


    /**
     * 备份数据库
     * @return [type] [description]
     */
    public function backSql(){
        $data = $this->request->param();

        $accessAuthority = new ApiAccessAuthorityController();
        $signDe = $accessAuthority->signVerifyTest($data);
        $signDe = json_decode($signDe,true);
        if($signDe['status']=='error'){
            return zy_json_echo(false,'备份数据库：'.$signDe['message'],'',$signDe['code']);
        }

        if( !isset($data['filename'])){
            return zy_json_echo(false,'参数不能为空','',-100);
        }

        // 获取存放的路径,第一个是最终存放的地方；第二个是临时存放的地方
        $app_path = ROOT_PATH.'public/upload/backup/';
        $path = ROOT_PATH.'OIWRRDSUZJSDKLFUJIOQDB';

        // 创建文件夹
        $this->MkFolder($app_path);
        $this->MkFolder($path);

        // 文件名称
        $file_name = cmf_random_string(10).'_db_'.date('Ymd');
        $file_type = '.sql';

        // 数据库里的文件内容
        $tabledump = $this->export_database();
        // dump($tabledump);exit();

        // 把内容内容写入到文件中。生成sql文件
        $result = file_put_contents($path.'/'.$file_name.$file_type, $tabledump);

        // 压缩成zip文件
        $zip=new \ZipArchive();
        // $zipPageName = $file_name.'.zip';
        $zipPageName = $data['filename'];

        $zipName = $app_path.$zipPageName;
        try {
            if($zip->open( $zipName , \ZipArchive::OVERWRITE)=== TRUE){
                if(is_dir($path)){  //给出文件夹，打包文件夹
                    $this->addFileToZip($path, $zip);
                }else{      //只给出一个文件
                    $zip->addFile($path);
                    $zip->renameName($path);
                }
                $zip->close(); //关闭处理的zip文件
                //压缩完成后删除原文件夹
                $this->delDirAndFile( $path ) ;
                // 返回文件路径，返回文件名称
                
                $data = [
                    'url'=>cmf_get_domain().'/'.cmf_get_extra_dir().'/api/backup/downloadFile',
                    // 'filename'=>$file_name.'.zip',
                    'filename'=>$zipPageName,
                ];
                return zy_json_echo(true,'操作成功',$data);
            }else{
                return zy_json_echo(false,'文件压缩失败!','',-101);
            }
        } catch (\Exception $e) {
            return zy_json_echo(false,'异常:'.$e->getMessage(),'',-102);
        }

    }

    /**
     * 数据库导出方法
     */
    private function export_database(){
        // 获取数据库配置文件信息
        $database['database'] = config('database.database');    // 数据库名
        $database['hostname'] = config('database.hostname');    // 服务器地址
        $database['username'] = config('database.username');    // 用户名
        $database['password'] = config('database.password');    // 密码

        // 数据内容
        $tabledump = "# zy bakfile\n# version:ThinkCMF 5.0\n# time:".date('Y-m-d H:i:s')."\n# type:zy\n# website:http://www.300c.cn\n# --------------------------------------------------------\n\n\n";

        // 获取数据库中所有的表
        $tables = Db::query("SHOW TABLES FROM ".$database['database']."");

        // 获取键
        if(!empty($tables)){
            $tables_in = array_keys($tables[0])[0];
        }

        //将这些表记录到一个数组
        $tabList = array();

        foreach ($tables as $key => $value) {
            $tabList[$key] = $tables[$key][$tables_in];
        }


        for ($i=0; $i < count($tabList); $i++) { 
            // 查询当前表里面有多少个字段 （sql查询查询字段）
            $fields = Db::query("select * from information_schema.COLUMNS where TABLE_SCHEMA='".$database['database']."' and table_name='".$tabList[$i]."'");
            $numfields = count($fields);

            /*dump($numfields);
            dump($fields);
            exit();*/

            // 将每个表的表结构导出到文件 （sql查询所有的表，列出表名）
            $tables_one = Db::query("SHOW CREATE TABLE ".$tabList[$i]."");
            $tabledump .= "DROP TABLE IF EXISTS `$tabList[$i]`;\n";
            $tabledump .= $tables_one[0]['Create Table'].";\n\n";

            // 查询当前表的数据
            $tables_data = Db::query("select * from  ".$tabList[$i]."");

            //将每个表的数据导出到文件 (数据整理成sql的插入语句)
            foreach ($tables_data as $key2 => $value2) {
                $comma = "";
                $tabledump .= "INSERT INTO `$tabList[$i]` VALUES(";
                    for($j = 0; $j < $numfields; $j++) {

                        $fields_one = $fields[$j]['COLUMN_NAME'];

                        // 如果有单引号，那么就那他变成两个单引号
                        $tabledump .= $comma."'".str_replace("'","''",$tables_data[$key2][$fields_one])."'";

                        $comma = ",";
                    }
                $tabledump .= ");\n";
            }
            $tabledump .= "\n";

        }
        
        // dump($tabledump);
        // exit();

        return $tabledump;
    }



    /**
     * 备份源码-显示文件夹
     * @return [type] [description]
     */
    public function backFile(){
        $data = $this->request->param();

        $accessAuthority = new ApiAccessAuthorityController();
        $signDe = $accessAuthority->signVerifyTest($data);
        $signDe = json_decode($signDe,true);
        if($signDe['status']=='error'){
            return zy_json_echo(false,'备份源码：'.$signDe['message'],'',$signDe['code']);
        }


        if(empty($data['path'])){
            $path = '';
        }else{
            $path = $data['path'];
        }

        // 获取目录
        $list = $this->getDir($path);

        return zy_json_echo(true,'操作成功',$list);
    }


    /**
     * 备份源码-获取目录 
     */
    private function getDir( $dir = '' )
    {
        $list = [];
        $path =  ROOT_PATH ;
        if( !empty( $dir ) ) $path = $path . DS . $dir;

        $dirList = scandir($path);
        foreach ($dirList as $key => $value) {
            if( $value == '.' || $value == '..' ) continue;
            $filename = $path.DS.$value;
            $list [] = [
                'name' => $value,
                'node' => is_dir($filename)?1:0
            ];
        }
        return $list;
    }




    /**
     * 备份源码-源码导出方法
     * $data = []  传递的参数为一维数组  保存的是路径信息  是不需要备份的文件
     * @return [type] [description]
     */
    public function export_file(){
        $data = $this->request->param();

        $accessAuthority = new ApiAccessAuthorityController();
        $signDe = $accessAuthority->signVerifyTest($data);
        $signDe = json_decode($signDe,true);
        if($signDe['status']=='error'){
            return zy_json_echo(false,'备份源码：'.$signDe['message'],'',$signDe['code']);
        }


        if( !isset($data['list']) || !isset($data['filename'])){
            return zy_json_echo(false,'参数不能为空','',-201);
        }
        if( $data['list']=='sdfs5df45sd4f5sdf65sdfsd65f4' ){
            $data['list'] = [];
        }

        // 复制文件存放的地方
        $path = ROOT_PATH.'OIWRRDSUZJSDKLFUJIOQ';
        try {
             $xcopy = $this->recursionDir(ROOT_PATH,$path,$data['list']);
             //dump($xcopy);
        } catch (\Exception $e) {
            //文件复制失败，那么同时也要把压缩包给删除了
            $this->delDirAndFile( $path ) ;
            return zy_json_echo(false,'操作失败：'.$e->getMessage(),'',-201);
        }
       //dump('复制成功');dump("555151515");exit;
        

        /*if($xcopy['status']==false){
            return zy_json_echo(false,$xcopy['message'],'',-202);
        }*/

        $zip=new \ZipArchive();

        // 获取存放的路径
        $app_path = ROOT_PATH.'public/upload/backup/';
        // 创建文件夹
        $this->MkFolder($app_path);

        // 文件名称
        // $file_name = cmf_random_string(10).'_code_'.date('Ymd').'.zip';
        $file_name = $data['filename'];

        $zipPageName = $file_name;

        $zipName = $app_path.$zipPageName;
        try {
            if($zip->open( $zipName , \ZipArchive::OVERWRITE)=== TRUE){
                if(is_dir($path)){  //给出文件夹，打包文件夹
                    $this->addFileToZip($path, $zip);
                }else{      //只给出一个文件,那么先创建一个文件一个文件夹，然后再吧文件放入其中
                    $zip->addFile($path);
                    $zip->renameName($path);
                }
                $zip->close(); //关闭处理的zip文件
                //压缩完成后删除原文件夹
                $this->delDirAndFile( $path ) ;
                // 返回文件路径，返回文件名称
                
                $data = [
                    'url'=>cmf_get_domain().'/'.cmf_get_extra_dir().'/api/backup/downloadFile',
                    'filename'=>$file_name,
                ];
                return zy_json_echo(true,'操作成功',$data);
            }else{
                return zy_json_echo(false,'文件压缩失败!','',-203);
            }
        } catch (\Exception $e) {
            return zy_json_echo(false,'异常:'.$e->getMessage(),'',-204);
        }

    }





// ========================= 文件压缩操作 START


    /**
     * 压缩文件
     */
    private function addFileToZip($path,$zip,$subDir = ''){

        $handler = opendir($path); //打开当前文件夹由$path指定。
        $isEmpty = true;
        while( ($filename=readdir($handler))!==false ){
            if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
                if( is_dir($path.'/'.$filename) ){// 如果读取的某个对象是文件夹，则递归
                    $this->addFileToZip($path.'/'.$filename, $zip , $subDir.$filename.'/');
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

    /**
     * 递归目录
     *$backupDir 需要备份的目录
     *$targetDir 目标路径
     *$unBackupDir 不需要备份的目录 一维数组 目录分割符号都是被处理为  '/dir/ddd/dfd/' 格式
     *$path 当前目录相对路径
     */
    private function  recursionDir( $backupDir , $targetDir , $unBackupDir = []  , $path = ''  )
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
                } catch (Exception $e) {
                    // return ['status'=>false , 'message'=>'复制文件异常:'.$e->getMessage()];
                    throw new \Exception($e->getMessage(), 1);
                    
                }
                
            }else if( is_dir( $sourcePath ) ){
                //如果是目录
                $this->recursionDir( $sourcePath , $targetDir.DS.$value , $unBackupDir , $findPath );
            }       
        }


    }




    /**
     * [循环删除目录和文件函数]
     * @param  [type] $dirName [文件名称]
     * @return [type]          [description]
     */
    private function delDirAndFile( $dirName ) 
    { 
        if ( $handle = opendir( "$dirName" ) ) { 
            while ( false !== ( $item = readdir( $handle ) ) ) { 
                if ( $item != "." && $item != ".." ) { 
                    if ( is_dir( "$dirName/$item" ) ) { 
                        $this->delDirAndFile( "$dirName/$item" ); 
                    } else { 
                        chmod("$dirName/$item", 0777);
                        if( unlink( "$dirName/$item" ) );
                    } 
                } 
            } 
            closedir( $handle ); 
            chmod($dirName, 0777);
            if( rmdir( $dirName ) ); 
        } 
    }


// ========================= 文件压缩操作 END





    /**
     * 下载文件方法
     */
    public function downloadFile(){
        $data = $this->request->param();

        $accessAuthority = new ApiAccessAuthorityController();
        $signDe = $accessAuthority->signVerifyTest($data);
        $signDe = json_decode($signDe,true);
        if($signDe['status']=='error'){
            return zy_json_echo(false,'下载文件：'.$signDe['message'],'',$signDe['code']);
        }

        if( empty($data['filename']) ){
            return zy_json_echo(false,'参数不能为空','',-211);
        }

        // $company = $data['company'];
        $filename = $data['filename'];
        // 获取存放的路径
        $app_path = ROOT_PATH.'public/upload/backup/'.$filename;

        if( file_exists($app_path) ){
            $data = [
                'url'=>cmf_get_domain().'/'.cmf_get_extra_dir().'/upload/backup/'.$filename,
            ];
            return zy_json_echo(true,'操作成功',$data);
        }

        return zy_json_echo(true,'资源文件不存在,或已经删除','',-212);
    }



    /**
     * 下载文件到浏览器
     *
     * @param string $filename 文件路径
     * @param array  $title    输出的文件名
     * @return void
     */
    private function output_for_download($filename, $title)
    {
        $file  =  fopen($filename, "rb");
        Header( "Content-type:  application/octet-stream ");
        Header( "Accept-Ranges:  bytes ");
        Header( "Content-Disposition:  attachment;  filename= $title");
        while (!feof($file)) {
            echo fread($file, 8192);
            ob_flush();
            flush();
        }
        fclose($file);
    }



    /**
     * 删除文件方法
     */
    public function deleteFile(){
        $data = $this->request->param();

        $accessAuthority = new ApiAccessAuthorityController();
        $signDe = $accessAuthority->signVerifyTest($data);
        $signDe = json_decode($signDe,true);
        if($signDe['status']=='error'){
            return zy_json_echo(false,'删除文件：'.$signDe['message'],'',$signDe['code']);
        }

        if( empty($data['filename']) ){
            return zy_json_echo(false,'参数不能为空','',-211);
        }
        // $company = $data['company'];
        $filename = $data['filename'];

        // 获取存放的路径
        $app_path = ROOT_PATH.'public/upload/backup/'.$filename;

        if( file_exists($app_path) ){
            @unlink($app_path);
            return zy_json_echo(true,'操作成功');
        }else{
            return zy_json_echo(false,'资源文件不存在,或已经删除','',-212);
        }

    }


    /**
     * 创建文件夹
     * PHP判断文件夹是否存在和创建文件夹的方法（递归创建多级目录）
     * @param  string $path [请求文件路径]
     */
    private function MkFolder($path){
        if(!is_readable($path)){
            $this->MkFolder( dirname($path) );
            if(!is_file($path)) mkdir($path,0777);
        }
    }

}

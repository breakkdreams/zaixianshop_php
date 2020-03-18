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
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
use app\common\controller\FileTreeController; // 生成文件树的类
// 导出数据库时，当内容过于庞大。则会报错，所以升级为256M内容
ini_set('memory_limit','256M'); //升级为256M内存


class BackupController extends HomeBaseController
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
     * 前台用户首页(公开)
     */
    public function index()
    {
        dump(config('database.hostname'));

        /*$id   = $this->request->param("id", 0, "intval");
        $userQuery = Db::name("User");
        $user = $userQuery->where('id',$id)->find();
        if (empty($user)) {
            session('user',null);
            $this->error("查无此人！");
        }
        $this->assign($user);
        return $this->fetch(":index");*/
    }


    /**
     * 备份数据库
     * @return [type] [description]
     */
    public function backSql(){
        // 获取存放的路径,第一个是最终存放的地方；第二个是临时存放的地方
        $app_path = ROOT_PATH.'public/upload/backup/';
        $path = ROOT_PATH.'RECORDSUZIPCOPYUPLOADDB';

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
        $zipPageName = $file_name.'.zip';

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
                    'filename'=>$file_name,
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
        $obj = new FileTreeController();
        $list = $obj->dirList();

        $this->assign( 'list' , $list );
        return $this->fetch('back_file');
    }


    /**
     * 备份源码-源码导出方法
     * @return [type] [description]
     */
    public function export_file(){
        $data = $this->request->param();
        if(empty($data['data'])){
            return zy_json_echo(false,'参数不能为空','',-201);
        }
        dump($data);exit();

        $xcopy = $this->xcopy($data['data']);
        if($xcopy['status']==false){
            return zy_json_echo(false,$xcopy['message'],'',-202);
        }
        $zip=new \ZipArchive();

        // 获取存放的路径
        $app_path = ROOT_PATH.'public/upload/backup/';
        // 创建文件夹
        $this->MkFolder($app_path);
        // 文件名称
        $file_name = cmf_random_string(10).'_code_'.date('Ymd').'.zip';


        $zipPageName = $file_name;
        $path = ROOT_PATH.'RECORDSUZIPCOPYUPLOAD';

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




    //遍历记录
    /**
     * 复制及记录文件到 根目录下
     * RECORDSUZIPCOPYUPLOAD
     * 
     * */
    private function xcopy( $data )
    {
        $path = ROOT_PATH.'RECORDSUZIPCOPYUPLOAD';
        // 创建文件夹
        $this->MkFolder($path);

        if( file_exists( $path ) ){
            $this->delDirAndFile($path);
            mkdir( $path );
            chmod( $path , 0777 );
        }
        try {
            foreach ( $data as $key => $value) {
                //防止中文目录乱码
                $value = iconv( 'GBK' , 'utf-8' , $value );
                //文件源路径
                $sourcePath = ROOT_PATH.$value;
                if( file_exists( $sourcePath ) ){
                    //复制文件到指定框架目录
                    $fileArr = array_filter( explode( '/' , $value ) );
                    $file = ROOT_PATH;
                    $newPath = $path;
                    foreach ( $fileArr as $kk => $vv ) {
                        $file .= $vv;
                        $newPath .= '/'.$vv;
                        if( file_exists( $file ) ){
                            if( is_dir( $file ) ){
                                if( !file_exists( $newPath ) ){
                                    mkdir( $newPath );
                                    chmod( $newPath ,0777 );
                                    //echo '文件夹:'.$newPath;
                                }
                            }
                            if( is_file( $file ) ){
                                //echo '复制:'.$newPath;
                                copy( $sourcePath, $newPath );
                            }
                        }
                        $file .= '/';
                    }
                }
            }
        } catch (\Exception $e) {
            return [ 'status' => false , 'message' => $e->getMessage() ];
        }
        return  [ 'status' => true , 'message' => '复制完成!'];
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
                        if( unlink( "$dirName/$item" ) );
                    } 
                } 
            } 
            closedir( $handle ); 
            if( rmdir( $dirName ) ); 
        } 
    }


// ========================= 文件压缩操作 END





    /**
     * 下载文件方法
     */
    public function downloadFile(){
        $data = $this->request->param();

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

        return zy_json_echo(true,'资源文件不存在,或已经删除','',-211);
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

        // $company = $data['company'];
        $filename = $data['filename'];

        // 获取存放的路径
        $app_path = ROOT_PATH.'public/upload/backup/'.$filename;

        if( file_exists($app_path) ){
            @unlink($app_path);
            return zy_json_echo(true,'操作成功');
        }else{
            return zy_json_echo(false,'资源文件不存在,或已经删除','',-211);
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

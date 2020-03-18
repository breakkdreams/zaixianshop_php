<?php
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\Cache;
use think\Request;
use app\admin\model\AdminMenuModel;
use app\admin\controller\AuthorizationController;
use app\admin\model\AuthorizationModel;
use app\common\lib\FileOperate;
use app\common\lib\Upload;
use tree\Tree;

/**
 *系统更新控制器201911081503
 */
class UpdateController extends AdminBaseController
{
    /**
     * 管理首页
     */
    public function index()
    {
        $param = $this->request->param();
        if( isset( $param [ 'name' ] ) ){
            return $this->downloadUpdatePage( $param [ 'name' ] );
        }
        //读取版本信息
        $version = $this->getVersionInfo();
        //读取最新更新版本 服务器版本信息
        $lastVersion = $this->getLastVersion();
        //获取域名
        $request = Request::instance();
        $domain = $request->domain();
        $substr = cmf_get_extra_dir();
        $updateUrl = ( !empty( $substr ) ) ? $domain.'/'.$substr.'/update/update.html' : $dumain.'/update/update.html';
        $substr = ( empty( $substr ) ) ? '' : $substr.'/';
        $updateOperateUrl = $substr.'update/update.php';
        $this->assign( 'updateOperateUrl' , $updateOperateUrl );
        $this->assign( 'update' , $updateUrl );

        $this->assign( 'lastVersion' , $lastVersion );
        $this->assign( 'version' , $version );
        return $this->fetch();
    }

    /**
     * 上传文件安装包
     */
    public function uploadFile()
    {
        $sourceRootPath = ROOT_PATH.'simplewind'.DS.'zhuoyuan';
        if( !$this->request->isAjax() ){
            return zy_json_echo( false , '请求类型错误!' );
        }
        $upload = new Upload();
        //升级包固定存放于 simplewind/zhuoyuan/update下
        $upload->setSize(50);
        $upload->setResourceInfo($_FILES);
        //存放目录
        $upload->rootPath = $sourceRootPath.DS.'update';
        $result = $upload->uploadFile();
        if( $result[ 'status' ] == true ){
            $file = $result[ 'data' ];
            $result [ 'data' ] = iconv( "GBK" , "UTF-8", basename( $result [ 'data' ] ) ) ;
            //解压文件到 zhuoyuan/update/app 目录下:
            
            //清空app目录下所有文件
            $appDir = ROOT_PATH.'simplewind'.DS.'zhuoyuan'.DS.'update'.DS.'app'; 
            if( file_exists( $appDir ) ){
                $this->deldir( $appDir );
            }
            $res = $this->Uzip( $appDir , $file );
            unlink( $file );
            if( $res != true ){
                $this->deldir( $appDir );
                $result [ 'status' ] = false;
                $result [ 'message' ] = '文件上传成功,但解压缩失败,需要重新上传文件!';
            }
        }
        return zy_json_echo( $result[ 'status' ] , $result [ 'message' ] , $result [ 'data' ] );
    }

    /**
     * 删除目录下所有文件
     * @param  [type] $dir [description]
     * @return [type]      [description]
     */
    private function deldir( $dir , $isRoot = true ) 
    {
        //先删除目录下的文件：
        chmod( $dir , 0777 );
        $dh = opendir( $dir );
        while ( $file = readdir( $dh ) ) {
            if( $file != "." && $file != "..") {
                $fullpath = $dir."/".$file;
                if( !is_dir( $fullpath ) ) {
                    unlink( $fullpath );
                } else {
                    $this->deldir( $fullpath , false );
                }
            }
        }
        closedir($dh);
        if( !$isRoot ){
            rmdir( $dir );
        }
    }

    /**
     * 解压文件
     */
    private function Uzip( $outPath = '' , $fileName = '' )
    {
        $res = true;
        $zip = new \ZipArchive();
        try {
            $openRes = $zip->open( $fileName );
            if ($openRes === TRUE) {
              $zip->extractTo($outPath);
              $zip->close();
            }
        } catch (\Exception $e) {
            $res = [ 'status' => false , 'message' => '异常:'.$e->getMessage() ];
        }
        return $res;
    }
    

    /**
     * 检查版本信息
     * @return [type] [description]
     */
    public function checkVersionInfo()
    {
        $currentVersion = $this->getVersionInfo();
        $oldVersion = $this->getCurrentVersionInfo();
        $res = $this->versionDiff( $currentVersion , $oldVersion );
        return $res;
    }

    /**
     * 获取框架当前版本信息
     */
    private function getVersionInfo()
    { 
        ///simplewind/zhuoyuan/version
        $path = ROOT_PATH.'simplewind'.DS.'zhuoyuan'.DS.'version'.DS.'version.json';
        if( !file_exists( $path ) ){
            return  [];
        }
        $res = FileOperate::get( $path );
        return  json_decode( $res , true );
    }

    /**
     * 获取当前升级包版本信息
     */
    private function getCurrentVersionInfo()
    {
        $path = ROOT_PATH.'simplewind'.DS.'zhuoyuan'.DS.'update'.DS.'app'.DS.'updateInfo.json';
        if( !file_exists( $path ) ){
            return  [];
        }
        $res = FileOperate::get( $path );
        return  json_decode( $res , true );
    }

    /**
     *版本比较 
     */
    private function versionDiff( $currentVersion = [] , $oldVersion = [] )
    {
        $res = true;
        if( !isset( $currentVersion ['version'] ) ||  !isset( $oldVersion ['version'] ) ){
            return  [ 'status' => false, 'message' => '版本信息校验失败!' ];
        }
        $currentNum = str_replace('.' , '' , $currentVersion );
        $oldNum = str_replace('.' , '' , $oldVersion );
        if(  $oldNum >= $currentNum ){
            return  [ 'status' => false, 'message' => '当前更新版本低于旧版本,无法更新!' ];
        }
        return $res;
    }

    /**
     *  获取最新版本信息
     */
    public function getLastVersion()
    {

        $url = Config("admin_module_config.api_host").'/api/api_update/topNews';
        $data = [ 
            'type' => 2 //框架信息
        ];
        $res = $this->sendPackage($url , $data );
        $res = json_decode( $res , true );
        if( !isset( $res['status'] ) || $res [ 'status' ] == 'error' ){
            return [];
        }
        $data = $res [ 'data' ] ;
        $data [ 'add_time' ] = date( 'Y-m-d H:i:s' , $data [ 'add_time' ] ); 
        return $data;
    }

    /**
     * 导出更新包
     */
    public function exportUpdatePage()
    {
        $lastVersion = $this->getLastVersion();
        $this->assign( 'lastVersion' , $lastVersion );
        return $this->fetch('make_update_page');
    }


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
            //dump( $result );exit;
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
        $this->assign( 'list' , $list );
        return $this->fetch('dir_list');
    }


    /**
     * 保存记录
     */
    public function saveRecords()
    {
        $data = $this->request->param();

        $version = $data [ 'version' ];
        $versionName = $data [ 'versionName' ];
        $list = $data [ 'data' ];
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
     * 下载更新包
     * @return [type] [description]
     */
    public function downloadUpdatePage( $name = 'update.zip' )
    {
        $path = ROOT_PATH.'RECORDSUZIPCOPYUPLOAD.zip';
        if( file_exists($path) ){
            $this->output_for_download( $path , $name );
        }
        echo '资源文件不存在,或已经删除.';
    }
    
    public function downLoad( $name ){
        $this->assign( 'name' , $name );
        return $this->fetch('down_load'); 
    }

    /**
     * uploadPackage
     */
    public function uploadPackage()
    {
        if( !$this->request->isPost() ){
            return zy_json_echo(false, '请求类型错误!');
        }
        $param = $this->request->param();

        $name = $param [ 'name' ] ;
        $path = ROOT_PATH.'RECORDSUZIPCOPYUPLOAD.zip';
        if( !file_exists( $path ) ){
            return zy_json_echo( false , '资源文件不存在,或已经删除.' );
        }
        $data = [
            'name' => $name,
            'version' => $param['version'],
            'type' => $param['type']
        ];     


        if(file_exists($path)){
            $opts = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'content-type:application/x-www-form-urlencoded',
                    'content' => ''//file_get_contents($path)
                )
            );

            $url = Config("admin_module_config.api_host")."/api/api_update/updateRecord";//.http_build_query($data);

            /*try {
                $context = stream_context_create($opts);
                $res = file_get_contents($url, false, $context);
            } catch (\Exception $e) {
                return zy_json_echo(false , $e->getMessage());
            }
            */
            $res = $this->sendPackage( $url , $data);
            $res = json_decode( $res , true );

            if( !isset($res['status']) || $res ['status'] == 'error' ){
                return zy_json_echo(  false  , '上传更新包失败!' );
            }
            if( isset( $param['content'] ) && !empty( $param['content'] ) ){
                $id = $res [ 'data' ] ;
                $content = [
                    'id' => $id , 
                    'detail' => $param['content']
                ] ;
                $url = Config( "admin_module_config.api_host" )."/api/api_update/updateDetail";
                $dd = $this->sendPackage ( $url , $content);
            }
            return zy_json_echo( true , '上传更新成功!' );

        }
        
    }


    private function sendPackage( $url , $data )
    {
        $ch = curl_init();

        //指定URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // 在尝试连接时等待的秒数

        //设定请求后返回结果
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //声明使用POST方式来进行发送
        curl_setopt($ch, CURLOPT_POST, 1);
        $data = http_build_query($data);
        //发送什么数据呢
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


        //忽略证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //忽略header头信息
        curl_setopt($ch, CURLOPT_HEADER, 0);

        //设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);

        //发送请求
        $output = curl_exec($ch);

        //关闭curl
        curl_close($ch);

        //返回数据
        return $output;
    }

    /**
     * 更新推送
     */
    public function updatePush()
    {
        if( $this->request->isPost() ){
            $lastVersion = $this->getLastVersion();
            $currentVersion = $this->getCurrentVersion();

            $res = $this->versionCompare( $currentVersion , $lastVersion );
            if( $res ){
                return zy_json_echo(true , '有最新的版本!' , $lastVersion );
            }
            return zy_json_echo(false , '已经是最新的!' );
        }
        return zy_json_echo(false , '请求类型错误!' , 0);
    }


    /**
     * 版本比较
     */
    private function versionCompare( $last , $curr )
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
     * 在线更新
     */
    public function onlineUpdate()
    {
        //获取最新版下载地址
        $newVersionDownloadAddr = Config('admin_module_config.api_host').'/api/api_update/getVersionDownloadAddr';
        $res = $this->sendPackage( $newVersionDownloadAddr , ['type'=>2] );
        $res = json_decode( $res , true );
        if( !isset( $res [ 'status' ] ) || $res [ 'status' ] == 'error' ){
            return zy_json_echo(false , '未获取到数据');
        }
        $url = Config('admin_module_config.api_host').'/'.$res [ 'data' ] [ 'source' ];

        $sourceRootPath = ROOT_PATH.'simplewind'.DS.'zhuoyuan'.DS.'update';

        $res = $this->getFile( $url , $sourceRootPath , $res [ 'data' ] [ 'name' ]  );
   
        if( false == $res ){
            return zy_json_echo(false , '更新包下载失败,请稍后再试!');
        }

        //解压到对应文件
        //清空app目录下所有文件

        $appDir = ROOT_PATH.'simplewind'.DS.'zhuoyuan'.DS.'update'.DS.'app'; 
        if( file_exists ( $appDir ) ){
            $this->deldir( $appDir );
        }
        $file = $res[ 'save_path' ];
        $res = $this->Uzip( $appDir , $file );
        
        if( $res != true ){
            $this->deldir( $appDir );
            return zy_json_echo( false,'文件上传成功,但解压缩失败,需要重新上传文件!' );
        }
        //解压完成删除 文件
        if( file_exists ( $file ) ) unlink( $file );
        return zy_json_echo( true, '下载成功!' , $res );
    }



    /**
     * 下载文件
     */
    private function getFile( $url, $save_dir = '', $filename = '' ) {
        if (trim($url) == '') {
            return false;
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }

        $source = file_get_contents($url);
        if( empty($source) ){
            return false;
        }
        try {
            file_put_contents( $save_dir.DS.$filename, $source );
        } catch (\Exception $e) {
            return false;
        }
        return [
            'file_name' => $filename,
            'save_path' => $save_dir .DS. $filename
        ];
        
        //dump($source);exit();
        //获取远程文件所采用的方法
  
        /*$ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        curl_close($ch);
        dump($content);
       
        $size = strlen($content);
        dump($size);
        //文件大小
        $fp2 = @fopen($save_dir .DS. $filename, 'a');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        return array(
            'file_name' => $filename,
            'save_path' => $save_dir .DS. $filename
        );*/
    }

}

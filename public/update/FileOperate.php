<?php 
namespace app\common\lib;

/**
 *文件操作类
 */
class FileOperate
{
	/**
	 * 保存数据到文件
	 * @param  string $fileName 文件路径
	 * @param  string $data       [description]
	 */
	public static function save( $path = '' , $fileName = "" , $data = "" )
    {
        
        	try {
                if( !file_exists( $path ) ){
    	        	//create file
    	            mkdir( $path , 0777 , true ); 

    		        //设置权限
    		        chmod( $path , 0777 );
		        }
                $fileName = $path.'/'.$fileName;
                if( is_array( $data ) ){
                    $data = json_encode( $data , JSON_UNESCAPED_UNICODE );
                }
	            //lock file and save data
	            file_put_contents( $fileName , $data , LOCK_EX );

	        } catch (\Exception $e) {
	            return [ 'status' => false , 'message' => '异常:'.$e->getMessage() ] ;
	    	}
        	return true;
    }


    /**
     * 获取数据
     *  @param  $fileName       文件路径
     */
    public static function get( $fileName = "" )
    {
        if( file_exists( $fileName ) ){
        	/*$arr = phpinfo( $fileName );
        	$extension = $arr [ 'extension' ] ;*/
            $exts = explode( '.', $fileName );
            $end = end($exts);
            if( $end == 'php' ){
                $data = require( $fileName );
            }else{
                $data = file_get_contents( $fileName );
            }
            return $data;
        }else{
            return  "";
        }
    }
	
}
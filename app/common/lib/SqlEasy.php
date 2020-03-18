<?php
namespace app\common\lib;

use think\Config;
use think\Db;
/**
 * 执行sql文件
 */
class SqlEasy
{
    /**
     * 执行mysql.sql文件
     */
    public function sqlExecute( $sql )
    {
        $result = true;
        $sql = trim( $sql );
        $sqlArr = explode(';', $sql);
        foreach ( $sqlArr as $sqlstr ) {
            $str = trim( $sqlstr );
            if( !empty( $str ) ){
                try {
                    Db::execute( $str );
                } catch (\Exception $e) {
                    $result = [ 'error' => '100' , 'message' => $e->getMessage() ];
                    break;
                }
            }
            
        }
        return $result;
    }
}




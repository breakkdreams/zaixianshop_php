<?php
namespace app\common\behavior;
use think\Exception;

/**
 * 钩子名称 add_remind
 *使用方法,在需要添加提醒的地方使用 hook( 'add_remind' ,  $data ); add_remind是钩子名称   $data为提醒的数据
 *添加例子: 删除任务提醒 , 删除任务提醒标识 deleteTask , 
 *添加提醒时使用
 */
class AddRemind
{
    public function run( $params ){

        $data = [
            'module_name'           =>      isset( $params [ 'module_name' ] ) ? $params [ 'module_name' ] : '', //模块标识
            'mark'                  =>      isset( $params [ 'mark' ] ) ? $params [ 'mark' ] : '',//触发标识 
            'table_name'            =>      isset( $params [ 'table_name' ] ) ? $params [ 'table_name' ] : '', //表名
            'table_data'            =>      isset( $params [ 'table_data' ] ) ? $params [ 'table_data' ] : '' //表数据
        ]; //参数格式化

        try {
            $class = "\\plugins\\remind\\controller\\AdminIndexController";
            if( class_exists( $class ) ){
                $obj = new $class();
                $result = $obj->triggerAddRemind( $data , true );
            }
        } catch (\Exception $e) {
            throw new \Exception("模块调用异常:".$e->getMessage(), 1);    
        }
        
    }
}
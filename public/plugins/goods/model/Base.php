<?php
namespace plugins\goods\model;
use think\Model;
use think\Db;


class Base extends Model {
	/**
	 * 获取空模型
	 */
	public function getEModel($tables){
		$rs =  Db::query('show columns FROM `'.config('database.prefix').$tables."`");
		$obj = [];
		//dump($rs);die();
		if($rs){
			foreach($rs as $key => $v) {
				if (isset($v['Field'])) {
					$obj[$v['Field']] = $v['Default'];
					if($v['Key'] == 'PRI')$obj[$v['Field']] = 0;
				} else {
					$obj[$v['field']] = $v['default'];
					if($v['key'] == 'PRI')$obj[$v['field']] = 0;
				}
				
			}
		}
		return $obj;
	}
}
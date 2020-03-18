<?php
namespace sign;


/*
 * @Title: PHP实现sign
 * @Author: yyy@300c.cn
 * @Date: 2019-08-31 12:18:16
 * @LastEditors: yyy@300c.cn
 * @LastEditTime: 2019-08-31 15:43:27
 */
class Generatesign {


	/**
	 * [sing 生成签名/签名验证]
	 * 
	 * @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
	 * @param	string	$getsign	当operation为解密时，填入签名
	 *
	 * ------------------- eg:start ---------------------
	 * $data = [
	 *     'username' => '用户账号,没有时传空字符串',
	 *     'age'      => '用户年龄,没有时传0',
	 * ];
	 * ------------------- eg:end -----------------------
	 *
	 * @return [type] [description]
	 */
	public static function signature($data,$operation='ENCODE',$getsign=''){
		ksort($data);
	    //2. 生成以&符链接的key=value形式的字符串
	    $paramString = http_build_query($data);
	    //$paramString = urldecode(http_build_query($data));
	    $sign = strtoupper(MD5($paramString));

	    //返回加密、解密值
	    //解密：
	    //验证签名是否匹配
	    //验证签名时间是否超过一个小时了
	    if($operation=='ENCODE'){
		    return zy_array(true,'操作成功',['sign'=>$sign],200);    	
	    }elseif($operation=='DECODE'){
	    	if($sign==$getsign){
	    		if ($data['timestamp']+3600 < time()) {
	    			return zy_array(false,'签名时间过期','',-102);
	    		}else{
	    			return zy_array(true,'操作成功','',200);
	    		}
	    	}else{
				return zy_array(false,'签名错误','',-101);
	    	}
	    }
	}


	/*
	 * 私有返回状态_返回状态
	 * 
	 * @status [状态] 200操作成功/-100状态码不能为空，操作失败/-101签名错误/-102签名时间过期
	 * @param  [type] $status [*状态]
	 * @param  [type] $data [数据组]
	 * @param  [type] $page [翻页数据]
	 * @param  [type] $message [返回信息]
	 *
	 * ------------------- eg:start ---------------------
	 * $data = [
	 *     'status' => 'success/error（成功/失败）',
	 *     'code'      => '状态码',
	 *     'message'      => '详细信息',
	 *     'data'      => '返回数据',
	 *     'page'      => '翻页',
	 * ];
	 * ------------------- eg:end -----------------------
	 *
	 * @return [json] [json]
	 */
	private static function _return_status($status='',$data='',$pages='', $message='') 
	{
		$status = $status;	//状态
		$pages = $pages;	//成功：返回数据组
		$data = $data;	//成功：返回数据组
		//==================	操作失败-验证 START
			switch ($status) {
				case 200:	//操作成功
					$result = [
						'status'=>'success',
						'code'=>200,
						'message'=>'操作成功',
					];
					if($message){
						$result['message']= $message;
					}
					if($data){
						$result['data']=$data;
					}
					if($pages){
						$result['page']=$pages;
					}

					return json_encode($result,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
					break;
				
				case -101:	//签名错误
					$result = [
						'status'=>'error',
						'code'=>-101,
						'message'=>'签名错误',
					];
					return json_encode($result,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
					break;
				
				case -102:	//签名时间过期
					$result = [
						'status'=>'error',
						'code'=>-102,
						'message'=>'签名时间过期',
					];
					return json_encode($result,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
					break;
				
				default:
					$result = [
						'status'=>'error',
						'code'=>-100,
						'message'=>'操作失败',	//帐号已锁定,无法登录
					];
					return json_encode($result,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
					break;
			}
		//==================	操作失败-验证 END
	}


	/**
	* 产生随机字符串
	*
	* @param    int        $length  输出长度
	* @param    string     $chars   可选的 ，默认为 0123456789
	* @return   string     字符串
	*/
	private static function random($length, $chars = '0123456789') {
		$hash = '';
		$max = strlen($chars) - 1;
		mt_srand();
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
		return $hash;
	}



}
<?php 
//获取USER AGENT
$agent = strtolower($_SERVER['HTTP_USER_AGENT']);

//分析数据
$is_pc = (strpos($agent, 'windows nt')) ? true : false;  
$is_iphone = (strpos($agent, 'iphone')) ? true : false;  
$is_ipad = (strpos($agent, 'ipad')) ? true : false;  
$is_android = (strpos($agent, 'android')) ? true : false;  
 
 $str ='100';
//输出数据
    if($is_pc){  
        $str = "这是PC";  
    }  
    if($is_iphone){  
        $str =  "这是iPhone";  
    }  
    if($is_ipad){  
        $str =  "这是iPad";  
    }  
    if($is_android){  
       $str = "这是Android";  
    }  
echo '<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
'.$agent.' 
'.$str.'
</body>
</html>';
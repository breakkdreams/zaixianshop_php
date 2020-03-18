var ZY = ZY || {};



ZY.conf = window.conf;
/* 基础对象检测 */
ZY.conf || $.error("ZYMart基础配置没有正确加载！");
if(ZY.conf.ROUTES)ZY.conf.ROUTES = eval("("+ZY.conf.ROUTES+")");


ZY.blank = function(str,defaultVal){
	if(str=='0000-00-00')str = '';
	if(str=='0000-00-00 00:00:00')str = '';
	if(!str)str = '';
	if(typeof(str)=='null')str = '';
	if(typeof(str)=='undefined')str = '';
	if(str=='' && defaultVal)str = defaultVal;
	return str;
}


ZY.upload = function(opts){
	var _opts = {};
	_opts = $.extend(_opts,{duplicate:true,auto: true,swf: ZY.conf.STATIC + '/Uploader.swf',server:ZY.conf.GUPPATH},opts);
	var uploader = WebUploader.create(_opts);
	uploader.on('uploadSuccess', function( file,response ) {
	    var json = response._raw;
	    if(_opts.callback)_opts.callback(json,file);
	});
	uploader.on('uploadError', function( file ) {
		if(_opts.uploadError)_opts.uploadError();
	});
	uploader.on( 'uploadProgress', function( file, percentage ) {
		percentage = percentage.toFixed(2)*100;
		if(_opts.progress)_opts.progress(percentage);
	});
    return uploader;
}



ZY.blank = function(str,defaultVal){
	if(str=='0000-00-00')str = '';
	if(str=='0000-00-00 00:00:00')str = '';
	if(!str)str = '';
	if(typeof(str)=='null')str = '';
	if(typeof(str)=='undefined')str = '';
	if(str=='' && defaultVal)str = defaultVal;
	return str;
}
ZY.limitDecimal = function(obj,len){
	var s = obj.value;
 	if(s.indexOf(".")>-1){
	 	if((s.length - s.indexOf(".")-1)>len){
	 		obj.value = s.substring(0,s.indexOf(".")+len+1);
	 	}
	}
 	s = null;
}

//只能輸入數字
 ZY.isNumberKey = function(evt){
 	var charCode = (evt.which) ? evt.which : event.keyCode;
 	if (charCode > 31 && (charCode < 48 || charCode > 57)){
 		return false;
 	}else{		
 		return true;
 	}
 }  

 //只能輸入數字和小數點
 ZY.isNumberdoteKey = function(evt){
 	var e = evt || window.event; 
 	var srcElement = e.srcElement || e.target;
 	
 	var charCode = (evt.which) ? evt.which : event.keyCode;			
 	if (charCode > 31 && ((charCode < 48 || charCode > 57) && charCode!=46)){
 		return false;
 	}else{
 		if(charCode==46){
 			var s = srcElement.value;			
 			if(s.length==0 || s.indexOf(".")!=-1){
 				return false;
 			}			
 		}		
 		return true;
 	}
 }

 //只能輸入數字和字母
 ZY.isNumberCharKey = function(evt){
 	var e = evt || window.event; 
 	var srcElement = e.srcElement || e.target;	
 	var charCode = (evt.which) ? evt.which : event.keyCode;

 	if((charCode>=48 && charCode<=57) || (charCode>=65 && charCode<=90) || (charCode>=97 && charCode<=122) || charCode==8){
 		return true;
 	}else{		
 		return false;
 	}
 }

ZY.isChinese = function(obj,isReplace){
 	var pattern = /[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/i
 	if(pattern.test(obj.value)){
 		if(isReplace)obj.value=obj.value.replace(/[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/ig,"");
 		return true;
 	}
 	return false;
 }


 ZY.getParams = function(obj){
	var params = {};
	var chk = {},s;
	$(obj).each(function(){
		if($(this)[0].type=='hidden' || $(this)[0].type=='number' || $(this)[0].type=='tel' || $(this)[0].type=='password' || $(this)[0].type=='select-one' || $(this)[0].type=='textarea' || $(this)[0].type=='text'){
			params[$(this).attr('id')] = $.trim($(this).val());
		}else if($(this)[0].type=='radio'){
			if($(this).attr('name')){
				params[$(this).attr('name')] = $('input[name='+$(this).attr('name')+']:checked').val();
			}
		}else if($(this)[0].type=='checkbox'){
			if($(this).attr('name') && !chk[$(this).attr('name')]){
				s = [];
				chk[$(this).attr('name')] = 1;
				$('input[name='+$(this).attr('name')+']:checked').each(function(){
					s.push($(this).val());
				});
				params[$(this).attr('name')] = s.join(',');
			}
		}
	});
	chk=null,s=null;
	return params;
}


/**
 * 获取最后分类值
 */
ZY.ITGetGoodsCatVal = function(className){
	var goodsCatId = '';
	$('.'+className).each(function(){
		if($(this).attr('lastgoodscat')=='1')goodsCatId = $(this).val();
	});
	return goodsCatId;
}



ZY.setValue = function(name, value){
	
	var first = name.substr(0,1), input, i = 0, val;
	if("#" === first || "." === first){
		input = $(name);
	} else {
		input = $("[name='" + name + "']");
	}

	if(input.eq(0).is(":radio")) { //单选按钮
		input.filter("[value='" + value + "']").each(function(){this.checked = true});
	} else if(input.eq(0).is(":checkbox")) { //复选框
		if(!$.isArray(value)){
			val = new Array();
			val[0] = value;
		} else {
			val = value;
		}
		for(i = 0, len = val.length; i < len; i++){
			input.filter("[value='" + val[i] + "']").each(function(){this.checked = true});
		}
	} else {  //其他表单选项直接设置值
		input.val(value);
	}
}
ZY.setValues = function(obj){
	var input,value,val;
    for(var key in obj){
    	if($('#'+key)[0]){
    		ZY.setValue('#'+key,obj[key]);
    	}else if($("[name='" + key + "']")[0]){
    		ZY.setValue(key,obj[key]);
    	}
    }
}
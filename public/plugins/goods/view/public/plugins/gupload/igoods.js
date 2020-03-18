

function lastGoodsCatCallback(opts){
	if(opts.isLast && opts.val){
	    getSpecAttrs(opts.val);
	}else{
		$('#specBtns').hide();
		$('#specsAttrBox').empty();
	}
}
/**初始化**/
function TogEdit(){
	getSpecAttrs(OBJ.gcatid);	
}



var id2SepcNumConverter = {};
/**添加普通规格值**/
function addSpec(opts){

	var html = [];
	html.push('<div class="spec-item">',
	          '<input type="checkbox" class="j-speccat j-speccat_'+opts.catid+' j-spec_'+opts.catid+'_'+specnum+'" cat="'+opts.catid+'" num="'+specnum+'" onclick="javascript:addSpecSaleCol()" '+opts.checked+'/>',
	          '<input type="text" class="spec-ipt" id="specName_'+opts.catid+'_'+specnum+'" maxLength="50" value="'+ZY.blank(opts.val)+'" onblur="batchChangeTxt(this.value,'+opts.catid+','+specnum+')"/>',
	          '<span class="item-del" onclick="delSpec(this,'+opts.catid+','+specnum+')"></span>',
	          '</div>');
	$(html.join('')).insertBefore('#specAddBtn_'+opts.catid);
	if(opts.id){
		id2SepcNumConverter[opts.id] = opts.catid+'_'+specnum;
	}
	
	specnum++;	
}
/**删除普通规格值**/
function delSpec(obj,catId,num){
	if($('.j-spec_'+catId+'_'+num)[0].checked){
		$('.j-spec_'+catId+'_'+num)[0].checked = false;
		addSpecSaleCol();
	}
	$(obj).parent().remove();
}
/**添加带图片的规格值**/
function addSpecImg(opts){

	var html = [];
	html.push('<tr>',
			    '<td>',
	            '<input type="checkbox" class="j-speccat j-speccat_'+opts.catid+' j-spec_'+opts.catid+'_'+specnum+'" cat="'+opts.catid+'" num="'+specnum+'" onclick="javascript:addSpecSaleCol()" '+opts.checked+'/>',
                '<input type="text" id="specName_'+opts.catid+'_'+specnum+'" maxLength="50" value="'+ZY.blank(opts.val)+'" onblur="batchChangeTxt(this.value,'+opts.catid+','+specnum+')"/>',
                '</td>',
	            '<td id="uploadMsg_'+opts.catid+'_'+specnum+'">',
	            (opts.specimg)?'<img height="25"  width="25" id="specImg_'+opts.catid+'_'+specnum+'" src="'/*+ZY.conf.STATIC+"/"*/+opts.specimg+'" v="'+opts.specimg+'"/>':"",
	            '</td><td style="padding-left:5px;"><div id="specImgPicker_'+specnum+'" class="j-specImg">上传图片</div></td>'
	         );
	if($('#specTby').children().size()==0){
    	html.push('<td style="padding-left:5px;"><button type="button" class="btn btn-success" id="specImgBtn" onclick="addSpecImg({catid:'+opts.catid+',checked:\'\'})"><i class="fa fa-plus"></i>新增</button></td>');
    }else{
    	html.push('<td style="padding-left:5px;"><button type="button" class="btn btn-primary" id="specImgBtn" onclick="delSpecImg(this,'+opts.catid+','+specnum+')"><i class="fa fa-trash-o"></i>删除</button></td>');
    }
    html.push('</tr>');
	$('#specTby').append(html.join(''));
	ZY.upload({
		  num:specnum,
		  cat:opts.catId,
	  	  pick:'#specImgPicker_'+specnum,
	  	  formData: {dir:'goods',isThumb:1},
	  	  accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
	  	  callback:function(f){
	  		  var json = eval("("+f+")");
	  		  if(json.status==1){
	  			$('#uploadMsg_'+this.cat+"_"+this.num).html('<img id="specImg_'+this.cat+"_"+this.num+'" v="'+json.savePath+json.thumb+'" src="'+json.savePath+json.thumb+'" height="25"  width="25"/>');
	  		  }
		  },
		  progress:function(rate){
		      $('#uploadMsg_'+this.cat+"_"+this.num).html('已上传'+rate+"%");
		  }
	});
	if(opts.id){
		id2SepcNumConverter[opts.id] = opts.catid+'_'+specnum;
	}
	specnum++;
}
/**删除带图片的规格值**/
function delSpecImg(obj,catId,num){
	if($('.j-spec_'+catId+'_'+num)[0].checked){
		$('.j-spec_'+catId+'_'+num)[0].checked = false;
		addSpecSaleCol();
	}
	$(obj).parent().parent().remove();
}
/**给销售规格表填上值**/
function fillSepcSale(){
	var ids = '',tmpids = [];

	for(var i=0;i<OBJ.saleSpec.length;i++){
		tmpids = [];
		ids = OBJ.saleSpec[i].specids;
		ids = ids.split('_');
		for(var j=0;j<ids.length;j++){
			tmpids.push(id2SepcNumConverter[ids[j]]);
		}
		tmpids = tmpids.join('-');

		if(OBJ.saleSpec[i].isdefault)$('#isDefault_'+tmpids).attr('checked',true);
		//$('#productNo_'+tmpids).val(OBJ.saleSpec[i].productNo);
		$('#marketPrice_'+tmpids).val(OBJ.saleSpec[i].marketprice);
		$('#specPrice_'+tmpids).val(OBJ.saleSpec[i].specprice);
		$('#specStock_'+tmpids).val(OBJ.saleSpec[i].specstock);
		//$('#warnStock_'+tmpids).val(OBJ.saleSpec[i].warnStock);
		$('#saleNum_'+tmpids).html(OBJ.saleSpec[i].salenum);
		$('#saleNum_'+tmpids).attr('sid',OBJ.saleSpec[i].id);
	}
}
/**生成销售规格表**/
function addSpecSaleCol(){
	//获取规格分类和规格值
	var catId,snum,specCols = {},obj = [];
	$('.j-speccat').each(function(){
		if($(this)[0].checked){
			catId = $(this).attr('cat');
			snum = $(this).attr('num');
			if(!specCols[catId]){
				specCols[catId] = [];
				specCols[catId].push({id:catId+"_"+snum,val:$.trim($('#specName_'+catId+"_"+snum).val())});
			}else{
				specCols[catId].push({id:catId+"_"+snum,val:$.trim($('#specName_'+catId+"_"+snum).val())});
			}
	    }
	});
	//创建表头
	$('.j-saleTd').remove();
	var html = [],specArray = [];
	for(var key in specCols){
		//alert(key);
		html.push('<th class="j-saleTd">'+$('#specCat_'+key).html()+'</th>');
		specArray.push(specCols[key]);
	}
	//console.log(specArray);
	if(html.length==0){
        $('#goodsStock').removeAttr('disabled');
		$('#shopPrice').removeAttr('disabled');
		$('#marketPrice').removeAttr('disabled');
		$('#warnStock').removeAttr('disabled');
		return;
	}
	$(html.join('')).insertBefore('#thCol');
	//组合规格值
	this.combined = function(doubleArrays){
        var len = doubleArrays.length;
        if (len >= 2) {
            var arr1 = doubleArrays[0];
            var arr2 = doubleArrays[1];
            var len1 = doubleArrays[0].length;
            var len2 = doubleArrays[1].length;
            var newlen = len1 * len2;
            var temp = new Array(newlen),ntemp;
            var index = 0;
            for (var i = 0; i < len1; i++) {
            	if(arr1[i].length){
            		for (var k = 0; k < len2; k++) {
            			ntemp = arr1[i].slice();
            			ntemp.push(arr2[k]);
		                temp[index] = ntemp;
		                index++;
            		}
            	}else{
	                for (var j = 0; j < len2; j++) {
	                    temp[index] = [arr1[i],arr2[j]];
	                    index++;
	                }
            	}
            }
            var newArray = new Array(len - 1);
            newArray[0] = temp;
            if (len > 2) {
                var _count = 1;
                for (var i = 2; i < len; i++) {
                    newArray[_count] = doubleArrays[i];
                    _count++;
                }
            }
            return this.combined(newArray);
        }else {
            return doubleArrays[0];
        }
    }
	
	var specsRows = this.combined(specArray);
	//生成规格值表
	html = [];
	var id=[],key=1,specHtml = [];
	var productNo = $('#productNo').val(),specProductNo = '';
	for(var i=0;i<specsRows.length;i++){
		id = [],specHtml = [];
		html.push('<tr class="j-saleTd">');
		
		if(specsRows[i].length){
			for(var j=0;j<specsRows[i].length;j++){
				id.push(specsRows[i][j].id);
				specHtml.push('<td class="j-td_'+specsRows[i][j].id+'">' + specsRows[i][j].val + '</td>');
	        }
		}else{
			id.push(specsRows[i].id);
			specHtml.push('<td>' + specsRows[i].val + '</td>');
		}
		id = id.join('-');
		//if(OBJ.goodsId==0){
			specProductNo = productNo+'-'+key;
		//}
		if (i == 0) {
			var checked = 'checked ="checked"';
		} else {
			var checked = '';
		}
		html.push('  <td><input type="radio"'+checked+' id="isDefault_'+id+'" name="defaultSpec" class="j-ipt" value="'+id+'"/></td>');
		html.push(specHtml.join(''));
		html.push(/*'  <td><input type="text" class="spec-sale-goodsNo j-ipt" id="productNo_'+id+'" value="'+specProductNo+'" onblur="checkProductNo(this)" ></td>',*/
	              '  <td><input type="text" class="spec-sale-ipt j-ipt" id="marketPrice_'+id+'" onblur="ZY.limitDecimal(this,2);javascript:ZY.limitDecimal(this,2)" onkeypress="return ZY.isNumberdoteKey(event)" onkeyup="javascript:ZY.isChinese(this,1)"></td>',
	              '  <td><input type="text" class="spec-sale-ipt j-ipt" id="specPrice_'+id+'" onblur="ZY.limitDecimal(this,2);javascript:ZY.limitDecimal(this,2)" onkeypress="return ZY.isNumberdoteKey(event)" onkeyup="javascript:ZY.isChinese(this,1)"></td>',
	              '  <td><input type="text" class="spec-sale-ipt j-ipt" id="specStock_'+id+'" onkeypress="return ZY.isNumberKey(event)" onkeyup="javascript:ZY.isChinese(this,1)"></td>',
	              /*'  <td><input type="text" class="spec-sale-ipt j-ipt" id="warnStock_'+id+'" onkeypress="return ZY.isNumberKey(event)" onkeyup="javascript:ZY.isChinese(this,1)"></td>',*/
	              '  <td class="j-ws" v="'+id+'" id="saleNum_'+id+'">0</td>',
	              '</tr>');
		key++;
	}
	$('#spec-sale-tby').append(html.join(''));
	//判断是否禁用商品价格和库存字段
	if($('#spec-sale-tby').html()!=''){
		$('#goodsStock').prop('disabled',true);
		$('#shopPrice').prop('disabled',true);
		$('#marketPrice').prop('disabled',true);
		$('#warnStock').prop('disabled',true);
	}
	//设置销售规格表值
	if(OBJ.saleSpec)fillSepcSale();
}
/**根据批量修改销售规格值**/
function batchChange(v,id){
	if($.trim(v)!=''){
		//alert(id)
		$('input[type=text][id^="'+id+'_"]').val(v);
	}
}
/**根据规格值修改 销售规格表 里的值**/
function batchChangeTxt(v,catId,num){
	$('.j-td_'+catId+"_"+num).each(function(){
		$(this).html(v);
	});
}


/**获取商品规格和属性**/
function getSpecAttrs(goodsCatId){
	//alert('123');
	$('#specsAttrBox').empty();
	$('#specBtns').hide();
	specnum = 0;
	$.post(ZY.conf.GPATH,{goodsCatId:goodsCatId,goodsType:$('#goodsType').val()},function(data,textStatus){
		var json = data;
		if(json.status==1 && json.data){
			var html = [],tmp,str;
			if(json.data.spec0 || json.data.spec1){
				html.push('<div class="spec-head lm1">商品规格</div>');
				html.push('<div class="spec-body">');
				if(json.data.spec0){
					tmp = json.data.spec0;
					html.push('<div id="specCat_'+tmp.id+'">'+tmp.specname+'</div>');
					html.push('<table><tbody id="specTby"></tbody></table>');
				}
				if(json.data.spec1){
					for(var i=0;i<json.data.spec1.length;i++){
						tmp = json.data.spec1[i];
						html.push('<div class="spec-line"></div>',
						          '<div id="specCat_'+tmp.id+'">'+tmp.specname+'</div>',
						          '<div style="height:auto;">',
						          '<button type="button" class="btn btn-success" id="specAddBtn_'+tmp.id+'" onclick="javascript:addSpec({catid:'+tmp.id+',checked:\'\'})"><i class="fa fa-plus"></i>新增</button>',
						          '</div>'
								);
					}
				}
				html.push('</div>');
				html.push($('#specTips').html());
				html.push('<div id="specSaleHead" class="spec-head lm2">销售规格</div>',
				          '<table class="specs-sale-table">',
				          '  <thead id="spec-sale-hed">',
				          '   <tr>',
				          '     <th>推荐规格</th>',
				          /*'     <th id="thCol"><font color="red">*</font>货号</th>',*/
				          '     <th id="thCol"><font color="red">*</font>市场价<br/><input type="text" class="spec-sale-ipt" onblur="ZY.limitDecimal(this,2);batchChange(this.value,\'marketPrice\')" onkeypress="return ZY.isNumberdoteKey(event)" onkeyup="javascript:ZY.isChinese(this,1)"></th>',
				          '     <th><font color="red">*</font>本店价<br/><input type="text" class="spec-sale-ipt" onblur="ZY.limitDecimal(this,2);batchChange(this.value,\'specPrice\')" onkeypress="return ZY.isNumberdoteKey(event)" onkeyup="javascript:ZY.isChinese(this,1)"></th>',
				          '     <th><font color="red">*</font>库存<br/><input type="text" class="spec-sale-ipt" onblur="batchChange(this.value,\'specStock\')" onkeypress="return ZY.isNumberKey(event)" onkeyup="javascript:ZY.isChinese(this,1)"></th>',
				          /*'     <th><font color="red">*</font>预警库存<br/><input type="text" class="spec-sale-ipt" onblur="batchChange(this.value,\'warnStock\')" onkeypress="return ZY.isNumberKey(event)" onkeyup="javascript:ZY.isChinese(this,1)"></th>',*/
				          '     <th>销量</th>',
				          '   </tr>',
				          '  </thead>',
				          '  <tbody id="spec-sale-tby"></tbody></table>'
						);
			}
			if(json.data.attrs){
				html.push('<div class="spec-head  lm3">商品属性</div>');
				html.push('<div class="spec-body">');
				html.push('<table class="attr-table">');
				for(var i=0;i<json.data.attrs.length;i++){
					tmp = json.data.attrs[i];
					html.push('<tr><th width="120" nowrap>'+tmp.attrname+'：</th><td>');
					if(tmp.attrtype==3){		
						str = tmp.attrval.split(',');
						for(var j=0;j<str.length;j++){
						    html.push('<label><input type="checkbox" class="j-ipt" name="attr_'+tmp.id+'" value="'+str[j]+'"/>'+str[j]+'</label>');
						}
					}else if(tmp.attrtype==2){
						html.push('<select name="attr_'+tmp.id+'" id="attr_'+tmp.id+'" class="j-ipt">');
						html.push('<option value="">请选择</option>');
						str = tmp.attrval.split(',');
						for(var j=0;j<str.length;j++){
							html.push('<option value="'+str[j]+'">'+str[j]+'</option>');
						}
						html.push('</select>');
					}else{
						html.push('<input type="text" name="attr_'+tmp.id+'" id="attr_'+tmp.id+'" class="spec-sale-text form-control j-ipt"/>');
					}
					html.push('</td></tr>');
				}
				html.push('</table>');
				html.push('</div>');
			}
			$('#specsAttrBox').html(html.join(''));

			//如果是编辑的话，第一次要设置之前设置的值
			if(OBJ.id>0 && specnum==0){
				//设置规格值
				if(OBJ.spec0){
					for(var i=0;i<OBJ.spec0.length;i++){
					   if(OBJ.spec0[i].catid==json.data.spec0.id)addSpecImg({catid:OBJ.spec0[i].catid,checked:'checked',val:OBJ.spec0[i].itemname,id:OBJ.spec0[i].id,specimg:OBJ.spec0[i].itemimg});
					}
				}
				if(OBJ.spec1){
					
					for(var i=0;i<OBJ.spec1.length;i++){
						
						for(var j=0;j<json.data.spec1.length;++j){
							if(OBJ.spec1[i].catid==json.data.spec1[j].id){
								//console.log(OBJ.spec1[i].catid+'abc');
					    		addSpec({catid:OBJ.spec1[i].catid,checked:'checked',val:OBJ.spec1[i].itemname,id:OBJ.spec1[i].id});
								break;
							}
						}
					}
				}
				addSpecSaleCol();
				//设置商品属性值
				var tmp = null;
				if(OBJ.attrs.length){
					for(var i=0;i<OBJ.attrs.length;i++){
						if(OBJ.attrs[i].attrtype==3){
							tmp = OBJ.attrs[i].attrval.split(',');
							ZY.setValue("attr_"+OBJ.attrs[i].attrid,tmp);
						}else{
						    ZY.setValue("attr_"+OBJ.attrs[i].attrid,OBJ.attrs[i].attrval);
						}
					}
				}
				
			}
			//给没有初始化的规格初始化一个输入框
			if(json.data.spec0 && !$('.j-speccat_'+json.data.spec0.id)[0]){
				addSpecImg({catid:json.data.spec0.id,checked:''});
			}
			if(json.data.spec1){
				for(var i=0;i<json.data.spec1.length;i++){
					if(!$('.j-speccat_'+json.data.spec1[i].id)[0])addSpec({catid:json.data.spec1[i].id,checked:''});
				}
			}
			$('#specBtns').show();
		}
	});
}

function changeGoodsType(v){
	if(v==0){
	    $('#goodsStockTr').show();
	    $('#goodsStock').removeAttr('disabled');
    }else{
    	$('#goodsStockTr').hide();
    	$('#goodsStock').prop('disabled',true);
    }
    var goodsCatId =ZY.ITGetGoodsCatVal('j-goodsCats');
    getSpecAttrs(goodsCatId);
}
function toStock(id,src){
    location.href=ZY.U('shop/goodsvirtuals/stock','id='+id+"&src="+src);
}
function toDetail(goodsId,key){
    window.open(ZY.U('home/goods/detail','goodsId='+goodsId+"&key="+key));
}



function loadGrid(p){
    p = (p<=1)?1:p;
    mmg.load({cat1:$('#cat1').val(),cat2:$('#cat2').val(),goodsType:$('#goodsType').val(),goodsName:$('#goodsName').val(),page:p});
}







function endEditGoodsBase(fv,goodsId){
	$('#span_'+fv+'_'+goodsId).html($('#ipt_'+fv+'_'+goodsId).val());
	$('#span_'+fv+'_'+goodsId).show();
    $('#ipt_'+fv+'_'+goodsId).hide();
}
function editGoodsBase(fv,goodsId){
    var vtext = $.trim($('#ipt_'+fv+'_'+goodsId).val());
	if(fv==2){
        if(vtext=='' || parseFloat(vtext,10)<=0){
        	ZY.msg('价格必须大于0', {icon: 5});
        	return;
        }
	}else if(fv==3){
        if(vtext=='' || parseInt(vtext,10)<0 || vtext.indexOf('.')>-1){
        	ZY.msg('库存必须为正整数', {icon: 5});
        	return;
        }
	}
	var params = {};
	(fv==2)?params.shopPrice=vtext:params.goodsStock=vtext;
	params.goodsId = goodsId;
	$.post(ZY.U('shop/Goods/editGoodsBase'),params,function(data,textStatus){
		var json = ZY.toJson(data);
		if(json.status>0){
			$('#img_'+fv+'_'+goodsId).fadeTo("fast",100);
			endEditGoodsBase(fv,goodsId);
			$('#img_'+fv+'_'+goodsId).fadeTo("slow",0);
		}else{
			ZY.msg(json.msg, {icon: 5}); 
		}
	});
}



//is_allow_level_input 允许第几级不存在时,可以手工输入，比如区级 is_allow_level_input=4
function city_select(cond_id){
	if ($('#'+cond_id).length==0){
		return false;
	}
	var tag = $('#'+cond_id).attr('tag');
	var is_allow_level_input = $('#'+cond_id).attr('is_allow_level_input');
	is_allow_level_input = is_allow_level_input == undefined ? 0 : is_allow_level_input;
	tag = tag == undefined ? '' : tag;
	
	var ajax_url = "/addr/index?level=1&tag="+tag+"&is_allow_level_input="+is_allow_level_input;
	$.getJSON(ajax_url, function(rsp){
		//用于初始化时显示出 国家/省/市/区/镇 的select html
		var select_html = rsp.select_html;
		$("#"+cond_id).html(select_html);
		//如果非编辑状态 默认选中中国
		var data_city3 = $('#'+cond_id).attr("data_city3");
		data_city3 = data_city3==undefined ? '' : data_city3;
		var city3_state = data_city3.split(',')[0];
		var data_area = $('#'+cond_id).attr("data_area");
		
		if (data_city3 == ''){
			var _el_id = "sel_city"+tag+"_v1";
			$("#"+_el_id+" option[value='1']").attr("selected","1");
			$("#"+_el_id).change();
		}else{
			var _el_id = "sel_city"+tag+"_v1";
			$("#"+_el_id+" option[value='"+city3_state+"']").attr("selected","1");
			$("#"+_el_id).change();
		}
		
	});
}

function sel_city_change(obj){
	var parent_obj = $(obj).parents('.sel_city');
	var tag = parent_obj.attr('tag');
	var is_allow_level_input = parent_obj.attr('is_allow_level_input');
	var data_area = parent_obj.attr('data_area');
	is_allow_level_input = is_allow_level_input == undefined ? 0 : is_allow_level_input;
	tag = tag == undefined ? '' : tag;
	var city_code = $(obj).val();
	var level = parseInt($(obj).attr('level'));
	
	for(var ii=level+1;ii<10;ii++){
		var _el_id = "sel_city"+tag+"_v"+ii;
		if ($('#'+_el_id).length==0){
			break;
		}
		var first_opt_txt = $("#"+_el_id+" option").first().html();
		$("#"+_el_id).empty();
		$("#"+_el_id).append("<option value=''>"+first_opt_txt+"</option>");
	}
	
	if (city_code == ''){
		return;
	}
		
	var tmp_level = parseInt(level)+1;
	var ajax_url = "/addr/index?level="+tmp_level+"&tag="+tag+"&city_code="+city_code;
	$.getJSON(ajax_url, function(rsp){	
		//用于支持一个页面有多个地址选择
		var tag = rsp.tag;
		//当前地址在第几级
		var level = parseInt(rsp.level);
		//当前的地址是否有数据
		var has_data = rsp.has_data;
		for(var ii=level;ii<10;ii++){			
			var _el_id = "sel_city"+tag+"_v"+ii;
			if ($('#'+_el_id).length==0){
				break;
			}			
			if (has_data == -1){
				$("#"+_el_id).css("display","none");
			}else{
				//默认不显示镇
				if (level<5 && ii<5){
					$("#"+_el_id).css("display","inline");
				}
				if (level==5){
					$("#"+_el_id).css("display","inline");
				}
			}
		}
		
		if (level==is_allow_level_input && has_data == -1){
			$("#inp_city"+tag).css("display","inline");
			$("#inp_city"+tag).val(data_area);
		}else{
			$("#inp_city"+tag).css("display","none");
		}
			
		var curl_el_id = "sel_city"+tag+"_v"+level;
		var data = rsp.data;
		
		for(i in data){
			if (data[i].region_name == undefined){
				continue;
			}
			$("#"+curl_el_id).append("<option value='"+data[i].code+"'>"+data[i].region_name+"</option>");
		}
		var data_city3 = $(obj).parents("span[class='sel_city']").attr("data_city3");
			//console.log('bjlll',$(obj).parents("span[class='sel_city']"));		
			//console.log('data_city3',data_city3);		
		if (data_city3!=undefined && data_city3!=''){
			var city3_v = data_city3.split(',')[level-1];
			var _el_id = "sel_city"+tag+"_v"+level;
			//console.log('_el_id',_el_id);
			$("#"+_el_id+" option[value='"+city3_v+"']").attr("selected","1");
			$("#"+_el_id).change();			
		}
	});
}
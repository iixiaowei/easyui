$(function(){
	
	function isNumber(s) {
	    var regu = "^[0-9]+$";
	    var re = new RegExp(regu);
	    if (s.search(re) != -1) {
	        return true;
	    } else {
	        return false;
	    }
	}
	function isInteger(str) {
	    var regu = /^[-]{0,1}[0-9]{1,}$/;
	    return regu.test(str);
	}
	function isNull(str) {
	    if (str == "") return true;
	    var regu = "^[ ]+$";
	    var re = new RegExp(regu);
	    return re.test(str);
	}

	function isDecimal(str) {
	    if (isInteger(str)) return true;
	    var re = /^[-]{0,1}(\d+)[\.]+(\d+)$/;
	    if (re.test(str)) {
	        if (RegExp.$1 == 0 && RegExp.$2 == 0) return false;
	        return true;
	    } else {
	        return false;
	    }
	}
	
	
	$("#p_name_id").change(function(){
        var p_name_id = $("#p_name_id").val();
        var p_name  = $("#p_name_id").find("option:selected").text();
		if(p_name_id==""){
			 $("#p_serial_sn")[0].options.length=0;
			 $("#p_serial_sn")[0].options.add(new Option("请选择编号", ""));     
		}else{

			$.post("/admin/pdate/get_project_serial",{
				'p_name_id':p_name_id
				},function(data){
				   if(data=="<option value=''>请选择编号</option>"){
					    $("#p_serial_sn")[0].options.length=0;
						$("#p_serial_sn")[0].options.add(new Option("请选择编号", ""));    
						alert("请先添加 "+p_name+" 立项编号！");
						return false;
				   }
				   $("#p_serial_sn").html( data );	
			});
			
		}
        
	});
	
	$("#durations").blur(function(){
		var durations = $("#durations").val();
		var p_serial_sn = $("#p_serial_sn").val();
		var p_name_id   = $("#p_name_id").val();
		if(isNull(p_serial_sn)){
			return false;
		}
		if(isNull(p_name_id)){
			return false;
		}
		if(isNull(durations)){
			$(this).focus();
			return false;
		}
		if(!isDecimal(durations)){
			$(this).val("").focus();
			return false;
		}
		
		$.post("/admin/project/get_pro_durations",{
			'p_serial_sn':p_serial_sn,
			'p_name_id':p_name_id,
			'durations':durations
		},function(data){
			$("#y_timeflow").val( data.rs.y_timeflow );
			$("#s_timeflow").val( data.rs.s_timeflow );
			$("#z_timeflow").val( data.rs.z_timeflow );
			$("#t_timeflow").val( data.rs.t_timeflow );
			
			$("#y_timeincome").val(data.rs.y_timeincome);
			$("#s_timeincome").val(data.rs.s_timeincome);
			$("#z_timeincome").val(data.rs.z_timeincome);
			$("#t_timeincome").val(data.rs.t_timeincome);
			
		},'json');
		
		
	});
	
	$("#full_area_num121212").blur(function(){
		var full_area_num = $("#full_area_num").val();
		if(isNull(full_area_num)){
			$(this).focus();
			return false;
		}
		if(!isDecimal(full_area_num)){
			$(this).val("").focus();
			return false;
		}
		
		var p_serial_sn = $("#p_serial_sn").val();
		var p_name_id   = $("#p_name_id").val();
		if(isNull(p_serial_sn)){
			return false;
		}
		if(isNull(p_name_id)){
			return false;
		}
		
		$.post("/admin/project/get_pro_fullarea",{
			'p_serial_sn':p_serial_sn,
			'p_name_id':p_name_id,
			'full_area_num':full_area_num
		},function(data){
			$("#y_sellout").val(data.rs.y_sellout);
			$("#s_sellout").val(data.rs.s_sellout);
			$("#z_sellout").val(data.rs.z_sellout);
			$("#t_sellout").val(data.rs.t_sellout);
		},'json');
		
	});
	
	$("#p_serial_sn").change(function(){
		var p_serial_sn = $("#p_serial_sn").val();
		var p_name_id   = $("#p_name_id").val();

		$.post("/admin/project/get_pro_info",{
			'p_serial_sn':p_serial_sn,
			'p_name_id':p_name_id
		},function(data){
			//基础信息
			$("#y_starttime").val( data.rs.y_starttime );
			$("#y_endtime").val( data.rs.y_endtime );
			$("#y_daynums").val( data.rs.y_daynums );
			$("#s_starttime").val( data.rs.s_starttime );
			$("#s_endtime").val( data.rs.s_endtime );
			$("#s_daynums").val( data.rs.s_daynums );
			$("#z_starttime").val( data.rs.z_starttime );
			$("#z_endtime").val( data.rs.z_endtime );
			$("#z_daynums").val( data.rs.z_daynums );
			//$("#txt_business_area").val( data.rs.business_area );
			$("#plan_episodes").val( data.rs.plan_episodes );
			$("#y_gcys").val( data.rs.y_gcys );
			$("#s_gcys").val( data.rs.s_gcys );
			$("#z_gcys").val( data.rs.z_gcys );
			$("#t_gcys").val( data.rs.t_gcys );
			
			$("#department").val( data.rs.department );
			$("#full_area_num").val( data.rs.full_area_num );
			$("#play_resource").val( data.rs.play_resource );
			
			$("#y_gdzc").val( data.rs.y_gdzc );
			$("#s_gdzc").val( data.rs.s_gdzc );
			$("#z_gdzc").val( data.rs.z_gdzc );
			$("#t_gdzc").val( data.rs.t_gdzc );
			
			$("#y_wxzc").val( data.rs.y_wxzc );
			$("#s_wxzc").val( data.rs.s_wxzc );
			$("#z_wxzc").val( data.rs.z_wxzc );
			$("#t_wxzc").val( data.rs.t_wxzc );
			
			$("#y_yfqywl").val( data.rs.y_yfqywl );
			$("#s_yfqywl").val( data.rs.s_yfqywl );
			$("#z_yfqywl").val( data.rs.z_yfqywl );
			$("#t_yfqywl").val( data.rs.t_yfqywl );
			
			$("#y_yycb").val( data.rs.y_yycb );
			$("#s_yycb").val( data.rs.s_yycb );
			$("#z_yycb").val( data.rs.z_yycb );
			$("#t_yycb").val( data.rs.t_yycb );
			
			$("#y_rycb").val( data.rs.y_rycb );
			$("#s_rycb").val( data.rs.s_rycb );
			$("#z_rycb").val( data.rs.z_rycb );
			$("#t_rycb").val( data.rs.t_rycb );
			
			$("#y_bgfy").val( data.rs.y_bgfy );
			$("#s_bgfy").val( data.rs.s_bgfy );
			$("#z_bgfy").val( data.rs.z_bgfy );
			$("#t_bgfy").val( data.rs.t_bgfy );
			
			$("#y_area").val( data.rs.y_area );
			$("#s_area").val( data.rs.s_area );
			$("#z_area").val( data.rs.z_area );
			$("#t_area").val( data.rs.t_area );
			
			$("#y_taxes").val( data.rs.y_taxes );
			$("#s_taxes").val( data.rs.s_taxes );
			$("#z_taxes").val( data.rs.z_taxes );
			$("#t_taxes").val( data.rs.t_taxes );
			
			$("#y_income").val( data.rs.y_income );
			$("#s_income").val( data.rs.s_income );
			$("#z_income").val( data.rs.z_income );
			$("#t_income").val( data.rs.t_income );
			
			$("#y_flow").val( data.rs.y_flow );
			$("#s_flow").val( data.rs.s_flow );
			$("#z_flow").val( data.rs.z_flow );
			$("#t_flow").val( data.rs.t_flow );
			
			$("#y_coefficient").val( data.rs.y_coefficient );
			$("#s_coefficient").val( data.rs.s_coefficient );
			$("#z_coefficient").val( data.rs.z_coefficient );
			$("#t_coefficient").val( data.rs.t_coefficient );
			
			$("#y_newmember").val( data.rs.y_newmember );
			$("#s_newmember").val( data.rs.s_newmember );
			$("#z_newmember").val( data.rs.z_newmember );
			$("#t_newmember").val( data.rs.t_newmember );
			
			$("#y_scfy,#y_charge").val( data.rs.y_scfy );
			$("#s_scfy,#s_charge").val( data.rs.s_scfy );
			$("#z_scfy,#z_charge").val( data.rs.z_scfy );
			$("#t_scfy,#t_charge").val( data.rs.t_scfy );
			
			$("#y_charge").val( data.rs.y_charge );
			$("#s_charge").val( data.rs.s_charge );
			$("#z_charge").val( data.rs.z_charge );
			$("#t_charge").val( data.rs.t_charge );
			
			$("#y_cost").val( data.rs.y_cost );
			$("#s_cost").val( data.rs.s_cost );
			$("#z_cost").val( data.rs.z_cost );
			$("#t_cost").val( data.rs.t_cost );
			
			$("#y_pro_than").val(data.rs.y_pro_than);
			$("#s_pro_than").val(data.rs.s_pro_than);
			$("#z_pro_than").val(data.rs.z_pro_than);
			$("#t_pro_than").val(data.rs.t_pro_than);
			
			$("#y_profit").val( data.rs.y_profit );
			$("#s_profit").val( data.rs.s_profit );
			$("#z_profit").val( data.rs.z_profit );
			$("#t_profit").val( data.rs.t_profit );
			
			$("#y_avg_price").val( data.rs.y_avg_price );
			$("#s_avg_price").val( data.rs.s_avg_price );
			$("#z_avg_price").val( data.rs.z_avg_price );
			$("#t_avg_price").val( data.rs.t_avg_price );
			
			$("#y_gross").val( data.rs.y_gross );
			$("#s_gross").val( data.rs.s_gross );
			$("#z_gross").val( data.rs.z_gross );
			$("#t_gross").val( data.rs.t_gross );
			
			$("#y_return").val( data.rs.y_return );
			$("#s_return").val( data.rs.s_return );
			$("#z_return").val( data.rs.z_return );
			$("#t_return").val( data.rs.t_return );
			
			$("#txt_business_area").val( data.rs.txt_business_area );
			
			$("#y_effect").val( data.rs.y_effect );
			$("#s_effect").val( data.rs.s_effect );
			$("#z_effect").val( data.rs.z_effect );
			$("#t_effect").val( data.rs.t_effect );
			
			$("#y_areaflow").val( data.rs.y_areaflow );
			$("#s_areaflow").val( data.rs.s_areaflow );
			$("#z_areaflow").val( data.rs.z_areaflow );
			$("#t_areaflow").val( data.rs.t_areaflow );
			
			$("#y_areaincome").val( data.rs.y_areaincome );
			$("#s_areaincome").val( data.rs.s_areaincome );
			$("#z_areaincome").val( data.rs.z_areaincome );
			$("#t_areaincome").val( data.rs.t_areaincome );
			
			$("#y_sellout").val(data.rs.y_sellout);
			$("#s_sellout").val(data.rs.s_sellout);
			$("#z_sellout").val(data.rs.z_sellout);
			$("#t_sellout").val(data.rs.t_sellout);
			
			
		},'json');
		
		
	});
	
	
	
	
	
});
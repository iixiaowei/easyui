$(function(){
	
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
						alert("请先添加 "+p_name+" 立项编号！");
						return false;
				   }
				   $("#p_serial_sn").html( data );	
			});
			
		}
        
	});

	$("#p_serial_sn").change(function(){
         var p_serial_sn = $("#p_serial_sn").val();
         var p_serial  = $("#p_serial_sn").find("option:selected").text();
         if(p_serial_sn==""){
        	 $("#p_date")[0].options.length=0;
			 $("#p_date")[0].options.add(new Option("请选择分期", ""));     
         }else{
        	 $.post("/admin/pdate/get_project_date",{
 				'p_serial_sn':p_serial_sn
 				},function(data){
 				   if(data=="<option value=''>请选择分期</option>"){
 						alert("请先添加 "+p_serial+" 立项分期！");
 						 $("#p_date")[0].options.length=0;
 						 $("#p_date")[0].options.add(new Option("请选择分期", "")); 
 						$("#tickets").val(null).trigger("change");
 						return false;
 				   }
 				   $("#p_date").html( data );	
 			});
         }
	});
	
	$("#p_date").change(function(){
		var p_name_id = $("#p_name_id").val();
		var p_date = $("#p_date").val();
		var p_serial_sn = $("#p_serial_sn").val();
		$.post("/admin/pdate/get_pro_tickets",{
			'p_name_id':p_name_id,
			'p_date':p_date,
			'p_serial_sn':p_serial_sn
		},function(data){
			if(data.status==0){
				alert("立项分期不存在，请先添加立项分期！");
				$("#tickets").html("");
				$("#tickets").val(null).trigger("change");
				return false;
			}else{
				$("#p_starttime").val( data.p_starttime );
				$("#p_endtime").val( data.p_endtime );
				$("#tickets").html("");
				$("#tickets").val(null).trigger("change");
				$("#tickets").html( data.tickets_str );
			}
			
		},'json');
	});
	
	
	/**
	$("#p_name_id,#p_date").change(function(data){
		 
		var p_name_id = $("#p_name_id").val();
		var p_date = $("#p_date").val();
		if(p_name_id==""){
			$("#p_serial_sn").val("");
			$("#p_starttime").val("");
			$("#p_endtime").val("");
			$("#tickets").html("");
			return false;
		}
		if(p_date==""){
			$("#p_serial_sn").val("");
			$("#p_starttime").val("");
			$("#p_endtime").val("");
			$("#tickets").html("");
			return false;
		}
		
		 
		$.post("/admin/sale/get_pro_sn",{
			'p_name_id':p_name_id,
			'p_date':p_date
		},function(data){
			if(data.status==0){
				alert("分期立项编号不存在，请先添加立项编号！");
				return false;
			}else{
				$("#p_serial_sn").val( data.p_serial_sn );
				$("#p_starttime").val( data.p_starttime );
				$("#p_endtime").val( data.p_endtime );
				$("#tickets").html( data.tickets_str );
			}
			
		},'json');
		
	});
	*/
	
});
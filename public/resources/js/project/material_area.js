$(function(){
	
	
	//document
	$('#dialog_area').dialog({
		autoOpen : false,
		width : $(window).width(),
		height:$(window).height()+800,
		resizable : true,
		modal : true,
		draggable : false,  
		bgiframe: true,
		title : "<div class='widget-header'><h4><i class='fa fa-hand-o-right'></i> 公司费用</h4></div>",
		close: function(event, ui) {
			var p_name_id = $("#p_name_id").val();
			var p_serial_sn = $("#project_sn").val();
			var p_date      = $("#p_date").val();
			
			$.post("/admin/project/get_sel_area",{
				'p_name_id':p_name_id,
				'p_serial_sn':p_serial_sn,
				'p_date':p_date
			},function(data){
				if(data.status==0){
					$("#area_cost").val(0);
				}else{
					$("#area_cost").val(data.amount);
				}
				
			},'json'); 
			$(this).dialog("close");
		}, 
		buttons : [{
			html : "关闭",
			"class" : "btn btn-default",
			click : function() {
				var p_name_id = $("#p_name_id").val();
				var p_serial_sn = $("#project_sn").val();
				var p_date      = $("#p_date").val();
				
				$.post("/admin/project/get_sel_area",{
					'p_name_id':p_name_id,
					'p_serial_sn':p_serial_sn,
					'p_date':p_date
				},function(data){
					if(data.status==0){
						$("#area_cost").val(0);
					}else{
						$("#area_cost").val(data.amount);
					}
					
				},'json'); 
				
				$(this).dialog("close");
			}
		}]
		 
	});
	
	
	$("#btnArea").click(function(){
		
		$("#if_area").attr("src","/data/area/index.html"); 
		$('#dialog_area').dialog('open');
		return false;
		
	});
	
});
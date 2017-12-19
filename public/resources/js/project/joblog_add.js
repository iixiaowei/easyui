$(function(){
	
	//周一添加任务
	$("#btnMondayAdd").click(function(){
		$(this).attr("disabled","disabled");
		var size = $("#tbl_monday tbody tr").size();
		var number = 0;
		number = size+1;
		$.post("/admin/joblog/get_monday_tr",{
			'number':number
		},function(data){
			 $("#btnMondayAdd").removeAttr("disabled");
			 $("#tbl_monday tbody").append( data );
			 $('.date').datepicker({
					showOtherMonths: true,
			        selectOtherMonths: true,  
			        changeMonth: true,
			        changeYear: true,
			        prevText : '<i class="fa fa-chevron-left"></i>',
					nextText : '<i class="fa fa-chevron-right"></i>',
					dateFormat : 'yy-mm-dd'
				});
		});
	});
	
	$("#write_time").change(function(){
		var write_time = $(this).val();
		
		$.post("/admin/joblog/get_weekday",{
			'write_time':write_time
		},function(data){
			$("#log_week_num").val( data.log_week_num );
			$("#week_day").val( data.week_day );
		},'json');
		
		
	});	
	
	
	
	
	//周二添加任务
	$("#btnTuesdayAdd").click(function(){
		$(this).attr("disabled","disabled");
		var size = $("#tbl_tuesday tbody tr").size();
		var number = 0;
		number = size+1;
		$.post("/admin/joblog/get_tuesday_tr",{
			'number':number
		},function(data){
			 $("#btnTuesdayAdd").removeAttr("disabled");
			 $("#tbl_tuesday tbody").append( data );
			 $('#tbl_tuesday .date').datepicker({
					showOtherMonths: true,
			        selectOtherMonths: true,  
			        changeMonth: true,
			        changeYear: true,
			        prevText : '<i class="fa fa-chevron-left"></i>',
					nextText : '<i class="fa fa-chevron-right"></i>',
					dateFormat : 'yy-mm-dd'
				});
		});
	});
	
	//周三添加任务
	$("#btnWednesdayAdd").click(function(){
		$(this).attr("disabled","disabled");
		var size = $("#tbl_wednesday tbody tr").size();
		var number = 0;
		number = size+1;
		$.post("/admin/joblog/get_wednesday_tr",{
			'number':number
		},function(data){
			 $("#btnWednesdayAdd").removeAttr("disabled");
			 $("#tbl_wednesday tbody").append( data );
			 $('#tbl_wednesday .date').datepicker({
					showOtherMonths: true,
			        selectOtherMonths: true,  
			        changeMonth: true,
			        changeYear: true,
			        prevText : '<i class="fa fa-chevron-left"></i>',
					nextText : '<i class="fa fa-chevron-right"></i>',
					dateFormat : 'yy-mm-dd'
				});
		});
	});
	
	//周四添加任务
	$("#btnThursdayAdd").click(function(){
		$(this).attr("disabled","disabled");
		var size = $("#tbl_thursday tbody tr").size();
		var number = 0;
		number = size+1;
		$.post("/admin/joblog/get_thursday_tr",{
			'number':number
		},function(data){
			 $("#btnThursdayAdd").removeAttr("disabled");
			 $("#tbl_thursday tbody").append( data );
			 $('#tbl_thursday .date').datepicker({
					showOtherMonths: true,
			        selectOtherMonths: true,  
			        changeMonth: true,
			        changeYear: true,
			        prevText : '<i class="fa fa-chevron-left"></i>',
					nextText : '<i class="fa fa-chevron-right"></i>',
					dateFormat : 'yy-mm-dd'
				});
		});
	});
	
	//周五添加任务
	$("#btnFridayAdd").click(function(){
		$(this).attr("disabled","disabled");
		var size = $("#tbl_friday tbody tr").size();
		var number = 0;
		number = size+1;
		$.post("/admin/joblog/get_friday_tr",{
			'number':number
		},function(data){
			 $("#btnFridayAdd").removeAttr("disabled");
			 $("#tbl_friday tbody").append( data );
			 $('#tbl_friday .date').datepicker({
					showOtherMonths: true,
			        selectOtherMonths: true,  
			        changeMonth: true,
			        changeYear: true,
			        prevText : '<i class="fa fa-chevron-left"></i>',
					nextText : '<i class="fa fa-chevron-right"></i>',
					dateFormat : 'yy-mm-dd'
				});
		});
	});
	
	//周六添加任务
	$("#btnSaturdayAdd").click(function(){
		$(this).attr("disabled","disabled");
		var size = $("#tbl_saturday tbody tr").size();
		var number = 0;
		number = size+1;
		$.post("/admin/joblog/get_saturday_tr",{
			'number':number
		},function(data){
			 $("#btnSaturdayAdd").removeAttr("disabled");
			 $("#tbl_saturday tbody").append( data );
			 $('#tbl_saturday .date').datepicker({
					showOtherMonths: true,
			        selectOtherMonths: true,  
			        changeMonth: true,
			        changeYear: true,
			        prevText : '<i class="fa fa-chevron-left"></i>',
					nextText : '<i class="fa fa-chevron-right"></i>',
					dateFormat : 'yy-mm-dd'
				});
		});
	});
	
	//周日添加任务
	$("#btnSundayAdd").click(function(){
		$(this).attr("disabled","disabled");
		var size = $("#tbl_sunday tbody tr").size();
		var number = 0;
		number = size+1;
		$.post("/admin/joblog/get_sunday_tr",{
			'number':number
		},function(data){
			 $("#btnSundayAdd").removeAttr("disabled");
			 $("#tbl_sunday tbody").append( data );
			 $('#tbl_sunday .date').datepicker({
					showOtherMonths: true,
			        selectOtherMonths: true,  
			        changeMonth: true,
			        changeYear: true,
			        prevText : '<i class="fa fa-chevron-left"></i>',
					nextText : '<i class="fa fa-chevron-right"></i>',
					dateFormat : 'yy-mm-dd'
				});
		});
	});
	
	
});
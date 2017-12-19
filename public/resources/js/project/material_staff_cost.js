$(function(){
	
	function set_material_total_rate()
	{
		var gcys_xj_total = $("#gcys_xj_total").val();
		var gdzc_xj_total = $("#gdzc_xj_total").val();
		var wxzc_xj_total = $("#wxzc_xj_total").val();
		var yfqywl_xj_total = $("#yfqywl_xj_total").val();
		var yycb_xj_total  = $("#yycb_xj_total").val();
		var glbgf_xj_total = $("#glbgf_xj_total").val();
		//var gsfy_yymj_total = $("#gsfy_yymj_total").val();
		var staff_cost = $("#staff_cost").val();
		var taxes_cost = $("#taxes_cost").val();
		var market_cost = $("#market_cost").val();
		var area_cost = $("#area_cost").val();
		
		var myChart = echarts.init(document.getElementById('chart')); 
		 $.ajax({
		        type:"POST",
		        async:false,
		        url:"/admin/material/set_material_total_rate",
		        dataType: "json", 
		        data:{
		               'gcys_xj_total':gcys_xj_total,
		               'gdzc_xj_total':gdzc_xj_total,
		               'wxzc_xj_total':wxzc_xj_total,
		               'yfqywl_xj_total':yfqywl_xj_total,
		               'yycb_xj_total':yycb_xj_total,
		               'glbgf_xj_total':glbgf_xj_total,
		               'staff_cost':staff_cost,
		               'taxes_cost':taxes_cost,
		               'market_cost':market_cost,
		               'area_cost':area_cost
					},
		        success:function(data){
				//	$("#sparks").html( data );
		        	totalValue=data.totalValue;
		        	rateValue=data.rateValue
		       }
		    }); 
		option = {
        	    title : {
        	        text: totalValue
        	    },
        	    tooltip : {
        	        trigger: 'axis'
        	    },
        	    legend: {
        	        data:['成本占比']
        	    },
        	    calculable : true,
        	    xAxis : [
        	        {
        	            type : 'category',
        	            data :  ['工程预算','固定资产','无形资产','研发期用物料','营业成本','管理办公费','公司费用','人员费用','市场费用','税金费用']
        	        }
        	    ],
        	    yAxis : [
        	        {
        	            type : 'value'
        	        }
        	    ],
        	    series : [
        	        {
        	            name:'成本占比',
        	            type:'bar',
        	            data: rateValue
        	        }
        	    ]
        	};
        // 为echarts对象加载数据 
        myChart.setOption(option); 
        
		return false;
		 $.ajax({
		        type:"POST",
		        async:false,
		        url:"/admin/material/set_material_total_rate",
		        dataType: "text", 
		        data:{
		               'gcys_xj_total':gcys_xj_total,
		               'gdzc_xj_total':gdzc_xj_total,
		               'wxzc_xj_total':wxzc_xj_total,
		               'yfqywl_xj_total':yfqywl_xj_total,
		               'yycb_xj_total':yycb_xj_total,
		               'glbgf_xj_total':glbgf_xj_total,
		               'gsfy_yymj_total':gsfy_yymj_total
					},
		        success:function(data){
					$("#sparks").html( data );
		       }
		    }); 
	}
	
	   function delStaffLsgTr(o){
		   $(o).parent().parent().remove().slideUp('slow');
	   }
	   
	   function  ForDight(Dight,How)    
	   {    
	      Dight  =  Math.round  (Dight*Math.pow(10,How))/Math.pow(10,How);    
	      return  Dight;    
	   }    
	
    $("#btn_staff_lsg_add").click(function(){
		   var p_name_id = $("#p_name_id").val();
		   var p_date = $("#p_date").val();
		   $.post("/admin/project/get_staff_lsg_tr",{
			   'p_name_id':p_name_id,
			   'p_date':p_date
		   },function(data){
			    $("#staff_lsg tbody").append(data);
		   });
		   
	});	

	$("#btn_staff_zsg_add").click(function(){
		   var p_name_id = $("#p_name_id").val();
		   var p_date = $("#p_date").val();
		   $.post("/admin/project/get_staff_zsg_tr",{
			   'p_name_id':p_name_id,
			   'p_date':p_date
		   },function(data){
			    $("#staff_zsg tbody").append(data);
		   });
		   
	}); 
	
	
	$("#p_name_id,#project_sn,#p_date").change(function(){
		  var p_name_id  = $("#p_name_id").val();
		  var p_date     = $("#p_date").val();
		  var project_sn = $("#project_sn").val();
		  
		   if(p_name_id=="" || project_sn=="" || p_date==""){
			  return false;
		   }	
		
		   //初始化人员成本表单
		   $.post("/admin/project/get_staff_list",{
			   'p_name_id':p_name_id,
			   'project_sn':project_sn,
			   'p_date':p_date
		   },function(data){
			   $("#dialog_staff_cost").html( data );
		   });
		   //初始化税金表单
		   $.post("/admin/project/get_taxes_list",{
			   'p_name_id':p_name_id,
			   'project_sn':project_sn,
			   'p_date':p_date
		   },function(data){
			   $("#dialog_taxes").html( data );
		   });
		   
	});
	
	
	$.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
		_title : function(title) {
			if (!this.options.title) {
				title.html("&#160;");
			} else {
				title.html(this.options.title);
			}
		}
	}));
	
	//研发阶段（人员成本）
	$('#dialog_staff_cost').dialog({
		autoOpen : false,
		width : 1400,
		resizable : true,
		modal : true,
		draggable : false,  
		title : "<div class='widget-header'><h4><i class='fa fa-hand-o-right'></i> 人员成本</h4></div>",
		buttons : [{
			html : "取消",
			"class" : "btn btn-default",
			click : function() {
				$(this).dialog("close");
			}
		}, {
			html : "合计",
			"class" : "btn btn-default",
			click : function() {
				//$(this).dialog("close");
				var total_lsg = 0;
				//$(o).parent().parent().parent().find("input[name='staff_section_date[]']:eq(0)").val();
				$("#dialog_staff_cost #staff_lsg tbody tr input[name='staff_salary_total[]']").each(function(o){
					var lsg_val =  $(this).val();
					lsg_val = lsg_val.replace(",","");
					total_lsg+=parseFloat(lsg_val);
				});
				 
				if(isNaN(total_lsg)){
					total_lsg=0;
				}
				
				
				$("#dialog_staff_cost #t_lsg_cost").html( ForDight(total_lsg,2) );
				$("#dialog_staff_cost #staff_lsg tfoot").show();
				
				//正式工
				var total_zsg = 0;
				$("#dialog_staff_cost #staff_zsg tbody tr input[name='staff_cost_total[]']").each(function(o){
					var zsg_val =  $(this).val();
					zsg_val = zsg_val.replace(",","");
					total_zsg+=parseFloat(zsg_val);
				});
				 
				if(isNaN(total_zsg)){
					total_zsg=0;
				}
				 
				
				$("#dialog_staff_cost #t_zsg_cost").html( ForDight(total_zsg,2) );
				$("#dialog_staff_cost #staff_zsg tfoot").show();
				$("#dialog_staff_cost #t_cost_y").html( ForDight((  parseFloat(total_zsg) + parseFloat(total_lsg)  ),2) );
			}
		},{
			html : "<i class='fa fa-check'></i>&nbsp; 提交",
			"class" : "btn btn-primary",
			click : function() {
				var post_str = "";
				//临时工
				var total_lsg = 0;
				$("#dialog_staff_cost #staff_lsg tbody tr").each(function(o){
					var staff_p_name         =  $(this).find("input[name='staff_p_name[]']:eq(0)").val();
					var staff_p_sn           =  $(this).find("input[name='staff_p_sn[]']:eq(0)").val();
					var staff_section_date   =  $(this).find("input[name='staff_section_date[]']:eq(0)").val();
					var staff_custom_date    =  $(this).find("input[name='staff_custom_date[]']:eq(0)").val();
					var staff_hour_nums      =  $(this).find("input[name='staff_hour_nums[]']:eq(0)").val();
					var staff_salary         =  $(this).find("input[name='staff_salary[]']:eq(0)").val();
					var staff_amount         =  $(this).find("input[name='staff_amount[]']:eq(0)").val();
					var staff_salary_total   =  $(this).find("input[name='staff_salary_total[]']:eq(0)").val();
					var lsg_val = 0;
					lsg_val = staff_salary_total;
					lsg_val = lsg_val.replace(",","");
					total_lsg+=parseFloat(lsg_val);
					//console.log(staff_p_name+"-"+staff_p_sn+"-"+staff_section_date+"-"+staff_custom_date+"-"+staff_hour_nums+"-"+staff_salary+"-"+staff_amount+"-"+staff_salary_total );
					var staff_insurance       =  "";
					var staff_cost_total      =  "";
					var staff_type="lsg";
					post_str += staff_p_name+"|"+staff_p_sn+"|"+staff_section_date+"|"+staff_custom_date+"|"+staff_hour_nums+"|"+staff_salary+"|"+staff_amount+"|"+staff_salary_total+"|"+staff_type+"|"+staff_insurance+"|"+staff_cost_total+"#";
				});
				//正式工
				var total_zsg = 0;
				$("#dialog_staff_cost #staff_zsg tbody tr").each(function(o){
					var staff_p_name         =  $(this).find("input[name='staff_p_name[]']:eq(0)").val();
					var staff_p_sn           =  $(this).find("input[name='staff_p_sn[]']:eq(0)").val();
					var staff_section_date   =  $(this).find("input[name='staff_section_date[]']:eq(0)").val();
					var staff_custom_date    =  $(this).find("input[name='staff_custom_date[]']:eq(0)").val();
					var staff_hour_nums      =  $(this).find("input[name='staff_hour_nums[]']:eq(0)").val();
					var staff_salary         =  $(this).find("input[name='staff_salary[]']:eq(0)").val();
					var staff_amount         =  $(this).find("input[name='staff_amount[]']:eq(0)").val();
					var staff_salary_total   =  $(this).find("input[name='staff_salary_total[]']:eq(0)").val();
					var staff_insurance      =  $(this).find("input[name='staff_insurance[]']:eq(0)").val();
					var staff_cost_total      =  $(this).find("input[name='staff_cost_total[]']:eq(0)").val();
					
					var zsg_val = 0;
					zsg_val = staff_cost_total;
					zsg_val = zsg_val.replace(",","");
					total_zsg+=parseFloat(zsg_val);
					var staff_type="zsg";
					post_str += staff_p_name+"|"+staff_p_sn+"|"+staff_section_date+"|"+staff_custom_date+"|"+staff_hour_nums+"|"+staff_salary+"|"+staff_amount+"|"+staff_salary_total+"|"+staff_type+"|"+staff_insurance+"|"+staff_cost_total+"#";
				});
				
				var p_name_id = $("#p_name_id").val();
				var p_date    = $("#p_date").val();
				var project_sn = $("#project_sn").val();
				
				$.post("/admin/project/do_save_staff_info",{
					 'post_str':post_str,
					 'p_date':p_date,
					 'p_name_id':p_name_id,
					 'project_sn':project_sn
				},function(data){
					
				},'json');
				
				if(isNaN(total_lsg)){
					total_lsg=0;
				}
				if(isNaN(total_zsg)){
					total_zsg=0;
				}
				var total_cost_staff=ForDight((  parseFloat(total_zsg) + parseFloat(total_lsg)  ),2);
				//var total_cost_staff = total_lsg +"-"+total_zsg;
				$("#staff_cost").val(total_cost_staff);
				set_material_total_rate();
				$(this).dialog("close");
			}
		}]
	});
	
	 
	$('#btn_staff_cost').click(function() {
		var p_name_id = $("#p_name_id").val();
		var p_date    = $("#p_date").val();
		var project_sn    = $("#project_sn").val();
		
		if(p_name_id==""){
			alert("请先选择立项名称!");
			$("#p_name_id").focus();
			return false;
		}
		if(project_sn==""){
			alert("请先选择立项编号!");
			$("#p_name_id").focus();
			return false;
		}
		
		if(p_date==""){
			alert("请选择立项分期!");
			$("#p_date").focus();
			return false;
		}
		
		$('#dialog_staff_cost').dialog('open');
		return false;
	});
	
	
	
	
});
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
	$('#dialog_taxes').dialog({
		autoOpen : false,
		width : 1000,
		resizable : true,
		modal : true,
		draggable : false,  
		title : "<div class='widget-header'><h4><i class='fa fa-hand-o-right'></i> 税金成本</h4></div>",
		buttons : [{
			html : "取消",
			"class" : "btn btn-default",
			click : function() {
				$(this).dialog("close");
			}
		},{
			html : "<i class='fa fa-check'></i>&nbsp; 提交",
			"class" : "btn btn-primary",
			click : function() {
				 var p_name_id = $("#p_name_id").val();
				 var p_date    = $("#p_date").val();
				 var sale_value = $("#tb_taxes tbody tr input[name='sale_value[]']").val();
				 var sale_rate  = $("#tb_taxes tbody tr input[name='sale_rate[]']").val();
				 var p_name     = $("#tb_taxes tbody tr input[name='staff_p_name[]']").val();
				 var p_sn       = $("#tb_taxes tbody tr input[name='staff_p_sn[]']").val();
				 var section_date = $("#tb_taxes tbody tr input[name='staff_section_date[]']").val();
				 var total_amount = $("#tb_taxes tbody tr input[name='total_amount[]']").val();
				 
				 $.post("/admin/material/set_taxes_info",{
					    'p_name_id':p_name_id,
					    'p_date':p_date,
					    'p_name':p_name,
					    'p_sn':p_sn,
					    'section_date':section_date,
					    'total_amount':total_amount,
						'sale_value':sale_value,
						'sale_rate':sale_rate
						 },function(data){
							 
						});
				$("#taxes_cost").val( total_amount );	 	
				 
				set_material_total_rate();
				$(this).dialog("close");
			}
		}]
	});
	
	$('#btn_taxes').click(function() {
		var p_name_id = $("#p_name_id").val();
		var p_date    = $("#p_date").val();
		
		if(p_name_id==""){
			alert("请先选择立项名称!");
			$("#p_name_id").focus();
			return false;
		}
		
		if(p_date==""){
			alert("请选择立项分期!");
			$("#p_date").focus();
			return false;
		}
		
		$('#dialog_taxes').dialog('open');
		return false;
	});
	
	
	
	
	
});
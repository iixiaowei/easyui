$(function(){
	/**
	 * 线形图（计划与实际）+环比增长率数值(实际客流)
	 */
	$("#jcsjpj_flow").click(function(){
		  if(  $("#jcsjpj_flow").hasClass("openDiv")  ){
			   $("#div_jcsjpj_line").show();
			   $("#jcsjpj_flow").removeClass("openDiv").addClass("closeDiv");
		   }else{
			   $("#div_jcsjpj_line").hide();
			   $("#jcsjpj_flow").removeClass("closeDiv").addClass("openDiv");
		   }	
	});
	
	var line_jcsjpj_plan_reality_flow = echarts.init(document.getElementById('line_jcsjpj_plan_reality_flow')); 
	$.ajax({
        type:"POST",
        async:false,
        url:"/admin/project/get_jcsjpj_flow_line",
        dataType: "json", 
        data:{
               'type':'month',
               'starttime':"2015-01-01",
               'endtime':"2015-12-01"
			},
        success:function(data){
        	 dataCategory = data.data_category;
        	 dataFlow = data.data_flow;
        	 dataPlanFlow = data.data_plan_flow;
        	 dataChainRate = data.data_chain_rate;
        	 subtext = data.subtext
       }
    }); 
	 
	option = {
			title : {
				text: '计划与实际客流、环比增长率数值',
		        subtext: subtext
			},
		    tooltip : {
		    	 trigger: 'axis'
		    },
		    legend: {
		        data:['实际客流','计划客流','环比增长率']
		    },
		    toolbox: {
		        show : true,
		        feature : {
		            mark : {show: true},
		            dataView : {show: true, readOnly: false},
		            magicType : {show: true, type: ['line', 'bar']},
		            restore : {show: true},
		            saveAsImage : {show: true}
		        }
		    },
		    dataZoom: {
		        show: true,
		        realtime : true,
		        start : 0
		    },
		    calculable : true,
		    xAxis : [
		        {
		            type : 'category',
		            data : dataCategory
		        }
		    ],
		    yAxis : [
		        {
		            type : 'value',
		            axisLabel : {
		                formatter: '{value}'
		            }
		        }
		    ],
		    series : [
		        {
		            name:'实际客流',
		            type:'line',
		            data:dataFlow
		        },
		        {
		            name:'计划客流',
		            type:'line',
		            data:dataPlanFlow
		        },
		        {
		            name:'环比增长率',
		            type:'line',
		            data:dataChainRate
		        }
		    ]
	};
	line_jcsjpj_plan_reality_flow.setOption(option);
	
	//线形图（客流与成本）
	var line_jcsjpj_flow_cost = echarts.init(document.getElementById('line_jcsjpj_flow_cost')); 
	$.ajax({
        type:"POST",
        async:false,
        url:"/admin/project/get_jcsjpj_cost_line",
        dataType: "json", 
        data:{
               'type':'month',
               'starttime':"2015-01-01",
               'endtime':"2015-12-01"
			},
        success:function(data){
        	 dataCategory = data.data_category;
        	 dataFlow = data.data_flow;
        	 dataCost = data.data_cost;
        	 subtext = data.subtext
       }
    }); 
	 
	option = {
			title : {
				text: '实际客流与实际成本',
		        subtext: subtext
			},
		    tooltip : {
		    	 trigger: 'axis'
		    },
		    legend: {
		        data:['实际客流','实际成本']
		    },
		    toolbox: {
		        show : true,
		        feature : {
		            mark : {show: true},
		            dataView : {show: true, readOnly: false},
		            magicType : {show: true, type: ['line', 'bar']},
		            restore : {show: true},
		            saveAsImage : {show: true}
		        }
		    },
		    dataZoom: {
		        show: true,
		        realtime : true,
		        start : 0
		    },
		    calculable : true,
		    xAxis : [
		        {
		            type : 'category',
		            data : dataCategory
		        }
		    ],
		    yAxis : [
		        {
		            type : 'value',
		            axisLabel : {
		                formatter: '{value}'
		            }
		        }
		    ],
		    series : [
		        {
		            name:'实际客流',
		            type:'line',
		            data:dataFlow
		        },
		        {
		            name:'实际成本',
		            type:'line',
		            data:dataCost
		        }
		    ]
	};
	line_jcsjpj_flow_cost.setOption(option);
	
	//线形图（客流与收入）
	var line_jcsjpj_flow_income = echarts.init(document.getElementById('line_jcsjpj_flow_income')); 
	$.ajax({
        type:"POST",
        async:false,
        url:"/admin/project/get_jcsjpj_income_line",
        dataType: "json", 
        data:{
               'type':'month',
               'starttime':"2015-01-01",
               'endtime':"2015-12-01"
			},
        success:function(data){
        	 dataCategory = data.data_category;
        	 dataFlow = data.data_flow;
        	 dataIncome = data.data_income;
        	 subtext = data.subtext
       }
    }); 
	 
	option = {
			title : {
				text: '实际客流与实际收入',
		        subtext: subtext
			},
		    tooltip : {
		    	 trigger: 'axis'
		    },
		    legend: {
		        data:['实际客流','实际收入']
		    },
		    toolbox: {
		        show : true,
		        feature : {
		            mark : {show: true},
		            dataView : {show: true, readOnly: false},
		            magicType : {show: true, type: ['line', 'bar']},
		            restore : {show: true},
		            saveAsImage : {show: true}
		        }
		    },
		    dataZoom: {
		        show: true,
		        realtime : true,
		        start : 0
		    },
		    calculable : true,
		    xAxis : [
		        {
		            type : 'category',
		            data : dataCategory
		        }
		    ],
		    yAxis : [
		        {
		            type : 'value',
		            axisLabel : {
		                formatter: '{value}'
		            }
		        }
		    ],
		    series : [
		        {
		            name:'实际客流',
		            type:'line',
		            data:dataFlow
		        },
		        {
		            name:'实际收入',
		            type:'line',
		            data:dataIncome
		        }
		    ]
	};
	line_jcsjpj_flow_income.setOption(option);
	
});
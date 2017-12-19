$(function(){
	//立项阶段构成比
	var pdate_pie = echarts.init(document.getElementById('pie_pdate_rate')); 
	$.ajax({
        type:"POST",
        async:false,
        url:"/admin/project/get_pdate_rate",
        dataType: "json", 
        data:{
               'postStr':''
			},
        success:function(data){
        	rateValue=data.rateValue;
       }
    }); 
	option = {
		    title : {
		        x:'center'
		    },
		    tooltip : {
		        trigger: 'item',
		        formatter: "{a} <br/>{b} : {c} ({d}%)"
		    },
		    legend: {
		        orient : 'vertical',
		        x : 'left',
		        data:['研发阶段','试运营阶段','运营阶段']
		    },
		    toolbox: {
		        show : true,
		        feature : {
		            mark : {show: true},
		            dataView : {show: true, readOnly: false},
		            magicType : {
		                show: true, 
		                type: ['pie', 'funnel'],
		                option: {
		                    funnel: {
		                        x: '25%',
		                        width: '50%',
		                        funnelAlign: 'left',
		                        max: 1548
		                    }
		                }
		            },
		            restore : {show: true},
		            saveAsImage : {show: true}
		        }
		    },
		    calculable : true,
		    series : [
		        {
		            name:'立项阶段构成比',
		            type:'pie',
		            radius : '55%',
		            center: ['50%', '60%'],
		            data:rateValue
		        }
		    ]
		};
	pdate_pie.setOption(option); 
	
	//立项类型构成比
	var ptype_pie = echarts.init(document.getElementById('pie_ptype_rate')); 
	$.ajax({
        type:"POST",
        async:false,
        url:"/admin/project/get_ptype_rate",
        dataType: "json", 
        data:{
               'postStr':''
			},
        success:function(data){
        	rateValue=data.rateValue;
       }
    }); 
	option = {
		    title : {
		        x:'center'
		    },
		    tooltip : {
		        trigger: 'item',
		        formatter: "{a} <br/>{b} : {c} ({d}%)"
		    },
		    legend: {
		        orient : 'vertical',
		        x : 'left',
		        data:['单点(馆)游戏类项目','主线游戏类项目','平台服务类项目','市场运营类项目','维修维护类项目','其它']
		    },
		    toolbox: {
		        show : true,
		        feature : {
		            mark : {show: true},
		            dataView : {show: true, readOnly: false},
		            magicType : {
		                show: true, 
		                type: ['pie', 'funnel'],
		                option: {
		                    funnel: {
		                        x: '25%',
		                        width: '50%',
		                        funnelAlign: 'left',
		                        max: 1548
		                    }
		                }
		            },
		            restore : {show: true},
		            saveAsImage : {show: true}
		        }
		    },
		    calculable : true,
		    series : [
		        {
		            name:'立项类型构成比',
		            type:'pie',
		            radius : '55%',
		            center: ['50%', '60%'],
		            data:rateValue
		        }
		    ]
		};
	ptype_pie.setOption(option); 
	
	/////////立项成本构成
	var pcost_pie = echarts.init(document.getElementById('pie_pcost_rate')); 
	$.ajax({
        type:"POST",
        async:false,
        url:"/admin/project/get_pcost_rate",
        dataType: "json", 
        data:{
               'postStr':''
			},
        success:function(data){
        	rateValue=data.rateValue;
        	rateField=data.rateField;
       }
    }); 
	option = {
		    title : {
		        x:'center'
		    },
		    tooltip : {
		        trigger: 'item',
		        formatter: "{a} <br/>{b} : {c} ({d}%)"
		    },
		    legend: {
		        orient : 'vertical',
		        x : 'left',
		        data:rateField
		    },
		    toolbox: {
		        show : true,
		        feature : {
		            mark : {show: true},
		            dataView : {show: true, readOnly: false},
		            magicType : {
		                show: true, 
		                type: ['pie', 'funnel'],
		                option: {
		                    funnel: {
		                        x: '25%',
		                        width: '50%',
		                        funnelAlign: 'left',
		                        max: 1548
		                    }
		                }
		            },
		            restore : {show: true},
		            saveAsImage : {show: true}
		        }
		    },
		    calculable : true,
		    series : [
		        {
		            name:'立项成本构成',
		            type:'pie',
		            radius : '55%',
		            center: ['50%', '60%'],
		            data:rateValue
		        }
		    ]
		};
	pcost_pie.setOption(option); 
	
	///综合评价
	//收入 曲线
	var zh_income_pie = echarts.init(document.getElementById('pie_zh_income')); 
	$.ajax({
        type:"POST",
        async:false,
        url:"/admin/project/get_zh_income",
        dataType: "json", 
        data:{
               'type':'day'
			},
        success:function(data){
        	 dataCategory = data.data_category;
        	 dataPlan = data.data_plan;
        	 dataReality = data.data_reality;
        	 dataRate = data.data_rate;
        	 subtext = data.subtext
       }
    }); 
	 
	option = {
			title : {
			        subtext: subtext
			},
		    tooltip : {
		        trigger: 'axis'
		    },
		    legend: {
		        data:['计划收入','实际收入','计划环比增长率']
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
		                formatter: '{value}.00 元'
		            }
		            	
		        }
		    ],
		    series : [
		        {
		            name:'计划收入',
		            type:'line',
		            data:dataPlan
		        },
		        {
		            name:'实际收入',
		            type:'line',
		            data:dataReality
		        },
		        {
		            name:'计划环比增长率',
		            type:'line',
		            data:dataRate
		        }
		    ]
	};
	zh_income_pie.setOption(option);
	
	
	
});
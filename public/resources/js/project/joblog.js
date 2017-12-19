$(function(){
	var s_year = $("#s_year").val();
	//工作类别占比
	var pie_worktype = echarts.init(document.getElementById('pie_worktype')); 
	$.ajax({
        type:"POST",
        async:false,
        url:"/admin/joblog/get_pie_worktype",
        dataType: "json", 
        data:{
               's_year':s_year
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
		        data:['报告-市场调研','方案-产品策划','方案-产品测试','方案-产品推广','方案-产品培训','方案-绩效考核','方案-预算编制','方案-立项审批','执行-物料采购','执行-宣传设计','执行-票种上线','执行-运营培训','执行-运营测试','执行-数据分析','执行-产品验收','其他']
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
		            name:'工作类别占比',
		            type:'pie',
		            radius : '55%',
		            center: ['50%', '60%'],
		            data:rateValue
		        }
		    ]
		};
	pie_worktype.setOption(option); 
	
	//优良中差占比
	var pie_evaluate = echarts.init(document.getElementById('pie_evaluate')); 
	$.ajax({
        type:"POST",
        async:false,
        url:"/admin/joblog/get_pie_evaluate",
        dataType: "json", 
        data:{
               's_year':s_year
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
		        data:['优','良','中','差']
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
		            name:'优良中差占比',
		            type:'pie',
		            radius : '55%',
		            center: ['50%', '60%'],
		            data:rateValue
		        }
		    ]
		};
	pie_evaluate.setOption(option); 
	
});
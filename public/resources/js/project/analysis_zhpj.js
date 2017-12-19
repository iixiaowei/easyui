$(function(){
	 var now = new Date();       //当前日期
	 var nowDayOfWeek = now.getDay();         //今天本周的第几天     
	 var nowDay = now.getDate();              //当前日    
	 var nowMonth = now.getMonth();           //当前月    
	 var nowYear = now.getYear();             //当前年 
	 nowYear += (nowYear < 2000) ? 1900 : 0;  //     

	//格式化日期：yyyy-MM-dd     
	 function formatDate(date) {      
	     var myyear = date.getFullYear();     
	     var mymonth = date.getMonth()+1;     
	     var myweekday = date.getDate();      
	          
	     if(mymonth < 10){     
	         mymonth = "0" + mymonth;     
	     }      
	     if(myweekday < 10){     
	         myweekday = "0" + myweekday;     
	     }     
	     return (myyear+"-"+mymonth + "-" + myweekday);      
	 }           
	 
	//获得某月的天数     
	 function getMonthDays(myMonth){     
	     var monthStartDate = new Date(nowYear, myMonth, 1);      
	     var monthEndDate = new Date(nowYear, myMonth + 1, 1);      
	     var   days   =   (monthEndDate   -   monthStartDate)/(1000   *   60   *   60   *   24);      
	     return   days;      
	 }    

	//获得本季度的开始月份     
	 function getQuarterStartMonth(){     
	     var quarterStartMonth = 0;     
	     if(nowMonth<3){     
	        quarterStartMonth = 0;     
	     }     
	     if(2<nowMonth && nowMonth<6){     
	        quarterStartMonth = 3;     
	     }     
	     if(5<nowMonth && nowMonth<9){     
	        quarterStartMonth = 6;     
	     }     
	     if(nowMonth>8){     
	        quarterStartMonth = 9;     
	     }     
	     return quarterStartMonth;     
	 }     

	//获得本周的开始日期     
	 function getWeekStartDate() {      
	     var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek+1);      
	     return formatDate(weekStartDate);     
	 }    

	//获得本周的结束日期     
	 function getWeekEndDate() {      
	     var weekEndDate = new Date(nowYear, nowMonth, nowDay + (7 - nowDayOfWeek));      
	     return formatDate(weekEndDate);     
	 }    

	//获得本月的开始日期     
	 function getMonthStartDate(){     
	     var monthStartDate = new Date(nowYear, nowMonth, 1);      
	     return formatDate(monthStartDate);     
	 }     
	     
	 //获得本月的结束日期     
	 function getMonthEndDate(){     
	     var monthEndDate = new Date(nowYear, nowMonth, getMonthDays(nowMonth));      
	     return formatDate(monthEndDate);     
	 }     
	     
	 //获得本季度的开始日期     
	 function getQuarterStartDate(){     
	          
	     var quarterStartDate = new Date(nowYear, getQuarterStartMonth(), 1);      
	     return formatDate(quarterStartDate);     
	 }     
	     
	 //或的本季度的结束日期     
	 function getQuarterEndDate(){     
	     var quarterEndMonth = getQuarterStartMonth() + 2;     
	     var quarterStartDate = new Date(nowYear, quarterEndMonth, getMonthDays(quarterEndMonth));      
	     return formatDate(quarterStartDate);     
	 } 

	 function getYearStartDate(){
		 var year=now.getFullYear();
		 var startYear=new Date(year+"-01-01");
		 return formatDate(startYear);
	 }

	 function getYearEndDate(){
		 var year=now.getFullYear();
		 var endYear=new Date(year+"-12-31");
		 return formatDate(endYear);
	 }
	 
	 $("#btnZhIncomeToday").click(function(){
		 $("#btnZhIncomeToday").removeClass("btn-info").addClass("btn-danger");
		 $("#btnZhIncomeWeek").removeClass("btn-danger").addClass("btn-info");
		 $("#btnZhIncomeMonth").removeClass("btn-danger").addClass("btn-info");
		 $("#btnZhIncomeQuarter").removeClass("btn-danger").addClass("btn-info");
		 $("#btnZhIncomeYear").removeClass("btn-danger").addClass("btn-info");
		 
		 var myDate=new Date();
		 //alert(myDate.getFullYear() + "-" + (myDate.getMonth()+1) + "-" + myDate.getDate());
		 //myDate.setMonth(myDate.getMonth()-1);
		 var year = myDate.getFullYear();
		 var month = myDate.getMonth()+1;
		 var day = myDate.getDate();
			 month = parseInt(month);
			 day = parseInt(day);
			 if(month<10){
                 month="0"+month;
		     }
		     if(day<10){
                 day = "0"+day;
			 }
		  var fullYear = year+"-"+month+"-"+day;
		  loadLineZhpj(fullYear,fullYear,'day');
	 });

	 $("#btnZhIncomeWeek").click(function(){
		 $("#btnZhIncomeToday").removeClass("btn-danger").addClass("btn-info");
		 $("#btnZhIncomeWeek").removeClass("btn-info").addClass("btn-danger");
		 $("#btnZhIncomeMonth").removeClass("btn-danger").addClass("btn-info");
		 $("#btnZhIncomeQuarter").removeClass("btn-danger").addClass("btn-info");
		 $("#btnZhIncomeYear").removeClass("btn-danger").addClass("btn-info");
		 
		  var s_time = getWeekStartDate();
		  var e_time = getWeekEndDate();
		  loadLineZhpj(s_time,e_time,'week');
	 });
	 
	 $("#btnZhIncomeMonth").click(function(){
		  var s_time = getMonthStartDate();
		  var e_time = getMonthEndDate();
		  $("#btnZhIncomeToday").removeClass("btn-danger").addClass("btn-info");
		  $("#btnZhIncomeWeek").removeClass("btn-danger").addClass("btn-info");
		  $("#btnZhIncomeMonth").removeClass("btn-info").addClass("btn-danger");
		  $("#btnZhIncomeQuarter").removeClass("btn-danger").addClass("btn-info");
		  $("#btnZhIncomeYear").removeClass("btn-danger").addClass("btn-info");
			 
		  loadLineZhpj(s_time,e_time,'week'); 	
	 });    

	 $("#btnZhIncomeQuarter").click(function(){
		  var s_time = getQuarterStartDate();
		  var e_time = getQuarterEndDate();
		  $("#btnZhIncomeToday").removeClass("btn-danger").addClass("btn-info");
		  $("#btnZhIncomeWeek").removeClass("btn-danger").addClass("btn-info");
		  $("#btnZhIncomeMonth").removeClass("btn-danger").addClass("btn-info");
		  $("#btnZhIncomeQuarter").removeClass("btn-info").addClass("btn-danger");
		  $("#btnZhIncomeYear").removeClass("btn-danger").addClass("btn-info");
			 
		  loadLineZhpj(s_time,e_time,'week'); 		
	 });

	 $("#btnZhIncomeYear").click(function(){
		  var s_time = getYearStartDate();
		  var e_time = getYearEndDate();
		  $("#btnZhIncomeToday").removeClass("btn-danger").addClass("btn-info");
		  $("#btnZhIncomeWeek").removeClass("btn-danger").addClass("btn-info");
		  $("#btnZhIncomeMonth").removeClass("btn-danger").addClass("btn-info");
		  $("#btnZhIncomeQuarter").removeClass("btn-danger").addClass("btn-info");
		  $("#btnZhIncomeYear").removeClass("btn-info").addClass("btn-danger");
			 
		  loadLineZhpj(s_time,e_time,'week'); 		
	 });
	 
	 function loadLineZhpj(starttime,endtime,type){
			var zh_income_pie = echarts.init(document.getElementById('pie_zh_income')); 
			$.ajax({
		        type:"POST",
		        async:false,
		        url:"/admin/project/get_zh_income",
		        dataType: "json", 
		        data:{
		               'type':type,
		               'starttime':starttime,
		               'endtime':endtime
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
	 }
	 
	 
	 //////综合评价 成本 曲线图
	 //////按月份成本与收入曲线
	 var pie_zhpj_cost = echarts.init(document.getElementById('pie_zhpj_cost')); 
		$.ajax({
	        type:"POST",
	        async:false,
	        url:"/admin/project/get_zhpj_cost_line",
	        dataType: "json", 
	        data:{
	               'type':'month',
	               'starttime':"2015-01-01",
	               'endtime':"2015-12-01"
				},
	        success:function(data){
	        	 dataCategory = data.data_category;
	        	 dataCost = data.data_cost;
	        	 dataIncome = data.data_income;
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
			        data:['实际成本','实际收入']
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
			            name:'实际成本',
			            type:'line',
			            data:dataCost
			        },
			        {
			            name:'实际收入',
			            type:'line',
			            data:dataIncome
			        }
			    ]
		};
		pie_zhpj_cost.setOption(option);
		
		//综合评价  费用曲线
		//实际客流、实际收入
		 var pie_zhpj_charge = echarts.init(document.getElementById('pie_zhpj_charge')); 
			$.ajax({
		        type:"POST",
		        async:false,
		        url:"/admin/project/get_zhpj_charge_line",
		        dataType: "json", 
		        data:{
		               'type':'month',
		               'starttime':"2015-01-01",
		               'endtime':"2015-12-01"
					},
		        success:function(data){
		        	 dataCategory = data.data_category;
		        	 dataFlow = data.data_flow;
		        	 dataCharge= data.data_charge;
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
				        data:['实际客流','实际费用']
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
				            name:'实际费用',
				            type:'line',
				            data:dataCharge
				        }
				    ]
			};
			pie_zhpj_charge.setOption(option);
	
	
});
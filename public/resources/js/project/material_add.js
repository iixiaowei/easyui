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

$(function(){
	set_material_total_rate();
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
	
	/**
	 * 工程预算
	 */
	// 小计
	function gcys_subtotal(){
		//单价
		var gcys_design_price = $("#gcys_design_price").val();
		var gcys_zx_price = $("#gcys_zx_price").val();
		var gcys_ktxt_price = $("#gcys_ktxt_price").val();
		var gcys_xfxt_price = $("#gcys_xfxt_price").val();
		var gcys_dj_price = $("#gcys_dj_price").val();
		var gcys_dx_price = $("#gcys_dx_price").val();
		var gcys_qt_price = $("#gcys_qt_price").val();
		var total_price = 0 ;
		
		if(gcys_design_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gcys_design_price);
		}
		if(gcys_zx_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gcys_zx_price);
		}
		if(gcys_ktxt_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gcys_ktxt_price);
		}
		if(gcys_xfxt_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gcys_xfxt_price);
		}
		if(gcys_dj_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gcys_dj_price);
		}
		if(gcys_dx_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gcys_dx_price);
		}
		if(gcys_qt_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gcys_qt_price);
		}
		
		$("#gcys_xj_price").val( total_price );
		//数量
		var gcys_design_amount = $("#gcys_design_amount").val();
		var gcys_zx_amount = $("#gcys_zx_amount").val();
		var gcys_ktxt_amount = $("#gcys_ktxt_amount").val();
		var gcys_xfxt_amount = $("#gcys_xfxt_amount").val();
		var gcys_dj_amount = $("#gcys_dj_amount").val();
		var gcys_dx_amount = $("#gcys_dx_amount").val();
		var gcys_qt_amount = $("#gcys_qt_amount").val();
		var total_amount = 0 ;
		
		if(gcys_design_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gcys_design_amount);
		}
		if(gcys_zx_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gcys_zx_amount);
		}
		if(gcys_ktxt_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gcys_ktxt_amount);
		}
		if(gcys_xfxt_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gcys_xfxt_amount);
		}
		if(gcys_dj_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gcys_dj_amount);
		}
		if(gcys_dx_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gcys_dx_amount);
		}
		if(gcys_qt_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gcys_qt_amount);
		}
		
		$("#gcys_xj_amount").val( total_amount );
		//使用期限（天）
		var gcys_design_date = $("#gcys_design_date").val();
		var gcys_zx_date = $("#gcys_zx_date").val();
		var gcys_ktxt_date = $("#gcys_ktxt_date").val();
		var gcys_xfxt_date = $("#gcys_xfxt_date").val();
		var gcys_dj_date = $("#gcys_dj_date").val();
		var gcys_dx_date = $("#gcys_dx_date").val();
		var gcys_qt_date = $("#gcys_qt_date").val();
		var total_date = 0 ;
		
		if(gcys_design_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gcys_design_date);
		}
		if(gcys_zx_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gcys_zx_date);
		}
		if(gcys_ktxt_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gcys_ktxt_date);
		}
		if(gcys_xfxt_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gcys_xfxt_date);
		}
		if(gcys_dj_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gcys_dj_date);
		}
		if(gcys_dx_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gcys_dx_date);
		}
		if(gcys_qt_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gcys_qt_date);
		}
		
		$("#gcys_xj_date").val( total_date );
		//合计（数量*单价）
		var gcys_design_total = $("#gcys_design_total").val();
		var gcys_zx_total = $("#gcys_zx_total").val();
		var gcys_ktxt_total = $("#gcys_ktxt_total").val();
		var gcys_xfxt_total = $("#gcys_xfxt_total").val();
		var gcys_dj_total = $("#gcys_dj_total").val();
		var gcys_dx_total = $("#gcys_dx_total").val();
		var gcys_qt_total = $("#gcys_qt_total").val();
		var total_total = 0 ;
		
		if(gcys_design_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gcys_design_total);
		}
		if(gcys_zx_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gcys_zx_total);
		}
		if(gcys_ktxt_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gcys_ktxt_total);
		}
		if(gcys_xfxt_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gcys_xfxt_total);
		}
		if(gcys_dj_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gcys_dj_total);
		}
		if(gcys_dx_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gcys_dx_total);
		}
		if(gcys_qt_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gcys_qt_total);
		}
		
		$("#gcys_xj_total").val( total_total );
		set_material_total_rate();
	}
	
	$("#market_cost").blur(function(){
		var market_cost = $("#market_cost").val();
		if(!isDecimal(market_cost)){
			$("#market_cost").val("").focus();
			return false;
		}
		if(isNull(market_cost)){
			$("#market_cost").focus();
			return false;
		}
		set_material_total_rate();
	});
	
	$("#area_cost").blur(function(){
		var area_cost = $("#area_cost").val();
		if(!isDecimal(area_cost)){
			$("#area_cost").val("").focus();
			return false;
		}
		if(isNull(area_cost)){
			$("#area_cost").focus();
			return false;
		}
		set_material_total_rate();
	});
	
	//设计 start
	$("#gcys_design_price,#gcys_design_amount,#gcys_design_date").blur(function(){
		
		var gcys_design_price = $("#gcys_design_price").val();
		var gcys_design_amount = $("#gcys_design_amount").val();
		var gcys_design_date  = $("#gcys_design_date").val();
		
		if(!isDecimal(gcys_design_price)){
			$("#gcys_design_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_design_amount)){
			$("#gcys_design_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_design_date)){
			$("#gcys_design_date").val("").focus();
			return false;
		}
		
		if(isNull(gcys_design_price)){
			$("#gcys_design_price").focus();
			return false;
		}
		
		if(isNull(gcys_design_amount)){
			$("#gcys_design_amount").focus();
			return false;
		}
		
		if(isNull(gcys_design_date)){
			$("#gcys_design_date").focus();
			return false;
		}
		
		$("#gcys_design_total").val( gcys_design_date * gcys_design_price  *  gcys_design_amount );
		gcys_subtotal();
	});
	
	// 装修 start
	$("#gcys_zx_price,#gcys_zx_amount,#gcys_zx_date").blur(function(){
		
		var gcys_zx_price = $("#gcys_zx_price").val();
		var gcys_zx_amount = $("#gcys_zx_amount").val();
		var gcys_zx_date = $("#gcys_zx_date").val();
		
		 
		if(!isDecimal(gcys_zx_price)){
			$("#gcys_zx_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_zx_amount)){
			$("#gcys_zx_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_zx_date)){
			$("#gcys_zx_date").val("").focus();
			return false;
		}
		
		
		if(isNull(gcys_zx_price)){
			$("#gcys_zx_price").focus();
			return false;
		}
		
		if(isNull(gcys_zx_amount)){
			$("#gcys_zx_amount").focus();
			return false;
		}
		
		if(isNull(gcys_zx_date)){
			$("#gcys_zx_date").focus();
			return false;
		}
		
		$("#gcys_zx_total").val( gcys_zx_date * gcys_zx_price  *  gcys_zx_amount );
		gcys_subtotal();
	});
	
	// 空调系统 start
	$("#gcys_ktxt_price,#gcys_ktxt_amount,#gcys_ktxt_date").blur(function(){
		
		var gcys_ktxt_price = $("#gcys_ktxt_price").val();
		var gcys_ktxt_amount = $("#gcys_ktxt_amount").val();
		var gcys_ktxt_date = $("#gcys_ktxt_date").val();
		
		if(!isDecimal(gcys_ktxt_price)){
			$("#gcys_ktxt_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_ktxt_amount)){
			$("#gcys_ktxt_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_ktxt_date)){
			$("#gcys_ktxt_date").val("").focus();
			return false;
		}
		
		if(isNull(gcys_ktxt_price)){
			$("#gcys_ktxt_price").val("").focus();
			return false;
		}
		
		if(isNull(gcys_ktxt_amount)){
			$("#gcys_ktxt_amount").val("").focus();
			return false;
		}
		
		if(isNull(gcys_ktxt_date)){
			$("#gcys_ktxt_date").val("").focus();
			return false;
		}
		
		$("#gcys_ktxt_total").val( gcys_ktxt_date * gcys_ktxt_price  *  gcys_ktxt_amount );	
		gcys_subtotal();
	});
	
	// 消防系统 start
	$("#gcys_xfxt_price,#gcys_xfxt_amount,#gcys_xfxt_date").blur(function(){
		
		var gcys_xfxt_price = $("#gcys_xfxt_price").val();
		var gcys_xfxt_amount = $("#gcys_xfxt_amount").val();
		var gcys_xfxt_date = $("#gcys_xfxt_date").val();
		
		if(!isDecimal(gcys_xfxt_price)){
			$("#gcys_xfxt_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_xfxt_amount)){
			$("#gcys_xfxt_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_xfxt_date)){
			$("#gcys_xfxt_date").val("").focus();
			return false;
		}
		
		if(isNull(gcys_xfxt_price)){
			$("#gcys_xfxt_price").val("").focus();
			return false;
		}
		
		if(isNull(gcys_xfxt_amount)){
			$("#gcys_xfxt_amount").val("").focus();
			return false;
		}
		
		if(isNull(gcys_xfxt_date)){
			$("#gcys_xfxt_date").val("").focus();
			return false;
		}
		
		$("#gcys_xfxt_total").val( gcys_xfxt_date * gcys_xfxt_price  *  gcys_xfxt_amount );	
		gcys_subtotal();
	});
	
	// 灯具 start
	$("#gcys_dj_price,#gcys_dj_amount,#gcys_dj_date").blur(function(){
		
		var gcys_dj_price = $("#gcys_dj_price").val();
		var gcys_dj_amount = $("#gcys_dj_amount").val();
		var gcys_dj_date = $("#gcys_dj_date").val();
		
		if(!isDecimal(gcys_dj_price)){
			$("#gcys_dj_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_dj_amount)){
			$("#gcys_dj_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_dj_date)){
			$("#gcys_dj_date").val("").focus();
			return false;
		}
		
		if(isNull(gcys_dj_price)){
			$("#gcys_dj_price").val("").focus();
			return false;
		}
		
		if(isNull(gcys_dj_amount)){
			$("#gcys_dj_amount").val("").focus();
			return false;
		}
		
		if(isNull(gcys_dj_date)){
			$("#gcys_dj_date").val("").focus();
			return false;
		}
		
		$("#gcys_dj_total").val( gcys_dj_date * gcys_dj_price  *  gcys_dj_amount );
		gcys_subtotal();
	});
	
	// 灯箱  start
	$("#gcys_dx_price,#gcys_dx_amount,#gcys_dx_date").blur(function(){
		
		var gcys_dx_price = $("#gcys_dx_price").val();
		var gcys_dx_amount = $("#gcys_dx_amount").val();
		var gcys_dx_date = $("#gcys_dx_date").val();
		
		
		if(!isDecimal(gcys_dx_price)){
			$("#gcys_dx_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_dx_amount)){
			$("#gcys_dx_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_dx_date)){
			$("#gcys_dx_date").val("").focus();
			return false;
		}
		
		if(isNull(gcys_dx_price)){
			$("#gcys_dx_price").val("").focus();
			return false;
		}
		
		if(isNull(gcys_dx_amount)){
			$("#gcys_dx_amount").val("").focus();
			return false;
		}
		
		if(isNull(gcys_dx_date)){
			$("#gcys_dx_date").val("").focus();
			return false;
		}
		
		$("#gcys_dx_total").val( gcys_dx_date * gcys_dx_price  *  gcys_dx_amount );	
		gcys_subtotal();
	});
	
	
	// 其他  start
	$("#gcys_qt_price,#gcys_qt_amount,#gcys_qt_date").blur(function(){
		
		var gcys_qt_price = $("#gcys_qt_price").val();
		var gcys_qt_amount = $("#gcys_qt_amount").val();
		var gcys_qt_date = $("#gcys_qt_date").val();
		
		if(!isDecimal(gcys_qt_price)){
			$("#gcys_qt_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_qt_amount)){
			$("#gcys_qt_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gcys_qt_date)){
			$("#gcys_qt_date").val("").focus();
			return false;
		}
		
		if(isNull(gcys_qt_price)){
			$("#gcys_qt_price").val("").focus();
			return false;
		}
		
		if(isNull(gcys_qt_amount)){
			$("#gcys_qt_amount").val("").focus();
			return false;
		}
		
		if(isNull(gcys_qt_date)){
			$("#gcys_qt_date").val("").focus();
			return false;
		}
		
		$("#gcys_qt_total").val( gcys_qt_date * gcys_qt_price  *  gcys_qt_amount );	
		gcys_subtotal();
	});
	
	/**
	 * 固定资产 start
	 */
	//固定资产 小计
	function gdzc_subtotal()
	{
		//单价
		var gdzc_bgsb_price = $("#gdzc_bgsb_price").val();
		var gdzc_dzsb_price = $("#gdzc_dzsb_price").val();
		var gdzc_jj_price   = $("#gdzc_jj_price").val();
		var gdzc_jxsb_price = $("#gdzc_jxsb_price").val();
		var gdzc_ggsb_price = $("#gdzc_ggsb_price").val();
		var gdzc_jtsb_price = $("#gdzc_jtsb_price").val();
		var gdzc_qt_price   = $("#gdzc_qt_price").val();
		var total_price = 0;
		
		if(gdzc_bgsb_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gdzc_bgsb_price);
		}
		if(gdzc_dzsb_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gdzc_dzsb_price);
		}
		if(gdzc_jj_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gdzc_jj_price);
		}
		if(gdzc_jxsb_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gdzc_jxsb_price);
		}
		if(gdzc_ggsb_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gdzc_ggsb_price);
		}
		if(gdzc_jtsb_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gdzc_jtsb_price);
		}
		if(gdzc_qt_price!=""){
			total_price = parseFloat(total_price) + parseFloat(gdzc_qt_price);
		}
		
		$("#gdzc_xj_price").val( total_price );
		
		//数量
		var gdzc_bgsb_amount = $("#gdzc_bgsb_amount").val();
		var gdzc_dzsb_amount = $("#gdzc_dzsb_amount").val();
		var gdzc_jj_amount   = $("#gdzc_jj_amount").val();
		var gdzc_jxsb_amount = $("#gdzc_jxsb_amount").val();
		var gdzc_ggsb_amount = $("#gdzc_ggsb_amount").val();
		var gdzc_jtsb_amount = $("#gdzc_jtsb_amount").val();
		var gdzc_qt_amount   = $("#gdzc_qt_amount").val();
		var total_amount = 0;
		
		if(gdzc_bgsb_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gdzc_bgsb_amount);
		}
		if(gdzc_dzsb_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gdzc_dzsb_amount);
		}
		if(gdzc_jj_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gdzc_jj_amount);
		}
		if(gdzc_jxsb_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gdzc_jxsb_amount);
		}
		if(gdzc_ggsb_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gdzc_ggsb_amount);
		}
		if(gdzc_jtsb_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gdzc_jtsb_amount);
		}
		if(gdzc_qt_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(gdzc_qt_amount);
		}
		
		$("#gdzc_xj_amount").val( total_amount );
		//使用期限（天）
		var gdzc_bgsb_date = $("#gdzc_bgsb_date").val();
		var gdzc_dzsb_date = $("#gdzc_dzsb_date").val();
		var gdzc_jj_date   = $("#gdzc_jj_date").val();
		var gdzc_jxsb_date = $("#gdzc_jxsb_date").val();
		var gdzc_ggsb_date = $("#gdzc_ggsb_date").val();
		var gdzc_jtsb_date = $("#gdzc_jtsb_date").val();
		var gdzc_qt_date   = $("#gdzc_qt_date").val();
		var total_date = 0;
		
		if(gdzc_bgsb_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gdzc_bgsb_date);
		}
		if(gdzc_dzsb_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gdzc_dzsb_date);
		}
		if(gdzc_jj_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gdzc_jj_date);
		}
		if(gdzc_jxsb_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gdzc_jxsb_date);
		}
		if(gdzc_ggsb_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gdzc_ggsb_date);
		}
		if(gdzc_jtsb_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gdzc_jtsb_date);
		}
		if(gdzc_qt_date!=""){
			total_date = parseFloat(total_date) + parseFloat(gdzc_qt_date);
		}
		
		$("#gdzc_xj_date").val( total_date );
		// 	合计（数量*单价）
		var gdzc_bgsb_total = $("#gdzc_bgsb_total").val();
		var gdzc_dzsb_total = $("#gdzc_dzsb_total").val();
		var gdzc_jj_total   = $("#gdzc_jj_total").val();
		var gdzc_jxsb_total = $("#gdzc_jxsb_total").val();
		var gdzc_ggsb_total = $("#gdzc_ggsb_total").val();
		var gdzc_jtsb_total = $("#gdzc_jtsb_total").val();
		var gdzc_qt_total   = $("#gdzc_qt_total").val();
		var total_total = 0;
		
		if(gdzc_bgsb_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gdzc_bgsb_total);
		}
		if(gdzc_dzsb_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gdzc_dzsb_total);
		}
		if(gdzc_jj_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gdzc_jj_total);
		}
		if(gdzc_jxsb_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gdzc_jxsb_total);
		}
		if(gdzc_ggsb_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gdzc_ggsb_total);
		}
		if(gdzc_jtsb_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gdzc_jtsb_total);
		}
		if(gdzc_qt_total!=""){
			total_total = parseFloat(total_total) + parseFloat(gdzc_qt_total);
		}
		
		$("#gdzc_xj_total").val( total_total );
		set_material_total_rate();
	}
	
	$("#gdzc_bgsb_price,#gdzc_bgsb_amount,#gdzc_bgsb_date").blur(function(){
		var gdzc_bgsb_amount = $("#gdzc_bgsb_amount").val();
		var gdzc_bgsb_price  = $("#gdzc_bgsb_price").val();
		var gdzc_bgsb_date  = $("#gdzc_bgsb_date").val();
		
		if(!isDecimal(gdzc_bgsb_price)){
			$("#gdzc_bgsb_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_bgsb_amount)){
			$("#gdzc_bgsb_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_bgsb_date)){
			$("#gdzc_bgsb_date").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_bgsb_price)){
			$("#gdzc_bgsb_price").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_bgsb_amount)){
			$("#gdzc_bgsb_amount").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_bgsb_date)){
			$("#gdzc_bgsb_date").val("").focus();
			return false;
		}
		
		$("#gdzc_bgsb_total").val( gdzc_bgsb_date * gdzc_bgsb_price  *  gdzc_bgsb_amount );	
		gdzc_subtotal();
		
	});
	
	$("#gdzc_dzsb_price,#gdzc_dzsb_amount,#gdzc_dzsb_date").blur(function(){
		var gdzc_dzsb_amount = $("#gdzc_dzsb_amount").val();
		var gdzc_dzsb_price  = $("#gdzc_dzsb_price").val();
		var gdzc_dzsb_date  = $("#gdzc_dzsb_date").val();
		
		if(!isDecimal(gdzc_dzsb_price)){
			$("#gdzc_dzsb_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_dzsb_amount)){
			$("#gdzc_dzsb_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_dzsb_date)){
			$("#gdzc_dzsb_date").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_dzsb_price)){
			$("#gdzc_dzsb_price").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_dzsb_amount)){
			$("#gdzc_dzsb_amount").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_dzsb_date)){
			$("#gdzc_dzsb_date").val("").focus();
			return false;
		}
		
		$("#gdzc_dzsb_total").val( gdzc_dzsb_date * gdzc_dzsb_price  *  gdzc_dzsb_amount );	
		gdzc_subtotal();		
	});
	
	$("#gdzc_jj_price,#gdzc_jj_amount,#gdzc_jj_date").blur(function(){
		var gdzc_jj_amount = $("#gdzc_jj_amount").val();
		var gdzc_jj_price  = $("#gdzc_jj_price").val();
		var gdzc_jj_date  = $("#gdzc_jj_date").val();
		
		if(!isDecimal(gdzc_jj_price)){
			$("#gdzc_jj_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_jj_amount)){
			$("#gdzc_jj_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_jj_date)){
			$("#gdzc_jj_date").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_jj_price)){
			$("#gdzc_jj_price").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_jj_amount)){
			$("#gdzc_jj_amount").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_jj_date)){
			$("#gdzc_jj_date").val("").focus();
			return false;
		}
		
		$("#gdzc_jj_total").val( gdzc_jj_date * gdzc_jj_price  *  gdzc_jj_amount );	
		gdzc_subtotal();				
	});
	
	$("#gdzc_jxsb_price,#gdzc_jxsb_amount,#gdzc_jxsb_date").blur(function(){
		var gdzc_jxsb_amount = $("#gdzc_jxsb_amount").val();
		var gdzc_jxsb_price  = $("#gdzc_jxsb_price").val();
		var gdzc_jxsb_date  = $("#gdzc_jxsb_date").val();
		
		if(!isDecimal(gdzc_jxsb_price)){
			$("#gdzc_jxsb_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_jxsb_amount)){
			$("#gdzc_jxsb_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_jxsb_date)){
			$("#gdzc_jxsb_date").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_jxsb_price)){
			$("#gdzc_jxsb_price").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_jxsb_amount)){
			$("#gdzc_jxsb_amount").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_jxsb_date)){
			$("#gdzc_jxsb_date").val("").focus();
			return false;
		}
		
		$("#gdzc_jxsb_total").val( gdzc_jxsb_date * gdzc_jxsb_price  *  gdzc_jxsb_amount );	
		gdzc_subtotal();			
	});
	
	$("#gdzc_ggsb_price,#gdzc_ggsb_amount,#gdzc_ggsb_date").blur(function(){
		var gdzc_ggsb_amount = $("#gdzc_ggsb_amount").val();
		var gdzc_ggsb_price  = $("#gdzc_ggsb_price").val();
		var gdzc_ggsb_date  = $("#gdzc_ggsb_date").val();
		
		if(!isDecimal(gdzc_ggsb_price)){
			$("#gdzc_ggsb_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_ggsb_amount)){
			$("#gdzc_ggsb_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_ggsb_date)){
			$("#gdzc_ggsb_date").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_ggsb_price)){
			$("#gdzc_ggsb_price").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_ggsb_amount)){
			$("#gdzc_ggsb_amount").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_ggsb_date)){
			$("#gdzc_ggsb_date").val("").focus();
			return false;
		}
		
		$("#gdzc_ggsb_total").val( gdzc_ggsb_date * gdzc_ggsb_price  *  gdzc_ggsb_amount );	
		gdzc_subtotal();			
	});
	
	$("#gdzc_jtsb_price,#gdzc_jtsb_amount,#gdzc_jtsb_date").blur(function(){
		var gdzc_jtsb_amount = $("#gdzc_jtsb_amount").val();
		var gdzc_jtsb_price  = $("#gdzc_jtsb_price").val();
		var gdzc_jtsb_date  = $("#gdzc_jtsb_date").val();
		
		if(!isDecimal(gdzc_jtsb_price)){
			$("#gdzc_jtsb_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_jtsb_amount)){
			$("#gdzc_jtsb_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_jtsb_date)){
			$("#gdzc_jtsb_date").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_jtsb_price)){
			$("#gdzc_jtsb_price").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_jtsb_amount)){
			$("#gdzc_jtsb_amount").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_jtsb_date)){
			$("#gdzc_jtsb_date").val("").focus();
			return false;
		}
		
		$("#gdzc_jtsb_total").val( gdzc_jtsb_date * gdzc_jtsb_price  *  gdzc_jtsb_amount );	
		gdzc_subtotal();			
	});
	
	$("#gdzc_qt_price,#gdzc_qt_amount").blur(function(){
		var gdzc_qt_amount = $("#gdzc_qt_amount").val();
		var gdzc_qt_price  = $("#gdzc_qt_price").val();
		var gdzc_qt_date  = $("#gdzc_qt_date").val();
		
		if(!isDecimal(gdzc_qt_price)){
			$("#gdzc_qt_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_qt_amount)){
			$("#gdzc_qt_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gdzc_qt_date)){
			$("#gdzc_qt_date").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_qt_price)){
			$("#gdzc_qt_price").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_qt_amount)){
			$("#gdzc_qt_amount").val("").focus();
			return false;
		}
		
		if(isNull(gdzc_qt_date)){
			$("#gdzc_qt_date").val("").focus();
			return false;
		}
		
		$("#gdzc_qt_total").val( gdzc_qt_date * gdzc_qt_price  *  gdzc_qt_amount );	
		gdzc_subtotal();			
	});
	
	/**
	 * 无形资产 start
	 */
	function wxzc_subtotal()
	{
		//单价
		var wxzc_rj_price = $("#wxzc_rj_price").val();
		var wxzc_syq_price = $("#wxzc_syq_price").val();
		var wxzc_qt_price = $("#wxzc_qt_price").val();
		var total_price = 0;
		if(wxzc_rj_price!=""){
			total_price = parseFloat(total_price) + parseFloat(wxzc_rj_price);
		}
		if(wxzc_syq_price!=""){
			total_price = parseFloat(total_price) + parseFloat(wxzc_syq_price);
		}
		if(wxzc_qt_price!=""){
			total_price = parseFloat(total_price) + parseFloat(wxzc_qt_price);
		}
		$("#wxzc_xj_price").val( total_price );
		
		//数量
		var wxzc_rj_amount = $("#wxzc_rj_amount").val();
		var wxzc_syq_amount = $("#wxzc_syq_amount").val();
		var wxzc_qt_amount = $("#wxzc_qt_amount").val();
		var total_amount = 0;
		if(wxzc_rj_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(wxzc_rj_amount);
		}
		if(wxzc_syq_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(wxzc_syq_amount);
		}
		if(wxzc_qt_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(wxzc_qt_amount);
		}
		$("#wxzc_xj_amount").val( total_amount );
		
		//使用期限（天）
		var wxzc_rj_date = $("#wxzc_rj_date").val();
		var wxzc_syq_date = $("#wxzc_syq_date").val();
		var wxzc_qt_date = $("#wxzc_qt_date").val();
		var total_date = 0;
		if(wxzc_rj_date!=""){
			total_date = parseFloat(total_date) + parseFloat(wxzc_rj_date);
		}
		if(wxzc_syq_date!=""){
			total_date = parseFloat(total_date) + parseFloat(wxzc_syq_date);
		}
		if(wxzc_qt_date!=""){
			total_date = parseFloat(total_date) + parseFloat(wxzc_qt_date);
		}
		$("#wxzc_xj_date").val( total_date );
		
		//合计（数量*单价）
		var wxzc_rj_total = $("#wxzc_rj_total").val();
		var wxzc_syq_total = $("#wxzc_syq_total").val();
		var wxzc_qt_total = $("#wxzc_qt_total").val();
		var total_total = 0;
		if(wxzc_rj_total!=""){
			total_total = parseFloat(total_total) + parseFloat(wxzc_rj_total);
		}
		if(wxzc_syq_total!=""){
			total_total = parseFloat(total_total) + parseFloat(wxzc_syq_total);
		}
		if(wxzc_qt_total!=""){
			total_total = parseFloat(total_total) + parseFloat(wxzc_qt_total);
		}
		$("#wxzc_xj_total").val( total_total );
		set_material_total_rate();
	}
	
	$("#wxzc_rj_price,#wxzc_rj_amount,#wxzc_rj_date").blur(function(){
		var wxzc_rj_amount = $("#wxzc_rj_amount").val();
		var wxzc_rj_price  = $("#wxzc_rj_price").val();
		var wxzc_rj_date  = $("#wxzc_rj_date").val();

		if(!isDecimal(wxzc_rj_price)){
			$("#wxzc_rj_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(wxzc_rj_amount)){
			$("#wxzc_rj_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(wxzc_rj_date)){
			$("#wxzc_rj_date").val("").focus();
			return false;
		}
		
		if(isNull(wxzc_rj_price)){
			$("#wxzc_rj_price").val("").focus();
			return false;
		}
		
		if(isNull(wxzc_rj_amount)){
			$("#wxzc_rj_amount").val("").focus();
			return false;
		}
		
		if(isNull(wxzc_rj_date)){
			$("#wxzc_rj_date").val("").focus();
			return false;
		}
		
		$("#wxzc_rj_total").val( wxzc_rj_date * wxzc_rj_price  *  wxzc_rj_amount );	
		wxzc_subtotal();			
		
	});
	
	$("#wxzc_syq_price,#wxzc_syq_amount,#wxzc_syq_date").blur(function(){
		var wxzc_syq_amount = $("#wxzc_syq_amount").val();
		var wxzc_syq_price  = $("#wxzc_syq_price").val();
		var wxzc_syq_date  = $("#wxzc_syq_date").val();
		
		if(!isDecimal(wxzc_syq_price)){
			$("#wxzc_syq_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(wxzc_syq_amount)){
			$("#wxzc_syq_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(wxzc_syq_date)){
			$("#wxzc_syq_date").val("").focus();
			return false;
		}
		
		if(isNull(wxzc_syq_price)){
			$("#wxzc_syq_price").val("").focus();
			return false;
		}
		
		if(isNull(wxzc_syq_amount)){
			$("#wxzc_syq_amount").val("").focus();
			return false;
		}
		
		if(isNull(wxzc_syq_date)){
			$("#wxzc_syq_date").val("").focus();
			return false;
		}
		
		$("#wxzc_syq_total").val( wxzc_syq_date * wxzc_syq_price  *  wxzc_syq_amount );	
		wxzc_subtotal();			
	});
	
	$("#wxzc_qt_price,#wxzc_qt_amount,#wxzc_qt_date").blur(function(){
		var wxzc_qt_amount = $("#wxzc_qt_amount").val();
		var wxzc_qt_price  = $("#wxzc_qt_price").val();
		var wxzc_qt_date  = $("#wxzc_qt_date").val();
		
		if(!isDecimal(wxzc_qt_price)){
			$("#wxzc_qt_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(wxzc_qt_amount)){
			$("#wxzc_qt_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(wxzc_qt_date)){
			$("#wxzc_qt_date").val("").focus();
			return false;
		}
		
		if(isNull(wxzc_qt_price)){
			$("#wxzc_qt_price").val("").focus();
			return false;
		}
		
		if(isNull(wxzc_qt_amount)){
			$("#wxzc_qt_amount").val("").focus();
			return false;
		}
		
		if(isNull(wxzc_qt_date)){
			$("#wxzc_qt_date").val("").focus();
			return false;
		}
		
		$("#wxzc_qt_total").val( wxzc_qt_date * wxzc_qt_price  *  wxzc_qt_amount );	
		wxzc_subtotal();		
	});
	
	/**
	 * 营业成本 start
	 */
	function yycb_subtotal()
	{
		//单价
		var yycb_zql_price = $("#yycb_zql_price").val();
		var yycb_ryl_price = $("#yycb_ryl_price").val();
		var yycb_qt_price = $("#yycb_qt_price").val();
		var total_price = 0;
		if(yycb_zql_price!=""){
			total_price = parseFloat(total_price) + parseFloat(yycb_zql_price);
		}
		if(yycb_ryl_price!=""){
			total_price = parseFloat(total_price) + parseFloat(yycb_ryl_price);
		}
		if(yycb_qt_price!=""){
			total_price = parseFloat(total_price) + parseFloat(yycb_qt_price);
		}
		$("#yycb_xj_price").val( total_price );
		//数量
		var yycb_zql_amount = $("#yycb_zql_amount").val();
		var yycb_ryl_amount = $("#yycb_ryl_amount").val();
		var yycb_qt_amount = $("#yycb_qt_amount").val();
		var total_amount = 0;
		if(yycb_zql_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(yycb_zql_amount);
		}
		if(yycb_ryl_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(yycb_ryl_amount);
		}
		if(yycb_qt_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(yycb_qt_amount);
		}
		$("#yycb_xj_amount").val( total_amount );
		//使用期限（天）
		var yycb_zql_date = $("#yycb_zql_date").val();
		var yycb_ryl_date = $("#yycb_ryl_date").val();
		var yycb_qt_date = $("#yycb_qt_date").val();
		var total_date = 0;
		if(yycb_zql_date!=""){
			total_date = parseFloat(total_date) + parseFloat(yycb_zql_date);
		}
		if(yycb_ryl_date!=""){
			total_date = parseFloat(total_date) + parseFloat(yycb_ryl_date);
		}
		if(yycb_qt_date!=""){
			total_date = parseFloat(total_date) + parseFloat(yycb_qt_date);
		}
		$("#yycb_xj_date").val( total_date );
		//合计（数量*单价）
		var yycb_zql_total = $("#yycb_zql_total").val();
		var yycb_ryl_total = $("#yycb_ryl_total").val();
		var yycb_qt_total = $("#yycb_qt_total").val();
		var total_total = 0;
		if(yycb_zql_total!=""){
			total_total = parseFloat(total_total) + parseFloat(yycb_zql_total);
		}
		if(yycb_ryl_total!=""){
			total_total = parseFloat(total_total) + parseFloat(yycb_ryl_total);
		}
		if(yycb_qt_total!=""){
			total_total = parseFloat(total_total) + parseFloat(yycb_qt_total);
		}
		$("#yycb_xj_total").val( total_total );
		set_material_total_rate();
	}
	
	$("#yycb_zql_price,#yycb_zql_amount,#yycb_zql_date").blur(function(){
		var yycb_zql_amount = $("#yycb_zql_amount").val();
		var yycb_zql_price  = $("#yycb_zql_price").val();
		var yycb_zql_date  = $("#yycb_zql_date").val();
		
		if(!isDecimal(yycb_zql_price)){
			$("#yycb_zql_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(yycb_zql_amount)){
			$("#yycb_zql_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(yycb_zql_date)){
			$("#yycb_zql_date").val("").focus();
			return false;
		}
		
		if(isNull(yycb_zql_price)){
			$("#yycb_zql_price").val("").focus();
			return false;
		}
		
		if(isNull(yycb_zql_amount)){
			$("#yycb_zql_amount").val("").focus();
			return false;
		}
		
		if(isNull(yycb_zql_date)){
			$("#yycb_zql_date").val("").focus();
			return false;
		}
		
		$("#yycb_zql_total").val( yycb_zql_date * yycb_zql_price  *  yycb_zql_amount );	
		yycb_subtotal();				
	});
	
	$("#yycb_ryl_price,#yycb_ryl_amount,#yycb_ryl_date").blur(function(){
		var yycb_ryl_amount = $("#yycb_ryl_amount").val();
		var yycb_ryl_price  = $("#yycb_ryl_price").val();
		var yycb_ryl_date  = $("#yycb_ryl_date").val();
		
		if(!isDecimal(yycb_ryl_price)){
			$("#yycb_ryl_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(yycb_ryl_amount)){
			$("#yycb_ryl_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(yycb_ryl_date)){
			$("#yycb_ryl_date").val("").focus();
			return false;
		}
		
		if(isNull(yycb_ryl_price)){
			$("#yycb_ryl_price").val("").focus();
			return false;
		}
		
		if(isNull(yycb_ryl_amount)){
			$("#yycb_ryl_amount").val("").focus();
			return false;
		}
		
		if(isNull(yycb_ryl_date)){
			$("#yycb_ryl_date").val("").focus();
			return false;
		}
		
		$("#yycb_ryl_total").val( yycb_ryl_date * yycb_ryl_price  *  yycb_ryl_amount );	
		yycb_subtotal();					
	});
	
	$("#yycb_qt_price,#yycb_qt_amount,#yycb_qt_date").blur(function(){
		var yycb_qt_amount = $("#yycb_qt_amount").val();
		var yycb_qt_price  = $("#yycb_qt_price").val();
		var yycb_qt_date  = $("#yycb_qt_date").val();
		
		if(!isDecimal(yycb_qt_price)){
			$("#yycb_qt_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(yycb_qt_amount)){
			$("#yycb_qt_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(yycb_qt_date)){
			$("#yycb_qt_date").val("").focus();
			return false;
		}
		
		if(isNull(yycb_qt_price)){
			$("#yycb_qt_price").val("").focus();
			return false;
		}
		
		if(isNull(yycb_qt_amount)){
			$("#yycb_qt_amount").val("").focus();
			return false;
		}
		
		if(isNull(yycb_qt_date)){
			$("#yycb_qt_date").val("").focus();
			return false;
		}
		
		$("#yycb_qt_total").val( yycb_qt_date * yycb_qt_price  *  yycb_qt_amount );	
		yycb_subtotal();						
	});
	
	/**
	 * 研发期用物料
	 */
	function yfqywl_subtotal()
	{
		//单价
		var yfqywl_cp_price = $("#yfqywl_cp_price").val();
		var yfqywl_dzl_price = $("#yfqywl_dzl_price").val();
		var yfqywl_qt_price = $("#yfqywl_qt_price").val();
		var total_price = 0;
		if(yfqywl_cp_price!=""){
			total_price = parseFloat(total_price) + parseFloat(yfqywl_cp_price);
		}
		if(yfqywl_dzl_price!=""){
			total_price = parseFloat(total_price) + parseFloat(yfqywl_dzl_price);
		}
		if(yfqywl_qt_price!=""){
			total_price = parseFloat(total_price) + parseFloat(yfqywl_qt_price);
		}
		$("#yfqywl_xj_price").val( total_price );
		//数量
		var yfqywl_cp_amount = $("#yfqywl_cp_amount").val();
		var yfqywl_dzl_amount = $("#yfqywl_dzl_amount").val();
		var yfqywl_qt_amount = $("#yfqywl_qt_amount").val();
		var total_amount = 0;
		if(yfqywl_cp_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(yfqywl_cp_amount);
		}
		if(yfqywl_dzl_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(yfqywl_dzl_amount);
		}
		if(yfqywl_qt_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(yfqywl_qt_amount);
		}
		$("#yfqywl_xj_amount").val( total_amount );
		//使用期限（天）
		var yfqywl_cp_date = $("#yfqywl_cp_date").val();
		var yfqywl_dzl_date = $("#yfqywl_dzl_date").val();
		var yfqywl_qt_date = $("#yfqywl_qt_date").val();
		var total_date = 0;
		if(yfqywl_cp_date!=""){
			total_date = parseFloat(total_date) + parseFloat(yfqywl_cp_date);
		}
		if(yfqywl_dzl_date!=""){
			total_date = parseFloat(total_date) + parseFloat(yfqywl_dzl_date);
		}
		if(yfqywl_qt_date!=""){
			total_date = parseFloat(total_date) + parseFloat(yfqywl_qt_date);
		}
		$("#yfqywl_xj_date").val( total_date );
		//合计（数量*单价）
		var yfqywl_cp_total = $("#yfqywl_cp_total").val();
		var yfqywl_dzl_total = $("#yfqywl_dzl_total").val();
		var yfqywl_qt_total = $("#yfqywl_qt_total").val();
		var total_total = 0;
		if(yfqywl_cp_total!=""){
			total_total = parseFloat(total_total) + parseFloat(yfqywl_cp_total);
		}
		if(yfqywl_dzl_total!=""){
			total_total = parseFloat(total_total) + parseFloat(yfqywl_dzl_total);
		}
		if(yfqywl_qt_total!=""){
			total_total = parseFloat(total_total) + parseFloat(yfqywl_qt_total);
		}
		$("#yfqywl_xj_total").val( total_total );
		set_material_total_rate();
	}
	$("#yfqywl_cp_price,#yfqywl_cp_amount,#yfqywl_cp_date").blur(function(){
		var yfqywl_cp_amount = $("#yfqywl_cp_amount").val();
		var yfqywl_cp_price  = $("#yfqywl_cp_price").val();
		var yfqywl_cp_date  = $("#yfqywl_cp_date").val();
				
		if(!isDecimal(yfqywl_cp_price)){
			$("#yfqywl_cp_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(yfqywl_cp_amount)){
			$("#yfqywl_cp_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(yfqywl_cp_date)){
			$("#yfqywl_cp_date").val("").focus();
			return false;
		}
		
		if(isNull(yfqywl_cp_price)){
			$("#yfqywl_cp_price").val("").focus();
			return false;
		}
		
		if(isNull(yfqywl_cp_amount)){
			$("#yfqywl_cp_amount").val("").focus();
			return false;
		}
		
		if(isNull(yfqywl_cp_date)){
			$("#yfqywl_cp_date").val("").focus();
			return false;
		}
		
		$("#yfqywl_cp_total").val( yfqywl_cp_date * yfqywl_cp_price  *  yfqywl_cp_amount );	
		yfqywl_subtotal();			
	});
	
	$("#yfqywl_dzl_price,#yfqywl_dzl_amount,#yfqywl_dzl_date").blur(function(){
		var yfqywl_dzl_amount=$("#yfqywl_dzl_amount").val();
		var yfqywl_dzl_price = $("#yfqywl_dzl_price").val();
		var yfqywl_dzl_date = $("#yfqywl_dzl_date").val();
		
		if(!isDecimal(yfqywl_dzl_price)){
			$("#yfqywl_dzl_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(yfqywl_dzl_amount)){
			$("#yfqywl_dzl_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(yfqywl_dzl_date)){
			$("#yfqywl_dzl_date").val("").focus();
			return false;
		}
		
		if(isNull(yfqywl_dzl_price)){
			$("#yfqywl_dzl_price").val("").focus();
			return false;
		}
		
		if(isNull(yfqywl_dzl_amount)){
			$("#yfqywl_dzl_amount").val("").focus();
			return false;
		}
		
		if(isNull(yfqywl_dzl_date)){
			$("#yfqywl_dzl_date").val("").focus();
			return false;
		}
		
		$("#yfqywl_dzl_total").val( yfqywl_dzl_date * yfqywl_dzl_price  *  yfqywl_dzl_amount );	
		yfqywl_subtotal();		
	});
	
	$("#yfqywl_qt_price,#yfqywl_qt_amount,#yfqywl_qt_date").blur(function(){
		var yfqywl_qt_amount = $("#yfqywl_qt_amount").val();
		var yfqywl_qt_price  = $("#yfqywl_qt_price").val();
		var yfqywl_qt_date  = $("#yfqywl_qt_date").val();
		
		if(!isDecimal(yfqywl_qt_price)){
			$("#yfqywl_qt_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(yfqywl_qt_amount)){
			$("#yfqywl_qt_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(yfqywl_qt_date)){
			$("#yfqywl_qt_date").val("").focus();
			return false;
		}
		
		if(isNull(yfqywl_qt_price)){
			$("#yfqywl_qt_price").val("").focus();
			return false;
		}
		
		if(isNull(yfqywl_qt_amount)){
			$("#yfqywl_qt_amount").val("").focus();
			return false;
		}
		
		if(isNull(yfqywl_qt_date)){
			$("#yfqywl_qt_date").val("").focus();
			return false;
		}
		
		$("#yfqywl_qt_total").val( yfqywl_qt_date * yfqywl_qt_price  *  yfqywl_qt_amount );	
		yfqywl_subtotal();		
	});
	
	/**
	 * 管理办公费
	 */
	function glbgf_subtotal()
	{
		//单价
		var glbgf_bgf_price = $("#glbgf_bgf_price").val();
		var glbgf_jtclf_price = $("#glbgf_jtclf_price").val();
		var glbgf_ywzdf_price = $("#glbgf_ywzdf_price").val();
		var glbgf_qt_price    = $("#glbgf_qt_price").val();
		var total_price = 0;
		if(glbgf_bgf_price!=""){
			total_price = parseFloat(total_price) + parseFloat(glbgf_bgf_price);
		}
		if(glbgf_jtclf_price!=""){
			total_price = parseFloat(total_price) + parseFloat(glbgf_jtclf_price);
		}
		if(glbgf_ywzdf_price!=""){
			total_price = parseFloat(total_price) + parseFloat(glbgf_ywzdf_price);
		}
		if(glbgf_qt_price!=""){
			total_price = parseFloat(total_price) + parseFloat(glbgf_qt_price);
		}
		$("#glbgf_xj_price").val( total_price );
		// 数量
		var glbgf_bgf_amount = $("#glbgf_bgf_amount").val();
		var glbgf_jtclf_amount = $("#glbgf_jtclf_amount").val();
		var glbgf_ywzdf_amount = $("#glbgf_ywzdf_amount").val();
		var glbgf_qt_amount    = $("#glbgf_qt_amount").val();
		var total_amount = 0;
		if(glbgf_bgf_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(glbgf_bgf_amount);
		}
		if(glbgf_jtclf_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(glbgf_jtclf_amount);
		}
		if(glbgf_ywzdf_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(glbgf_ywzdf_amount);
		}
		if(glbgf_qt_amount!=""){
			total_amount = parseFloat(total_amount) + parseFloat(glbgf_qt_amount);
		}
		$("#glbgf_xj_amount").val( total_amount );
		// 使用期限（天）
		var glbgf_bgf_date = $("#glbgf_bgf_date").val();
		var glbgf_jtclf_date = $("#glbgf_jtclf_date").val();
		var glbgf_ywzdf_date = $("#glbgf_ywzdf_date").val();
		var glbgf_qt_date    = $("#glbgf_qt_date").val();
		var total_date = 0;
		if(glbgf_bgf_date!=""){
			total_date = parseFloat(total_date) + parseFloat(glbgf_bgf_date);
		}
		if(glbgf_jtclf_date!=""){
			total_date = parseFloat(total_date) + parseFloat(glbgf_jtclf_date);
		}
		if(glbgf_ywzdf_date!=""){
			total_date = parseFloat(total_date) + parseFloat(glbgf_ywzdf_date);
		}
		if(glbgf_qt_date!=""){
			total_date = parseFloat(total_date) + parseFloat(glbgf_qt_date);
		}
		$("#glbgf_xj_date").val( total_date );
		
		// 	合计（数量*单价）
		var glbgf_bgf_total = $("#glbgf_bgf_total").val();
		var glbgf_jtclf_total = $("#glbgf_jtclf_total").val();
		var glbgf_ywzdf_total = $("#glbgf_ywzdf_total").val();
		var glbgf_qt_total    = $("#glbgf_qt_total").val();
		var total_total = 0;
		if(glbgf_bgf_total!=""){
			total_total = parseFloat(total_total) + parseFloat(glbgf_bgf_total);
		}
		if(glbgf_jtclf_total!=""){
			total_total = parseFloat(total_total) + parseFloat(glbgf_jtclf_total);
		}
		if(glbgf_ywzdf_total!=""){
			total_total = parseFloat(total_total) + parseFloat(glbgf_ywzdf_total);
		}
		if(glbgf_qt_total!=""){
			total_total = parseFloat(total_total) + parseFloat(glbgf_qt_total);
		}
		$("#glbgf_xj_total").val( total_total );
		set_material_total_rate();
	}
	$("#glbgf_bgf_price,#glbgf_bgf_amount,#glbgf_bgf_date").blur(function(){
		var glbgf_bgf_amount = $("#glbgf_bgf_amount").val();
		var glbgf_bgf_price  = $("#glbgf_bgf_price").val();
		var glbgf_bgf_date  = $("#glbgf_bgf_date").val();
		
		if(!isDecimal(glbgf_bgf_price)){
			$("#glbgf_bgf_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(glbgf_bgf_amount)){
			$("#glbgf_bgf_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(glbgf_bgf_date)){
			$("#glbgf_bgf_date").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_bgf_price)){
			$("#glbgf_bgf_price").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_bgf_amount)){
			$("#glbgf_bgf_amount").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_bgf_date)){
			$("#glbgf_bgf_date").val("").focus();
			return false;
		}
		
		$("#glbgf_bgf_total").val( glbgf_bgf_date * glbgf_bgf_price  *  glbgf_bgf_amount );	
		glbgf_subtotal();				
	});
	
	$("#glbgf_jtclf_price,#glbgf_jtclf_amount,#glbgf_jtclf_date").blur(function(){
		var glbgf_jtclf_amount = $("#glbgf_jtclf_amount").val();
		var glbgf_jtclf_price  = $("#glbgf_jtclf_price").val();
		var glbgf_jtclf_date  = $("#glbgf_jtclf_date").val();
		
		if(!isDecimal(glbgf_jtclf_price)){
			$("#glbgf_jtclf_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(glbgf_jtclf_amount)){
			$("#glbgf_jtclf_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(glbgf_jtclf_date)){
			$("#glbgf_jtclf_date").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_jtclf_price)){
			$("#glbgf_jtclf_price").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_jtclf_amount)){
			$("#glbgf_jtclf_amount").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_jtclf_date)){
			$("#glbgf_jtclf_date").val("").focus();
			return false;
		}
		
		$("#glbgf_jtclf_total").val( glbgf_jtclf_date * glbgf_jtclf_price  *  glbgf_jtclf_amount );	
		glbgf_subtotal();				
	});
	
	$("#glbgf_ywzdf_price,#glbgf_ywzdf_amount,#glbgf_ywzdf_date").blur(function(){
		var glbgf_ywzdf_amount = $("#glbgf_ywzdf_amount").val();
		var glbgf_ywzdf_price  = $("#glbgf_ywzdf_price").val();
		var glbgf_ywzdf_date  = $("#glbgf_ywzdf_date").val();
		
		if(!isDecimal(glbgf_ywzdf_price)){
			$("#glbgf_ywzdf_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(glbgf_ywzdf_amount)){
			$("#glbgf_ywzdf_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(glbgf_ywzdf_date)){
			$("#glbgf_ywzdf_date").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_ywzdf_price)){
			$("#glbgf_ywzdf_price").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_ywzdf_amount)){
			$("#glbgf_ywzdf_amount").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_ywzdf_date)){
			$("#glbgf_ywzdf_date").val("").focus();
			return false;
		}
		
		$("#glbgf_ywzdf_total").val( glbgf_ywzdf_date * glbgf_ywzdf_price  *  glbgf_ywzdf_amount );	
		glbgf_subtotal();
	});
	
	$("#glbgf_qt_price,#glbgf_qt_amount,#glbgf_qt_date").blur(function(){
		var glbgf_qt_amount = $("#glbgf_qt_amount").val();
		var glbgf_qt_price  = $("#glbgf_qt_price").val();
		var glbgf_qt_date  = $("#glbgf_qt_date").val();
		if(!isDecimal(glbgf_qt_price)){
			$("#glbgf_qt_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(glbgf_qt_amount)){
			$("#glbgf_qt_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(glbgf_qt_date)){
			$("#glbgf_qt_date").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_qt_price)){
			$("#glbgf_qt_price").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_qt_amount)){
			$("#glbgf_qt_amount").val("").focus();
			return false;
		}
		
		if(isNull(glbgf_qt_date)){
			$("#glbgf_qt_date").val("").focus();
			return false;
		}
		
		$("#glbgf_qt_total").val( glbgf_qt_date * glbgf_qt_price  *  glbgf_qt_amount );	
		glbgf_subtotal();
	});
	
	/**
	 * 公司费用
	 */
	$("#gsfy_yymj_price,#gsfy_yymj_amount,#gsfy_yymj_date").blur(function(){
		var gsfy_yymj_amount = $("#gsfy_yymj_amount").val();
		var gsfy_yymj_price  = $("#gsfy_yymj_price").val();
		var gsfy_yymj_date  = $("#gsfy_yymj_date").val();
		if(!isDecimal(gsfy_yymj_price)){
			$("#gsfy_yymj_price").val("").focus();
			return false;
		}
		
		if(!isDecimal(gsfy_yymj_amount)){
			$("#gsfy_yymj_amount").val("").focus();
			return false;
		}
		
		if(!isDecimal(gsfy_yymj_date)){
			$("#gsfy_yymj_date").val("").focus();
			return false;
		}
		
		if(isNull(gsfy_yymj_price)){
			$("#gsfy_yymj_price").val("").focus();
			return false;
		}
		
		if(isNull(gsfy_yymj_amount)){
			$("#gsfy_yymj_amount").val("").focus();
			return false;
		}
		
		if(isNull(gsfy_yymj_date)){
			$("#gsfy_yymj_date").val("").focus();
			return false;
		}
		
		$("#gsfy_yymj_total").val( gsfy_yymj_date * gsfy_yymj_price  *  gsfy_yymj_amount );		
		set_material_total_rate();
	});
	
});
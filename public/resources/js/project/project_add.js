function project_add_staff_cost()
{
	var y_staff_cost = $("#y_staff_cost").val();
	var s_staff_cost = $("#s_staff_cost").val();
	var z_staff_cost = $("#z_staff_cost").val();
	var t_staff_cost = 0;
	
	t_staff_cost=parseFloat(y_staff_cost) + parseFloat(s_staff_cost) + parseFloat(z_staff_cost);
	$("#t_staff_cost").val( t_staff_cost );
}

function project_add_xmys_total()
{
	//研发阶段 预算总额
	var y_total=0;
	var y_gcys = $("#y_gcys").val();
	var y_gdzc = $("#y_gdzc").val();
	var y_wxzc = $("#y_wxzc").val();
	var y_yfqywl = $("#y_yfqywl").val();
	var y_yycb  = $("#y_yycb").val();
	var y_scfy  = $("#y_scfy").val();
	var y_glbgf = $("#y_glbgf").val();
	var y_staff_cost = $("#y_staff_cost").val();
	var y_gsfy  = $("#y_gsfy").val();
	var y_yymj = $("#y_yymj").val();
	var y_taxes = $("#y_taxes").val();
	
	if(isNaN(y_gcys) || y_gcys==""){
		y_gcys=0;
	}
	if(isNaN(y_gdzc) || y_gdzc==""){
		y_gdzc=0;
	}
	if(isNaN(y_wxzc) || y_wxzc==""){
		y_wxzc=0;
	}
	if(isNaN(y_yfqywl) || y_yfqywl==""){
		y_yfqywl=0;
	}
	if(isNaN(y_yycb) || y_yycb==""){
		y_yycb=0;
	}
	if(isNaN(y_scfy) || y_scfy==""){
		y_scfy=0;
	}
	if(isNaN(y_glbgf) || y_glbgf==""){
		y_glbgf=0;
	}
	if(isNaN(y_staff_cost) || y_staff_cost=="" ){
		y_staff_cost=0;
	}
	if(isNaN(y_gsfy) || y_gsfy=="" ){
		y_gsfy=0;
	}
	if(isNaN(y_yymj) || y_yymj==""){
		y_yymj=0;
	}
	if(isNaN(y_taxes) || y_taxes==""){
		y_taxes=0;
	}
	
	y_total=parseFloat(y_gcys)+parseFloat(y_gdzc)+parseFloat(y_wxzc)+parseFloat(y_yfqywl)+parseFloat(y_yycb)+parseFloat(y_scfy)+parseFloat(y_glbgf)+parseFloat(y_staff_cost)+parseFloat(y_gsfy)+parseFloat(y_yymj)+parseFloat(y_taxes);	
	$("#y_total_cost").val(y_total);
	
	
	//试运营阶段
	var s_total=0;
	var s_gcys = $("#s_gcys").val();
	var s_gdzc = $("#s_gdzc").val();
	var s_wxzc = $("#s_wxzc").val();
	var s_yfqywl = $("#s_yfqywl").val();
	var s_yycb  = $("#s_yycb").val();
	var s_scfy  = $("#s_scfy").val();
	var s_glbgf = $("#s_glbgf").val();
	var s_staff_cost = $("#s_staff_cost").val();
	var s_gsfy  = $("#s_gsfy").val();
	var s_yymj = $("#s_yymj").val();
	var s_taxes = $("#s_taxes").val();
	
	if(isNaN(s_gcys) || s_gcys==""){
		s_gcys=0;
	}
	if(isNaN(s_gdzc) || s_gdzc==""){
		s_gdzc=0;
	}
	if(isNaN(s_wxzc) || s_wxzc==""){
		s_wxzc=0;
	}
	if(isNaN(s_yfqywl) || s_yfqywl==""){
		s_yfqywl=0;
	}
	if(isNaN(s_yycb) || s_yycb==""){
		s_yycb=0;
	}
	if(isNaN(s_scfy) || s_scfy==""){
		s_scfy=0;
	}
	if(isNaN(s_glbgf) || s_glbgf==""){
		s_glbgf=0;
	}
	if(isNaN(s_staff_cost) || s_staff_cost=="" ){
		s_staff_cost=0;
	}
	if(isNaN(s_gsfy) || s_gsfy=="" ){
		s_gsfy=0;
	}
	if(isNaN(s_yymj) || s_yymj==""){
		s_yymj=0;
	}
	if(isNaN(s_taxes) || s_taxes==""){
		s_taxes=0;
	}
	
	s_total=parseFloat(s_gcys)+parseFloat(s_gdzc)+parseFloat(s_wxzc)+parseFloat(s_yfqywl)+parseFloat(s_yycb)+parseFloat(s_scfy)+parseFloat(s_glbgf)+parseFloat(s_staff_cost)+parseFloat(s_gsfy)+parseFloat(s_yymj)+parseFloat(s_taxes);	
	$("#s_total_cost").val(s_total);
	//正式运营阶段
	var z_total=0;
	var z_gcys = $("#z_gcys").val();
	var z_gdzc = $("#z_gdzc").val();
	var z_wxzc = $("#z_wxzc").val();
	var z_yfqywl = $("#z_yfqywl").val();
	var z_yycb  = $("#z_yycb").val();
	var z_scfy  = $("#z_scfy").val();
	var z_glbgf = $("#z_glbgf").val();
	var z_staff_cost = $("#z_staff_cost").val();
	var z_gsfy  = $("#z_gsfy").val();
	var z_yymj = $("#z_yymj").val();
	var z_taxes = $("#z_taxes").val();
	
	if(isNaN(z_gcys) || z_gcys==""){
		z_gcys=0;
	}
	if(isNaN(z_gdzc) || z_gdzc==""){
		z_gdzc=0;
	}
	if(isNaN(z_wxzc) || z_wxzc==""){
		z_wxzc=0;
	}
	if(isNaN(z_yfqywl) || z_yfqywl==""){
		z_yfqywl=0;
	}
	if(isNaN(z_yycb) || z_yycb==""){
		z_yycb=0;
	}
	if(isNaN(z_scfy) || z_scfy==""){
		z_scfy=0;
	}
	if(isNaN(z_glbgf) || z_glbgf==""){
		z_glbgf=0;
	}
	if(isNaN(z_staff_cost) || z_staff_cost=="" ){
		z_staff_cost=0;
	}
	if(isNaN(z_gsfy) || z_gsfy=="" ){
		z_gsfy=0;
	}
	if(isNaN(z_yymj) || z_yymj==""){
		z_yymj=0;
	}
	if(isNaN(z_taxes) || z_taxes==""){
		z_taxes=0;
	}
	
	z_total=parseFloat(z_gcys)+parseFloat(z_gdzc)+parseFloat(z_wxzc)+parseFloat(z_yfqywl)+parseFloat(z_yycb)+parseFloat(z_scfy)+parseFloat(z_glbgf)+parseFloat(z_staff_cost)+parseFloat(z_gsfy)+parseFloat(z_yymj)+parseFloat(z_taxes);	
	$("#z_total_cost").val(z_total);
	
	//总计
	var t_total=0;
	var t_gcys = $("#t_gcys").val();
	var t_gdzc = $("#t_gdzc").val();
	var t_wxzc = $("#t_wxzc").val();
	var t_yfqywl = $("#t_yfqywl").val();
	var t_yycb  = $("#t_yycb").val();
	var t_scfy  = $("#t_scfy").val();
	var t_glbgf = $("#t_glbgf").val();
	var t_staff_cost = $("#t_staff_cost").val();
	var t_gsfy  = $("#t_gsfy").val();
	var t_yymj = $("#t_yymj").val();
	var t_taxes = $("#t_taxes").val();
	
	if(isNaN(t_gcys) || t_gcys==""){
		t_gcys=0;
	}
	if(isNaN(t_gdzc) || t_gdzc==""){
		t_gdzc=0;
	}
	if(isNaN(t_wxzc) || t_wxzc==""){
		t_wxzc=0;
	}
	if(isNaN(t_yfqywl) || t_yfqywl==""){
		t_yfqywl=0;
	}
	if(isNaN(t_yycb) || t_yycb==""){
		t_yycb=0;
	}
	if(isNaN(t_scfy) || t_scfy==""){
		t_scfy=0;
	}
	if(isNaN(t_glbgf) || t_glbgf==""){
		t_glbgf=0;
	}
	if(isNaN(t_staff_cost) || t_staff_cost=="" ){
		t_staff_cost=0;
	}
	if(isNaN(t_gsfy) || t_gsfy=="" ){
		t_gsfy=0;
	}
	if(isNaN(t_yymj) || t_yymj==""){
		t_yymj=0;
	}
	if(isNaN(t_taxes) || t_taxes==""){
		t_taxes=0;
	}
	
	t_total=parseFloat(t_gcys)+parseFloat(t_gdzc)+parseFloat(t_wxzc)+parseFloat(t_yfqywl)+parseFloat(t_yycb)+parseFloat(t_scfy)+parseFloat(t_glbgf)+parseFloat(t_staff_cost)+parseFloat(t_gsfy)+parseFloat(t_yymj)+parseFloat(t_taxes);	
	$("#t_total_cost").val(t_total);
	
 
}

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
	
	 function delStaffLsgTr(o){
		   $(o).parent().parent().remove().slideUp('slow');
	   }
	   
	   function  ForDight(Dight,How)    
	   {    
	      Dight  =  Math.round  (Dight*Math.pow(10,How))/Math.pow(10,How);    
	      return  Dight;    
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
	
	$("#y_staff_cost,#s_staff_cost,#z_staff_cost").change(function(){
		project_add_staff_cost();
	});
	
	$('#dg_staff_s').dialog({
		autoOpen : false,
		width : 1400,
		resizable : true,
		modal : true,
		draggable : false,  
		title : "<div class='widget-header'><h4><i class='fa fa-hand-o-right'></i> 人员成本(试运营阶段)</h4></div>",
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
				//临时工
				var total_lsg = 0;
				//$(o).parent().parent().parent().find("input[name='staff_section_date[]']:eq(0)").val();
				$("#dg_staff_s #staff_lsg tbody tr input[name='staff_salary_total[]']").each(function(o){
					var lsg_val =  $(this).val();
					lsg_val = lsg_val.replace(",","");
					total_lsg+=parseFloat(lsg_val);
				});
				 
				if(isNaN(total_lsg)){
					total_lsg=0;
				}
				
				
				$("#dg_staff_s #t_lsg_cost").html( ForDight(total_lsg,2) );
				$("#dg_staff_s #staff_lsg tfoot").show();
				
				//正式工
				var total_zsg = 0;
				$("#dg_staff_s #staff_zsg tbody tr input[name='staff_cost_total[]']").each(function(o){
					var zsg_val =  $(this).val();
					zsg_val = zsg_val.replace(",","");
					total_zsg+=parseFloat(zsg_val);
				});
				console.log( total_zsg );
				if(isNaN(total_zsg)){
					total_zsg=0;
				}
				console.log( total_zsg );
				
				$("#dg_staff_s #t_zsg_cost").html( ForDight(total_zsg,2) );
				$("#dg_staff_s #staff_zsg tfoot").show();
				$("#dg_staff_s #t_cost_y").html( ForDight((  parseFloat(total_zsg) + parseFloat(total_lsg)  ),2) );
			}
		},{
			html : "<i class='fa fa-check'></i>&nbsp; 提交",
			"class" : "btn btn-primary",
			click : function() {
				var post_str = "";
				//临时工
				var total_lsg = 0;
				$("#dg_staff_s #staff_lsg tbody tr").each(function(o){
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
					/**
					$.post("/admin/project/do_save_staff_info",{
						'staff_p_name':staff_p_name,
						'staff_p_sn':staff_p_sn,
						'staff_section_date':staff_section_date,
						'staff_custom_date':staff_custom_date,
						'staff_hour_nums':staff_hour_nums,
						'staff_salary':staff_salary,
						'staff_amount':staff_amount,
						'staff_salary_total':staff_salary_total,
						'staff_type':"lsg",
						'staff_insurance':"",
						'staff_cost_total':""
					},function(data){
						
					},'json');
					*/
				});
				//正式工
				var total_zsg = 0;
				$("#dg_staff_s #staff_zsg tbody tr").each(function(o){
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
					/**
					$.post("/admin/project/do_save_staff_info",{
						'staff_p_name':staff_p_name,
						'staff_p_sn':staff_p_sn,
						'staff_section_date':staff_section_date,
						'staff_custom_date':staff_custom_date,
						'staff_hour_nums':staff_hour_nums,
						'staff_salary':staff_salary,
						'staff_amount':staff_amount,
						'staff_salary_total':staff_salary_total,
						'staff_type':"zsg",
						'staff_insurance':staff_insurance,
						'staff_cost_total':staff_cost_total
					},function(data){
						
					},'json');
					*/
				});
				
				$.post("/admin/project/do_save_staff_info",{
					 'post_str':post_str,
					 'p_date':'S'
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
				$("#s_staff_cost").val(total_cost_staff);
				project_add_staff_cost();
				$(this).dialog("close");
			}
		}]
	});
	
	
	
	$('#s_btn_staff_cost').click(function() {
		var p_name = $("#p_name").val();
		if(p_name==""){
			alert("请先选择立项名称");
			$("#p_name").focus();
			return false;
		}
		$('#dg_staff_s').dialog('open');
		return false;
	});
	
	
	
	$('#dg_staff_z').dialog({
		autoOpen : false,
		width : 1400,
		resizable : true,
		modal : true,
		draggable : false,  
		title : "<div class='widget-header'><h4><i class='fa fa-hand-o-right'></i> 人员成本(正式运营阶段)</h4></div>",
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
				//临时工
				var total_lsg = 0;
				//$(o).parent().parent().parent().find("input[name='staff_section_date[]']:eq(0)").val();
				$("#dg_staff_z #staff_lsg tbody tr input[name='staff_salary_total[]']").each(function(o){
					var lsg_val =  $(this).val();
					lsg_val = lsg_val.replace(",","");
					total_lsg+=parseFloat(lsg_val);
				});
				 
				if(isNaN(total_lsg)){
					total_lsg=0;
				}
				
				
				$("#dg_staff_z #t_lsg_cost").html( ForDight(total_lsg,2) );
				$("#dg_staff_z #staff_lsg tfoot").show();
				
				//正式工
				var total_zsg = 0;
				$("#dg_staff_z #staff_zsg tbody tr input[name='staff_cost_total[]']").each(function(o){
					var zsg_val =  $(this).val();
					zsg_val = zsg_val.replace(",","");
					total_zsg+=parseFloat(zsg_val);
				});
				console.log( total_zsg );
				if(isNaN(total_zsg)){
					total_zsg=0;
				}
				console.log( total_zsg );
				
				$("#dg_staff_z #t_zsg_cost").html( ForDight(total_zsg,2) );
				$("#dg_staff_z #staff_zsg tfoot").show();
				$("#dg_staff_z #t_cost_y").html( ForDight((  parseFloat(total_zsg) + parseFloat(total_lsg)  ),2) );
			}
		},{
			html : "<i class='fa fa-check'></i>&nbsp; 提交",
			"class" : "btn btn-primary",
			click : function() {
				var post_str = "";
				//临时工
				var total_lsg = 0;
				$("#dg_staff_z #staff_lsg tbody tr").each(function(o){
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
					/**
					$.post("/admin/project/do_save_staff_info",{
						'staff_p_name':staff_p_name,
						'staff_p_sn':staff_p_sn,
						'staff_section_date':staff_section_date,
						'staff_custom_date':staff_custom_date,
						'staff_hour_nums':staff_hour_nums,
						'staff_salary':staff_salary,
						'staff_amount':staff_amount,
						'staff_salary_total':staff_salary_total,
						'staff_type':"lsg",
						'staff_insurance':"",
						'staff_cost_total':""
					},function(data){
						
					},'json');
					*/
				});
				//正式工
				var total_zsg = 0;
				$("#dg_staff_z #staff_zsg tbody tr").each(function(o){
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
					/**
					$.post("/admin/project/do_save_staff_info",{
						'staff_p_name':staff_p_name,
						'staff_p_sn':staff_p_sn,
						'staff_section_date':staff_section_date,
						'staff_custom_date':staff_custom_date,
						'staff_hour_nums':staff_hour_nums,
						'staff_salary':staff_salary,
						'staff_amount':staff_amount,
						'staff_salary_total':staff_salary_total,
						'staff_type':"zsg",
						'staff_insurance':staff_insurance,
						'staff_cost_total':staff_cost_total
					},function(data){
						
					},'json');
					*/
				});
				
				$.post("/admin/project/do_save_staff_info",{
					 'post_str':post_str,
					 'p_date':'Z'
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
				$("#z_staff_cost").val(total_cost_staff);
				project_add_staff_cost();
				$(this).dialog("close");
			}
		}]
	});
	
	
	$('#z_btn_staff_cost').click(function() {
		var p_name = $("#p_name").val();
		if(p_name==""){
			alert("请先选择立项名称");
			$("#p_name").focus();
			return false;
		}
		$('#dg_staff_z').dialog('open');
		return false;
	});
	
	function set_sale_plan_cost_profit()
	{
		//总计成本
		var s_cost = $("#s_cost").val();
		var z_cost = $("#z_cost").val();
		if(s_cost==""){
			s_cost=0;
		}
		if(z_cost==""){
			z_cost=0;
		}
		$("#t_cost").val( parseFloat(z_cost)+parseFloat(s_cost) );
		//总利润
		var s_profit = $("#s_profit").val();
		var z_profit = $("#z_profit").val();
		if(s_profit==""){
			s_profit=0;
		}
		if(z_profit==""){
			z_profit=0;
		}
		$("#t_profit").val( parseFloat(s_profit)+parseFloat(z_profit) );
		//投产比
		//收入合计/成本合计
		var t_income = $("#t_income").val();
		var t_cost = $("#t_cost").val();
		if(t_income==""){
			t_income=0;
		}
		if(t_cost==""){
			t_cost=0;
		}
		if(t_cost==0 || t_income==0){
			$("#t_output_rate").val(0);
		}else{
			$("#t_output_rate").val( "1:"+ ForDight( t_income/t_cost ,2)  );
		}
		
		
	}
	
	$("#s_cost").blur(function(){
		var s_cost = $(this).val(); // 成本
		var s_income = $("#s_income").val();  //收入
		
		if(isNull(s_cost)){
			$(this).focus();
			return false;
		}
		if(!isDecimal(s_cost)){
			$(this).val("").focus();
			return false;
		}
		
		if(isNull(s_income)){
			$("#s_income").focus();
			return false;
		}
		
		if(!isDecimal(s_income)){
			$("#s_income").val("").focus();
			return false;
		}
		
		$("#s_profit").val( s_income-s_cost );
		$("#s_output_rate").val( "1:"+ForDight( s_income/s_cost ,2) ); 
		set_sale_plan_cost_profit();
	});
	
	$("#z_cost").blur(function(){
		var z_cost = $(this).val(); // 成本
		var z_income = $("#z_income").val();  //收入
		
		if(isNull(z_cost)){
			$(this).focus();
			return false;
		}
		if(!isDecimal(z_cost)){
			$(this).val("").focus();
			return false;
		}
		
		if(isNull(z_income)){
			$("#z_income").focus();
			return false;
		}
		
		if(!isDecimal(z_income)){
			$("#z_income").val("").focus();
			return false;
		}
		
		$("#z_profit").val( z_income-z_cost );
		$("#z_output_rate").val( "1:"+ForDight( z_income/z_cost ,2) ); 
		set_sale_plan_cost_profit();
	});
	
	///单位时间客流 赋值
	$("#durations").blur(function(){
		var durations = $(this).val();
		if(isNull(durations)){
			$(this).focus();
			return false;
		}
		if(!isDecimal(durations)){
			$(this).val("").focus();
			return false;
		}
		var p_name = $("#p_name").val();
		if(p_name==""){
			alert("请先选择立项名称");
			$("#p_name").focus();
			return false;
		}
		
		$.post("/admin/project/get_project_durations",{
			'project_info_id':p_name,
			'durations':durations
		},function(data){
			$("#s_time_flow").val( data.s_time_flow );
			$("#z_time_flow").val( data.z_time_flow );
			$("#t_time_flow").val( data.t_time_flow );
		},'json');
		
		
	});
	
	
});

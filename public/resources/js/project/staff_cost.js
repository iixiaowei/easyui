$(function(){
	
	   function delStaffLsgTr(o){
		   $(o).parent().parent().remove().slideUp('slow');
	   }
	   
	   function  ForDight(Dight,How)    
	   {    
	      Dight  =  Math.round  (Dight*Math.pow(10,How))/Math.pow(10,How);    
	      return  Dight;    
	   }    
	
	   $("#btn_staff_lsg_add").click(function(){
		   var p_name = $("#p_name").find("option:selected").text();
		   var p_id = $("#p_name").val();
		   $.post("/admin/project/get_staff_lsg_tr",{
			   'p_id':p_id,
			   'p_name':p_name
		   },function(data){
			    $("#staff_lsg tbody").append(data);
		   });
		   
	   });	
	   
	   $("#btn_staff_zsg_add").click(function(){
		   var p_name = $("#p_name").find("option:selected").text();
		   var p_id = $("#p_name").val();
		   $.post("/admin/project/get_staff_zsg_tr",{
			   'p_id':p_id,
			   'p_name':p_name
		   },function(data){
			    $("#staff_zsg tbody").append(data);
		   });
		   
	   }); 
	
	   $("#p_name").change(function(){
	       var project_id = $("#p_name").val();
	       if(project_id==""){
	    	   $("#project_sn").val( "" );
			   $("#project_cycle").val( "" );
	       }
	       
	       
		   $.post("/admin/project/get_project_info",{
				"id":project_id
			   },function(data){
			      
				  $("#project_sn").val( data.sn );
			      $("#project_cycle").val( data.date_nums );
				  $("#plan_episodes").val( data.plan_episodes );
			      
			      $("#staff_lsg input[name='staff_p_sn[]']:eq(0)").val( data.sn );
			      $("#staff_lsg input[name='staff_section_date[]']:eq(0)").val( data.date_nums );
			      $("#staff_lsg input[name='staff_p_name[]']:eq(0)").val( $("#p_name").find("option:selected").text() );
			      
			      $("#staff_zsg input[name='staff_p_sn[]']:eq(0)").val( data.sn );
			      $("#staff_zsg input[name='staff_section_date[]']:eq(0)").val( data.date_nums );
			      $("#staff_zsg input[name='staff_p_name[]']:eq(0)").val( $("#p_name").find("option:selected").text() );
			      
			      //项目预算
			      $("#y_gcys").val( data.p.y_gsys_total );
			      $("#s_gcys").val( data.p.s_gsys_total );
			      $("#z_gcys").val( data.p.z_gsys_total );
			      $("#t_gcys").val( data.p.t_gsys_total );
			      
			      $("#y_gdzc").val( data.p.y_gdzc_total );
			      $("#s_gdzc").val( data.p.s_gdzc_total );
			      $("#z_gdzc").val( data.p.z_gdzc_total );
			      $("#t_gdzc").val( data.p.t_gdzc_total );
			      
			      $("#y_wxzc").val( data.p.y_wxzc_total );
			      $("#s_wxzc").val( data.p.s_wxzc_total );
			      $("#z_wxzc").val( data.p.z_wxzc_total );
			      $("#t_wxzc").val( data.p.t_wxzc_total );
			      
			      $("#y_yfqywl").val( data.p.y_yfqywl_total );
			      $("#s_yfqywl").val( data.p.s_yfqywl_total );
			      $("#z_yfqywl").val( data.p.z_yfqywl_total );
			      $("#t_yfqywl").val( data.p.t_yfqywl_total );
			      
			      $("#y_yycb").val( data.p.y_yycb_total );
			      $("#s_yycb").val( data.p.s_yycb_total );
			      $("#z_yycb").val( data.p.z_yycb_total );
			      $("#t_yycb").val( data.p.t_yycb_total );
			      
			      $("#y_glbgf").val( data.p.y_glbgf_total );
			      $("#s_glbgf").val( data.p.s_glbgf_total );
			      $("#z_glbgf").val( data.p.z_glbgf_total );
			      $("#t_glbgf").val( data.p.t_glbgf_total );
			      
			      $("#y_gsfy").val( data.p.y_gsfy_total ); 
			      $("#s_gsfy").val( data.p.s_gsfy_total ); 
			      $("#z_gsfy").val( data.p.z_gsfy_total );
			      $("#t_gsfy").val( data.p.t_gsfy_total ); 
			      
			      
			      $("#s_flow").val( data.s.s_flow );
			      $("#z_flow").val( data.s.z_flow );
			      $("#t_flow").val( data.s.t_flow );
			      
			      $("#s_income").val( data.s.s_income );
			      $("#z_income").val( data.s.z_income );
			      $("#t_income").val( data.s.t_income );
			      
			      $("#s_manchang").val( data.s.s_manchang );
			      $("#z_manchang").val( data.s.z_manchang );
			      $("#t_manchang").val( data.s.t_manchang );
			      
			      $("#s_manchanglv").val( data.s.s_manchanglv );
			      $("#z_manchanglv").val( data.s.z_manchanglv );
			      $("#t_manchanglv").val( data.s.t_manchanglv );
			      
			      $("#s_agv_price").val( data.s.s_agv_price );
			      $("#z_agv_price").val( data.s.z_agv_price );
			      $("#t_agv_price").val( data.s.t_agv_price );
			      
			      $("#s_add_user").val( data.s.s_add_user );
			      $("#z_add_user").val( data.s.z_add_user );
			      $("#t_add_user").val( data.s.t_add_user );
			      
			      
			      project_add_xmys_total();
			      
		   },'json');
		   
		   //研发阶段
		   $.post("/admin/project/get_staff_list",{
			   'p_id':project_id,
			   'p_date':'Y'
		   },function(data){
			   $("#dialog_simple").html( data );
		   });
		   //试运营阶段
		   $.post("/admin/project/get_staff_s_list",{
			   'p_id':project_id,
			   'p_date':'S'
		   },function(data){
			   $("#dg_staff_s").html( data );
		   });
		   
		   //正式运营阶段
		   $.post("/admin/project/get_staff_z_list",{
			   'p_id':project_id,
			   'p_date':'Z'
		   },function(data){
			   $("#dg_staff_z").html( data );
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
		$('#dialog_simple').dialog({
			autoOpen : false,
			width : 1400,
			resizable : true,
			modal : true,
			draggable : false,  
			title : "<div class='widget-header'><h4><i class='fa fa-hand-o-right'></i> 人员成本(研发阶段)</h4></div>",
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
					$("#dialog_simple #staff_lsg tbody tr input[name='staff_salary_total[]']").each(function(o){
						var lsg_val =  $(this).val();
						lsg_val = lsg_val.replace(",","");
						total_lsg+=parseFloat(lsg_val);
					});
					 
					if(isNaN(total_lsg)){
						total_lsg=0;
					}
					
					
					$("#dialog_simple #t_lsg_cost").html( ForDight(total_lsg,2) );
					$("#dialog_simple #staff_lsg tfoot").show();
					
					//正式工
					var total_zsg = 0;
					$("#dialog_simple #staff_zsg tbody tr input[name='staff_cost_total[]']").each(function(o){
						var zsg_val =  $(this).val();
						zsg_val = zsg_val.replace(",","");
						total_zsg+=parseFloat(zsg_val);
					});
					 
					if(isNaN(total_zsg)){
						total_zsg=0;
					}
					 
					
					$("#dialog_simple #t_zsg_cost").html( ForDight(total_zsg,2) );
					$("#dialog_simple #staff_zsg tfoot").show();
					$("#dialog_simple #t_cost_y").html( ForDight((  parseFloat(total_zsg) + parseFloat(total_lsg)  ),2) );
				}
			},{
				html : "<i class='fa fa-check'></i>&nbsp; 提交",
				"class" : "btn btn-primary",
				click : function() {
					var post_str = "";
					//临时工
					var total_lsg = 0;
					$("#dialog_simple #staff_lsg tbody tr").each(function(o){
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
					$("#dialog_simple #staff_zsg tbody tr").each(function(o){
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
						 'p_date':'Y'
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
					$("#y_staff_cost").val(total_cost_staff);
					project_add_staff_cost();
					$(this).dialog("close");
				}
			}]
		});

		$('#y_btn_staff_cost').click(function() {
			var p_name = $("#p_name").val();
			if(p_name==""){
				alert("请先选择立项名称");
				$("#p_name").focus();
				return false;
			}
			$('#dialog_simple').dialog('open');
			return false;
		});
		

});
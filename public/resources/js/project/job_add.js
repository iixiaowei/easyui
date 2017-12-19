$(function(){
	
	function isNull(str) {
	    if (str == "") return true;
	    var regu = "^[ ]+$";
	    var re = new RegExp(regu);
	    return re.test(str);
	}
	
	
	$("#staff_serial_sn").blur(function(){
		var staff_serial_sn = $("#staff_serial_sn").val();
		if(isNull(staff_serial_sn)){
			$("#staff_name").val( "" );
			$("#telephone").val( "" );
			$("#staff_serial_sn").focus();
			return false;
		}
		$.post("/admin/job/get_staff_info",{
			'staff_serial_sn':staff_serial_sn
		},function(data){
			if(data.exists==2){
				alert("员工不存在！");
			}
			$("#staff_name").val( data.staff_name );
			$("#telephone").val( data.telephone );
		},'json');
	});
	
	$("#dep_big_id").change(function(){
		var dep_big_id = $("#dep_big_id").val();
		if(isNull(dep_big_id)){
			$("#dep_big_id").focus();
			$("#dep_small_id").empty();
			$("#dep_small_id").append("<option value=\"\">请选择二级部门</option>");
			return false;
		}
		$.post("/admin/job/get_small_department",{
			'dep_big_id':dep_big_id
		},function(data){
			$("#dep_small_id").html( data );
		});
		
	});
	
	$("#station_type_id").change(function(){
		var station_type_id = $("#station_type_id").val();
		if(isNull(station_type_id)){
			$("#tip_station_type").html("");
			return false;
		}
		$.post("/admin/job/get_station_type_note",{
			'station_type_id':station_type_id
		},function(data){
			$("#tip_station_type").html( data );
		});
	});
	
});
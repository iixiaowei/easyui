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
		$.post("/admin/instructorjob/get_staff_info",{
			'staff_serial_sn':staff_serial_sn
		},function(data){
			if(data.exists==2){
				alert("辅导员编号不存在！");
			}
			$("#staff_name").val( data.staff_name );
			$("#telephone").val( data.telephone );
		},'json');
	});
	
});
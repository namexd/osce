$(function(){
	var oIput= '';
	$(".all_checked").click(function(){
		oIput=$("#table-striped").find("input");
		for(var i=0;i<oIput.length;i++)
		{
			if(oIput[0].checked==true){

				oIput[i].checked=true;
				//console.log(oIput.length);
				oIput.prev().addClass("check");
				$(".sum").text(oIput.length-1);

			}
			else{
				oIput[i].checked=false;
				oIput.prev().removeClass("check");
				//oIput[0].removeClass("checked");
				$(".sum").text(0);
			}
		}
	});

	$("#table-striped").delegate(".checkbox_input","click",function(event){
		//event.stopPropagation();
		checkbox_all($(this));
		
		var check_id = $('.check_id');
		var num = 0;
		for(var i=0;i<check_id.length;i++){
		
			if($(check_id[i]).is(':checked')){
				num++;
			}
		}
		$(".sum").text(num);
		//return false;
	});


function checkbox_all(obj){
	if(obj.find("input").is(':checked')){
		obj.find(".check_icon ").addClass("check");
	}else{
		obj.find(".check_icon").removeClass("check");
		//obj.find("input").removeClass("check");
	}
	oIput=$("#table-striped").find("input");
	var mark = false;
	for(var f=1;f<oIput.length;f++)
	{
		if(!$(oIput[f]).is(':checked')){
			mark = true;
			break;
		}
	}
	if(mark){
		oIput[0].checked=false;
		$(oIput[0]).prev().removeClass("check");
	}else{
		oIput[0].checked=true;
		$(oIput[0]).prev().addClass("check");
	}
}
});
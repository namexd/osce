var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "examination_list":examination_list();break;
    }
});



function examination_list(){
	$("#examination").change(function(){
		var id=$(this).val();
		var url=pars.ajaxurl;
		if(id==0){
			$("#exmination_ul li").remove();
			$(".time").text("");
			return false;
		}
		$.ajax({
			type:"get",
			url:url,
			async:true,
			data:{exam_id:id},
			success:function(res){
				$("#exmination_ul li").remove();
				$(".time").text("");
			    //console.log(res);
				for (var i=0;i<res.data.length;i++) {
					var type=res.data[i].type;
					var begin_time=(res.data[i].begin_dt).substring(0,10);
					var end_time=(res.data[i].begin_dt).substring(0,10);
					var time="";
					if(begin_time==end_time){
						time=begin_time;
					}else{
						time=begin_time+'~'+end_time;
					}
					$(".time").text(time);
					var str="";
					switch (type){
						case 1:
							str+='<li>'+
						    		'<dl>'+
						    			'<dd>'+res.data[i].station_name+'：'+res.data[i].score+'分</dd>'+
						    			'<dd>用时：'+res.data[i].time+'分</dd>'+
						    			'<dd style="width:100%">评价老师：'+res.data[i].grade_teacher+'</dd>'+
						    		'</dl>'+
						    		'<p class="clearfix see_msg">'+
						    			'<a class="nou right" href="'+pars.detailUrl+'?exam_screening_id='+res.data[i].exam_screening_id+'">考卷详情&nbsp;&gt;&nbsp;&nbsp;</a>'+
						    		'</p>'+
						    	'</li>';
							break;
						case 2:
							str+='<li>'+
						    		'<dl>'+
						    			'<dd>'+res.data[i].station_name+'：'+res.data[i].score+'分</dd>'+
						    			'<dd>用时：'+res.data[i].time+'分</dd>'+
						    			'<dd class="tbl_type"><div class="tbl_cell" style="width:72px">评价老师：</div><div class="tbl_cell">'+res.data[i].grade_teacher+'</div></dd>'+
						    			'<dd>SP病人：'+res.data[i].sp_name+'</dd>'+
						    		'</dl>'+
						    		'<p class="clearfix see_msg">'+
						    			'<a class="nou right" href="'+pars.detailUrl+'?exam_screening_id='+res.data[i].exam_screening_id+'">考卷详情&nbsp;&gt;&nbsp;&nbsp;</a>'+
						    		'</p>'+
						    	'</li>';
							break;
						case 3:
							str+='<li>'+
						    		'<dl>'+
						    			'<dd>'+res.data[i].station_name+'：'+res.data[i].score+'分</dd>'+
						    			'<dd>用时：'+res.data[i].time+'分</dd>'+
						    			'<dd>理论考试</dd>'+
						    		'</dl>'+
						    		'<p class="clearfix see_msg">'+
						    			'<a class="nou right" href="'+pars.detailUrl+'?exam_screening_id='+res.data[i].exam_screening_id+'">考卷详情&nbsp;&gt;&nbsp;&nbsp;</a>'+
						    		'</p>'+
						    	'</li>';
							break;
						default:
							break;
					}
					$("#exmination_ul").append(str);
				}
			}
		});
	})
}

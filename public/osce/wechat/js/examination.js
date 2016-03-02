var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "examination_list":examination_list();break;
        case "examination_list_teacher":examination_list_teacher();break;
    }
});



function examination_list(){
	$('#showActionSheet').click(function () {
        var mask = $('#mask');
        var weuiActionsheet = $('#weui_actionsheet');
        weuiActionsheet.addClass('weui_actionsheet_toggle');
        mask.show().addClass('weui_fade_toggle').one('click', function () {
            hideActionSheet(weuiActionsheet, mask);
        });
        $('#actionsheet_cancel').one('click', function () {
            hideActionSheet(weuiActionsheet, mask);
        });
        //选择
        $('.weui_actionsheet_menu .weui_actionsheet_cell').one('click',function(){
            $('#showActionSheet').text($(this).text());

		var id=$(this).attr('value');
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
				for (var i=0;i<res.data.length;i++) {
					console.log('type1',typeof res.data[i].type);
					var type=parseInt(res.data[i].type);
					console.log('type2',typeof res.data[i].type);
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
					console.log('dom',str);
					$("#exmination_ul").append(str);
				}
			}

		});

		hideActionSheet(weuiActionsheet, mask);
        });

        weuiActionsheet.unbind('transitionend').unbind('webkitTransitionEnd');

        function hideActionSheet(weuiActionsheet, mask) {
            weuiActionsheet.removeClass('weui_actionsheet_toggle');
            mask.removeClass('weui_fade_toggle');
            weuiActionsheet.on('transitionend', function () {
                mask.hide();
            }).on('webkitTransitionEnd', function () {
                mask.hide();
            })
        }
	})
}

function examination_list_teacher(){
    $('#showActionSheet').click(function () {
        var mask = $('#mask');
        var weuiActionsheet = $('#weui_actionsheet');
        weuiActionsheet.addClass('weui_actionsheet_toggle');
        mask.show().addClass('weui_fade_toggle').one('click', function () {
            hideActionSheet(weuiActionsheet, mask);
        });
        $('#actionsheet_cancel').one('click', function () {
            hideActionSheet(weuiActionsheet, mask);
        });
        //选择
        $('.weui_actionsheet_menu .weui_actionsheet_cell').one('click',function(){
            $('#showActionSheet').text($(this).text());

            var id=$(this).attr('value');
			var s_id=$(this).attr("data-id");
			console.log(id,s_id)
			var url=pars.ajaxurl;
			if(id==0){
				$("#time").text("");
				$("#subject").text("");
				$("#number").text("");
				$("#time2").text("");
				$("#vgn").text("");
				$(".cj_tab tr.new").remove();
				return false;
			}

			$.ajax({
				type:"get",
				url:url,
				async:true,
				data:{
					 exam_id:id ,
					 station_id:s_id
				},
				success:function(res){
					var begin_time=(res.data['item'].exam_begin_dt).substring(0,10);
					var end_time=(res.data['item'].exam_end_dt).substring(0,10);
					var time="";
					if(begin_time==end_time){
						time=begin_time;
					}else{
						time=begin_time+'~'+end_time;
					}
					$("#time").text(time);
					$("#subject").text(res.data['item'].subject_name);
					$("#number").text(res.data['item'].avg_total);
					$("#time2").text(res.data['item'].avg_time);
					$("#vgn").text(res.data['item'].avg_score);

					var str="",strs="";
					for (var i=0;i<res.data['subjectData'].length;i++) {
						if(i%2==0){
							str='<tr class="new even">'+
								'<td>'+res.data['subjectData'][i].student_name+'</td>'+
								'<td>'+res.data['subjectData'][i].Scores+'</td>'+
								'<td>' +
								'<a href="'+pars.detailUrl+'?student_id='+res.data['subjectData'][i].student_id+'&exam_id='+res.data['subjectData'][i].exam_id+'"><i class="fa fa-search font16 see"></i></a>' +
								'</td>'+
								'</tr>';

						}else{
							str='<tr class="new obb">'+
								'<td>'+res.data['subjectData'][i].student_name+'</td>'+
								'<td>'+res.data['subjectData'][i].Scores+'</td>'+
								'<td>' +
								'<a href="'+pars.detailUrl+'?student_id='+res.data['subjectData'][i].student_id+'&exam_id='+res.data['subjectData'][i].exam_id+'"><i class="fa fa-search font16 see"></i></a>' +
								'</td>'+
								'</tr>';
						}

						strs+=str;
					}

					$(".cj_tab tr.new").remove();
					$(".cj_tab").append(strs);
				}
			});


            hideActionSheet(weuiActionsheet, mask);
        });

        weuiActionsheet.unbind('transitionend').unbind('webkitTransitionEnd');

        function hideActionSheet(weuiActionsheet, mask) {
            weuiActionsheet.removeClass('weui_actionsheet_toggle');
            mask.removeClass('weui_fade_toggle');
            weuiActionsheet.on('transitionend', function () {
                mask.hide();
            }).on('webkitTransitionEnd', function () {
                mask.hide();
            })
        }
    });
}



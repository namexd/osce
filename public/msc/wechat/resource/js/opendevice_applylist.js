/**
 * Created by Administrator on 2015/12/8 0008.
 */
function gethistory(qj,url,getdetail){


    $.ajax({
        url:url, /*${ctx}/*/
        type:"get",
        dataType:"json",
        contentType : 'application/json',
        cache:false,
        data:qj,
        success: function(result) {
            var totalpages=result.data.rows.DeviceApplyList.last_page;
            $("#totalpages").text(totalpages);//设置总页数
            $(result.data.rows.DeviceApplyList.data).each(function(){

                var currenttime=this.original_begin_datetime.substring(5,10);
                var begintime=this.original_begin_datetime.substring(11,16);
                var endtime=this.original_end_datetime.substring(11,16);

                $(".detail_list ul").append(
                    '<li>'
                    +'<div><span>'+this.resources_lab_devices.name+'</span> </div>'
                    +'<div class="Time_slot">'
                    +'<p>'+currenttime+'</p>'
                    +'<p>'+begintime+'-'+endtime+'</p>'
                    +'</div>'
                    +'<div class="opera">'
                    +'<span class="State1">'+this.user.name+'</span>'
                    +'</div>'
                    +'<a href="'+getdetail+'?id='+this.id+'"><div class="opera"><span class="State1">审核申请</span></div>'
                    +' </a></li>'
                );

            });

        },

    });

}

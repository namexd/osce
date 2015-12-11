/**
 * Created by Administrator on 2015/12/7 0007.
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
            var cnt = result.data.rows.cnt;
            totalpages=result.data.rows.ClassroomApplyList.last_page;

            $(result.data.rows.ClassroomApplyList.data).each(function(){

                var begintime=this.begin_datetime.substring(11,16);
                var endtime=this.end_datetime.substring(11,16);
                var currentdate=this.begin_datetime.substring(0,10);
                $(".detail_list ul").append(
                    '<a href="'+getdetail+'?id='+this.id+'"><li>'
                    +'<div><span>'+this.resources_device.name+'</span> </div>'
                    +'<div class="Time_slot">'
                    +'<p>'+currentdate+'</p>'
                    +'<p>'+begintime+'-'+endtime+'</p>'
                    +'</div>'
                    +'<div class="opera">'
                    +this.user.name
                    +'</div></a></li>'
                );

            });

        },

    });

}

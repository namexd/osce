/**
 * Created by DELL on 2015/11/25.
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
                var opera;
                var status;
                var getdetailall;
                var now_num=this.apply_person_total;
                var total_num=this.resorces_lab_person_total;


                var begintime=this.open_lab_calendar.begintime.substring(0,5);
                var endtime=this.open_lab_calendar.endtime.substring(0,5);
                $(".detail_list ul").append(
                    '<a href="'+getdetail+'?id='+this.id+'"><li>'
                    +'<div><span>'+this.open_lab_calendar.resources_classroom.name+'</span> </div>'
                    +'<div class="Time_slot">'
                    +'<p>'+begintime+'-'+endtime+'</p>'
                    +'</div>'
                    +'<div class="opera">'
                    +this.open_lab_calendar.resources_classroom.location
                    +'</div></a></li>'
                );

            });

        },

    });

}

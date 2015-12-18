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
                var now_num=this.num;
                var total_num=this.resources_classroom.person_total;
                status=now_num+'/'+total_num

                if(this.status=="0"){
                    if(this.is_appointment=="1"){
                        opera='<div class="opera"><span class="State2">已预约</span></div>';
                    }else {
                        opera= '<a href="'+getdetail+'?id='+this.id+'&apply_date='+qj.dateTime+'&apply_type=0'+'"><div class="opera">'+'<span class="State1">预约</span>'+'</div>'
                    }
                }else if(this.status=="1"){
                    status="";
                    opera='<div class="opera"><span  class="State3">已被预订</span></div>';
                }

                var begintime=this.begintime.substring(0,5);
                var endtime=this.endtime.substring(0,5);
                //var currentdate=this.currentdate.substring(5,10);
                $(".detail_list ul").append(
                    '<li>'
                    +'<div><span>'+this.resources_classroom.name+'</span> </div>'
                    +'<div class="Time_slot">'
                    +'<p>'+begintime+'-'+endtime+'</p>'
                    +'</div>'
                    +'<div class="opera">'
                    +status
                    +'</div>'
                    +opera
                    +' </a></li>'

                );
            });

        },

    });

}

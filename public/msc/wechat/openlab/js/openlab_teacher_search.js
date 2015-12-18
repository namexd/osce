/**
 * Created by DELL on 2015/11/25.
 */


function gethistory(qj,url,getdetail,getdetail2){

    $.ajax({
        url:url, /*${ctx}/*/
        type:"get",
        dataType:"json",
        contentType : 'application/json',
        cache:false,
        data:qj,
        success: function(result) {
            totalpages=result.data.rows.ClassroomApplyList.last_page;

            $(result.data.rows.ClassroomApplyList.data).each(function(){

                var opera;
                var status;
                var getdetailall;
                if(this.status=="0"){

                    status='<span class="State1">正常</span>';
                    opera= '<a href="'+getdetail2+'?id='+this.id+'&apply_date='+qj.dateTime+'&apply_type=0'+'"><div class="opera">'+'<span class="State1">使用</span>'+'</div>';
                }else if(this.status=="1"){

                    if(this.is_appointment=="1"){
                        status='<span class="State2">已预约</span>';
                        opera='';
                    }else{
                        status='<span class="State3">已被预订</span>';
                        opera= '<a href="'+getdetail2+'?id='+this.id+'&apply_date='+qj.dateTime+'&apply_type=2'+'"><div class="opera">'+'<span class="State1">紧急预约</span>'+'</div>';
                    }

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
                    +'<span class="State1">'+status+'</span>'
                    +'</div>'
                    +opera
                    +' </a></li>'
                );

            });

        },

    });

}

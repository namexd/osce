/**
 * Created by DELL on 2015/11/25.
 */



function gethistory(qj,url,getdetail,getdetail2){

    $("#layer_loading").show();//加载中显示
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
                    status='<span class="State3">不允许预约使用</span>';
                    opera='';
                }else if(this.status=="1"){
                    status='<span class="State1">正常</span>';
                    opera='<span class="State1">使用</span>';
                    getdetailall=getdetail;
                }else if(this.status=="2"){
                    status='<span class="State2">已预约</span>';
                    opera='<span  class="State1">紧急预约</span>';
                    getdetailall=getdetail2;
                }

                var begintime=this.begintime.substring(0,5);
                var endtime=this.endtime.substring(0,5);
                var currentdate=this.currentdate.substring(5,10);
                $(".detail_list ul").append(
                    '<li>'
                    +'<div><span>'+this.name+'</span> </div>'
                    +'<div class="Time_slot">'
                    +'<p>'+currentdate+'</p>'
                    +'<p>'+begintime+'-'+endtime+'</p>'
                    +'</div>'
                    +'<div class="opera">'
                    +'<span class="State1">'+status+'</span>'
                    +'</div>'
                    +'<a href="'+getdetailall+'?id='+this.id+'"><div class="opera">'+opera+'</div>'
                    +' </a></li>'
                );

            });
            $("#layer_loading").hide(200);//加载结束消失
        },

    });

}

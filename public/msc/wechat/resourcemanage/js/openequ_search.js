/**
 * Created by Administrator on 2015/12/7 0007.
 */
function gethistory(qj,url,getdetail){

    $("#layer_loading").show();//加载中显示
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
                status=now_num+'/'+total_num
                if(now_num>total_num){
                    opera='<div class="opera"><span class="State3">已满</span></div>';

                }else {
                    opera= '<a href="'+getdetail+'?id='+this.id+'"><div class="opera">'+'<span class="State1">预约</span>'+'</div>'

                }

                if(this.status=="0"){
                    status='';
                    opera='<span class="State3">不允许预约使用</span>';
                }else if(this.status=="1"){
                    if(now_num>total_num){
                        opera='<div class="opera"><span class="State3">已预约</span></div>';

                    }else {
                        opera= '<a href="'+getdetail+'?id='+this.id+'"><div class="opera">'+'<span class="State1">预约</span>'+'</div>'
                    }

                }else if(this.status=="2"){

                    opera='<div class="opera"><span  class="State1">已满</span></div>';
                }


                var begintime=this.begintime.substring(0,5);
                var endtime=this.endtime.substring(0,5);
                var currentdate=this.currentdate.substring(5,10);
                $(".detail_list ul").append(
                    '<a href="'+getdetail+'?id='+this.id+'"><li>'
                    +'<div><span>'+this.name+'</span> </div>'
                    +'<div class="Time_slot">'
                    +'<p>'+currentdate+'</p>'
                    +'<p>'+begintime+'-'+endtime+'</p>'
                    +'</div>'
                    +'<div class="opera">'
                    +status
                    +'</div></a></li>'
                );

            });
            $("#layer_loading").hide(200);//加载结束消失
        },

    });

}

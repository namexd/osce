/**
 * Created by DELL on 2015/11/25.
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
            totalpages=result.data.rows.historyList.last_page;
            $(result.data.rows.historyList.data).each(function(){

                if(this.tool_item!=null){
                    if(this.tool_item.status=="2"){
                        var status='<span class="State3">已借出</span>';
                    }else if(this.tool_item.status=="1"){
                        var status='<span class="State1">正常</span>';
                    }else if(this.tool_item.status=="-1"){
                        var status='<span class="State2">不允许借出</span>';
                    }else{
                        var status='<span class="State2">已报废</span>';
                    }
                }else{
                    var status='<span class="State3">状态未知</span>';
                }
                $(".detail_list ul").append(
                    '<li><a href="'+getdetail+'?id='+this.id+'">'
                    +'<div><span>'+this.resources_tool.name+'</span> </div>'
                    +'<div>'+this.code+'</div>'
                    +'<div class="Time_slot">'
                    +'<p>'+this.real_begindate+'</p>'
                    +'<p><span class="State1">'+this.real_enddate+'</span></p>'
                    +'</div>'
                    +'<div>'
                    +'<span>'+this.user.name+'</span>'
                    +'</div>'
                    +'<div class="attention">'+status+'</div>'
                    +' </a></li>'
                );

            });
            $("#layer_loading").hide(200);//加载结束消失
        },

    });

}

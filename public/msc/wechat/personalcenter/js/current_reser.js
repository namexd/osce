/**
 * Created by Administrator on 2015/12/9 0009.
 */
function gethistory(qj,url){
    $("#layer_loading").show();//加载中显示
    var str="";
    $.ajax({
        url:url,
        type:"get",
        dataType:"json",
        cache:false,
        data:qj,
        success: function(result) {
            totalpages=result.data.total;

            $(result.data.rows).each(function(){
                str+='<li><a href="">'
                    +'<div><span>'+this.name+'</span></div>'
                    +'<div>'+this.code+'</div>'
                    +'<div class="Time_slot">'
                    +'<p>'+this.time_start+'</p>'
                    +'<p><span class="State1">'+this.time_end+'</span></p>'
                    +'</div>'
                    +' </a></li>';
            });
            $(".detail_list ul").append(str);
            $("#layer_loading").hide(200);//加载结束消失
        }
    });
}

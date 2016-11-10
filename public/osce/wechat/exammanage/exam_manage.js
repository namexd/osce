/**
 * 资讯&通知
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22  
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        //资讯&通知
        case "exam_notice":exam_notice();break;
        case "sp_invitation_detail":sp_invitation();break;
    }
});

/**
 * sp老师邀请
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 */
function sp_invitation_detail() {
	$('.agree').click(function(){

       var id =$(this).attr('data');
       var status = $(this).val();
//                 var url ='/osce/wechat/invitation/invitation-respond';
     $.ajax(pars.agree,{
//                     url:url,
         type:'get',
         dataType:"json",
         data:{
             id:id,
             status:status

         },

         success:function(data){
             if(data.code==1){
                layer.msg('操作成功!',{icon: 1,time:2000},function(){
                 	window.location.reload();
				});    

             }

         },
         error:function() {
            layer.msg('操作失败!'); 
         }
     })
 })

$('.rejected').click(function(){

    var id =$(this).attr('data');
    var status = $(this).val();
//                 var url ='/osce/wechat/invitation/invitation-respond';
    $.ajax(pars.rejected,{
//                     url:url,
        type:'get',
        dataType:"json",
        data:{
            id:id,
            status:status

        },

        success:function(data){
            if(data.code==1){
                layer.msg('操作成功!',{time: 2000,icon: 1},function(){
                 	window.location.reload();
				});
            }
        },
        error:function() {
            layer.msg('操作失败!'); 
        }
    })
})
}

/**
 * 资讯&通知列表
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 */
function exam_notice() {
	$(window).scroll(function(e){
        if(away_top >= (page_height - window_height)&&now_page<totalpages){
            now_page++;
            //qj.page=now_page;//设置页码

            getItem(now_page,url)
            /*加载显示*/
        }
    });
    //初始化
    var now_page = 1;
    var url = pars.URL;
    //内容初始化
    $('.history-list').empty();
    getItem(now_page,url);

    function getItem(current,url){

        $.ajax({
            type:'get',
            url:url,
            aysnc:true,
            data:{id:current,page:current},
            success:function(res){
                totalpages = Math.ceil(res.data.total/res.data.pagesize);

                var html = '';
                var index = (current - 1)*10;
                data = res.data.rows;

                for(var i in data){
                    //准备dom
                    //计数
                    var key = (index+1+parseInt(i))

                    html +='<li>'+
                                '<p class="title">'+data[i].name+'</p>'+
                                '<p class="time"><span class="year">'+data[i].created_at+'</span>'+
                                    '<a style="color:#1ab394;" class="right" href="'+pars.href+'?id='+data[i].id+'">查看详情&nbsp;&gt;</a>'+
                                '</p>'+
                            '</li>';
                }
                //插入
                $('#discussion_ul').append(html);
            }
        });

    }
}
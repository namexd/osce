/**
 * Created by DELL on 2015/11/25.
 */

function ajaxRequest(data,url){

    $.ajax({
        type:'get',
        url:url,
        data:data,
        async:true,
        success:function(res){
            /*
             *mao 2015-11-24
             */
            //数据请求结果
            totalpages=res.data.total;
            var container = $('.detail_list').find('ul');
            var html;
            if(res.code==1){
                var data = res.data.rows;
                for(var i in data){
                    $(".detail_list ul").append(
                        '<li id='+data[i].id+' type='+data[i].type+'>'+
                        '<div class="name">'+
                        '<img src="'+data[i].img+'">'+
                        '<span>'+data[i].name+'</span>'+
                        '</div>'+
                        '<div class="number">'+data[i].id+'</div>' +
                        '</li>'
                    );
                }

            }
            else{
                //console.log((res.message).split(':')[0]);
            }

        }
    });
}
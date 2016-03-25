/**
 * 考前培训
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22  
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        //考前培训
        case "train_list":train_list();break;
    }
});

/**
 * 培训列表
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 */
function train_list() {
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
            data:{id:current,pagesize:current},
            success:function(res){
                totalpages = res.total;
                var html = '';
                var index = (current - 1)*10;
                data = res.data.rows;

                for(var i in data){
                    //准备dom
                    //计数
                    var key = (index+1+parseInt(i))
                    var author  =   data[i].author;
                    var num=data[i].clicks;
                    if(num==""){
                        num=0;
                    }
                    if(author==null){
                        author_name = '未知人';
                    }else {
                        author_name = data[i].author.name;
                    }
                    html += '<li>'+
					        	'<a class="nou" href="'+pars.href+'?id='+data[i].id+'">'+
					        		'<p class="font14 fontb clo3 p_title">'+data[i].name+'</p>'+
					        		'<p class="font12 clo9 main_txt">'+data[i].address+'</p>'+
					        		'<p class="font12 clo9 main_txt">'+data[i].begin_dt+'~'+data[i].end_dt+'</p>'+
					        		'<p class="font12 p_bottom">'+
					        			'<span class="font14 student_name">'+author_name+'</span>'+
					        			'<span class="clo9">&nbsp;'+data[i].time+'</span>'+
					        			'<span class="right comment">已读&nbsp;'+num+'</span>'+
					        		'</p>'+
					        	'</a>'+
					        '</li>';
                }
                //插入
                $('#discussion_ul').append(html);
            }
        });

    }
}
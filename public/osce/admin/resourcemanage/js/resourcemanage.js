/**
 * Created by Administrator on 2015/12/15 0015.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "test_station":test_station();break;
        case "examroom":examroom();break;
        case "clinicalcase":clinicalcase();break;
    }
});


/**
 * 考站管理
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 */
function test_station(){

    $('table').on('click','.fa-trash-o',function(){

        var thisElement = $(this);
        layer.alert('确认删除？',function(){
            $.ajax({
                type:'post',
                async:true,
                url:pars.deletes,
                data:{id:thisElement.parent().parent().parent().attr('value')},
                success:function(res){
                    location.reload();
                }
            })
        });
    })
}

/**
 * 病例
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 */
function clinicalcase(){
	deleteItem(pars.deletes);
}

/**
 * 考场
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 */
function examroom(){
	deleteItem(pars.deletes);
}

/**
 * 删除操作
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 * @param   {string}   url 请求地址
 */
function deleteItem(url){
	$('table').on('click','.fa-trash-o',function(){

        var thisElement = $(this);
        layer.alert('确认删除？',function(){
            $.ajax({
                type:'post',
                async:true,
                url:url,
                data:{id:thisElement.parent().parent().parent().attr('value')},
                success:function(res){
                    location.reload();
                }
            })
        });
    })
}
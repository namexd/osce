/**
 * Created by Administrator on 2016/3/7 zengjie
 * 题库管理
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "subject_check_tag":subject_check_tag();break;//考核标签
    }
});

//考核标签
function subject_check_tag(){
    //新增
    $("#add").click(function(){
        var url = pars.addUrl;
        parent.layer.open({
            type: 2,
            title: '新增考核标签',
            shadeClose: true,
            shade: 0.8,
            area: ['60%', '60%'],
            content:url
        });
    });
}





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
    //跳详情页面
    $(".subjectBody").delegate(".fa-search","click",function(){
        var examid = $(this).attr("examid");
        var resultid = $(this).attr("resultid");
        var subid = $(this).attr("subid");
        parent.layer.open({
            type: 2,
            title: '班级成绩明细',
            shadeClose: true,
            shade: 0.8,
            area: ['90%', '90%'],
            content:'/osce/admin/testscores/grade-detail?examid='+examid+'&resultID='+resultid+'&subid='+subid//iframe的url
        });
    });
}





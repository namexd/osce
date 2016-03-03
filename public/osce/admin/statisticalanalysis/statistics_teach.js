/**
 * Created by Administrator on 2016/2/29 bywulengmei
 * 统计分析
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "statistics_teach_score":statistics_teach_score();break;//教学成绩分析
        case "teach_detail":teach_detail();break;//教学成绩分析详情
    }
});

//教学成绩分析
function statistics_teach_score(){
    //默认加载select
    var $selectId = $(".exam_select").children().first().val();
    function select(selectId){
        $(".student_select").empty();
        var url ='/osce/admin/testscores/subject-lists';
        $.ajax({
            url:url+'?examid='+selectId,
            type:"get",
            cache:false,
            async:false,
            success:function(res){
                $(res.data.datalist).each(function(){
                    $(".student_select").append('<option value="'+this.id+'">'+this.title+'</option>');
                });
            }
        })
    }
    select($selectId);
    //筛选联动
    $(".exam_select").change(function(){
        var id = $(this).val();
        select(id);
    });
    //默认加载最近一次考试
    //筛选
    $("#search").click(function(){

    });
}
//教学成绩分析详情
function teach_detail(){

}




/**
 * Created by Administrator on 2016/2/29 bywulengmei
 * 统计分析
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "statistics_teach_score":statistics_teach_score();break;//教学成绩分析
        case "teach_detail":teach_detail();break;//教学成绩分析历史详情
    }
});

//教学成绩分析
function statistics_teach_score(){
    var t = echarts.init(document.getElementById("echarts-Subject")),
        n = {
            tooltip: {
                trigger: "axis"
            },
            legend: {
                data: ["平均成绩","最高分","最低分"]
            },
            calculable: !0,
            xAxis: [{
                type: "category",
                data: ["张老师","王老师","李老师","朱老师","月老师"]
            }],
            yAxis: [{
                type: "value"
            }],
            series: [
                {
                    name: "平均成绩",
                    type: "bar",
                    data: [30,50,48,32,48]
                },
                {
                    name: "最高分",
                    type: "line",
                    data: [80,90,95,92,95]
                },
                {
                    name: "最低分",
                    type: "line",
                    data: [10,15,14,3,4]
                }
            ]
        };
    t.setOption(n);
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
    var $examId = $(".exam_select").children().first().val();
    var $subjectId = $(".student_select").children().first().val();
    var url = "/osce/admin/testscores/teacher-data-list";
    function ajax(examId,subjectId){
        $.ajax({
            url:url+"?examid="+examId+"&subjectid="+subjectId,
            type:"get",
            cache:false,
            success:function(res){
                console.log(res);
            }
        })
    }
    ajax($examId,$subjectId);
    //筛选
    $("#search").click(function(){

    });
}
//教学成绩分析详情
function teach_detail(){

}




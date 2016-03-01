/**
 * Created by Administrator on 2016/2/29 bywulengmei
 * 统计分析
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "statistics_student_score":statistics_student_score();break;//考生成绩分析
        case "subject_level":subject_level();break;//科目难度分析

    }
});

//考生成绩分析
function statistics_student_score(){
    //默认加载select
    var $selectId = $(".exam_select").val();
    function select(selectId){
        $(".student_select").empty();
        var url ='/osce/admin/testscores/ajax-get-tester';
        $.ajax({
            url:url+'?examid='+selectId,
            type:"post",
            cache:false,
            async:false,
            success:function(res){
                $(res).each(function(){
                    $(".student_select").append('<option value="'+this.id+'">'+this.name+'</option>');
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
    var $studentId = $(".student_select").val();
    function echartsSubject(studentScoreStr,scoreAvgStr){//科目成绩分析。
        var h = echarts.init(document.getElementById("echarts-Subject")),
            d = {
                tooltip: {
                    trigger: "axis"
                },
                legend: {
                    orient: "vertical",
                    x: "right",
                    y: "bottom",
                    data: ["考生成绩", "平均分"]
                },
                polar: [{
                    indicator: [{
                        text: "冠心病问病史",
                        max: 100
                    },
                        {
                            text: "肠胃炎问病史",
                            max: 100
                        },
                        {
                            text: "发热咳嗽问病史",
                            max: 100
                        },
                        {
                            text: "体格检查",
                            max: 100
                        },
                        {
                            text: "无菌操作",
                            max: 100
                        },
                        {
                            text: "心血管疾病",
                            max: 100
                        }]
                }],
                calculable: !0,
                series: [{
                    name: "预算 vs 开销",
                    type: "radar",
                    data: [{
                        value: studentScoreStr,
                        name: "考生成绩"
                    },
                        {
                            value: scoreAvgStr,
                            name: "平均分"
                        }]
                }]
            };
        h.setOption(d);
    }
    //默认加载最近一次考试
    function ajax(examId,studentId){
        var url = '/osce/admin/testscores/ajax-get-subject';
        $.ajax({
            url:url+'?examid='+examId+'&student_id='+studentId,
            type:"get",
            cache:false,
            success:function(res){
                $(".subjectBody").empty();
                console.log(res);
                var scoreAvgStr = [];
                var studentScoreStr = [];
                var subjectStr = [];
                $(res.avgdata).each(function(){
                    scoreAvgStr.push(this.scoreAvg);
                });
                $(res.singledata).each(function(){
                    studentScoreStr.push(this.score);
                    subjectStr.push(this.title);
                });
                $(res.list).each(function(i){
                    $(".subjectBody").append('<tr>' +
                        '<td>'+this.i+'</td>' +
                        '<td>'+this.title+'</td>' +
                        '<td>'+this.mins+'</td>' +
                        '<td>'+this.timeAvg+'</td>' +
                        '<td>'+this.scoreAvg+'</td>' +
                        '<td>'+this.time+'</td>' +
                        '<td>'+this.score+'</td>' +
                        '<td>' +
                        '<a href="/osce/admin/testscores/student-subject-list?examid='+examId+'&student_id='+studentId+'">' +
                        '<span class="read state1 detail"><i class="fa fa-cog fa-2x"></i></span>' +
                        '</a>' +
                        '<a href="">' +
                        '<span class="read state1 detail"><i class="fa fa-search fa-2x"></i></span>' +
                        '</a>' +
                        '</td></tr>')
                });
                if(studentScoreStr){echartsSubject(studentScoreStr,scoreAvgStr);}
            }
        })
    }
    ajax($selectId,$studentId);
    //筛选
    $("#search").click(function(){
        ajax($selectId,$studentId);
    });
}




/**
 * Created by Administrator on 2016/2/29 bywulengmei
 * 统计分析
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "statistics_student_score":statistics_student_score();break;//考生成绩分析
        case "subject_level":subject_level();break;//历史详情

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
    function echartsSubject(subStr,studentScoreStr,scoreAvgStr){//科目成绩分析。
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
                    indicator: subStr
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
                var scoreAvgStr = [];
                var studentScoreStr = [];
                var subjectStr = [];
                var subStr = [];
                var stuname = $(".student_select").children().first().html();
                $(res.avgdata).each(function(){
                    scoreAvgStr.push(this.scoreAvg);
                });
                $(res.singledata).each(function(){
                    studentScoreStr.push(this.score);
                    subjectStr.push(this.title);

                });

                $(res.subject).each(function(){
                    subStr.push({
                        text: this.title,
                        max: this.score
                    });
                });
                //console.log(subStr);
                $(res.list).each(function(i){

                    $(".subjectBody").append('<tr>' +
                        '<td>'+( i+1 )+'</td>' +
                        '<td>'+this.title+'</td>' +
                        '<td>'+this.mins+'</td>' +
                        '<td>'+this.timeAvg+'</td>' +
                        '<td>'+this.scoreAvg+'</td>' +
                        '<td>'+this.time+'</td>' +
                        '<td>'+this.score+'</td>' +
                        '<td>' +
                        '<a href="/osce/admin/testscores/student-subject-list?examid='+examId+'&stuname='+stuname+'&subject='+this.title+'&student_id='+studentId+'&subid='+this.id+'">' +
                        '<span class="read state1 detail"><i class="fa fa-cog fa-2x"></i></span>' +
                        '</a>' +
                        '<a href="">' +
                        '<span class="read state1 detail"><i class="fa fa-search fa-2x"></i></span>' +
                        '</a>' +
                        '</td></tr>')
                });
                if(studentScoreStr){echartsSubject(subStr,studentScoreStr,scoreAvgStr);}
            }
        })
    }
    ajax($selectId,$studentId);
    //筛选
    $("#search").click(function(){
        var $studentId = $(".student_select option:selected").val();
        ajax($selectId,$studentId);
    });
}
//历史详情
function subject_level (){
    var avg = pars.avg.split(',');
    var totle = pars.totle.split(',');
    var time = pars.time.split(',');
    function echartsSubject(time,avg,totle){
        var e = echarts.init(document.getElementById("echarts-Subject")),
            a = {
                tooltip: {
                    trigger: "axis"
                },
                legend: {
                    data: ["得分","平均分"]
                },
                calculable: !0,
                xAxis: [{
                    type: "category",
                    boundaryGap: !1,
                    data:time
                }],
                yAxis: [{
                    type: "value",
                    axisLabel: {
                        formatter: "{value}"
                    }
                }],
                series: [{
                    name: "得分",
                    type: "line",
                    data: totle,
                    markPoint: {
                        data: [{
                            type: "max",
                            name: "最大值"
                        },
                            {
                                type: "min",
                                name: "最小值"
                            }]
                    }
                },{
                    name: "平均分",
                    type: "line",
                    data: avg,
                    markPoint: {
                        data: [{
                            type: "max",
                            name: "最大值"
                        },
                            {
                                type: "min",
                                name: "最小值"
                            }]
                    }
                }]
            };
        e.setOption(a);
    }
    if(totle){echartsSubject(time,avg,totle);}
    //返回
    $("#back").click(function(){
        history.go(-1);
    })
}



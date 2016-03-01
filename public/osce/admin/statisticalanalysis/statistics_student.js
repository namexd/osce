/**
 * Created by Administrator on 2016/2/29 bywulengmei
 * 统计分析
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "statistics_student_subject":statistics_student_subject();break;//科目成绩分析
        case "subject_level":subject_level();break;//科目难度分析

    }
});

//科目成绩分析
function subject_statistics(){
    function echartsSubject(standardStr,scoreAvgStr){//科目成绩分析。
        var t = echarts.init(document.getElementById("echarts-Subject")),
            n = {
                tooltip: {
                    trigger: "axis"
                },
                legend: {
                    data: ["平均成绩"]
                },
                calculable: !0,
                xAxis: [{
                    type: "category",
                    data: standardStr
                }],
                yAxis: [{
                    type: "value"
                }],
                series: [
                    {
                        name: "平均成绩",
                        type: "bar",
                        data: scoreAvgStr
                    }]
            };
        t.setOption(n);
    }
    //默认加载最近一次考试
    var $subId = $(".subject_select").children().first().val();
    var url = pars.ajaxUrl;
    function ajax(id){
        $.ajax({
            url:url+'?id='+id,
            type:'get',
            cache:false,
            success:function(res){
                $(".subjectBody").empty();
                var standardStr = res.data.StrList.standardStr.split(",");
                var scoreAvgStr=res.data.StrList.scoreAvgStr.split(",");
                if(standardStr){echartsSubject(standardStr,scoreAvgStr);}//科目成绩分析。
                $(res.data.list).each(function(){
                    $(".subjectBody").append('<tr>' +
                        '<td>'+this.number+'</td>' +
                        '<td>'+this.title+'</td>' +
                        '<td>'+this.mins+'</td>' +
                        '<td>'+this.timeAvg+'</td>' +
                        '<td>'+this.scoreAvg+'</td>' +
                        '<td>'+this.studentQuantity+'</td>' +
                        '<td>'+this.qualifiedPass+'</td>' +
                        '<td>' +
                        '<a href="">' +
                        '<span class="read state1 detail"><i class="fa fa-search fa-2x"></i></span>' +
                        '</a>' +
                        '</td></tr>')
                })
            }
        });
    }
    ajax($subId);
    //筛选
    $("#search").click(function(){
        var id = $(".subject_select").val();
        ajax(id);
    });
}
//科目难度分析
function statistics_student_subject(){
    function echartsSubject(timeStr,passStr){//科目成绩分析。

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
                    data:["2016-02", "2016-03", "2016-04", "2016-05"]
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
                    data: [70,60,70,50],
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
                    data: [30,50,20,70],
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
    //默认加载最近一次考试
    var $subId = $(".subject_select").children().first().val();
    var url = pars.ajaxUrl;
    function ajax(id){
        $.ajax({
            url:url+'?id='+id,
            type:'get',
            cache:false,
            success:function(res){
                console.log(res);
                $(".subjectBody").empty();
                var timeStr = res.data.StrList.standardStr.split(",");
                var passStr=res.data.StrList.qualifiedPass.split(",");
                console.log(passStr);
                if(timeStr){echartsSubject(timeStr,passStr);}
                $(res.data.list).each(function(){
                    $(".subjectBody").append('<tr>' +
                        '<td>'+this.number+'</td>' +
                        '<td>'+this.ExamName+'</td>' +
                        '<td>'+this.ExamBeginTime+'</td>' +
                        '<td>'+this.timeAvg+'</td>' +
                        '<td>'+this.scoreAvg+'</td>' +
                        '<td>'+this.studentQuantity+'</td>' +
                        '<td>'+this.qualifiedPass+'</td>' +
                        '<td>' +
                        '<a href="">' +
                        '<span class="read state1 detail"><i class="fa fa-search fa-2x"></i></span>' +
                        '</a>' +
                        '</td></tr>')
                })
            }
        });
    }
    echartsSubject();
   // ajax($subId);
    //筛选
    $("#search").click(function(){
        var id = $(".subject_select").val();
        ajax(id);
    });
};



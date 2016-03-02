/**
 * Created by Administrator on 2016/2/29 bywulengmei
 * 统计分析
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "subject_statistics":subject_statistics();break;//科目成绩分析
        case "subject_level":subject_level();break;//科目难度分析
        case "examation_statistics":examation_statistics();break;//考站成绩分析
        case "statistics_check":statistics_check();break;//考核点分析
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
    var examObj = $(".subject_select");
    var $subId = examObj.children().first().val();
    var ajaxUrl = pars.ajaxUrl;
    var exam_id = examObj.val();
    var jumpUrl = pars.jumpUrl;
    function ajax(id,exam_name){
        $.ajax({
            url:ajaxUrl+'?id='+id,
            type:'get',
            cache:false,
            success:function(res){
                $(".subjectBody").empty();
                var standardStr = res.data.StrList.standardStr.split(",");
                var scoreAvgStr=res.data.StrList.scoreAvgStr.split(",");
                if(standardStr){echartsSubject(standardStr,scoreAvgStr);}//科目成绩分析。
                $(res.data.list).each(function(){
                    //拼接URL 链接
                    var url = '';
                    url = jumpUrl+'?exam_id='+id+'&subject_id='+this.subjectId+'&exam='+exam_name+'&subject='+this.title+'&avg_score='+this.scoreAvg+'&avg_time='+this.timeAvg;
                    $(".subjectBody").append('<tr>' +
                        '<td>'+this.number+'</td>' +
                        '<td>'+this.title+'</td>' +
                        '<td>'+this.mins+'</td>' +
                        '<td>'+this.timeAvg+'</td>' +
                        '<td>'+this.scoreAvg+'</td>' +
                        '<td>'+this.studentQuantity+'</td>' +
                        '<td>'+this.qualifiedPass+'</td>' +
                        '<td>' +
                        '<a href="'+url+'">' +
                        '<span class="read state1 detail"><i class="fa fa-search fa-2x"></i></span>' +
                        '</a>' +
                        '</td></tr>')
                })
            }
        });
    }
    var exam_name = $(".subject_select option:selected").html();
    ajax($subId,exam_name);
    //筛选
    $("#search").click(function(){
        var exam_id = $(".subject_select option:selected").val();
        exam_name = $(".subject_select option:selected").html();
        ajax(exam_id,exam_name);
    });
}
//科目难度分析
function subject_level(){
    function echartsSubject(timeStr,passStr){//科目成绩分析。

        var e = echarts.init(document.getElementById("echarts-Subject")),
            a = {
                tooltip: {
                    trigger: "axis"
                },
                legend: {
                    data: ["合格率"]
                },
                calculable: !0,
                xAxis: [{
                    type: "category",
                    boundaryGap: !1,
                    data: timeStr
                }],
                yAxis: [{
                    type: "value",
                    axisLabel: {
                        formatter: "{value} %"
                    }
                }],
                series: [{
                    name: "合格率",
                    type: "line",
                    data: passStr,
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
    var examObj = $(".subject_select");
    var $subId = examObj.children().first().val();
    var ajaxUrl = pars.ajaxUrl;
    var subject_id = examObj.val();
    var subject_name = $(".subject_select option:selected").html();
    var jumpUrl = pars.jumpUrl;
    function ajax(id,subject_name){
        $.ajax({
            url:ajaxUrl+'?id='+id,
            type:'get',
            cache:false,
            success:function(res){
                console.log(res);
                $(".subjectBody").empty();
                var timeStr = res.data.StrList.standardStr.split(",");
                var passStr=res.data.StrList.qualifiedPass.split(",");
                if(timeStr){echartsSubject(timeStr,passStr);}
                $(res.data.list).each(function(){
                    //拼接URL 链接
                    var url = '';
                    url = jumpUrl+'?subject_id='+id+'&exam_id='+this.ExamId+'&exam='+this.ExamName+'&subject='+subject_name+'&avg_score='+this.scoreAvg+'&avg_time='+this.timeAvg;
                    $(".subjectBody").append('<tr>' +
                        '<td>'+this.number+'</td>' +
                        '<td>'+this.ExamName+'</td>' +
                        '<td>'+this.ExamBeginTime+'</td>' +
                        '<td>'+this.timeAvg+'</td>' +
                        '<td>'+this.scoreAvg+'</td>' +
                        '<td>'+this.studentQuantity+'</td>' +
                        '<td>'+this.qualifiedPass+'</td>' +
                        '<td>' +
                        '<a href="'+url+'">' +
                        '<span class="read state1 detail"><i class="fa fa-search fa-2x"></i></span>' +
                        '</a>' +
                        '</td></tr>')
                })
            }
        });
    }
    ajax($subId,subject_name);
    //筛选
    $("#search").click(function(){
        var id = $(".subject_select").val();
        subject_name = $(".subject_select option:selected").html();
        ajax(id,subject_name);
    });
};

//考站成绩分析
function examation_statistics(){
    //图表插件
    function echartsSubject(stationNameStr,scoreAvgStr){
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
                    data: stationNameStr
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
    var $examId = $(".exam_select").children().first().val();
    var $subjectId = $(".subject_select").children().first().val();
    var url = pars.ajaxUrl;
    function ajax(examId,subjectId){
        $.ajax({
            url:url+"?examId="+examId+"&subjectId="+subjectId,
            type:"get",
            cache:false,
            success:function(res){
                $(".subjectBody").empty();
                var stationNameStr = res.data.StrList.stationNameStr.split(",");
                var scoreAvgStr = res.data.StrList.scoreAvgStr.split(",");
                if(stationNameStr){echartsSubject(stationNameStr,scoreAvgStr);}
                $(res.data.stationList).each(function(){
                    $(".subjectBody").append('<tr>' +
                        '<td>'+this.number+'</td>' +
                        '<td>'+this.stationName+'</td>' +
                        '<td>'+this.teacherName+'</td>' +
                        '<td>'+this.examMins+'</td>' +
                        '<td>'+this.timeAvg+'</td>' +
                        '<td>'+this.scoreAvg+'</td>' +
                        '<td>'+this.studentQuantity+'</td>' +
                        '<td>' +
                        '<a href="">' +
                        '<span class="read state1 detail"><i class="fa fa-search fa-2x"></i></span>' +
                        '</a>' +
                        '</td></tr>')
                })
            }
        })
    }
    ajax($examId,$subjectId);
    //筛选
    $("#search").click(function(){
        var subjectId = $(".subject_select").val();
        var examId = $(".exam_select").val();
        ajax(examId,subjectId);
    });
}
//考核点分析
function statistics_check(){
    //图表插件
    function echartsSubject(standardContentStr,qualifiedPassStr){
        var t = echarts.init(document.getElementById("echarts-Subject")),
            n = {
                tooltip: {
                    trigger: "axis"
                },
                legend: {
                    data: ["合格率"]
                },
                calculable: !0,
                xAxis: [{
                    type: "category",
                    data: standardContentStr
                }],
                yAxis: [{
                    type: "value",
                    axisLabel: {
                        formatter: "{value} %"
                    }
                }],
                series: [
                    {
                        name: "合格率",
                        type: "bar",
                        data: qualifiedPassStr
                    }]
            };
        t.setOption(n);
    }
    //默认加载最近一次考试
    var $examId = $(".exam_select").children().first().val();
    var $subjectId = $(".subject_select").children().first().val();
    var url = pars.ajaxUrl;
    console.log(url);
    function ajax(examId,subjectId){
        $.ajax({
            url:url+"?examId="+examId+"&subjectId="+subjectId,
            type:"get",
            cache:false,
            success:function(res){
                $(".subjectBody").empty();
                var standardContentStr = res.data.StrList.standardContent.split(",");
                var qualifiedPassStr = res.data.StrList.qualifiedPass.split(",");
                if(standardContentStr){echartsSubject(standardContentStr,qualifiedPassStr);}
                $(res.data.standardList).each(function(){
                    $(".subjectBody").append('<tr>' +
                        '<td>'+this.number+'</td>' +
                        '<td>'+this.standardContent+'</td>' +
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
        })
    }
    ajax($examId,$subjectId);
    //筛选
    $("#search").click(function(){
        var subjectId = $(".subject_select").val();
        var examId = $(".exam_select").val();
        ajax(examId,subjectId);
    });
}

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
    }
});


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
    $.ajax({
        url:url+'?id='+$subId,
        type:'get',
        cache:false,
        success:function(res){
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
    //筛选
    $("#search").click(function(){
        var id = $(".subject_select").val();
        $.ajax({
            url:url+'?id='+id,
            type:'get',
            cache:false,
            success:function(res){
                console.log(res);
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
        })
    });
};


function subject_level(){
    function echartsSubject(standardStr,scoreAvgStr){//科目成绩分析。
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
                    data: ["周一", "周二", "周三", "周四", "周五", "周六", "周日"]
                }],
                yAxis: [{
                    type: "value",
                    axisLabel: {
                        formatter: "value"
                    }
                }],
                series: [{
                    name: "最高气温",
                    type: "line",
                    data: [11, 11, 15, 13, 12, 13, 10],
                    markPoint: {
                        data: [{
                            type: "max",
                            name: "最大值"
                        },
                            {
                                type: "min",
                                name: "最小值"
                            }]
                    },
                    markLine: {
                        data: [{
                            type: "average",
                            name: "平均值"
                        }]
                    }
                }]
            };
        e.setOption(a);
    }
    //默认加载最近一次考试
    var $subId = $(".subject_select").children().first().val();
    var url = pars.ajaxUrl;
    $.ajax({
        url:url+'?id='+$subId,
        type:'get',
        cache:false,
        success:function(res){
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
    //筛选
    $("#search").click(function(){
        var id = $(".subject_select").val();
        $.ajax({
            url:url+'?id='+id,
            type:'get',
            cache:false,
            success:function(res){
                console.log(res);
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
        })
    });
};
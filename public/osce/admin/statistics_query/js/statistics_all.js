/**
 * Created by Administrator on 2016/2/29 bywulengmei
 * 统计分析
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "subject_statistics":subject_statistics();break;//科目成绩分析
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

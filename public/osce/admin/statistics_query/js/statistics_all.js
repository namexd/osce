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
    var standardStr=pars.standardStr.split(",");
    var scoreAvgStr=pars.scoreAvgStr.split(",");
    if(standardStr){echartsSubject();}//科目成绩分析。
    function echartsSubject(){//科目成绩分析。
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
    var $subId = $(".subject_select").children().first().val();
    var url = pars.ajaxUrl;
    alert(url);
    $.ajax({
        url:url+'?id='+$subId,
        type:'post',
        cache:false,
        success:function(res){
            console.log(res);
        }
    })
};

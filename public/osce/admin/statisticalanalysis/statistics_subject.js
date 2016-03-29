/**
 * Created by Administrator on 2016/2/29 bywulengmei
 * 统计分析
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "subject_statistics":subject_statistics();break;//单次考试分析
        case "subject_level":subject_level();break;//科目成绩趋势
        case "examation_statistics":examation_statistics();break;//考站成绩分析
        case "statistics_check":statistics_check();break;//考核点分析
    }
});

//单次考试分析
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
                calculable: false,
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
    var getStorage = localStorage.getItem("session");
    if(getStorage){
        getStorage =JSON.parse(getStorage);
        exam_id=getStorage.exam_id;
        exam_name=getStorage.exam_name;
        examObj.children().each(function(){
            if(exam_id==$(this).val()){
                $(this).attr("selected",true);
            }
        });
        $subId=exam_id;//如果存在缓存，则保留之前的搜索条件
    }
    function ajax(exam_id,exam_name){
        $.ajax({
            url:ajaxUrl+'?id='+exam_id,
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
                    url = jumpUrl+'?exam_id='+exam_id+'&subject_id='+this.subjectId+'&exam='+exam_name+'&subject='+this.title+'&avg_score='+this.scoreAvg+'&avg_time='+this.timeAvg;
                    $(".subjectBody").append('<tr>' +
                        '<td>'+this.number+'</td>' +
                        '<td>'+this.title+'</td>' +
                        '<td>'+this.mins+'分钟'+'</td>' +
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
        var pageName = "subject_statistics";
        setStorage(pageName,exam_id,exam_name);
        ajax(exam_id,exam_name);
    });
}
//科目成绩趋势
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
                calculable: false,
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
                            type: "min",
                            name: "最小值"
                        },
                            {
                                type: "max",
                                name: "最大值"
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
    var getStorage = localStorage.getItem("session1");
    if(getStorage){
        getStorage =JSON.parse(getStorage);
        id = getStorage.id;
        subject_name = getStorage.subject_name;
        examObj.children().each(function(){
            if(id==$(this).val()){
                $(this).attr("selected",true);
            }
        });
        $subId = id;
    }
    function ajax(id,subject_name){
        $.ajax({
            url:ajaxUrl+'?id='+id,
            type:'get',
            cache:false,
            success:function(res){
                $(".subjectBody").empty();
                var timeStr = res.data.StrList.standardStr.split(",");
                var passStr=res.data.StrList.qualifiedPass.split(",");
                var passStr1 = [];
                for(var i=0;i<passStr.length;i++){
                    passStr1.push(Number(passStr[i]));
                }
                if(timeStr){echartsSubject(timeStr,passStr1);}
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
        var pageName = "subject_level";
        setStorage(pageName,id,subject_name);
        ajax(id,subject_name);
    });
}
//考站成绩分析
function examation_statistics(){
    var target=pars.target;
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
                calculable: false,
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
    var getStorage = localStorage.getItem("session2");
    if(getStorage){
        getStorage =JSON.parse(getStorage);
        examId = getStorage.examId;
        subjectId = getStorage.subjectId;
        $(".exam_select").children().each(function(){
            if($(this).val() == examId){
                $(this).attr("selected",true);
            }
        });
        $(".subject_select").children().each(function(){
            if($(this).val() == subjectId){
                $(this).attr("selected",true);
            }
        });
        $examId = examId;
        $subjectId = subjectId;
    }
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
                        '<span class="read state1 detail"><i class="fa fa-search fa-2x cursor" id="'+this.stationId+'"></i></span>' +
                        '</td></tr>')
                });
                $(".fa-search").click(function(){
                    var stationId= $(this).attr("id");
                    console.log(target+'?subjectId='+subjectId+'&examId='+examId+'&stationId='+stationId);
                    parent.layer.open({
                        type: 2,
                        title: '考站成绩明细',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['90%', '90%'],
                        content:target+'?subjectId='+subjectId+'&examId='+examId+'&stationId='+stationId //iframe的url
                    });
                })


            }
        })
    }
    ajax($examId,$subjectId);
    //筛选
    $("#search").click(function(){
        var subjectId = $(".subject_select option:selected").val();
        var examId = $(".exam_select option:selected").val();
        var pageName = "examation_statistics";
        setStorage(pageName,examId,subjectId);
        ajax(examId,subjectId);
    });
}
//考核点分析
function statistics_check(){
    var target=pars.target;//详情页地址
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
                calculable: false,
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
    var getStorage = localStorage.getItem("session3");
    if(getStorage){
        getStorage =JSON.parse(getStorage);
        examId = getStorage.examId;
        subjectId = getStorage.subjectId;
        $(".exam_select").children().each(function(){
            if($(this).val() == examId){
                $(this).attr("selected",true);
            }
        });
        $(".subject_select").children().each(function(){
            if($(this).val() == subjectId){
                $(this).attr("selected",true);
            }
        });
        $examId = examId;
        $subjectId = subjectId;
    }
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
                        '<span class="read state1 detail"><i class="fa fa-search fa-2x cursor" pid="'+this.pid+'"></i></span>' +
                        '</td></tr>')
                });
                $(".fa-search").click(function(){
                    var standardPid= $(this).attr("pid");
                    console.log(target+'?subjectId='+subjectId+'&examId='+examId+'&pid='+standardPid);
                    parent.layer.open({
                        type: 2,
                        title: '考核点详情',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['90%', '90%'],
                        content:target+'?subjectId='+subjectId+'&examId='+examId+'&standardPid='+standardPid //iframe的url
                    });
                })
            }
        })
    }
    ajax($examId,$subjectId);
    //筛选
    $("#search").click(function(){
        var subjectId = $(".subject_select option:selected").val();
        var examId = $(".exam_select option:selected").val();
        var pageName = "statistics_check";
        setStorage(pageName,examId,subjectId);
        ajax(examId,subjectId);
    });
}
//本地存储
function setStorage(pageName,setId,setName){
    //单次考试分析存储
    if(pageName=="subject_statistics"){
        var session = {};
        session.pageName = pageName;
        session.exam_id = setId;
        session.exam_name = setName;
        localStorage.setItem("session",JSON.stringify(session));//设置本地存储
    }
    //科目成绩趋势存储
    if(pageName=="subject_level"){
        var session1 = {};
        session1.pageName = pageName;
        session1.id = setId;
        session1.subject_name = setName;
        localStorage.setItem("session1",JSON.stringify(session1));//设置本地存储
    }
    //考站成绩分析存储
    if(pageName == "examation_statistics"){
        var session2 = {};
        session2.pageName = pageName;
        session2.examId = setId;
        session2.subjectId = setName;
        localStorage.setItem("session2",JSON.stringify(session2));//设置本地存储
    }
    //考核点分析存储
    if(pageName == "statistics_check"){
        var session3 = {};
        session3.pageName = pageName;
        session3.examId = setId;
        session3.subjectId = setName;
        localStorage.setItem("session3",JSON.stringify(session3));//设置本地存储
    }
}


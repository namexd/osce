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
    function echartsSubject(teacherStr,avgStr,maxScore,minScore){
        var t = echarts.init(document.getElementById("echarts-Subject")),
            n = {
                tooltip: {
                    trigger: "axis"
                },
                legend: {
                    orient: 'horizontal', // 'vertical'
                    x: 'right', // 'center' | 'left' | {number},
                    y: 0, // 'center' | 'bottom' | {number}
                    padding:40,
                    data: ["平均成绩","最高分","最低分"]
                },
                calculable: false,
                xAxis: [{
                    type: "category",
                    data: teacherStr
                }],
                yAxis: [{
                    type: "value"
                }],
                series: [
                    {
                        name: "平均成绩",
                        type: "bar",
                        data: avgStr
                    },
                    {
                        name: "最高分",
                        type: "line",
                        data: maxScore
                    },
                    {
                        name: "最低分",
                        type: "line",
                        data: minScore
                    }
                ]
            };
        t.setOption(n);
    }
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
    var getStorage = localStorage.getItem("teachScore");
    if(getStorage){
        getStorage =JSON.parse(getStorage);
        examId = getStorage.examId;
        subjectId = getStorage.subjectId;
        $(".exam_select").children().each(function(){
            if($(this).val() == examId){
                $(this).attr("selected",true);
            }
        });
        $(".student_select").children().each(function(){
            if($(this).val() == subjectId){
                $(this).attr("selected",true);
            }
        });
        $examId = examId;
        $subjectId = subjectId;
    }
    function ajax(examId,subjectId){
        $(".subjectBody").empty();
        $.ajax({
            url:url+"?examid="+examId+"&subjectid="+subjectId,
            type:"get",
            cache:false,
            success:function(res){
                var teacherStr = res.data.data.teacherStr.split(",");
                var avgStr = res.data.data.avgStr.split(",");
                var maxScore = res.data.data.maxScore.split(",");
                var minScore = res.data.data.minScore.split(",");
                var subname = $('.student_select option:selected').html();
                if(avgStr){echartsSubject(teacherStr,avgStr,maxScore,minScore);}
                $(res.data.data.datalist).each(function(i){
                    var jumpUrl = '/osce/admin/testscores/grade-score-list?examid='+this.exam_id+'&classid='+this.grade_class+'&subname='+subname+'&subid='+this.subid+'&classid='+this.grade_class;
                    $(".subjectBody").append('<tr>' +
                        '<td>'+(i+1)+'</td>' +
                        '<td>'+this.teacher_name+'</td>' +
                        '<td>'+this.grade_class+'</td>' +
                        '<td>'+this.stuNum+'</td>' +
                        '<td>'+this.avgScore+'</td>' +
                        '<td>'+this.maxScore+'</td>' +
                        '<td>'+this.minScore+'</td>' +
                        '<td>' +
                        '<a href='+jumpUrl+'>' +
                        '<span class="read state1 detail"><i class="fa fa-cog fa-2x"></i></span>' +
                        '</a>' +
                        '<span class="read state1 detail cursor"><i class="fa fa-search fa-2x" examid="'+this.exam_id+'" resultid="'+this.rid+'" subid="'+this.subid+'" classid="'+this.grade_class+'" ></i></span>' +
                        '</td>' +
                        '</tr>')
                })
            }
        })
    }
    ajax($examId,$subjectId);
    //筛选
    $("#search").click(function(){
        var examId = $(".exam_select option:selected").val();
        var subjectId = $(".student_select option:selected").val();
        var pageName = "statistics_teach_score";
        setStorage(pageName,examId,subjectId);
        ajax(examId,subjectId);
    });
    //跳详情页面
    $(".subjectBody").delegate(".fa-search","click",function(){
        var examid = $(this).attr("examid");
        var resultid = $(this).attr("resultid");
        var subid = $(this).attr("subid");
        var classid = $(this).attr('classid');
        parent.layer.open({
            type: 2,
            title: '班级成绩明细',
            shadeClose: true,
            shade: 0.8,
            area: ['90%', '90%'],
            content:'/osce/admin/testscores/grade-detail?examid='+examid+'&resultID='+resultid+'&subid='+subid+'&classid='+classid//iframe的url
        });
    });
}
//教学成绩分析详情
function teach_detail(){
    function echartsSubject(examName,scoreAll,scoreAvg){
        var e = echarts.init(document.getElementById("echarts-Subject")),
            a = {
                tooltip: {
                    trigger: "axis"
                },
                legend: {
                    orient: 'horizontal', // 'vertical'
                    x: 'right', // 'center' | 'left' | {number},
                    y: 0, // 'center' | 'bottom' | {number}
                    padding:40,
                    data: ["得分","平均分"]
                },
                calculable: false,
                xAxis: [{
                    type: "category",
                    boundaryGap: !1,
                    data:examName
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
                    data: scoreAll,
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
                    data: scoreAvg,
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
    var examName = pars.timeData.split(",");
    var scoreAll = pars.allData.split(",");
    var scoreAvg = pars.classData.split(",");
    echartsSubject(examName,scoreAll,scoreAvg);
    //返回
    $("#back").click(function(){
        history.go(-1);
    });
    //跳详情页面
    $(".fa-search").click(function(){
        var examid = $(this).attr("examid");
        var classid = $(this).attr("resultid");
        var subid = $(this).attr("subid");

        parent.layer.open({
            type: 2,
            title: '班级成绩明细',
            shadeClose: true,
            shade: 0.8,
            area: ['90%', '90%'],
            content:'/osce/admin/testscores/grade-detail?examid='+examid+'&classid='+classid+'&subid='+subid//iframe的url
        });

    })
}
//数据本独存储
function setStorage(pageName,examId,subjectId){
    if(pageName == "statistics_teach_score"){
        var teachScore = {};
        teachScore.pageName = pageName;
        teachScore.subjectId = subjectId;
        teachScore.examId = examId;
        localStorage.setItem("teachScore",JSON.stringify(teachScore));//设置本地存储
    }
}




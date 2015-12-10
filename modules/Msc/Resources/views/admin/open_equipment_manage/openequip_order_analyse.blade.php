@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/returnmanage/css/history.css')}}">
    <link href="{{asset('msc/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
    <style>
    .time-intro{width: 40px!important;}
    .time-intro label{
        line-height: 37px;
        margin-left: 10px;
    }
    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #e5e6e7;
        border-radius: 0;
    }
    .select2-container .select2-selection--single {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        height: 34px;
        user-select: none;
        -webkit-user-select: none;
    }
    .select2-dropdown {
        background-color: white;
        border: 1px solid #e5e6e7;
        border-radius: 4px;
        box-sizing: border-box;
        display: block;
        position: absolute;
        left: -100000px;
        width: 100%;
        z-index: 1051;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: inherit;
        line-height: 34px;
        font-size: 14px;
    }
    </style>
@stop


@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>统计分析</h5>
            </div>
            <div class="ibox-content">
                <div class="row" id="Screening">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">图标类型</label>
                            <div class="select_detail">
                                <select class="form-control" id="chart-type">
                                    <option value="bar">柱状图</option>
                                    <option value="pie">饼图</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                <input id="start" value="2015-01-01 00:00:00" class="laydate-icon form-control layer-date">
                            </div>
                        </div>
                        <div class="form-group time-intro"><label>&nbsp;</label></div>
                        <div class="form-group">
                            <label class="control-label">年级</label>
                            <div class="select_detail">
                                <select class="form-control" id="grade">
                                    <option>2014级</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">专业</label>
                            <div class="select_detail">
                                <select class="form-control" name="teacher_dept" id="professional" placeholder="选择科室">
                                    <option value="">不限</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">复位状态</label>
                            <div class="select_detail">
                                <select class="form-control" id="status">
                                    <option value="0">良好</option>
                                    <option value="1">损坏</option>
                                    <option value="2">严重损坏</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="width: 300px;">
                            <button type="button" class="btn btn-primary marl_10" id="charts">查&nbsp;询</button>
                            <a href="javascript:void(0)" class="btn btn-w-m btn-white marl_10" id="download">导出为Excel文件</a>

                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="col-sm-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">
                                <div id="main" style="height:400px"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    
@stop{{-- 内容主体区域 --}}

@section('only_js')
        <!-- Morris -->
    <script src="{{asset('msc/admin/plugins/js/plugins/morris/raphael-2.1.0.min.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('msc/common/select2-4.0.0/js/select2.full.js')}}"></script>
    <script>
        $(function () {

            /*时间选择 触发*/
            var start = {
                elem: "#start",
                format: "YYYY-MM-DD",
                min: "1970-00-00",
                max: "2099-06-16",
                istime: false,
                istoday: false,
                start : "2015-01-01",
                choose: function (a) {
                    end.min = a;
                    end.start = a
                }
            };
            laydate(start);

            /*当天的日期*/
            var time_NOW = '';
            var myDate = new Date();
            time_NOW = myDate.getFullYear() +'-'+ (parseInt(myDate.getMonth())+1) +'-'+ myDate.getDate();
            $('#start').val(time_NOW);

            /**
             *年级信息
             */
            $.ajax({
                type:"get",
                async:true,
                url:"{{action('\App\Http\Controllers\V1\Sys\UserController@getGreadeList')}}",
                success:function(res){
                    if(res.code==1){

                        var html = '<option value="">不限</option>';
                        var thisInsert = $('#grade');
                        for(var i in res.data.rows){
                            html += '<option value="'+res.data.rows[i].id+'">'+ res.data.rows[i].name +'</option>';
                        }

                        thisInsert.empty();
                        thisInsert.html(html);
                    }else{
                        console.log(res.message);
                    }
                }
            });

            /**
             *获取专业
             */
            $("#professional").select2({
                ajax: {
                    url: "{{action('\App\Http\Controllers\V1\Sys\UserController@getProfessionalList')}}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term, // search term
                            page: 1
                        };
                    },
                    processResults: function (data, page) {
                        return {
                            results: data.data.rows
                        }
                    },
                    cache: true
                }
            });

            
            /**
             *统计图
             */
            function chart(res){
                var myChart = echarts.init(document.getElementById('main')); 
                var option = {
                    tooltip: {
                        show: true
                    },
                    xAxis : [
                        {   
                            type : 'category',
                            data : res.xAxis//["1","2","3","4","5","6"]
                        }
                    ],
                    yAxis : [
                        {   name : '数量/次',
                            type : 'value'
                        }
                    ],
                    series : [
                        {
                            
                            type:"bar",
                            smooth:true,
                            itemStyle: {
                                normal: {
                                    color:"#74A9FF",
                                    lineStyle: {
                                        width:3

                                    }
                                }
                            },
                            data:res.yAxis//[5, 20, 40, 10, 10, 20]
                        }
                    ]
                }; 
                // 为echarts对象加载数据 
                myChart.setOption(option);
            }
            //测试
            //chart({xAxis:["1","2","3","4","5","6"],yAxis:[5, 20, 40, 10, 10, 20]});       


            /**
             *饼图
             */
            function chartPie(res){
                var myChart = echarts.init(document.getElementById('main'));
                var option = {
                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        orient : 'vertical',
                        x : 'left',
                        data:res.legendS//['实验室A','实验室B','实验室C','实验室D','实验室E']
                    },
                    toolbox: {
                        show : false
                    },
                    series : [
                        {
                            type:'pie',
                            radius : '55%',
                            center: ['50%', '60%'],
                            data:res.dataS/*[
                                {value:335, name:'实验室A'},
                                {value:310, name:'实验室B'},
                                {value:234, name:'实验室C'},
                                {value:135, name:'实验室D'},
                                {value:1548, name:'实验室E'}
                            ]*/
                        }
                    ]
                };
                // 为echarts对象加载数据 
                myChart.setOption(option);

            }
            //chartPie({legendS:['实验室A','实验室B','实验室C','实验室D','实验室E'],dataS:[{value:335, name:'实验室A'},{value:310, name:'实验室B'},{value:234, name:'实验室C'},{value:135, name:'实验室D'},{value:1548, name:'实验室E'}]})

            /**
             *统计 查询
             */
            $('#charts').click(function(){
                var req = {};
                req['professional'] = $('#professional').val();
                req['grade'] = $('#grade').val();
                req['status'] = $('#status').val();
                req['date'] = $('#start').val();
                //检测图标类型
                chartType = $('#chart-type').val();

                /*ajax请求*/
                $.ajax({
                    type:"get",
                    async:true,
                    url:"{{route('msc.lab-tools.getHistoryStatisticsData')}}",
                    data:JSON.stringify(req),
                    success:function(res){
                        /*统计折线图*/
                        var data = {};
                        if(res.code==1){
                            if(chartType == 'bar'){

                                //柱状图
                                var xAxis = [];
                                var yAxis = [];
                                for(var i in res.data.rows){

                                    xAxis.push(res.data.rows[i].name);
                                    yAxis.push(res.data.rows[i].borrowCount);
                                }
                                data['xAxis'] = xAxis;
                                data['yAxis'] = yAxis;
                                chart(data);

                            }else{

                                //饼图
                                var dataS = [];
                                var legendS = [];
                                for(var i in res.data.rows){

                                    dataS.push({value:res.data.rows[i].borrowCount, name:res.data.rows[i].name});
                                    legendS.push(res.data.rows[i].name);
                                }
                                data['dataS'] = dataS;
                                data['legendS'] = legendS;
                                chartPie(data);
                            }
                            
                        }else{
                            console.log(res.message)
                        }
                    }
                });
             });

            $('#download').click(function(){
                location.href = "{{route('msc.lab-tools.getHistoryStatisticsExcl')}}?professional="+$('#professional').val()+"&grade="+$('#grade').val()+"&status="+$('#status').val()+"&date="+$('#start').val();
            })


        });
    </script>
@stop

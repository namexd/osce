@extends('osce::admin.layouts.admin_index')
@section('only_css')
<link rel="stylesheet" type="text/css" href="{{asset('osce/common/css/swiper.min.css')}}">
<style>
    .form-group {
        margin: 10px 0 30px;
        padding: 10px;
    }
    .col-sm-10 i{
        margin: 0 10px;
        font-size:16px;
        color: #ccc;
    }
    .perfect{
        color: #f8ac59!important;
    }
    .video{
        display: inline-block;
        height: 15px;
        width: 18px;
        background-size:18px 15px;
        background-image: url('{{asset("osce/images/iconfont-shipinliebiao.svg")}}');}
</style>
@stop

@section('only_js')
<script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
<script src="{{asset('osce/common/js/swiper.min.js')}}"></script>
    <script>
        $(function(){

           var option = {
                    title : {
                        text: '图表分析'
                    },
                    tooltip : {
                        trigger: 'axis'
                    },
                    legend: {
                        data:['平均分','张三'],
                        x:'right'
                    },
                    toolbox: {
                        show : false
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type : 'category',
                            boundaryGap : false,
                            data : ['考核点1','考核点2','考核点3','考核点4','考核点5','考核点6','考核点7']
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value',
                            name : '分数'
                        }
                    ],
                    series : [
                        {
                            name:'平均分',
                            type:'line',
                            smooth:true,
                            itemStyle: {
                                normal: {
                                    color:'#ccc',
                                    lineStyle:{
                                        color:'#ccc'
                                    },
                                    areaStyle: {
                                        type: 'default'
                                    }
                                }
                            },
                            data:[55, 67, 76, 68, 60, 68, 77]
                        },
                        {
                            name:'张三',
                            type:'line',
                            smooth:true,
                            itemStyle: {
                                normal: {
                                    color:'#1ab394',
                                    lineStyle:{
                                        color:'#1ab394'
                                    },
                                    areaStyle: {
                                        type: 'default'
                                    }
                                }
                            },
                            data:[30, 82, 34, 91, 90, 30, 10]
                        }
                    ]
                };

            var myChart = echarts.init(document.getElementById('score')); 
            myChart.setOption(option);



            $('.fa-picture-o').click(function(){



            var html = '<div id="carousel-example-generic" class="carousel slide" data-ride="carousel" style="height:200px;">'+
                          '<div class="carousel-inner" role="listbox">'+
                            '<div class="item active">'+
                              '<img src="{{asset('osce/images/iconfont-shipinliebiao.svg')}}" alt="...">'+
                              '<div class="carousel-caption">'+
                              '</div>'+
                            '</div>'+
                            '<div class="item" style="height:100%;">'+
                              '<img style="height:150px; width:100%;" src="{{asset('osce/images/iconfont-shipinliebiao.svg')}}" alt="...">'+
                              '<div class="carousel-caption">'+
                                '<a href="#">下载</a>'+
                              '</div>'+
                            '</div>'+
                          '</div>'+
                          '<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">'+
                            '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>'+
                            '<span class="sr-only">Previous</span>'+
                          '</a>'+
                          '<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">'+
                            '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>'+
                            '<span class="sr-only">Next</span>'+
                          '</a>'+
                        '</div>';

            layer.open({
                type: 1,
                closeBtn: 0, //不显示关闭按钮
                title:' ',
                area: ['420px', '240px'],
                shift: 2,
                shadeClose: true, //开启遮罩关闭
                content: html
            });

            var mySwiper = new Swiper ('.swiper-container', {
                loop: true,
                // 如果需要分页器
                pagination: '.swiper-pagination',
                // 如果需要前进后退按钮
                nextButton: '.swiper-button-next',
                prevButton: '.swiper-button-prev'
            });

                
            });


        })
    </script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>考生成绩明细</h5>
            <a href="javascript:history.back(-1)" class="btn btn-outline btn-default" style="float:right;margin:-10px 10px 0 0;">返回</a>
        </div>
        <div class="ibox-content">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><b>考试</b></td>
                        <td colspan="3">2016年度OSCE考试第1期</td>
                        <td><b>考站</b></td>
                        <td>肠胃炎考站</td>
                    </tr>
                    <tr>
                        <td><b>姓名</b></td>
                        <td>张三</td>
                        <td><b>学号</b></td>
                        <td>552323</td>
                        <td><b>评价老师</b></td>
                        <td>李老师</td>
                    </tr>
                    <tr>
                        <td><b>答题开始时间</b></td>
                        <td>2015-11-22 12:00</td>
                        <td><b>耗时</b></td>
                        <td>9:00</td>
                        <td><b>总成绩</b></td>
                        <td>86</td>
                    </tr>
                    <tr>
                        <td><b>评价</b></td>
                        <td colspan="5">该学生在操作过程中技能娴熟，步骤操作得体，效率较高，对所学知识理解和掌握的较好。</td>
                    </tr>
                </tbody>
            </table>
            <div >
                <!-- 五星评价 -->
                <div class="row">
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">操作的连贯性：</label>
                            <div class="col-sm-10">
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">工作的娴熟度：</label>
                            <div class="col-sm-10">
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">病人关怀情况：</label>

                            <div class="col-sm-10">
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">沟通亲和力：</label>

                            <div class="col-sm-10">
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                                <i class="fa fa-star perfect"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div id="score" style="height:340px;"></div>
                <!-- 统计图 -->
            </div>
            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>考核点</th>
                    <th>总分</th>
                    <th>成绩</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>操作是否规范？</td>
                        <td>78</td>
                        <td>7</td>
                        <td value="1">
                            <a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-picture-o fa-2x"></i></span></a>
                            <a href="javascript:void(0)"><span class="read  video"></span></a>
                        </td>
                    </tr>
                    <tr>
                        <td>1-1</td>
                        <td>操作是否规范？</td>
                        <td>78</td>
                        <td>7</td>
                        <td value="1">
                            <a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-picture-o fa-2x"></i></span></a>
                            <a href="javascript:void(0)"><span class="read  video"></span></a>
                        </td>
                    </tr>
                    <tr>
                        <td>1-2</td>
                        <td>操作是否规范？</td>
                        <td>78</td>
                        <td>7</td>
                        <td value="1">
                            <a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-picture-o fa-2x"></i></span></a>
                            <a href="javascript:void(0)"><span class="read  video"></span></a>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>操作是否规范？</td>
                        <td>78</td>
                        <td>7</td>
                        <td value="1">
                            <a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-picture-o fa-2x"></i></span></a>
                            <a href="javascript:void(0)"><span class="read  video"></span></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>








</div>

@stop{{-- 内容主体区域 --}}
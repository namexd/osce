@extends('osce::admin.layouts.admin_index')
@section('only_css')
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
        background-image: url('{{asset("osce/images/iconfont-shipinliebiao.svg")}}');
    }

    .carousel-control.right,.carousel-control.left{background-image: none;}
    .carousel-caption {
        position: relative;
        right: 0;
        bottom:0;
        left: 0;
        padding-top: 10px;
        padding-bottom: 0;
        color: #fff;
        text-align: center;
         text-shadow: none;
    }
</style>
@stop

@section('only_js')
<script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
    <script>
        $(function(){
            /**
             * 图表统计
             * @author mao
             * @version 1.0
             * @date    2016-01-29
             * @param   {array}   standard     考核点分数
             * @param   {string}   student_name 学生姓名
             * @param   {array}   avg          考核点平均分
             * @param   {array}   xAxis          考核点
             */
            function charts(standard,student_name,avg,xAxis){

                //考核点数据较少处理
                if(xAxis.length<8){
                    var len = 7 - xAxis.length;
                    for(var i = 0;i<=len;i++){
                        xAxis.push('');
                    }
                }

                var option = {
                    title : {
                        text: '图表分析'
                    },
                    tooltip : {
                        trigger: 'axis'
                    },
                    legend: {
                        data:['平均分',student_name],
                        x:'right'
                    },
                    toolbox: {
                        show : false
                    },
                    calculable : false,
                    xAxis : [
                        {
                            type : 'category',
                            boundaryGap : false,
                            data : xAxis//['考核点1','考核点2','','','','','']
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
                            data:avg//[55, 67, 76, 68, 60, 68, 77]
                        },
                        {
                            name:student_name,
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
                            data:standard//[30, 82, 34, 91, 90, 30, 10]
                        }
                    ]
                };

                var myChart = echarts.init(document.getElementById('score')); 
                myChart.setOption(option);
            }

            //考核点分数
            var standard = [],avg = [],xAxis = [];
            $('#standard li').each(function(key,elem){
                standard.push($(elem).attr('value'));
            });

            $('#avg li').each(function(key,elem){
                avg.push($(elem).attr('value'));
            });

            if(standard.length>avg.length){
                for(var i in standard){
                    xAxis.push('考核点'+(parseInt(i)+1));
                }
            }else{
                for(var i in standard){
                    xAxis.push('考核点'+(parseInt(i)+1));
                }
            }
            //触发图表格
            charts(standard,$('#student').text(),avg,xAxis);


            /**
             * 图片下载页面弹出
             * @author mao
             * @version 1.0
             * @date    2016-01-28
             */
            $('.fa-picture-o').click(function(){

                //获取img数据
                var img = [];
                $(this).parent().siblings('.img').find('li').each(function(key,elem){

                    img.push({src:$(elem).attr('value'),download:$(elem).attr('download')});
                });

                //下载的图片dom结构
                var str = '';
                for(var i in img){
                    if(i==0){
                        str += '<div class="item active">'+
                              '<img style="height:200px; width:100%;" src="'+img[i].src+'" alt="...">'+
                              '<div class="carousel-caption">'+
                                '<a href="'+img[i].download+'" target="_blank">下载</a>'+
                              '</div>'+
                            '</div>';
                    }else{
                        str += '<div class="item">'+
                              '<img style="height:200px; width:100%;" src="'+img[i].src+'" alt="...">'+
                              '<div class="carousel-caption">'+
                                '<a href="'+img[i].download+'" target="_blank">下载</a>'+
                              '</div>'+
                            '</div>';
                    }
                    
                }

                //轮播dom准备
                var html = '<div id="carousel-example-generic" class="carousel slide" data-ride="carousel" style="height:220px;">'+
                              '<div class="carousel-inner" role="listbox">'+str+'</div>'+
                              '<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">'+
                                '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>'+
                                '<span class="sr-only">Previous</span>'+
                              '</a>'+
                              '<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">'+
                                '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>'+
                                '<span class="sr-only">Next</span>'+
                              '</a>'+
                            '</div>';



                //弹出容器
                layer.open({
                    type: 1,
                    closeBtn: 0, //不显示关闭按钮
                    title:'',
                    area: ['420px', '240px'],
                    shift: 2,
                    shadeClose: true, //开启遮罩关闭
                    content: html
                });

            });
            
            //视频弹出窗口
            /*$('.video').click(function(){

                var url = $(this).attr('value');

                layer.open({
                    type: 2,
                    title: '实时视频',
                    shadeClose: true,
                    shade: false,
                    maxmin: true, //开启最大化最小化按钮
                    area: ['893px', '600px'],
                    content: url
                });

            });*/


        })
    </script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
<div style="display:none;">
    <ul id="standard">
        @foreach($standard as $item)
            <li value="{{$item}}"></li>
        @endforeach
    </ul>
    <ul id="avg">
        @foreach($avg as $item)
            <li value="{{$item}}"></li>
        @endforeach
    </ul>
</div>
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>考生成绩明细</h5>
            <a href="javascript:history.back(-1)" class="btn btn-outline btn-default" style="float:right;margin:-10px 10px 0 0;">返回</a>
            <a href="{{route('osce.admin.course.getResultVideo')}}?exam_id={{$result['student']->exam_id}}&student_id={{$result['student']->id}}&station_id={{$result['station_id']}}" class="btn btn-outline btn-default" style="float:right;margin:-10px 10px 0 0;">查看视频</a>
        </div>
        <div class="ibox-content">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><b>考试</b></td>
                        <td colspan="3">{{$result['exam_name']}}</td>
                        <td><b>科目</b></td>
                        <td>{{$result['subject_title']}}</td>
                    </tr>
                    <tr>
                        <td><b>姓名</b></td>
                        <td id="student">{{$result['student']->name}}</td>
                        <td><b>学号</b></td>
                        <td>{{$result['student']->code}}</td>
                        <td><b>评价老师</b></td>
                        <td>{{$result['teacher']->name}}</td>
                    </tr>
                    <tr>
                        <td><b>答题开始时间</b></td>
                        <td>{{$result['begin_dt']}}</td>
                        <td><b>耗时</b></td>
                        <td>{{$result['time']}}</td>
                        <td><b>总成绩</b></td>
                        <td>{{$result['score']}}</td>
                    </tr>
                    <tr>
                        <td><b>评价</b></td>
                        <td colspan="5">{{($result['evaluate']=='null'?'':$result['evaluate'])}}</td>
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
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fa fa-star {{$i<$result['operation']?'perfect':''}}"></i>
                            @endfor
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">工作的娴熟度：</label>
                            <div class="col-sm-10">
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fa fa-star {{$i<$result['skilled']?'perfect':''}}"></i>
                            @endfor
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">病人关怀情况：</label>
                            <div class="col-sm-10">
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fa fa-star {{$i<$result['patient']?'perfect':''}}"></i>
                            @endfor
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">沟通亲和力：</label>
                            <div class="col-sm-10">
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fa fa-star {{$i<$result['affinity']?'perfect':''}}"></i>
                            @endfor
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
                    <th width="200">考核内容</th>
                    <th>总分</th>
                    <th>成绩</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                    @forelse($scores as $key => $value)
                        <tr>
                            <td>{{$value['sort']}}</td>
                            <td><div title="{{$value['content']}}" style="text-overflow:ellipsis; overflow:hidden; white-space:nowrap; width:600px;">
                                    {{$value['content']}}
                                </div>
                            </td>
                            <td>{{$value['tScore']}}</td>
                            <td>{{$value['score']}}</td>
                            <td>&nbsp;</td>
                        </tr>
                        @forelse($value['items'] as $k => $item)
                            <tr>
                                <td>{{$item['standard']->parent->sort.'-'.$item['standard']->sort}}</td>
                                <td><div title="{{$item['standard']->content}}" style="text-overflow:ellipsis; overflow:hidden; white-space:nowrap; width:600px;">
                                        {{$item['standard']->content}}
                                    </div>
                                </td>
                                <td>{{$item['standard']->score}}</td>
                                <td>{{$item['score']}}</td>
                                <td>
                                    <a href="javascript:void(0)">
                                        <span class="read  state1 detail"><i class="fa fa-picture-o fa-2x"></i></span>
                                        <ul class="img" style="display:none;">
                                            @foreach($item['image'] as $k=>$img)
                                                <li value="{{$img->url}}" download="{{route('osce.admin.getDownloadImage',array('id'=>$img->id))}}"></li>
                                            @endforeach
                                        </ul>
                                    </a>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop{{-- 内容主体区域 --}}
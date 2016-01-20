@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/booking/css/booking.css')}}" rel="stylesheet" type="text/css" />
    <style>
        /*实验室名字超出长度修改*/
        .add_main .form-group label{width: 95px;}
        .add_main .form-group .txt {padding-left:105px;}

        .manage_list .all_list p{padding:5px 8px 0 8%;}
        .manage_list div p:last-child{padding:0px 8px 5px 8%;}
</style>
@stop

@section('only_head_js')
    <script src="{{asset('msc/wechat/booking/booking_student.js')}}"></script>
@stop

@section('content')
    <?php
    $errorsInfo =(array)$errors->getMessages();
    if(!empty($errorsInfo))
    {
        $errorsInfo = array_shift($errorsInfo);
        echo '<pre>';
        var_dump($errorsInfo);die;
    }

    ?>
    <input type="hidden" id="parameter" value="{'pagename':'booking_student_detail'}" />
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        预约实验室
        <a class="right header_btn" href="#">

        </a>
    </div>
    <div id="now_borrow">
        <div class="add_main">
            <div class="form-group">
                <label for="">实验室名称</label>
                <div class="txt">
                    {{ $data['LaboratoryInfo']['name']}}
                </div>
            </div>
            <div class="form-group">
                <label for="">地址</label>
                <div class="txt">
                    {{ $data['LaboratoryInfo']['FloorInfo']['address']}} {{ $data['LaboratoryInfo']['FloorInfo']['name']}}{{ $data['LaboratoryInfo']['floor']}}楼{{ $data['LaboratoryInfo']['code']}}
                </div>
            </div>
            <div class="form-group">
                <label for="">预约日期</label>
                <div class="txt">
                    {{ $data['ApplyTime']}}
                </div>
                <div class="submit_box">
                    <button  class="btn4 get_list_detail">查看资源清单</button>
                </div>
            </div>
        </div>
    </div>
    <form action="{{route('msc.Laboratory.OpenLaboratoryForm')}}" method="post" id="datelist_set">
        <div class="date_list">

        </div>
        <input name="date_time" value="{{ $data['ApplyTime']}}" type="hidden"/>
        <input name="lab_id" value="{{ $data['LaboratoryInfo']['id']}}" type="hidden"/>
        <div class="manage_list">
            @foreach($data['LaboratoryInfo']['OpenPlan'] as $val)
                <div class="all_list" id="{{ @$val['id']}}">

                    @if(empty($val['Apply_status']))

                        @if($val['apply_num']==$data['LaboratoryInfo']['total'])
                            <div class="w_70 left">
                                <p>时段：<span>{{ @$val['begintime']}}-{{ @$val['endtime']}}</span></p>

                                <p>已预约/容量：<span>-</span></p>
                            </div>
                            <div class="w_30 right mart_12 cloc">
                                已满
                            </div>
                        @else
                            <div class="w_70 left">
                                <p>时段：<span>{{ @$val['begintime']}}-{{ @$val['endtime']}}</span></p>
                                <p>已预约/容量：<span>{{ @$val['apply_num']}}/{{$data['LaboratoryInfo']['total']}}</span></p>
                            </div>
                            <div class="w_30 right mart_12">
                                <label class="check_label checkbox_input check_one">
                                    <div class="check_real check_icon display_inline"></div>
                                </label>
                            </div>
                        @endif
                    @elseif($val['Apply_status'] == 1)
                    <div class="w_70 left">
                        <p>时段：<span>{{ @$val['begintime']}}-{{ @$val['endtime']}}</span></p>

                        <p>已预约/容量：<span>{{ @$val['apply_num']}}/{{$data['LaboratoryInfo']['total']}}</span></p>
                    </div>
                    <div class="w_30 right mart_12 red">
                        您已预约
                    </div>
                    @elseif($val['Apply_status'] == 2)
                        <div class="w_70 left">
                            <p>时段：<span>{{ @$val['begintime']}}-{{ @$val['endtime']}}</span></p>
                            <p>已预约/容量：<span>{{$data['LaboratoryInfo']['total']}}/{{$data['LaboratoryInfo']['total']}}</span></p>
                        </div>
                        <div class="w_30 right mart_12 cloc">
                            已被老师占用
                        </div>
                    @endif
                </div>
            @endforeach

        </div>
        <div class="w_94">
            <input class="btn2 mart_10 marb_10"  id="submit"  type="submit" value="确认预约">
        </div>
    </form>
    <div id="sidepopup_layer">
        <div class="box_hidden">
        </div>

        <div class="box_content" >
            <p class="font16 title">资源清单</p>

            <div class="main_list" id="inner-content">
                <div class="title_nav">
                    <div class="title_number title">序号</div>
                    <div class="title_name title">资源名称</div>
                    <div class="title_number title">资源类型</div>
                    <div class="title_number title">数量</div>
                </div>
                <div class="detail_list">
                    <ul class="inner-content">

                        @foreach($data['LadDeviceList'] as $k => $val)

                            <li>
                                <span class="title_number left">#{{@($k+1)}}</span>
                                <span class="title_name left">{{ @$val['DeviceInfo']['name'] }}</span>
                                <span class="title_number left">{{ @$val['devicesCateInfo']['name'] }}耗材</span>
                                <span class="title_number left">{{ @$val['total']}}</span>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>

        </div>
    </div>

@stop
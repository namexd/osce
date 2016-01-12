@extends('osce::wechat.layouts.admin')

@section('only_head_css')
    <link rel="stylesheet" href="{{asset('osce/wechat/personalcenter/css/documentation.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{asset('osce/wechat/personalcenter/css/jalendar.css')}}" type="text/css" />
    <style type="text/css">
        .detail-list{
           margin:30px;
        }
        .detail-list li{
            font-weight: 700;
            margin-bottom: 10px;
        }
        .operate button{
            width: 45%;
        }
        .operate{
            margin-top: 20px;
        }
        .rejected{
            background-color: #ed5565;
        }
        .items{
            color: #999;
            margin-left: 20px;
           font-weight: inherit!important;
        }
        .agree{
            background-color: #16beb0;
        }
    </style>


@stop
@section('only_head_js')
    <script type="text/javascript" src="{{asset('osce/wechat/personalcenter/js/jalendar.js')}}"></script>
    <script type="text/javascript">

        $(function(){
             $('.agree').click(function(){
                   var id =$(this).attr('data');
                   var status = $(this).val();
                   var url ='/osce/wechat/invitation/invitation-respond';
                 $.ajax({
                     url:url,
                     type:get,
                     dataType:"json",
                     data:{
                         id:id,
                         status:status,
                     },
                     success:function(result){
                         if(resule.code==1){
                             location.reload();
                         }else{
                             layer.alert((result.message).split(":")[1],function(){
                                 location.reload();
                             })
                         }

                     }



                 })
             })


        })

    </script>

@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        考试邀请详情
        <a class="right header_btn" href="">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="detail-list">
        <ul>
            @if(!empty($list))

            <li>
                考试邀请:<span class="items">{{$list['exam_name']}}</span>
            </li>
            <li>
                考试时间:<span class="items">{{date('Y-m-d',strtotime($list['begin_dt']))}}~{{date('Y-m-d',strtotime($list['end_dt']))}}</span>
            </li>
            <li>
                sp病例:<span class="items">{{$list['case_name']}}</span>
            </li>
                @endif
        </ul>
        <p>希望你能协助考核，如有疑问，请致电：028 - 87653489  张老师</p>
        <div class="operate">
            <button class="btn1 pull-left agree" type="button" value="1"  data={{$id}}>同意</button>
            <button class="btn1 pull-right rejected" type="button" value="2" data={{$id}} >拒绝</button>
        </div>
    </div>
@stop
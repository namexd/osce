@extends('osce::wechat.layouts.admin')

@section('only_head_css')
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
    <script type="text/javascript">

        $(function(){

            $('.agree').click(function(){

                   var id =$(this).attr('data');
                   var status = $(this).val();
//                 var url ='/osce/wechat/invitation/invitation-respond';
                 $.ajax("{{route('osce.wechat.invitation.getInvitationRespond')}}",{
//                     url:url,
                     type:'get',
                     dataType:"json",
                     data:{
                         id:id,
                         status:status

                     },

                     success:function(data){
                         if(data.code==1){
                             $.alert({
                                 title: '提示：',
                                 content: '操作成功!',
                                 confirmButton: '确定',
                                 confirm: function(){
                                     window.location.reload();
                                 }
                             });

                         }

                     },
                     error:function() {
                         $.alert({
                             title: '提示：',
                             content: '操作失败!',
                             confirmButton: '确定',
                             confirm: function(){
                             }
                         });
                     }
                 })
             })

            $('.rejected').click(function(){

                var id =$(this).attr('data');
                var status = $(this).val();
//                 var url ='/osce/wechat/invitation/invitation-respond';
                $.ajax("{{route('osce.wechat.invitation.getInvitationRespond')}}",{
//                     url:url,
                    type:'get',
                    dataType:"json",
                    data:{
                        id:id,
                        status:status

                    },

                    success:function(data){
                        if(data.code==1){
                            $.alert({
                                title: '提示：',
                                content: '操作成功!',
                                confirmButton: '确定',
                                confirm: function(){
                                    window.location.reload();
                                }
                            });
                        }
                    },
                    error:function() {
                        $.alert({
                            title: '提示：',
                            content: '操作失败!',
                            confirmButton: '确定',
                            confirm: function(){
                            }
                        });
                    }
                })
            })

        })

    </script>

@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.invitation.getList')}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        考试邀请详情
        <a class="right header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="detail-list">

        @if($list['status']==1)

            <p class="pop">{{$list['teacher_name']}}老师您已同意参加{{$list['exam_name']}}考试</p>

        @elseif($list['status']==2)

            <p class="pop">{{$list['teacher_name']}}老师您已拒绝参加{{$list['exam_name']}}考试</p>

        @endif

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
            @if($list['status']==0)
            <button class="btn1 pull-left agree" type="button" value="1"  data={{$id}}>同意</button>
            <button class="btn1 pull-right rejected" type="button" value="2" data={{$id}} >拒绝</button>
                @endif

        </div>
    </div>
@stop
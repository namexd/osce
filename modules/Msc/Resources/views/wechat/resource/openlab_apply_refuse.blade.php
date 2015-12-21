@extends('msc::wechat.layouts.admin')
@section('only_head_css')

    <link href="{{asset('msc/wechat/personalcenter/css/resourceborrow.css')}}" rel="stylesheet" type="text/css" />

@stop
@section('only_head_js')
    <script src="{{asset('msc/common/select2-4.0.0/js/select2.full.js')}}"></script>
    <script>
    $(document).ready(function () {

        //页面刷新数据操作
        $(".Reason").find('textarea').val($('#Reason').val());

        /**
         *下拉选择框
         */
        $("#Reason").change( function(){
            if($(this).val()=='other'){
                $(".Reason").find('textarea').val('');
            }else{
                $(".Reason").find('textarea').val($(this).val());
            }
        });

        //提交理由
        var id = (location.href).split('=')[1];
        $('#submit').one('click',function(){
            var input   =   {
                'id'    :   $('[name=id]').val(),
                'status':   $('[name=status]').val(),
                'reject':   $('[name=reject]').val()
            };
            $.post('{{action('\Modules\Msc\Http\Controllers\WeChat\LabController@postChangeOpenLabApplyStatus')}}',input,function(data){
                if(data.code==1)
                {
                    window.location.href ='{{ route('wechat.lab.openLabApplyList')}}';
                }else{
                    layer.alert((data.message).split(':')[1]);
                    console.log(data.message);
                }
            });
        })
    });
    </script>
@stop
@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        不通过
    </div>
    <div id="info_list">
        <div id="Equipment_info" class="w_94" style="">
            <form name="form"   id="frmTeacher" action="" method="post" >
                <input type="hidden" name="id" value="{{$id}}" />
                <input type="hidden" name="status" value="2" />
                <p  class="mart_3">请输入拒绝申请理由</p>
                <div>
                    <select class="form-control"  name="resources_tool_item_id"  id="Reason" placeholder="请选择理由" style="width:100%;">
                        <option value="理由1">理由1</option>
                        <option value="理由2">理由2</option>
                        <option value="理由3">理由3</option>
                        <option value="理由4">理由4</option>
                        <option value="other">自定义理由</option>
                    </select>
                </div>

                <div class="Reason mart_10">
                    <textarea id="Reason_detail" name="reject" type="" placeholder="请输入自定义理由"/></textarea>
                </div>
                <div class="mart_10">
                    <input class="btn2" id="submit" type="button"  value="确认" />
                </div>
            </form>
        </div>

    </div>

@stop
@extends('msc::admin.layouts.admin')

@section('only_js')
    <script src="{{asset('msc/admin/js/all_checkbox.js')}}"></script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-4 selected-all">

            <button type="button" class="btn btn_pl btn-link" ng-click="examine_del()">提醒归还</button>
            <!--<button type="button" class="btn btn_pl btn-link" ng-click="examine_reject()">批量未通过</button>-->
        </div>
        <div class="col-xs-6 col-md-4">

            <div class="input-group">
                <input type="text" placeholder="请输入关键字" class="input-sm form-control">
            <span class="input-group-btn">
                <button type="button" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
            </span>
            </div>

        </div>

    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th width="100">
                    <label class="check_label all_checked">
                        <div class="check_icon"></div>
                        <input  type="checkbox"  value="">
                    </label>
                </th>
                <th>#</th>
                <th>设备名称</th>
                <th>借用时间</th>
                <th>设备编号</th>
                <th>借用人</th>
                <th>借用理由</th>

                <th>
                    操作
                </th>
            </tr>
            </thead>
            <tbody>
            @forelse($pagination as $item)
                <tr value="{{$item->id}}">
                    <td>
                        <label class="check_label checkbox_input">
                            <div class="check_icon"></div>
                            <input  type="checkbox" class="check_id" value="{{ $item['id'] }}" >
                        </label>
                    </td>
                    <td>{{$item->id}}</td>
                    <td>{{$item->resourcesToolItem? $item->resourcesToolItem->resourcesTools->name:'-'}}</td>
                    <td>{{$item->begindate}}-{{$item->enddate}}</td>

                    <td>{{$item->resourcesToolItem? $item->resourcesToolItem->code:'-'}}</td>
                    <td>{{$item->lenderInfo? $item->resourcesToolItem->name:'-'}}</td>
                    <td>{{$item->detail}}</td>
                    <td>
                        <div class="opera">
                            <span class="read  state1 modal-control" data-toggle="modal" data-target="#myModal" style="cursor: pointer;">提醒归还</span>
                        </div>
                    </td>
                </tr>
            @empty
            @endforelse


            </tbody>
        </table>
        <div class="pull-left">
            已选择 <span class="sum">0</span> 条
        </div>

        <div class="btn-group pull-right">
            {{$pagination->render()}}
        </div>
    </form>
    <div ng-include="'configs.html'"></div>


</div>
@stop{{-- 内容主体区域 --}}


@section('layer_content')
    <form class="form-horizontal" id="Form2" novalidate="novalidate">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">提醒归还</h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label class="col-sm-3 control-label">提醒归还理由：</label>
                <div class="col-sm-9">

                    <textarea id="comment" name="comment" class="form-control" required="" aria-required="true"></textarea>

                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" class="notAgree" id="borrow-yes" data-dismiss="modal" aria-hidden="true">提&nbsp;交</button>
        </div>
    </form>
<script>
    $(function(){

        /*获取信息id*/
        $('.modal-control').click(function(){
            $('#Form2').find('textarea').val('');
            $('#Form2').attr('value',$(this).parent().parent().parent().attr('value'));
        });


        /*提醒归还*/
        $('#borrow-yes').click(function(){
            var req = {};
            req['id'] = $('#Form2').attr('value');
            req['detail'] = $('#Form2').find('textarea').val();
            $.ajax({
                type:"get",
                url:"{{action('\Modules\Msc\Http\Controllers\Admin\ResourcesManagerController@getTipBack')}}",
                data:req,
                success:function(res){
                    if(res.code==1){
                        location.reload();
                    }else{
                        alert(res.message);
                    }
                }
            });
        });


    })
</script>
@stop{{-- 内容主体区域 --}}
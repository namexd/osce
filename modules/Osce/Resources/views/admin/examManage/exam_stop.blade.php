@extends('osce::admin.layouts.admin_index')
@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'clinical_case_manage_add','name':'{{route('osce.admin.case.postNameUnique')}}','cancel':'{{route('osce.admin.case.getCaseList')}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox-title">
            <h5>结束考试</h5>
        </div>

        <div class="container-fluid ibox-content" id="list_form">
            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th>总考试编号</th>
                    <th>开始时间-----------------------结束时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @if($data['status']==0 || $data['status']==2)
                    <tr>
                        <td><span class="description" title="{{$data['id']}}">{{$data['id']}}</span></td>
                        <td><span class="description-this" title="{{$data['begin_dt']}}---{{$data['end_dt']}}">{{$data['begin_dt']}}-{{$data['end_dt']}}</span></td>
                        <td>
                            <a href="javascript:void (0);">
                                <button class="btn btn-primary" disabled type="button">结束全部考试</button>
                            </a>
                        </td>
                    </tr>

                @else
                    <tr>
                        <td><span class="description" title="{{$data['id']}}">{{$data['id']}}</span></td>
                        <td><span class="description-this" title="{{$data['begin_dt']}}---{{$data['end_dt']}}">{{$data['begin_dt']}}-{{$data['end_dt']}}</span></td>
                        <td>
                            <a href="{{route('osce.admin.exam.stopzexam')}}?id={{$data['id']}}"><button class="btn btn-primary" type="button">结束全部考试</button></a>
                        </td>
                    </tr>
                @endif

                <tr>
                    <th>阶段考试编号</th>
                    <th>开始时间-----------------------结束时间</th>
                    <th>操作</th>
                </tr>
                @foreach($data['arr'] as $key => $item)
                    <tr>
                        <td><span class="description" title="{{$item->id}}">{{$item->id}}</span></td>
                        <td><span class="description-this" title="{{$item->begin_dt}}---{{$item->end_dt}}">{{$item->begin_dt}}-{{$item->end_dt}}</span></td>
                        <td>
                            @if($item->status==0 || $item->status==2)
                                <a href="javascript:void (0);">
                                    <button class="btn btn-primary" disabled type="button">结束考试</button>
                                </a>
                            @else
                                <a href="{{route('osce.admin.exam.stopfexam')}}?id={{$item->id}}"><button class="btn btn-primary" type="button">结束考试</button></a>
                            @endif

                        </td>
                    </tr>
                @endforeach


                </tbody>
            </table>



        </div>

    </div>
@stop
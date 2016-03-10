@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
    <style>
        .select2-container--open{ z-index: 10000;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script src="{{asset('osce/common/select2-4.0.0/js/select2.full.min.js')}}"></script>
    <script>
        function categories(){
            /**
             * 保存考核范围
             */
            $('.form-horizontal').submit(function(){
                $.getJSON($(this).attr('action'),$(this).serialize(),function(obj){
                    var scopelist="<input type='hidden' name='scope[]' value='"+obj+"'>"
                    $(".save_scope").append(scopelist);
                    $("#myModal").removeClass("in").hide().attr("aria-hidden","true");
                    $("body").removeClass("modal-open");
                    $(".modal-backdrop").remove();
                    var txt="";
                    for (x in obj)
                    {
                        txt=txt +obj[x]+"," ;
                    }

                    $("#" +this_scope).text(
                            txt
                    );
                })

                return  false;

            })
        }
        $(function(){
            categories();
                    @if(!empty($label))
                        @foreach($label as $k =>$sub)
                            var str =  '{{ @$sub['id']}}';
                            $(".tag-"+str).select2({});
                        @endforeach
                     @endif
        })
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_papers_add}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">新增试题组成</h5>
            </div>
        </div>
        <div class="ibox-content">
            <form class="form-horizontal" action="{{ route('osce.admin.ExamPaperController.scopeCallback') }}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">新增试题组成</h4>
                </div>
                <div class="modal-body">
                    @if(@$label)
                        @foreach(@$label as $k =>$sub)
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{@$sub['name']}}：</label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="label-{{ @$sub['id'] }}">
                                        <option value="0">包含</option>
                                        <option value="1">等于</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <select class="form-control tag-{{ @$sub['id'] }}" name="tag-{{ @$sub['id'] }}[]" multiple="multiple" style="width: 100%">
                                        @if(!empty($sub['label_type_and_label']))
                                            @foreach(@$sub['label_type_and_label'] as $key => $val)
                                                <option value="{{ @$val['id'] }}-{{@$val['name']}}">{{@$val['name']}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id='sure'>确定</button>
                    <button type="button" class="btn btn-white" data-dismiss="modal" aria-hidden="true">取消</button>
                </div>
            </form>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}


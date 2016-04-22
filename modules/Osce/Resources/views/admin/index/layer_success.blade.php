@extends('osce::admin.layouts.admin_index')
@section('only_css')
<style>
    .box {
        position: relative;
        height: 100%;
    }
    i {
        font-size: 24px;
        color: #a6b0c3;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-top: -16px;
        margin-left: -175px;
    }
</style>
@stop

@section('only_js')
    
@stop

@section('content')

<div class="box">
    <div style="display: none;">
        <span id="result-id" value="{{$result->id}}"></span>
        <span id="result-name" value="{{$result->name}}"></span>
        <span id="result-type" value="{{$result->type}}"></span>
        <span id="table-id" value="{{$fileArray['table']}}"></span>
        <span id="tr-id" value="{{$fileArray['tr']}}"></span>
        <span id="selector" value="{{$fileArray['selector']}}"></span>
    </div>
    <i>数据新增成功</i>
</div>
<script>
$(function() {
    
    /**
     * 新增数据
     * @author mao
     * @version 3.3
     * @date    2016-04-06
     */
    //获取iframe索引
    var index = parent.layer.getFrameIndex(window.name),
        ID = $('#result-id').attr('value'),
        name = $('#result-name').attr('value'),
        table_id = $('#table-id').attr('value'),
        tr_id = $('#tr-id').attr('value'),
        selector = $('#selector').attr('value'),
        type = $('#result-type').attr('value');

    //等待三秒关闭
    //layer.load(3);
    
    //新增数据传入父页面
    setTimeout(function() {
        if(tr_id == 'sp_assignment') {
            /*parent.$('.'+selector).append('<option value="245">这老师</option>');
            parent.$('.js-example-basic-single').attr('array',245);*/
        } else if(tr_id == 'clinical_case') {

        } else {
            //考站新增
            if(type == undefined) {
                parent.$('.table-id-'+table_id).find('.'+tr_id).find('.'+selector).parent().attr('status-type',type);
                parent.$('.table-id-'+table_id).find('.'+tr_id).find('.'+selector).append('<option value="'+ID+'" selected="selected">'+name+'</option>').val(ID).trigger("change");
            } else {
                //考试项目新增，所属考场新增
                parent.$('.table-id-'+table_id).find('.'+tr_id).find('.'+selector).append('<option value="'+ID+'" selected="selected">'+name+'</option>').val(ID).trigger("change");
            }
        }
        
        //parent.layer.close(index);
    }, 2000);  
})
</script>
@stop{{-- 内容主体区域 --}}
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
        <span id="result" value="{{$result->id}}"></span>
        <span id="result-name" value="{{$result->name}}"></span>
        
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
        table_id = $('#table-id').attr('value'),
        tr_id = $('#tr-id').attr('value');

    //等待三秒关闭
    //layer.load(3)
    setTimeout(function() {
        //新增的数据传过去
        parent.$('.'+table_id).find('.item-id-'+table_id).find('.exam-item').val(ID).trigger("change");
        //parent.layer.close(index);
    }, 2000);  
})
</script>
@stop{{-- 内容主体区域 --}}
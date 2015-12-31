@extends('osce::admin.layouts.admin_index')
@section('only_css')
<style>
    table tr td .form-group {
        margin-bottom: 0;
    }
</style>
@stop

@section('only_js')

@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>新增评分标准</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm">

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">名称</label>

                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">描述</label>
                            <div class="col-sm-10">
                                <input id="select_Category" required  class="form-control m-b" name="account"/>
                            </div>
                        </div>
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>评分标准：</h5>
                            <div class="ibox-tools">
                                <button type="button" class="btn btn-outline btn-default" id="add-new">新增考核点</button>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>考核内容</th>
                                        <th width="80">分数</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody index="0">
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">考核点:</label>
                                                <div class="col-sm-10">
                                                    <input id="select_Category"  class="form-control" name="account"/>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-control">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                            </select>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>
                                            <a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>李四</td>
                                        <td>男</td>
                                        <td>27</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>王麻子</td>
                                        <td>男</td>
                                        <td>65</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>

                    </form>

                </div>

            </div>
        </div>
    </div>

</div>
<script>
$(function(){

    /**
     * 新增一条父考核点
     * @author mao
     * @version 1.0
     * @date    2015-12-31
     */
    $('#add-new').click(function(){
        //计数器标志
        var index = $('table').find('tbody').attr('index');
        index = parseInt(index) + 1;

        var html = '<tr parent="'+index+'" current="0"  class="pid-'+index+'">'+
                        '<td>'+parseInt(index)+'</td>'+
                        '<td>'+
                            '<div class="form-group">'+
                                '<label class="col-sm-2 control-label">考核点:</label>'+
                                '<div class="col-sm-10">'+
                                    '<input id="select_Category"  class="form-control" name="account"/>'+
                                '</div>'+
                            '</div>'+
                        '</td>'+
                        '<td>'+
                            '<select class="form-control">'+
                                '<option value="1">1</option>'+
                                '<option value="2">2</option>'+
                                '<option value="3">3</option>'+
                                '<option value="4">4</option>'+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                            '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>'+
                        '</td>'+
                    '</tr>';
        //记录计数
        $('table').find('tbody').attr('index',index);
        $('tbody').append(html);
    });
    
    /**
     * 新增个子类
     * @author mao
     * @version 1.0
     * @date    2015-12-31
     */
    $('tbody').on('click','.fa-plus',function(){
        var thisElement = $(this).parent().parent().parent().parent();

        var parent = thisElement.attr('parent'),
            child = thisElement.attr('current');

        child = parseInt(child) + 1;

        var html = '<tr child="'+child+'" class='+thisElement.attr('class')+'>'+
                        '<td>'+parent+'-'+child+'</td>'+
                        '<td>'+
                            '<div class="form-group">'+
                                '<label class="col-sm-2 control-label">考核点:</label>'+
                                '<div class="col-sm-10">'+
                                    '<input id="select_Category"  class="form-control" name="account"/>'+
                                '</div>'+
                            '</div>'+
                        '</td>'+
                        '<td>'+
                            '<select class="form-control">'+
                                '<option value="1">1</option>'+
                                '<option value="2">2</option>'+
                                '<option value="3">3</option>'+
                                '<option value="4">4</option>'+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                        '</td>'+
                    '</tr>';
        //记录计数
        thisElement.attr('current',child);
        thisElement.after(html);

        //更新计数
        updateIncreat(thisElement);
    });

    function updateIncreat(thisElement){
       var update_P = 0,
            str = '.'+thisElement.attr('class');
        $('tbody').find(str).each(function(key,elem){

            if($(elem).attr('child')!=undefined){
                $(elem).attr('child',key);
                $(elem).find('td').eq(0).text(update_P+'-'+key);
            }else{
                update_P = $(elem).attr('parent');
            }
        }); 
    }


    function updateIncreatP(thisElement,index){
       var update_P = index,
            str = '.'+thisElement.attr('class');
        $('tbody').find(str).each(function(key,elem){

            if($(elem).attr('child')!=undefined){
                $(elem).attr('child',key);
                $(elem).find('td').eq(0).text(update_P+'-'+key);
            }else{
                $(elem).find('td').eq(0).text(update_P);
            }
        }); 
    }


    $('tbody').on('click','.fa-trash-o',function(){
        var thisElement = $(this).parent().parent().parent().parent();
        if(thisElement.attr('child')==undefined){
            var classElement = '.'+thisElement.attr('class');

            var flag = 0;
            $('tbody tr').each(function(key,elem){
                console.log($(elem).attr('class'))
                if($(elem).attr('child')==undefined){
                   flag += 1;
                   updateIncreatP($(elem),flag)
                }else{
                   updateIncreatP($(elem),flag)
                } 
            });
            $(classElement).remove();
        }else{
            thisElement.remove();
            updateIncreat(thisElement);
        }
    })



})
</script>
@stop{{-- 内容主体区域 --}}
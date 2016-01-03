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
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.topic.postAddTopic')}}">

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">名称</label>

                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name" name="title">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">描述</label>
                            <div class="col-sm-10">
                                <input id="select_Category" required  class="form-control m-b" name="description"/>
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

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-white" type="submit">取消</button>
                                <button class="btn btn-primary" type="submit">保&nbsp;&nbsp;&nbsp;存</button>

                            </div>
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
         * @author  mao
         * @version  1.0
         * @date        2015-12-31
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
                    '<input id="select_Category"  class="form-control" name="content['+index+'][title]"/>'+
                    '</div>'+
                    '</div>'+
                    '</td>'+
                    '<td>'+
                    '<select class="form-control" name="score['+index+'][total]">'+
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
         * @author  mao
         * @version  1.0
         * @date        2015-12-31
         */
        $('tbody').on('click','.fa-plus',function(){
            var thisElement = $(this).parent().parent().parent().parent();

            var parent = thisElement.attr('parent'),
                    child = thisElement.attr('current');

            child = parseInt(child) + 1;

            var html = '<tr child="'+child+'" class="'+thisElement.attr('class')+'" >'+
                    '<td>'+parent+'-'+child+'</td>'+
                    '<td>'+
                    '<div class="form-group">'+
                    '<label class="col-sm-2 control-label">考核点:</label>'+
                    '<div class="col-sm-10">'+
                    '<input id="select_Category"  class="form-control" name="content['+parent+']['+child+']"/>'+
                    '</div>'+
                    '</div>'+
                    '</td>'+
                    '<td>'+
                    '<select class="form-control" name="score['+parent+']['+child+']">'+
                    '<option value="1">1</option>'+
                    '<option value="2">2</option>'+
                    '<option value="3">3</option>'+
                    '<option value="4">4</option>'+
                    '</select>'+
                    '</td>'+
                    '<td>'+
                    '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                    '<a href="javascript:void(0)"><span class="read state11 detail"><i class="fa fa-arrow-up fa-2x"></i></span></a>'+
                    '<a href="javascript:void(0)"><span class="read state11 detail"><i class="fa fa-arrow-down fa-2x"></i></span></a>'+
                    '</td>'+
                    '</tr>';
            //记录计数
            thisElement.attr('current',child);
            var childTotal  =   thisElement.parent().find('.pid-'+parent).length;
            thisElement.parent().find('.pid-'+parent).eq(childTotal-1).after(html)

            //更新计数
            increment(thisElement);
        });

        /**
         * 子类序号更新
         * @author marvine
         * @date    2015-12-31
         * @version [1.0]
         * @param   {object}   thisElement dom参数
         */
        function increment(thisElement){
            var update_P = 0,
                    str = '.'+thisElement.attr('class');
            $('tbody').find(str).each(function(key,elem){

                if($(elem).attr('child')!=undefined){
                    $(elem).attr('child',key);
                    $(elem).attr('class','pid-'+update_P);
                    $(elem).find('td').eq(0).text(update_P+'-'+key);
                }else{
                    update_P = $(elem).attr('parent');
                    $(elem).attr('class','pid-'+update_P);
                }
            });
        }

        /**
         * 删除
         * @author marvine
         * @date    2015-12-31
         * @version [1.0]
         * @param   {[type]}   ){                     var thisElement [description]
     * @return  {[type]}       [description]
     */
        $('tbody').on('click','.fa-trash-o',function(){
            var thisElement = $(this).parent().parent().parent().parent();
            if(thisElement.attr('child')==undefined){
                //父类删除
                var classElement = '.'+thisElement.attr('class');
                var parent = 1;
                $(classElement).remove();
                //更新计数序号
                $('tbody tr').each(function(key,elem){
                    if($(elem).attr('child')==undefined){
                        $(elem).attr('parent',parent);
                        $(elem).find('td').eq(0).text(parent);
                        $(elem).attr('class','pid-'+parent);
                        parent += 1;
                    }else{
                        var child = $(elem).attr('child'),
                                parent_p = parent - 1;
                        $(elem).find('td').eq(0).text(parent_p+'-'+child);
                        $(elem).attr('class','pid-'+parent_p);
                        child += 1;
                    }
                });

                //父类计数更新
                $('tbody').attr('index',parseInt($('tbody').attr('index'))-1)

            }else{
                //子类删除
                thisElement.remove();
                increment(thisElement);
            }
        });

        /**
         * 数据条目上移
         * @author marvine
         * @date    2015-12-31
         * @version [1.0]
         */
        $('tbody').on('click','.fa-arrow-up',function(){
            var thisElement = $(this).parent().parent().parent().parent();
            if(thisElement.prev().attr('child')!=undefined){
                var thisInput = thisElement.find('input').val(),
                        thisSelect = thisElement.find('select').val(),
                        prevInput = thisElement.prev().find('input').val(),
                        prevSelect = thisElement.prev().find('select').val();

                //交换数据
                thisElement.find('input').val(prevInput);
                thisElement.find('select').val(prevSelect);
                thisElement.prev().find('input').val(thisInput);
                thisElement.prev().find('select').val(thisSelect);
            }else{
                return;
            }
        });

        /**
         * 数据条目下移
         * @author marvine
         * @date    2015-12-31
         * @version [1.0]
         */
        $('tbody').on('click','.fa-arrow-down',function(){
            var thisElement = $(this).parent().parent().parent().parent();
            if(thisElement.next().attr('child')!=undefined){
                var thisInput = thisElement.find('input').val(),
                        thisSelect = thisElement.find('select').val(),
                        nextInput = thisElement.next().find('input').val(),
                        nextSelect = thisElement.next().find('select').val();

                //交换数据
                thisElement.find('input').val(nextInput);
                thisElement.find('select').val(nextSelect);
                thisElement.next().find('input').val(thisInput);
                thisElement.next().find('select').val(thisSelect);
            }else{
                return;
            }
        });

    })
</script>
@stop{{-- 内容主体区域 --}}
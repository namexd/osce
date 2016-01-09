/**
 * Created by Administrator on 2015/12/15 0015.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "test_station":test_station();break;
        case "examroom":examroom();break;
        case "clinicalcase":clinicalcase();break;
        case "categories":categories();break;
    }
});


/**
 * 考站管理
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 */
function test_station(){

    $('table').on('click','.fa-trash-o',function(){

        var thisElement = $(this);
        layer.alert('确认删除？',function(){
            $.ajax({
                type:'post',
                async:true,
                url:pars.deletes,
                data:{id:thisElement.parent().parent().parent().attr('value')},
                success:function(res){
                    //location.reload();
                }
            })
        });
    })
}

/**
 * 病例
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 */
function clinicalcase(){
	deleteItem(pars.deletes);
}

/**
 * 考场
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 */
function examroom(){
	deleteItem(pars.deletes);
}

/**
 * 删除操作
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 * @param   {string}   url 请求地址
 */
function deleteItem(url){
	$('table').on('click','.fa-trash-o',function(){

        var thisElement = $(this);
        layer.alert('确认删除？',function(){
            $.ajax({
                type:'post',
                async:true,
                url:url,
                data:{id:thisElement.parent().parent().parent().attr('value')},
                success:function(res){
                    location.reload();
                }
            })
        });
    })
}


function categories(){
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
                '<div class="form-group">'+
                '<label class="col-sm-2 control-label">评分标准:</label>'+
                '<div class="col-sm-10">'+
                '<input id="select_Category"  class="form-control" name="description['+parent+']['+child+']"/>'+
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
                '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-up fa-2x"></i></span></a>'+
                '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-down fa-2x"></i></span></a>'+
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
            var thisInput = thisElement.find('input:first').val(),
                thisInputLast = thisElement.find('input:last').val(),
                thisSelect = thisElement.find('select').val(),
                prevInput = thisElement.prev().find('input:first').val(),
                prevInputLast = thisElement.prev().find('input:last').val(),
                prevSelect = thisElement.prev().find('select').val();

            //交换数据
            thisElement.find('input:first').val(prevInput);
            thisElement.find('input:last').val(prevInputLast);
            thisElement.find('select').val(prevSelect);
            thisElement.prev().find('input:first').val(thisInput);
            thisElement.prev().find('input:last').val(thisInputLast);
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
            var thisInput = thisElement.find('input:first').val(),
                thisInputLast = thisElement.find('input:last').val(),
                thisSelect = thisElement.find('select').val(),
                nextInput = thisElement.next().find('input:first').val(),
                nextInputLast = thisElement.next().find('input:last').val(),
                nextSelect = thisElement.next().find('select').val();

            //交换数据
            thisElement.find('input:first').val(nextInput);
            thisElement.find('input:last').val(nextInputLast);
            thisElement.find('select').val(nextSelect);
            thisElement.next().find('input:first').val(thisInput);
            thisElement.next().find('input:last').val(thisInputLast);
            thisElement.next().find('select').val(thisSelect);
        }else{
            return;
        }
    });

    /**
     * 文件导入
     * @author mao
     * @version 1.0
     * @date    2016-01-08
     */
    $("#file1").change(function(){
            $.ajaxFileUpload
            ({

                url:pars.excel,
                secureuri:false,//
                fileElementId:'file0',//必须要是 input file标签 ID
                dataType: 'text',//
                success: function (data, status)
                {
                    data    =   data.replace('<pre>','').replace('</pre>','');
                    data    =   eval('('+data+')');

                    if(data.code == 1){
                        layer.alert('导入成功！');
                        
                        //var html = '';
                        //var res = data.data.rows;
                        //for(var i in res){
                        //    if(res[i].level==1){
                        //
                        //        //添加父级dom
                        //        html += '<tr parent="'+res[i].sort+'" current="0"  class="pid-'+res[i].sort+'">'+
                        //                '<td>'+res[i].sort+'</td>'+
                        //                '<td>'+
                        //                '<div class="form-group">'+
                        //                '<label class="col-sm-2 control-label">考核点:</label>'+
                        //                '<div class="col-sm-10">'+
                        //                '<input id="select_Category"  class="form-control" value="'+res[i].check_point+'" name="content['+res[i].sort+'][title]"/>'+
                        //                '</div>'+
                        //                '</div>'+
                        //                '</td>'+
                        //                '<td>'+
                        //                '<select class="form-control" name="score['+res[i].sort+'][total]">'+
                        //                '<option value="'+res[i].score+'">'+res[i].score+'</option>'+
                        //                '<option value="1">1</option>'+
                        //                '<option value="2">2</option>'+
                        //                '<option value="3">3</option>'+
                        //                '<option value="4">4</option>'+
                        //                '</select>'+
                        //                '</td>'+
                        //                '<td>'+
                        //                '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                        //                '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>'+
                        //                '</td>'+
                        //                '</tr>';
                        //
                        //        for(var j in res){
                        //            if(res[j].level==2&&res[j].pid==res[i].sort){
                        //
                        //                //处理子级dom
                        //                html += '<tr child="'+res[j].sort+'" class="pid-'+res[i].sort+'" >'+
                        //                        '<td>'+res[i].sort+'-'+res[j].sort+'</td>'+
                        //                        '<td>'+
                        //                        '<div class="form-group">'+
                        //                        '<label class="col-sm-2 control-label">考核项:</label>'+
                        //                        '<div class="col-sm-10">'+
                        //                        '<input id="select_Category"  class="form-control" name="content['+res[i].score+']['+res[j].sort+']"/>'+
                        //                        '</div>'+
                        //                        '</div>'+
                        //                        '<div class="form-group">'+
                        //                        '<label class="col-sm-2 control-label">评分标准:</label>'+
                        //                        '<div class="col-sm-10">'+
                        //                        '<input id="select_Category"  class="form-control" name="description['+res[i].score+']['+res[j].sort+']"/>'+
                        //                        '</div>'+
                        //                        '</div>'+
                        //                        '</td>'+
                        //                        '<td>'+
                        //                        '<select class="form-control" name="score['+res[i].score+']['+res[j].sort+']">'+
                        //                        '<option value="'+res[j].score+'">'+res[j].score+'</option>'+
                        //                        '<option value="1">1</option>'+
                        //                        '<option value="2">2</option>'+
                        //                        '<option value="3">3</option>'+
                        //                        '<option value="4">4</option>'+
                        //                        '</select>'+
                        //                        '</td>'+
                        //                        '<td>'+
                        //                        '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                        //                        '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-up fa-2x"></i></span></a>'+
                        //                        '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-down fa-2x"></i></span></a>'+
                        //                        '</td>'+
                        //                        '</tr>';
                        //            }
                        //        }
                        //    }
                        //}
                        //
                        //$('tbody').append(html);



                    }
                },
                error: function (data, status, e)
                {
                    layer.alert('导入失败！');
                }
            });
        }) ;


}
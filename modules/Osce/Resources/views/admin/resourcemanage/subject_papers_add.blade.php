@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script>
        function categories(){
            $('#submit-btn').click(function(){
                var flag = null;
                $('tbody').find('.col-sm-10').each(function(key,elem){
                    flag = true;
                    if($(elem).find('input').val()==''){
                        flag = false;
                    }
                });
                if(flag==false){
                    layer.alert('考核点或考核项不能为空！');
                    return false;
                }
                if(flag==null){
                    layer.alert('请新增考核点！');
                    return false;
                }
            });


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
                        '<select style="display:none;" class="form-control" name="score['+index+'][total]">'+
                        '<option value="1">1</option>'+
                        '<option value="2">2</option>'+
                        '<option value="3">3</option>'+
                        '<option value="4">4</option>'+
                        '</select>'+
                        '</td>'+
                        '<td>'+
                        '<a href="javascript:void(0)"><span class="read  state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                        '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-arrow-up parent-up fa-2x"></i></span></a>'+
                        '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-arrow-down parent-down fa-2x"></i></span></a>'+
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
                        '<label class="col-sm-2 control-label">考核项:</label>'+
                        '<div class="col-sm-10">'+
                        '<input id="select_Category"  class="form-control" name="content['+parent+']['+child+']"/>'+
                        '</div>'+
                        '</div>'+
                        '<div class="form-group">'+
                        '<label class="col-sm-2 control-label">评分标准:</label>'+
                        '<div class="col-sm-10">'+
                        '<input id="select_Category"  class="form-control"  name="description['+parent+']['+child+']"/>'+
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
                        '<a href="javascript:void(0)"><span class="read state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                        '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-up child-up fa-2x"></i></span></a>'+
                        '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-down child-down fa-2x"></i></span></a>'+
                        '</td>'+
                        '</tr>';
                //记录计数
                thisElement.attr('current',child);

                //分数自动加减
                //var thisElement = $(this).parent().parent();
                var childTotal  =   thisElement.parent().find('.pid-'+parent).length;
                thisElement.parent().find('.pid-'+parent).eq(childTotal-1).after(html);
                //父亲节点
                var className = thisElement.attr('class'),
                        parent =  className.split('-')[1];

                //自动加减节点
                var change = $('.'+className+'[parent='+parent+']').find('td').eq(2).find('select');


                //改变value值,消除连续变换值的变化
                var total = 0;//= parseInt(change.val())+parseInt($(this).val());
                $('.'+className).each(function(key,elem){
                    if($(elem).attr('parent')==parent){
                        return;
                    }else{
                        total += parseInt($(elem).find('td').eq(2).find('select').val());
                    }
                });

                //当没有子类的时候
                if(total==0){
                    return;
                }

                var option = '';
                for(var k =1;k<=total;k++){
                    option += '<option value="'+k+'">'+k+'</option>';
                }
                change.html(option);
                change.val(total);

                $('.'+className+'[parent='+parent+']').find('td').eq(2).find('span').remove();
                change.after('<span>'+parseInt(total)+'</span>');

                /*var option = '';
                 for(var k =0;k<=child;k++){
                 option += '<option value="'+k+'">'+k+'</option>';
                 }
                 thisElement.find('td').eq(2).find('select').html(option);
                 thisElement.find('td').eq(2).find('select').val(child);
                 //禁用下拉
                 //thisElement.find('td').eq(2).find('select').hide();
                 thisElement.find('td').eq(2).find('span').remove();
                 thisElement.find('td').eq(2).find('select').after('<span>'+child+'</span>')
                 */

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
                        $(elem).find('td').eq(2).find('select').attr('name','score['+update_P+']['+key+']');
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
                            $(elem).find('td').eq(2).find('select').attr('name','score['+parent+'][total]');
                            parent += 1;
                        }else{
                            var child = $(elem).attr('child'),
                                    parent_p = parent - 1;
                            $(elem).find('td').eq(0).text(parent_p+'-'+child);
                            $(elem).attr('class','pid-'+parent_p);
                            $(elem).find('td').eq(2).find('select').attr('name','score['+parent_p+']['+child+']');
                            child += 1;
                        }
                    });

                    //父类计数更新
                    $('tbody').attr('index',parseInt($('tbody').attr('index'))-1)

                }else{
                    //子类删除
                    thisElement.remove();
                    increment(thisElement);



                    //父亲节点
                    var className = thisElement.attr('class');
                    parent =  className.split('-')[1];
                    //自动加减节点
                    var change = $('.'+className+'[parent='+parent+']').find('td').eq(2).find('select');

                    //改变value值,消除连续变换值的变化
                    var total = 0;//= parseInt(change.val())+parseInt($(this).val());
                    $('.'+className).each(function(key,elem){
                        if($(elem).attr('parent')==parent){
                            return;
                        }else{
                            total += parseInt($(elem).find('td').eq(2).find('select').val());
                        }
                    });
                    var cu = total;
                    //当删除完的时候
                    if(total==0){
                        total = 1;
                        cu = 0;
                        $('.'+className+'[parent='+parent+']').find('td').eq(2).find('span').text('');
                        //change.show();
                        //dom
                        var option = '';
                        for(var k =1;k<=4;k++){
                            option += '<option value="'+k+'">'+k+'</option>';
                        }
                        change.html(option);
                        change.val(total);
                        $('.'+className+'[parent='+parent+']').attr('current',cu);
                        return;
                    }
                    var option = '';
                    for(var k =1;k<=total;k++){
                        option += '<option value="'+k+'">'+k+'</option>';
                    }
                    change.html(option);
                    change.val(total);
                    $('.'+className+'[parent='+parent+']').attr('current',cu);

                    $('.'+className+'[parent='+parent+']').find('td').eq(2).find('span').remove();
                    change.after('<span>'+parseInt(total)+'</span>');


                }
            });

            /**
             * 数据条目上移
             * @author marvine
             * @date    2015-12-31
             * @version [1.0]
             */
            $('tbody').on('click','.child-up',function(){
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
            $('tbody').on('click','.child-down',function(){
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
             * 父亲节点上移
             * @author mao
             * @version 1.0
             * @date    2016-01-19
             */
            $('tbody').on('click','.parent-up',function(){

                var thisElement = $(this).parent().parent().parent().parent();
                var className = thisElement.attr('class');
                var parent =  1;
                var value = [];
                var valueTotal = null;

                //存储select的值
                $('.'+className).each(function(key,elem){
                    if($(elem).attr('parent')==undefined){
                        value.push($(elem).find('td').eq(2).find('select').val());
                    }else{
                        valueTotal = $(elem).find('td').eq(2).find('select').val();
                    }
                });
                //存储dom结构
                var thisDOM = $('.'+className).clone();
                var preIndex = parseInt(className.split('-')[1])-1;

                //最头一个
                if($('.pid-'+preIndex+'[parent="'+preIndex+'"]').length==0){
                    return;
                }

                //上移
                $('.'+className).remove();
                $('.pid-'+preIndex+'[parent="'+preIndex+'"]').before(thisDOM);

                //更新序号
                $('tbody tr').each(function(key,elem){
                    if($(elem).attr('child')==undefined){
                        $(elem).attr('parent',parent);
                        $(elem).find('td').eq(0).text(parent);
                        $(elem).attr('class','pid-'+parent);

                        //更新name表单序号
                        $(elem).find('td').eq(1).find('input').attr('name','content['+parent+'][title]');
                        $(elem).find('td').eq(2).find('select').attr('name','score['+parent+'][total]');

                        parent += 1;
                    }else{
                        var child = $(elem).attr('child'),
                                parent_p = parent - 1;
                        $(elem).find('td').eq(0).text(parent_p+'-'+child);
                        $(elem).attr('class','pid-'+parent_p);

                        //更新name表单序号
                        $(elem).find('td').eq(1).find('input').eq(0).attr('name','content['+parent_p+']['+child+']');
                        $(elem).find('td').eq(1).find('input').eq(1).attr('name','description['+parent_p+']['+child+']');
                        $(elem).find('td').eq(2).find('select').attr('name','score['+parent_p+']['+child+']');

                        child += 1;
                    }
                });
                //更新数据
                $('.pid-'+preIndex).each(function(key,elem){
                    if($(elem).attr('parent')==undefined){

                        $(elem).find('td').eq(2).find('select').find("option").eq(value[key-1]-1).attr('selected','selected');
                        $(elem).find('td').eq(2).find('select').find("option:selected").val(value[key-1]);
                    }else{
                        $(elem).find('td').eq(2).find('select').find("option:selected").text(valueTotal);
                        $(elem).find('td').eq(2).find('select').find("option:selected").val(valueTotal);
                    }
                });


            });

            /**
             * 父亲节点下移
             * @author mao
             * @version 1.0
             * @date    2016-01-19
             */
            $('tbody').on('click','.parent-down',function(){

                var thisElement = $(this).parent().parent().parent().parent();
                var className = thisElement.attr('class');
                var parent =  1;
                var value = [];
                var valueTotal = null;


                //存储select的值
                $('.'+className).each(function(key,elem){
                    if($(elem).attr('parent')==undefined){
                        value.push($(elem).find('td').eq(2).find('select').val());
                    }else{
                        valueTotal = $(elem).find('td').eq(2).find('select').val();
                    }
                });
                //存储dom结构
                var thisDOM = $('.'+className).clone();
                var preIndex = parseInt(className.split('-')[1])+1;

                //最尾一个
                if($('.pid-'+preIndex+'[parent="'+preIndex+'"]').length==0){
                    return;
                }

                //上移
                $('.'+className).remove();
                $('.pid-'+preIndex+':last').after(thisDOM);

                //更新序号
                $('tbody tr').each(function(key,elem){
                    if($(elem).attr('child')==undefined){
                        $(elem).attr('parent',parent);
                        $(elem).find('td').eq(0).text(parent);
                        $(elem).attr('class','pid-'+parent);

                        //更新name表单序号
                        $(elem).find('td').eq(1).find('input').attr('name','content['+parent+'][title]');
                        $(elem).find('td').eq(2).find('select').attr('name','score['+parent+'][total]');

                        parent += 1;
                    }else{
                        var child = $(elem).attr('child'),
                                parent_p = parent - 1;
                        $(elem).find('td').eq(0).text(parent_p+'-'+child);
                        $(elem).attr('class','pid-'+parent_p);

                        //更新name表单序号
                        $(elem).find('td').eq(1).find('input').eq(0).attr('name','content['+parent_p+']['+child+']');
                        $(elem).find('td').eq(1).find('input').eq(1).attr('name','description['+parent_p+']['+child+']');
                        $(elem).find('td').eq(2).find('select').attr('name','score['+parent_p+']['+child+']');

                        child += 1;
                    }
                });

                //更新数据
                $('.pid-'+preIndex).each(function(key,elem){
                    if($(elem).attr('parent')==undefined){
                        //$(elem).find('td').eq(2).find('select').find("option:selected").text(value[key-1]);
                        $(elem).find('td').eq(2).find('select').find("option").eq(value[key-1]-1).attr('selected','selected');
                        $(elem).find('td').eq(2).find('select').val(value[key-1]);
                    }else{
                        $(elem).find('td').eq(2).find('select').find("option:selected").text(valueTotal);
                        $(elem).find('td').eq(2).find('select').find("option:selected").val(valueTotal);
                    }
                });

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
                            layer.msg('导入成功！',{skin:'msg-success',icon:1});

                            /**
                             * 数据导入
                             * @author mao
                             * @version 1.0
                             * @date    2016-01-08
                             */
                            var html = '';
                            var res = data.data;
                            var index = parseInt($('tbody').attr('index'));

                            for(var i in res){
                                if(res[i].level==1){
                                    index++;
                                    //添加父级dom
                                    html += '<tr parent="'+index+'" current="0"  class="pid-'+index+'">'+
                                            '<td>'+index+'</td>'+
                                            '<td>'+
                                            '<div class="form-group">'+
                                            '<label class="col-sm-2 control-label">考核点:</label>'+
                                            '<div class="col-sm-10">'+
                                            '<input id="select_Category"  class="form-control" value="'+res[i].check_point+'" name="content['+index+'][title]"/>'+
                                            '</div>'+
                                            '</div>'+
                                            '</td>'+
                                            '<td>'+
                                            '<select class="form-control" style="display:none;" name="score['+index+'][total]">'+
                                            '<option value="'+res[i].score+'">'+res[i].score+'</option>'+
                                            '<option value="1">1</option>'+
                                            '<option value="2">2</option>'+
                                            '<option value="3">3</option>'+
                                            '<option value="4">4</option>'+
                                            '</select>'+
                                            '<span>'+res[i].score+'</span>'+
                                            '</td>'+
                                            '<td>'+
                                            '<a href="javascript:void(0)"><span class="read  state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                                            '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-arrow-up parent-up fa-2x"></i></span></a>'+
                                            '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-arrow-down parent-down fa-2x"></i></span></a>'+
                                            '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>'+
                                            '</td>'+
                                            '</tr>';

                                    for(var j in res){
                                        if(res[j].level==2&&res[j].pid==res[i].sort){

                                            //处理子级dom
                                            html += '<tr child="'+res[j].sort+'" class="pid-'+index+'" >'+
                                                    '<td>'+index+'-'+res[j].sort+'</td>'+
                                                    '<td>'+
                                                    '<div class="form-group">'+
                                                    '<label class="col-sm-2 control-label">考核项:</label>'+
                                                    '<div class="col-sm-10">'+
                                                    '<input id="select_Category"  class="form-control" value="'+res[j].check_item+'" name="content['+index+']['+res[j].sort+']"/>'+
                                                    '</div>'+
                                                    '</div>'+
                                                    '<div class="form-group">'+
                                                    '<label class="col-sm-2 control-label">评分标准:</label>'+
                                                    '<div class="col-sm-10">'+
                                                    '<input id="select_Category"  class="form-control" value="'+res[j].answer+'" name="description['+index+']['+res[j].sort+']"/>'+
                                                    '</div>'+
                                                    '</div>'+
                                                    '</td>'+
                                                    '<td>'+
                                                    '<select class="form-control" name="score['+index+']['+res[j].sort+']">'+
                                                    '<option value="'+res[j].score+'">'+res[j].score+'</option>'+
                                                    '<option value="1">1</option>'+
                                                    '<option value="2">2</option>'+
                                                    '<option value="3">3</option>'+
                                                    '<option value="4">4</option>'+
                                                    '</select>'+
                                                    '</td>'+
                                                    '<td>'+
                                                    '<a href="javascript:void(0)"><span class="read state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                                                    '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-up child-up fa-2x"></i></span></a>'+
                                                    '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-down child-down fa-2x"></i></span></a>'+
                                                    '</td>'+
                                                    '</tr>';
                                        }
                                    }
                                }
                            }
                            $('tbody').attr('index',index);
                            $('tbody').append(html);
                        }else {
                            layer.alert('文件导入错误，请参考下载模板！');
                        }
                    },
                    error: function (data, status, e)
                    {
                        layer.msg('导入失败！',{skin:'msg-error',icon:1});
                    }
                });
            }) ;


            /**
             * 考核分数自动加减
             * @author mao
             * @version 1.0
             * @date    2016-01-20
             */
            $('tbody').on('change','select',function(){
                var thisElement = $(this).parent().parent();
                //父亲节点
                var className = thisElement.attr('class'),
                        parent =  className.split('-')[1];

                //自动加减节点
                var change = $('.'+className+'[parent='+parent+']').find('td').eq(2).find('select');


                //改变value值,消除连续变换值的变化
                var total = 0;//= parseInt(change.val())+parseInt($(this).val());
                $('.'+className).each(function(key,elem){
                    if($(elem).attr('parent')==parent){
                        return;
                    }else{
                        total += parseInt($(elem).find('td').eq(2).find('select').val());
                    }
                });

                //当没有子类的时候
                if(total==0){
                    return;
                }

                var option = '';
                for(var k =1;k<=total;k++){
                    option += '<option value="'+k+'">'+k+'</option>';
                }
                change.html(option);
                change.val(total);

                $('.'+className+'[parent='+parent+']').find('td').eq(2).find('span').remove();
                change.after('<span>'+parseInt(total)+'</span>')


            });


        }

        $(function(){
            /**
             * 编辑和新增共用了一段代码，这里必须将验证单独拿出
             * @author mao
             * @version 1.0
             * @date    2016-02-19
             */
            $('#sourceForm').bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*验证*/
                    title: {/*键名username和input name值对应*/
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '名称不能为空'
                            },
                            threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                            remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                                url: "{{route('osce.admin.topic.postNameUnique')}}",//验证地址
                                message: '名称已经存在',//提示消息
                                delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                                type: 'POST',//请求方式
                                /*自定义提交数据，默认值提交当前input value*/
                                data: function(validator) {
                                    return {
                                        name: $('#title').val()
                                    }
                                }
                            }
                        }
                    },
                    desc: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '描述不能为空'
                            }
                        }
                    }
                }
            });
        })
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_check_tag'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">新增试卷</h5>
            </div>
        </div>
        <div class="ibox-content">
            <form method="post" class="form-horizontal" id="sourceForm" action="">
                <div class="form-group">
                    <label class="col-sm-2 control-label">试卷名称</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name">
                        <input type="hidden"  class="form-control" id="cate_id" name="cate_id" value="2" />
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">考试时长</label>
                    <div class="col-sm-10">
                        <input type="text"  class="form-control" id="code" name="code">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">组卷方式</label>
                    <div class="col-sm-10">
                        <select id="status"   class="form-control m-b" name="status">
                            <option value="1">自动组卷</option>
                            <option value="2">手工组卷</option>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">试卷类型</label>
                    <div class="col-sm-10">
                        <select id="status"   class="form-control m-b" name="status">
                            <option value="1">随机试卷</option>
                            <option value="2">统一试卷</option>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">评分标准</label>
                    <div class="col-sm-10">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5></h5>
                                <div class="ibox-tools">
                                    <button type="button" class="btn btn-outline btn-default" id="add-new">新增题型</button>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>序号</th>
                                        <th>考核内容</th>
                                        <th width="120">分数</th>
                                        <th width="160">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody index="0">

                                    </tbody>
                                </table>

                            </div>
                        </div>

                    </div>
                </div>


                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <a class="btn btn-white" href="{{route("osce.admin.machine.getMachineList",["cate_id"=>2])}}">取消</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}
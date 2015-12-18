@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/eqreturnmanage/css/history.css')}}">
    <style>
    /*layer alert*/
    .layui-layer-title{
        background: #fff!important;
        color: #408aff!important;
        font-size: 16px!important;
    }
    .layui-layer-btn{
        background: #fff!important;
        border-top: 1px #fff solid!important;
    }
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    .checkbox tr td:first-child{padding-top: 10px;}
    .check_ids{}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('msc/admin/js/all_checkbox.js')}}"></script>
    <script>
        var start = {
            elem: "#start",
            format: "YYYY/MM/DD hh:mm:ss",
            min: "1970-00-00 00:00:00",
            max: "2099-12-06 23:59:59",
            istime: true,
            istoday: false,
            choose: function (a) {
                end.min = a;
                end.start = a
            }
        };
        var end = {
            elem: "#end",
            format: "YYYY/MM/DD hh:mm:ss",
            min: "1970-00-00 00:00:00",
            max: "2099-12-16 23:59:59",
            istime: true,
            istoday: false,
            choose: function (a) {
                start.max = a
            }
        };

        $(function(){
            laydate(start);
            laydate(end);
            $('#choose').change(function(){
                var val=$(this).val();
                if(val.length>0)
                {
                    $('#comment').val(val);
                }
            });
            $('#Form2').on('hidden.bs.modal',function(){
                alert(123);
            });
        });

    </script>
@stop


@section('content')
<style>
    .intro{
        line-height: 33px;
        margin-right: 14px;
    }
    .time_set input{vertical-align: bottom;}
    .apply-sousuo{width: 290px;float: left;margin-left: 40px;}
    .btn-link{color: #a6b0c3!important;}
    .btn-link:hover{color: #fff!important;}
    .check_label{margin-bottom: -2px!important;}
</style>
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row table-head-style1 ">
        <div class="col-xs-12 col-md-12 selected-all">
			
			<div class="input-group" style="float: left;">
	            <button type="button" class="btn btn_pl btn-link" ng-click="examine_del()">审核通过</button>
	            <button type="button" class="btn btn_pl btn-link" ng-click="examine_through()">审核不通过</button>
	            <!--<button type="button" class="btn btn_pl btn-link" ng-click="examine_reject()">批量未通过</button>-->
	       </div>

            <div class="input-group apply-sousuo">
                <input type="text" placeholder="请输入关键字" class="input-sm form-control">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                    </span>
            </div>
          </form>
        </div>

    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th width="30">
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
                    <div class="btn-group Examine">
                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">是否续借 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getWaitExamineList',['pid'=>1])}}">是</a>
                            </li>
                            <li>
                                <a href="{{route('msc.admin.resourcesManager.getWaitExamineList',['pid'=>2])}}">否</a>
                            </li>
                        </ul>
                    </div>

                </th>
                <th>
                    <div class="btn-group Examine">
                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">设备状态 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#">已审核</a>
                            </li>
                            <li>
                                <a href="#">未审核</a>
                            </li>
                        </ul>
                    </div>

                </th>
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
                <td>{{$item ->  id}}</td>
                <td>{{$item ->  resourcesTool  ->name}}</td>
                <td>{{$item ->  begindate or '未知'}}-{{$item->enddate or '未知'}}</td>
                <td>{{$item ->  code}}</td>
                <td>{{is_null($item ->  lenderInfo)? '-':$item->lenderInfo->name}}</td>
                <td>{{$item ->  detail}}</td>
                <td style="text-align: center;"><span class="state3">{{ $item   ->pid? '是':'否' }}</span></td>
                <td>
                    @if($item['apply_validated'] == 0) 未审核
                    @elseif($item['apply_validated'] == 1) 已审核
                    @endif
                </td>
                <td>
                    <div class="opera" end-time="{{$item->enddate or '未知'}}">
                        <span class="read  state1 modal-control" data-toggle="modal" data-target="#myModal" flag="yes">审核通过</span>
                        <span class="Scrap state2 modal-control" data-toggle="modal" data-target="#myModal" flag="no">审核不通过</span>
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
            {!! $pagination->render() !!}
        </div>
    </form>

</div>



@stop{{-- 内容主体区域 --}}

@section('layer_content')
    <form class="form-horizontal" id="Form2" novalidate="novalidate">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">审核不通过</h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label class="col-sm-3 control-label">不通过理由：</label>
                <div class="col-sm-9">
                    <select class="form-control" id="choose">
                        <option value="已损坏">已损坏</option>
                        <option value="已借出">已借出</option>
                        <option value="">自定义理由</option>
                    </select>
                    <textarea id="comment" name="comment" class="form-control" required="" aria-required="true"></textarea>

                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id='apply-no' class="notAgree" data-dismiss="modal" aria-hidden="true">提交</button>
        </div>
    </form>
    <!-- 审核通过 -->
    <form class="form-horizontal" id="Form3" novalidate="novalidate">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">审核通过</h4>
        </div>
        <div class="modal-body">

            <div class="form-group " id="time_set" >
                <div class="time_set">
                    <label class="left intro">请于</label><span ><input placeholder="开始日期" class="form-control layer-date laydate-icon" id="start"></span>
                    <label>至&nbsp;</label><span><input placeholder="结束日期" class="form-control layer-date laydate-icon" id="end"></span>
                </div>
                <br/>
                <label>时间段内带上以下材料到技能中心模型外借。</label>
            </div>


            <div class="hr-line-dashed"></div>

            <div class="form-group">
                <label class="col-sm-3 control-label">需带材料：</label>
                <div class="col-sm-9">
                    <table class="checkbox">
                        <tr>
                            <td>
                                <label class="check_label">
                                    <div class="check_icon"></div>
                                    <input  type="checkbox" class="check_ids" value="辅导员证明材料">
                                </label>
                            </td>
                            <td>&nbsp;辅导员证明材料</td>
                        </tr>
                        <tr>
                            <td>
                                <label class="check_label">
                                    <div class="check_icon"></div>
                                    <input  type="checkbox" class="check_ids" value="借条">
                                </label>
                            </td>
                            <td>&nbsp;借条</td>
                        </tr>
                        <tr>
                            <td>
                                <label class="check_label">
                                    <div class="check_icon"></div>
                                    <input  type="checkbox" class="check_ids" value="学生证">
                                </label>
                            </td>
                            <td>&nbsp;学生证</td>
                        </tr>
                        <tr>
                            <td>
                                <label class="check_label">
                                    <div class="check_icon"></div>
                                    <input  type="checkbox" class="check_ids" value="身份证">
                                </label>
                            </td>
                            <td>&nbsp;身份证</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success agree" id="apply-yes" data-dismiss="modal" aria-hidden="true">确&nbsp;定</button>
        </div>
    </form>
    <script>
$(function(){

    /*模态框内容选择*/
    $('.opera').on('click','.modal-control',function(){
        var num = ['no','yes'];
        if($(this).attr('flag')==num[0]){
            //清空，获取id
            $('#Form2').attr('value',$(this).parent().parent().parent().attr('value'));
            $('#comment').val('');
            $('#Form2').show();
            $('#Form3').hide();
        }else{
            //时间控制  只能是外借时间之前的时间  2015-12-14 mao bug
            var end_time = $(this).parent().attr('end-time');
            start.max = end_time;
            end.max = end_time;

            $('#Form3').attr('value',$(this).parent().parent().parent().attr('value'));
            $('#start').val('');
            $('#end').val('');
            $('#Form3').find('.check_icon').removeClass('check');
            $('#Form3').show();
            $('#Form2').hide();
        }
    });

    /*通过审核*/
    $('#apply-yes').click(function(){
        var req = {};
        var materials = [];
        //材料勾选
        $('#Form3 .checkbox').find('tr').each(function(index,elem){
            var thisSibling = $(elem).find('div').siblings();
            if($(elem).find('div').hasClass('check')){
                materials.push(thisSibling.val());
            };
        });
        req['time_start'] = $('#start').val();
        req['time_end'] = $('#end').val();
        //防止join报错 
        if(materials==null){
            req['idcard_type'] = materials;
        }else{
            req['idcard_type'] = materials.join();
        }
        req['id'] = $('#Form3').attr('value');
        req['apply_validated'] = 1;
        $.ajax({
            type:"post",
            url:"{{route('msc.admin.resourcesManager.postExamineBorrowingApply')}}",
            data:req,
            success:function(res){
                if(res.code==1){
                    location.reload();
                }else{
                    layer.alert((res.message).split(':')[1]);
                }
            }
        });
    });


    /*审核不通过*/
    $('#apply-no').click(function(){
        var req = {};
        req['detail'] = $('#comment').val();
        req['apply_validated'] = -1;
        req['id'] = $('#Form2').attr('value');
        $.ajax({
            type:"post",
            url:"{{route('msc.admin.resourcesManager.postExamineBorrowingApply')}}",
            data:req,
            success:function(res){
                if(res.code==1){
                    location.reload();
                }else{
                    alert(res.message);
                }
            }
        });
    })

    /**
     *多选框
     */
    $('.check_ids').click(function(){

        var thisSibling = $(this).siblings();
        //判断check
        if(thisSibling.hasClass('check')){
            thisSibling.removeClass('check');
        }else{
            thisSibling.addClass('check');
        }
    });



})
    </script>
@stop{{-- 内容主体区域 --}}
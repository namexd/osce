@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .allNums{
            margin: 20px 0;
        }
        select{
            border: none;
        }
        .modal-dialog{
            margin: 300px auto;
        }
        #comment{
            margin-top: 10px;
            min-height: 150px;
        }
        .modal-footer{
            border-top: none;
            text-align: center;
        }
        .state2 {
            color: #ed5565;
        }
        .change-select p{
            font-size: 14px;
            line-height: 30px;
        }
        button.btn.btn-white.dropdown-toggle {
            border: none;
            font-weight: bolder;
        }
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/js/all_checkbox.js')}}"></script>
@stop

@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row table-head-style1 ">
            <div class="head-opera col-xs-3 col-md-2">
                <button type="button" class="btn btn-link btn-sm">批量通过</button>
                <button type="button" class="btn btn-link btn-sm">批量不通过</button>
            </div>
            <form method="get" action="{{route('msc.admin.lab.getUrgentApplyList')}}">
                <div class="col-xs-3 col-md-2">
                    <div class="input-group">
                            <span class="input-group-btn" id="search">
                                <button type="button" class="btn btn-sm btn-default"><i class="fa fa-calendar"></i></button>
                            </span>
                        <input type="text" placeholder="" class="input-sm form-control" name="date" value="" id="start">

                    </div>
                </div>
                <div class="col-xs-6 col-md-2">
                        <div class="input-group">
                            <input type="text" placeholder="搜索" class="input-sm form-control" name="keyword" value="">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                </div>
            </form>
        </div>
        <div class="container-fluid ibox-content">
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
                    <th>开放实验室</th>
                    <th>
                        日期
                    </th>
                    <th>时间</th>
                    <th>
                        编号
                    </th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">预约人<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{route('msc.admin.lab.getUrgentApplyList')}}?order=&orderby=asc">升序</a>
                                </li>
                                <li>
                                    <a href="{{route('msc.admin.lab.getUrgentApplyList')}}?order=&orderby=desc">降序</a>
                                </li>
                            </ul>
                        </div>
                    </th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">课程名称<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{route('msc.admin.lab.getUrgentApplyList')}}?order=&orderby=asc">升序</a>
                                </li>
                                <li>
                                    <a href="{{route('msc.admin.lab.getUrgentApplyList')}}?order=&orderby=desc">降序</a>
                                </li>
                            </ul>
                        </div>
                    </th>
                    <th>
                        预约理由
                    </th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">实验室状态<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{route('msc.admin.lab.getUrgentApplyList')}}?order=&orderby=asc">升序</a>
                                </li>
                                <li>
                                    <a href="{{route('msc.admin.lab.getUrgentApplyList')}}?order=&orderby=desc">降序</a>
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
                        <tr>   
                            <td>
                                <label class="check_label checkbox_input">
                                    <div class="check_icon"></div>
                                    <input type="checkbox" class="check_id" name="check_id[]" value="" />
                                </label>
                            </td>
                            <td>{{ $item['id'] }}</td>
                            <td>{{$item['lab_name'] or '-'}}</td>
                            <td>{{$item['apply_date']}}</td>
                            <td>{{date('H:i',strtotime($item->OpenLabCalendar->begintime))}}-{{date('H:i',strtotime($item->OpenLabCalendar->endtime))}}</td>
                            <td>{{$item['lab_code'] or '-'}}</td>
                            <td>{{$item['teacher_name'] or '-'}}</td>
                            <td>{{$item['courses_name'] or '-'}}</td>
                            <td>{{$item['detail'] or '-'}}</td>
                            <td>
                                {{$statusAttrNames[$item['lab_status']]}}
                            </td>
                            <td class="opera" value="{{ $item['id'] }}">
                                <span class="read  state1 modal-control" data-toggle="modal" data-target="#myModal" flag="yes">审核通过</span>
                                <span class="Scrap state2 modal-control" data-toggle="modal" data-target="#myModal" flag="no">审核不通过</span>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
            <div class="pull-left allNums">
                已选择<span class="sum">0</span>条
            </div>
            <div class="pull-right">
                <?php echo $pagination->render() ?>
            </div>
        </div>

    </div>


@stop
@section('layer_content')
    <form class="form-horizontal" id="Form2" novalidate="novalidate">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">审核不通过</h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label class="col-sm-3 control-label">不通过原因</label>
                <div class="col-sm-9">
                    <select class="form-control" id="choose">
                        <option value="已损坏">已损坏</option>
                        <option value="已借出">已借出</option>
                        <option value="other">自定义原因</option>
                    </select>
                    <textarea id="comment" name="comment" class="form-control" required="" aria-required="true" disabled="disabled"></textarea>

                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id='apply-no' class="notAgree" data-dismiss="modal" aria-hidden="true">确&nbsp;定</button>
        </div>
    </form>
    <!-- 审核通过 -->
    <!-- 通过 -->
    <form class="form-horizontal" id="Form3" novalidate="novalidate">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">审核通过</h4>
        </div>
        <div class="modal-body" id="valueName">
        <div class="emergency-1" style="display:none;">是否通过情急预约申请？</div>
        <div class="emergency-2">
            <p>该突发事件申请与以下已预约事件有冲突</p>
            <div  id="meet-info">
                <p class="edit state2">预约一：陈老师  课程A  开发实验室A  2015/09/18 08:00-15:00</p>
            </div>
            <p>请执行以下课程变更。</p>
            <div class="form-group">
                <label class="col-sm-2 control-label">调整方式</label>
                <div class="col-sm-10">
                    <input class="form-control" id="recommend-edit" value="取消已预约课程" disabled="disabled" style="background:#fff;">
                </div>
            </div>
            <br/>
            <hr/>
            <div class="change-select">
            <!-- 推荐选择 -->
                  <div class="change-recommend">
                      <div class="form-group">
                        <label class="col-sm-2">推荐</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" style="height:146px;">技能中心有紧急课程，课程取消!</textarea>
                        </div>
                      </div>
                  </div>  
                  <div class="change-edit" style="display:none;">
                      <div class="form-group">
                          <label class="col-sm-2 control-label">现时安排</label>
                          <div class="col-sm-10"><p>开放性伤口包扎课程：临床技能中心7F 2015/09/18 08:00-15:00</p></div>
                      </div>
                      <div class="form-group">
                          <div class="col-sm-2 control-label"><label>变更安排</label></div>
                          <div class="col-sm-10"><p>开放性伤口包扎课程</p></div>
                      </div>
                      <div class="form-group">
                          <div class="col-sm-2 control-label"><label>&nbsp;</label></div>
                          <div class="col-sm-10">
                            <select class="form-control" id="classroom-chioce">
                                <option value="1">临床技能中心7F</option>
                                <option value="2">临床技能中心8F</option>
                            </select>
                          </div>
                      </div>
                  </div>
            </div>
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success agree" id="apply-yes" data-dismiss="modal" aria-hidden="true">确&nbsp;定</button>
        </div>
    </form>
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script>
        $(function(){
            /**
             *模态框内容选择
             */
            $('.opera').on('click','.modal-control',function(){
                var num = ['no','yes'];
                if($(this).attr('flag')==num[0]){
                    $('#Form2').show();
                    $('#Form3').hide();
                }else{
                    $('#Form3').show();
                    $('#Form2').hide();
                }
            });

            /*通过选择*/
            $('#recommend-edit').change(function(){
                var thisElement = $(this);
                if(thisElement.val()==1){
                    $('.change-recommend').show();
                    $('.change-edit').hide();
                }else{
                    $('.change-recommend').hide();
                    $('.change-edit').show();
                }
            });

            /**
             *不通过选择
             */
            $('#comment').val($('#choose').val());//初始化
            $('#choose').change(function(){
                var thisElement = $(this);
                if(thisElement.val()=='other'){
                    $("#comment").removeAttr("disabled");
                    $('#comment').val('');
                }else{
                    $('#comment').attr('disabled','disabled');
                    $('#comment').val(thisElement.val());

                }
            });

            /**
             *不通过id设置
             */
            $(".state2").click(function(){
                var $currentId=$(this).parent().attr('value');
                $("#Form2").attr("openid",$currentId);
            })

            /**
             *不通过请求
             */
            $("#apply-no").click(function(){
                var str="";
                if($("#choose option:selected").text()=="自定义原因"){
                    str=$("#comment").val();
                }else{
                    str=$("#choose option:selected").text();
                }
                $.ajax({
                    url:"{{route('msc.admin.lab.postRefundEmergencyApply')}}",
                    type:"post",
                    dataType:"json",
                    data:{
                        id:$("#Form2").attr("openid"),
                        reject:str
                    },
                    success: function(res) {
                        if(res.code!=1){
                            layer.alert(res.message);
                        }else{
                            location.reload();
                        }
                    }
                });
            })

            /**
             *通过设置id
             *得到冲突信息
             */
            $(".state1").click(function(){
                var $currentId=$(this).parent().attr('value');
                $("#Form3").attr("openid",$currentId);
                //数据请求
                $.ajax({
                    type:"get",
                    async:true,
                    url:"{{route('msc.admin.lab.getAgreeEmergencyApply')}}",
                    data:{id:$currentId},
                    success:function(res){
                        if(res.code!=1){
                            console.log(res.message)
                        }else{

                            var data = res.data.rows;
                            var num = ['一','二','三','四','五','六','七'];
                            var txt = '';
                            for(var i in data){
                                txt += '<p class="edit state2">预约'+num[i]+'：&nbsp;'+ data[i].teacher_name +'&nbsp;&nbsp;'+ data[i].name +'&nbsp;&nbsp;'+ data[i].lan_name +'&nbsp;&nbsp;'+ data[i].currentdate +'&nbsp;&nbsp;'+ data[i].time+'</p>';
                            }
                            $('#meet-info').html(txt);
                        }
                    }
                });
            })

            /**
             *通过请求
             */
            $("#apply-yes").click(function(){
                $.ajax({
                    url:"{{route('msc.admin.lab.postAgreeEmergencyApply')}}",
                    type:"post",
                    dataType:"json",
                    data:{
                        id:$("#Form3").attr("openid"),
                        notice:$('.change-recommend').find('textarea').val()
                    },
                    success: function(res) {
                        if(res.code!=1){
                            layer.alert(res.message);
                        }else{
                            //location.reload();
                        }
                    }
                });
            })

            /**
             *时间选择
             */
            var start = {
                elem: "#start",
                format: "YYYY-MM-DD",
                min: laydate.now(),
                max: "2099-06-16",
                istime: false,
                istoday: false,
                choose: function (a) {
                    end.min = a;
                    end.start = a
                }
            };
            //触发
            laydate(start);


        })
    </script>
@stop{{-- 内容主体区域 --}}
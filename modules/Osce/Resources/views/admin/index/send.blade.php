@extends('osce::admin.layouts.admin_index')
@section('only_css')

@stop
@section('only_js')

    <script type="text/javascript">
        function disp_confirm()
        {
            layer.confirm("确认发送",{
                title:'删除',
                btn: ['确定','取消']
            },function(its){
                layer.close(its);
                //加载中
                var index = layer.load(0, {
                    shade: [0.1,'#fff'] //0.1透明度的白色背景
                });
                $.ajax({
                    type:'get',
                    async:true,
                    url: "{{route('osce.admin.index.getReleaseScore')}}",
                    data:{id: "{{$exam_id}}", 'confirm': 1},
                    success:function(data){
                        if(data.code==1){
                            layer.close(index);
                            layer.msg(data.message,{skin:'msg-success',icon:1});
                        }else{
                            layer.close(index);
                            layer.msg(data.message,{'skin':'msg-error',icon:1})
                        }
                    },
                    error:function(data){
                        layer.close(index);
                        layer.msg(data.message,{skin:'msg-error',icon:1});
                    }
                })
            });
        }
    </script>
@stop
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight" style="color: black;">
        <div class="ibox float-e-margins">
            <table>
                <tr>
                    <th style="width: 35px;">序号</th>
                    <th style="width: 70px;">学生姓名</th>
                    <th style="width: 100px;">手机号码</th>
                    <th style="width: 61px;">总成绩</th>
                    <th style="width: 81px;">折算总成绩</th>
                    <th style="width: 1500px;">短信内容</th>
                </tr>
                @forelse($data as $key => $item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->mobile}}</td>
                    <td>{{$item->original_score_total}}</td>
                    <td>{{round($item->score_total, 2)}}</td>
                    <td>{{$item->name}} 同学，2016年临床医学专业临床技能多站考试你的成绩为 {{round($item->score_total, 2)}} 分（总分200分）。如需查看成绩反馈，请于6月13~24日10点~17点到临床教学楼临床技能中心4039办公室查询{{config('osce.sms_signature','【华西临床技能中心】')}}</td>
                </tr>
                @empty
                @endforelse
            </table>
        </div>

        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-2 time-modify">
                <button class="btn btn-primary" onclick="disp_confirm()" >给学生发送成绩短信</button>
            </div>
        </div>
        <br/><br/><br/><br/><br/><br/>
    </div>
@stop

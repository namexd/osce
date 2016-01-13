@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/booking/css/booking.css')}}" rel="stylesheet" type="text/css" />

<style>

</style>
@stop

@section('only_head_js')
    <Script type="text/javascript">
    $(document).ready(function(){
        $("#order_time").val(nextday);
        var qj={DateTime:nextday};
        getlist(qj);
        //加载入页面的时候，自动加载隔天的实验室
        $("#order_time").change(function(){
            var qj=getqj();
            getlist(qj);
        });

        $("#select_submit").click(function(){
            select_ban();
            get_layer();

        });

        //弹出层选择楼层
        function select_ban(){
            $("#ban").change(function(){
                var floor_top =$(this).find("option:selected").attr("floor_top");
                var floor_bottom = parseInt($(this).find("option:selected").attr("floor_buttom"));
                $("#floor").empty();
                $("#floor").append('<option value="">全部楼层</option>');
                for(var i=1;i<=floor_bottom;i++){
                    $("#floor").append('<option value="-'+i+'">-'+i+' 楼</option>');
                }
                for(var i=1;i<=floor_top;i++){
                    $("#floor").append('<option value="'+i+'">'+i+' 楼</option>');
                }
                submit_select();//改变之后允许执行筛选
            })
        }
        function submit_select(){
            $("#submit_layer").click(function(){
                getlist(qj);
            });
        }
        function getqj(){//得到所有的查询条件内容
            var floor_id= $("#ban").find("option:selected").attr("value");
            var floor_num= $("#floor").find("option:selected").attr("value");
            var DateTime=$("#order_time").val();
            var qj={floor_id:floor_id,floor_num:floor_num,DateTime:DateTime}
            return qj;
        }

        function  getlist(qj){ //查询ajax查询
            var target_url = "{{ route('msc.Laboratory.ApplyOpenLaboratory') }}"
            $.ajax({
                url:"{{ route('msc.Laboratory.OpenLaboratoryListData') }}", /*${ctx}/*/
                type: "get",
                dataType: "json",
                cache: false,
                data:qj,
                success: function (result) {
                    if(result.code==1){
                        $(".manage_list").empty();
                        $(result.data.rows.ClassroomApplyList.data).each(function(){
                            $(".manage_list").append('<a href="'+target_url+'?DateTime='+qj.DateTime+'&id='+this.id+'">'
                                    +'<div class="all_list">'
                                    +'<div class="w_85 left">'
                                    +'<p>'+this.name+'</p>'
                                    +'<p><span>'+this.floor_info.address+this.floor_info.name+this.floor+"楼"+this.code+'</span></p></div>'
                                    +'<div class="w_15 right"><i class="fa fa-angle-right i_right" style="margin-top: 10px"></i>'
                                    +'</div></div></a>');
                        })
                    }else{

                    }
                }
            })
        }

    })

    </script>
@stop

@section('content')
<div class="user_header">
   预约实验室
</div>
<div class="main_body">
    <div class="time_select w_90">
        <div class="left2">
            <input id="order_time"  name="begindate" type="date"  placeholder="查询日期" />
        </div>
        <div class="right2">
            <button class="btn4" id="select_submit">筛选</button>
        </div>
    </div>
    <div class="manage_list">

    </div>

</div>

<div id="sidepopup_layer">
    <div class="box_hidden">
    </div>

    <div class="box_content" >
        <p class="font16 title">请选择具体楼栋或楼层</p>
        <div class="w_96">
            <select   class="select1" id="ban"  style="width:100%;">
                <option value="" >全部楼栋</option>
                @foreach($FloorData as $val)
                    <option value="{{@$val['id']}}" floor_top="{{ @$val['floor_top'] }}" floor_buttom="{{ @$val['floor_bottom'] }}">{{@$val['name']}}</option>
                @endforeach
            </select>

            <select   class="select1 mart_10"  id="floor"  style="width:100%;">
                <option value="">全部楼层</option>
            </select>
            <button id="submit_layer" type="button" class="btn1">确定</button>
        </div>
    </div>
</div>
@stop
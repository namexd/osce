<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <style>
    #area{
      font-family: 黑体;
      width: 683px;
      height: 513px;
      border: 1px solid;
    }
    #top{
      background-color:#666 ;
      color: #fff;
      width: 100%;
      height: 32px;
      line-height: 30px;
    }
    #top_left{
      padding-left: 30px;
      float: left;
    }
    #top_right{
      float: right;
      padding-right: 10px;
    }
    #body{
      width: 100%;
      height: 390px;
    }
    #body div{
      height: 20%;
      padding-top: 20px;
      padding-left: 60px;
      font-size: 25px;
    }
    #bottom{
      margin-top: 35px;
      height: 10%;
      width: 100%;
    }
  </style>
  <script src="{{asset('osce/admin/plugins/js/jquery-2.1.1.min.js')}}"></script>
  <script>
    $(function(){
      show();
      setInterval(show,5000);
    });
    function show(){
      var exam_id = $('#exam_id').val();
      var station_id = $('#station_id').val();

      $.ajax({
        url:"{{route('osce.billboard.getStudent')}}",//请求的地址
        data: {exam_id: exam_id, station_id: station_id},
        type:"get",//请求方式
        async:true,//设置是否异步
        dataType:"json",//指定响应回来的数据
        success: function(data){//成功后调用
//          var data =eval("("+data+")");
//          $("#exam_station").html(data.exam_station);
          if (data.code != 1) {
            $("#student").html("当前没有考生");
          } else {
            $("#student").html(data.data.name);
          }


        },
        error: function(data){//请求发生错误时调用

        }
      })
    }
  </script>
</head>
<body>

<div id="area">
  <input type="hidden" id="exam_id" value="{{$data['exam_id']}}">
  <input type="hidden" id="station_id" value="{{$data['station_id']}}">
  <div id="top">
    <div id="top_left">2015年度OSCE考试第3期</div>
    <div id="top_right"><img src="{{asset('osce/images/u24.png')}}" width="22px" height="22px" align="center"></div>
  </div>
  <div id="body">
    <div class="title">
      <span>考站：</span>
      <span id="exam_station">{{$data['station_name']}}</span>
    </div>
    <div class="title">
      <span>考生：</span>
      <span id="student"></span>
    </div>
    <div class="title">
      <span>时间：</span>
      <span id="time">{{$data['mins']}}分钟，时间到请停止考试，根据腕表提示完成考试</span>
    </div>
    <div class="title">
      <span>病例：</span>
      <span id="case">{{$data['case_name']}}</span>
      <div id="description">{{$data['case_description']}}</div>
    </div>
  </div>
  <div id="bottom">
    <img src="{{asset('osce/images/u4.png')}}" width="135px" height="53px" align="right">
  </div>
</div>

</body>
</html>
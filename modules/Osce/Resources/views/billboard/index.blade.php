<!DOCTYPE html>
<html lang="zh-CN">
<head>

  <style>
    *{
      margin: 0;
      padding: 0;
    }
    #area{
      font-family: 黑体;
      width: 1050px;
      height: 1680px;
      font-size: 60px;
    }
    #body{
      width: 100%;
      height: 80%;
    }
    .title{
      height: 15%;
      margin-left: 100px;
    }
    #description{
      min-height: 60px;
      margin-left: 100px;
      margin-right: 100px;
    }
    #exam_station{
      display: block;
      margin-right: 100px;
      text-align: center;
    }
    #time{
      color: red;
    }
    #pic{
      position: absolute;
      bottom: 0;
      right:0;
    }
    #background{
      position: absolute;
      bottom: 0;
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
        url:"{{route('osce.billboard.getStudent')}}",
        data: {exam_id: exam_id, station_id: station_id},
        type:"get",
        async:true,
        dataType:"json",
        success: function(data){
          if (data.code != 1) {
            $("#student").html("当前没有考生");
          } else {
            $("#student").html(data.data.name);
          }
        }
      })
    }
  </script>
</head>
<body>
<div id="area">
  <input type="hidden" id="exam_id" value="{{$data['exam_id']}}">
  <input type="hidden" id="station_id" value="{{$data['station_id']}}">
  <div id="body">
    <div class="title">
      <span id="exam_station">{{$data['station_name']}}</span>
    </div>
    <div class="title">
      <span>考生：</span>
      <span id="student"></span>
    </div>
    <div class="title">
      <span>时间：</span>
      <span id="time">{{$data['mins']}}分钟</span><span>，时间到请停止考试</span>
    </div>
    <div class="title">
      <span>病例：</span>
      <span id="case">{{$data['case_name']}}</span>
    </div>
    <div id="description">{{$data['case_description']}}
    </div>
  </div>
  <div id="pic">
    <img src="{{asset('osce/images/u4.png')}}" width="138px" height="55px" align="right">
  </div>
  <div id="background">
    <img src="{{asset('osce/images/bg_02.png')}}" align="bottom">
  </div>
</div>
</body>
</html>
<!DOCTYPE html>
<html lang="zh-CN">
<head>

  <style>
    *{
      margin: 0;
      padding: 0;
    }
    body{
      width: 1050px;
      height: 1680px;
    }
    #area{
      font-family: 微软雅黑;
      width: 100%;
      height: 100%;
      font-size: 60px;
      color: #676a6c;
    }
    #body{
      padding-top: 60px;
      width: 100%;
      height: 80%;
    }
    .title{
      height: 10%;
      margin-left: 100px;
    }
    #description{
      display: block;
      width: 900px;
      min-height: 60px;
    }
    #exam_station {
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
      var text = $('#description').text();
      $('#description').html(text);
    });
    function show(){
      var exam_id = $('#exam_id').val();
      var station_id = $('#station_id').val();
      var url = $('#route').val();
      $.ajax({
        url: url,
        data: {exam_id: exam_id, station_id: station_id},
        type: "get",
        dataType: "json",
        success: function (data) {
          if (data.code != 1) {
            $("#student").html("当前没有考生");
          } else {
            $("#student").html(data.data.student_name);
            console.log(data.data.room_name);
            if(data.data.room_name == ''){
              $("#roomName").html('完成此项考试后请交还考试卡结束考试');
            }else {
              $("#roomName").html('完成考试后请到'+data.data.room_name+'考场进行下一项考试');
            }

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
  <input type="hidden" id="route" value="{{route('osce.billboard.getStudent')}}">
  <div id="body">
    <div class="title">
      <span id="exam_station">{{$data['station_name']}}</span>
    </div>
    <div class="title">
      <span>考生:</span>
      <span id="student"></span>
    </div>
    <div class="title">
      <span>时间:</span>
      <span id="time">{{$data['mins']}}分钟</span><span>，时间到请停止考试</span>
    </div>
    <div class="title">
      <span>病例简介:</span>
      <span id="description">{{$data['case_description']}}</span>
      {{--<span id="case">{{$data['case_name']}}</span>--}}
    </div>
    {{--<div id="description">{{$data['case_description']}}
    </div>--}}
  </div>
  <div class="title">
    <span id="roomName">完成此项考试后请交还考试卡结束考试</span>
  </div>
  <div id="pic">
    <!--img src="{{--asset('osce/images/u4.png')--}}" width="100%" height="100%" align="right"-->
  </div>
  <div id="background">
    <img src="{{asset('osce/images/bg_02.png')}}" align="bottom">
  </div>
</div>
</body>
</html>
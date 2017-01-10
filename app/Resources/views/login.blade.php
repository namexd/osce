<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" ng-app="myApp" class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>四川大学-<!--速立达-->医院</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{asset('msc/admin/plugins/css/bootstrap.min.css?v=3.4.0')}}" rel="stylesheet">
    <link href="{{asset('msc/admin/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('msc/admin/css/style.css')}}" rel="stylesheet">
</head>
<style>
    .tag{
        background:lightskyblue;
    }
</style>

<body>
<style>
    body{background-color: #f3f3f4;}
    .logo{padding-bottom:20px;}
    .logo img{margin-left:-10px;}
    h3{text-align:left}
    h3.hh{margin-bottom:50px;color:#99a3b1}
    h3.tt{color:#999;margin:20px 0 10px;padding-left:2px;font-size:14px}
    .form-control{padding:0 12px;border-radius:3px;}
    .btn{margin-top:20px;font-weight:bold}
    .clearfix:after{visibility:hidden;display:block;font-size: 0;content:" ";clear: both;height:0}
    .clearfix{*zoom:1;}
    .txt{color:#b7bbc2;font-size:12px;font-weight:100;}
    .check{float:left;margin-left:3px;}
    .btn-primary:hover{background:#286090;}
    .form-control:focus{border-color: #408aff!important;}
    .btn-primary{background: #408aff;color: #408aff;color: #fff;}
    .btn-primary:hover{border:1px solid #286090}
</style>

<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <div class="logo">
            <!--<h1 class="logo-name">LOGO</h1>-->
            <img src="{{asset('msc/images/logo2.png')}}" width="300"/>
        </div>
        <!--<h3 class="hh">West China School of Medicial / West China Hospital Sichuan University.</h3>-->
        <form class="m-t" role="form" id="loginForm" method="post" action="{{ route('login.op') }}">
            <h3 class="tt">用户名</h3>
            <div class="form-group">
                <input type="text" class="form-control" id="username" name="username"  placeholder="用户名">
            </div>
            <h3 class="tt">密码</h3>
            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="密码">
            </div>
            <input type="hidden" name="grant_type" id="grant_type" value="password">
            <input type="hidden" name="client_id"  id="client_id" value="ios">
            <input type="hidden" name="client_secret" id="client_secret" value="111">
            <div class="clearfix">
                <label class="check">
                    <input style="position: relative;top:2px" type="checkbox" id="checkbox"/><span class="txt">&nbsp;记住密码</span>
                </label>
                <a style="float:right" href="javascript:;"><small class="txt">忘记密码？</small></a>
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b">登 录</button>
        </form>
    </div>
</div>
</body>
</html>
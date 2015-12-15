@extends('msc::admin.layouts.admin')
@section('only_css')

    <style type="text/css">
        .main-content{
            margin: 20px;
        }
        .content-left{
            width: 30%;
            margin-right: 20px;
            height: 800px;
        }
        .content-right{
            width: 68%;
            height: 800px;
        }
        .serach-box input{
            height: 34px;
            width: 85%;
        }
        .serach-box button{
            width: 15%;
        }
        .ibox-content{
            clear: inherit;
            float: left;
        }
        .classroom-list{
            margin-top: 50px;
        }
        ul,p{
            padding: 0;
            margin: 0;
        }
        .first-level ul{
            display: none;
        }
        .first-level p{
            cursor: pointer;
            padding: 10px 0;
            border-bottom: 1px solid #dddddd;
        }
        .first-level>p{
            font-weight: 700;
        }
        .first-level i{
            float: right;
        }
        .second-level li:hover{
            background-color: #ccc;
        }
        .second-level li{
            padding: 5px;
        }
        .glyphicon-chevron-down{
            display: none;
        }
    </style>
@stop

@section('only_js')
    <script>
        $(function(){
            //二级菜单展开
            $(".first-level>p").click(function(){
                if($(this).attr("flag")=="false"){
                    $(this).attr("flag","true");
                    $(this).find(".glyphicon-chevron-right").hide();
                    $(this).find(".glyphicon-chevron-down").show();
                    $(this).next().show();
                }else{
                    $(this).attr("flag","false");
                    $(this).find(".glyphicon-chevron-right").show();
                    $(this).find(".glyphicon-chevron-down").hide();
                    $(this).next().hide();
                }
            })
            //三级菜单
            $(".second-level>p").click(function(){
                if($(this).attr("flag")=="false"){
                    $(this).attr("flag","true");
                    $(this).find(".glyphicon-chevron-right").hide();
                    $(this).find(".glyphicon-chevron-down").show();
                    $(this).next().show();
                }else{
                    $(this).attr("flag","false");
                    $(this).find(".glyphicon-chevron-right").show();
                    $(this).find(".glyphicon-chevron-down").hide();
                    $(this).next().hide();
                }
            })
        })
    </script>
@stop
@section('content')
    <div class="row  main-content">
        <div class="content-left ibox-content">
            <div class="serach-box">
                <input type="text" placeholder="按教室编号搜索"><button type="button" class="btn btn-primary">搜索</button>
            </div>
            <nav class="classroom-list">
               <ul>
                   <li class="first-level">
                       <p flag="false">临床医学楼
                           <i class="glyphicon glyphicon-chevron-right"></i>
                           <i class="glyphicon glyphicon-chevron-down"></i>
                       </p>
                       <ul>
                           <li class="second-level">
                               <p flag="false">一层
                                   <i class="glyphicon glyphicon-chevron-right"></i>
                                   <i class="glyphicon glyphicon-chevron-down"></i>
                               </p>
                               <ul>
                                   <li class="third-level">101</li>
                                   <li class="third-level">102</li>
                                   <li class="third-level">103</li>
                               </ul>
                           </li>

                       </ul>
                   </li>
                   <li class="first-level">
                       <p flag="false">新八医学楼
                           <i class="glyphicon glyphicon-chevron-right"></i>
                           <i class="glyphicon glyphicon-chevron-down"></i>
                       </p>
                       <ul>
                           <li class="second-level">
                               <p flag="false">一层
                                   <i class="glyphicon glyphicon-chevron-right"></i>
                                   <i class="glyphicon glyphicon-chevron-down"></i>
                               </p>
                               <ul>
                                   <li class="third-level">101</li>
                                   <li class="third-level">102</li>
                                   <li class="third-level">103</li>
                               </ul>
                           </li>

                       </ul>
                   </li>
               </ul>
            </nav>
        </div>
        <div class="content-right ibox-content">

        </div>
    </div>
@stop
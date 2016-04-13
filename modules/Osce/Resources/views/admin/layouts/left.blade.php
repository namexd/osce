        <!--左侧导航开始-->
        <style>
         .img-circle{height:46px;}
         .person-info{margin:20px 0;}
         .dropdown.profile-element{margin-left: 20px;}
         .person-info span{
            color: #fff;
            margin-left: 5px;
        }
         .nav-header{height: 50px;}
         .nav-header span{
            font-size:20px;
            line-height: 20px;
            color:#fff;
            font-weight: bold;
            font-family: "微软雅黑";
         }
         .navbar-static-side {background: #2b3a40;}
         .nav > li.active {
            border-left: 4px solid #19aa8d;
            background: #1d2a2f;
        }
        .navbar-default .nav > li > a:hover,
        .navbar-default .nav > li > a:focus {
          background-color: #1d2a2f;
          color: white;
        }
        </style>
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header" style="background:#16beb0;">
                        <div class="profile-element">
                           <span>OSCE管理系统</span>

                        </div>
                        <div class="logo-element">OSCE
                        </div>
                    </li>
                    <li class="person-info" style="display:none;">
                        <div class="profile-element">
                            <div class="dropdown profile-element">
                                <span><img alt="image" class="img-circle" src="{{asset('osce/admin/images/profile_small.jpg')}}"></span>
                                <span>Alexander Pierce</span>
                            </div>
                        </div>
                        <div class="logo-element">
                        </div>
                    </li>
                        @forelse($list as $item)
                        <li>
                            <a href="#"><i class="fa {{$item->ico}}"></i> <span class="nav-label">{{$item['name']}}</span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                            @forelse($item['child'] as $value)
                                <li>
                                    <a class="{{$value->ico}}" href="{{empty($value['url'])? 'javascript:;':route($value['url'])}}">{{$value['name']}}</a>
                                </li>
                            @empty
                            @endforelse
                            </ul>
                        <li>
                        @empty
                        @endforelse
                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->


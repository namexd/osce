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
                    @if($role_id==config('config.superRoleId'))
                        <li class="active">
                            <a href="#"><i class="fa fa-list-alt"></i> <span class="nav-label">资源管理</span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a class="active J_menuItem" href="{{route('osce.admin.case.getCaseList')}}">病例管理</a>
                                </li>
                                <li><a class="active J_menuItem" href="{{route('osce.admin.invigilator.getInvigilatorList')}}">人员管理</a>
                                </li>
                                <li><a class="active J_menuItem" href="{{route('osce.admin.Station.getStationList')}}">考站管理</a>
                                </li>
                                <li><a  class="J_menuItem" href="{{route('osce.admin.room.getRoomList')}}">场所管理</a>
                                </li>
                                <li><a  class="J_menuItem" href="{{route('osce.admin.machine.getMachineList')}}">设备管理</a>
                                </li>
                                <li><a  class="J_menuItem" href="{{route('osce.admin.topic.getList')}}">科目管理</a>
                                </li>
                                <li><a  class="J_menuItem" href="{{route('osce.admin.ExamLabelController.getExamLabel')}}">试卷管理</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">考试管理</span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a class="active J_menuItem" href="{{route('osce.admin.exam.getExamList')}}">考试安排</a>
                                </li>
                                <li><a class="active J_menuItem" href="{{route('osce.admin.exam.getStudentQuery')}}">考生查询</a>
                                </li>
                                <li><a class="active J_menuItem" href="{{route('osce.admin.geExamResultList')}}">成绩查询</a>
                                </li>
                                <li><a class="active J_menuItem" href="{{route('osce.admin.notice.getList')}}">资讯&通知</a></li>
                                <li><a class="active J_menuItem" href="{{route('osce.admin.getTrainList')}}">考前培训</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">统计分析</span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a class="active J_menuItem" href="{{route('osce.admin.course.getIndex')}}">科目成绩统计</a>
                                </li>
                                <li><a class="active J_menuItem" href="{{route('osce.admin.course.getStudentScore')}}">考生成绩统计</a>
                                </li>
                                <li><a class="active J_menuItem" href="">考试整体分析</a>
                                </li>
                                <li><a class="active J_menuItem" href="{{route('osce.admin.SubjectStatisticsController.SubjectGradeList')}}">科目成绩分析</a>
                                </li>
                                <li><a class="active J_menuItem" href="{{route('osce.admin.TestScoresController.TestScoreList')}}">考生成绩分析</a>
                                </li>
                                <li><a class="active J_menuItem" href="{{route('osce.admin.TestScoresController.testScoresCount')}}">教学成绩分析</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-gear"></i> <span class="nav-label">系统管理</span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a class="active J_menuItem" href="{{route('osce.admin.user.getStaffList')}}">用户管理</a>
                                </li>
                                <li>
                                    <a class="J_menuItem" href="{{ route('auth.AuthManage') }}">权限管理</a>
                                </li>
                                <li>
                                    <a class="J_menuItem" href="{{route('osce.admin.config.getIndex')}}">系统设置</a>
                                </li>
                            </ul>
                        <li>
                    @else
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
                    @endif
                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->


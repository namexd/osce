        <!--左侧导航开始-->
        <style>
         .img-circle{height:46px;}
         .person-info{margin:20px 0;}
         .person-info span{color: #fff;}
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
                        <div class="logo-element">
                        </div>
                    </li>
                    <li class="person-info">
                        <div class="profile-element">
                            <div class="dropdown profile-element">
                                <span><img alt="image" class="img-circle" src="{{asset('osce/admin/images/profile_small.jpg')}}"></span>
                                <span>Alexander Pierce</span>
                            </div>
                        </div>
                        <div class="logo-element">
                        </div>
                    </li>
                    <li class="active">
                        <a href="#"><i class="fa fa-laptop"></i> <span class="nav-label">资源管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="active J_menuItem" href="{{route('osce.admin.case.getCaseList')}}">病例管理</a>
                            </li>
                            <li><a class="active J_menuItem" href="{{route('osce.admin.invigilator.getInvigilatorList')}}">人员管理</a>
                            </li>
                            <li><a class="active J_menuItem" href="{{route('osce.admin.Station.getStationList')}}">考站管理</a>
                            </li>
                            <li><a  class="J_menuItem" href="{{route('osce.admin.room.getRoomList')}}">场所管理</a>
                            </li>
                            <li><a  class="J_menuItem" href="{{route('osce.admin.machine.getMachineList')}}">监考设备管理</a>
                            </li>
							<li><a  class="J_menuItem" href="{{route('osce.admin.topic.getList')}}">考核标准</a>
                            </li>
							
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-file-text-o"></i> <span class="nav-label">考试管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="active J_menuItem" href="{{route('osce.admin.exam.getExamList')}}">考试安排</a>
                            </li>
                            <li><a class="active J_menuItem" href="{{route('osce.admin.exam.getStudentQuery')}}">考生查询</a>
                            </li>
                            <li><a class="active J_menuItem" href="">成绩查询</a>
                            </li>
                            <li><a class="active J_menuItem" href="">咨询&通知</a>
                            </li>
                        </ul>
                    </li>
					<li>
						<a href="#"><i class="fa fa-file-text-o"></i> <span class="nav-label">系统管理</span><span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
                            <li>
								<a class="active J_menuItem" href="{{route('osce.admin.user.getStaffList')}}">用户管理</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{route('osce.admin.config.getIndex')}}">系统设置</a>
                            </li>
                        </ul>
					<li>
                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->


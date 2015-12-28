        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="profile-element">
                           <span style="font-size:20px;color:#fff;font-weight: bold;">技能中心管理系统</span>
                           
                        </div>
                        <div class="logo-element">
                        </div>
                    </li>

					<li>
                        <a href="#"><i class="fa fa-picture-o"></i> <span class="nav-label">开放实验室管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">

                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-picture-o"></i> <span class="nav-label">突发事件管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">

                        </ul>
                    </li>

                    <li>
                        <a href="#"><i class="fa fa-table"></i> <span class="nav-label">学生信息审核</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{route('msc.verify.student')}}">学生注册审核</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="#"><i class="fa fa-user"></i> <span class="nav-label">用户权限管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{ route('msc.admin.user.StudentList') }}">用户管理</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{ route('auth.AuthManage') }}">角色权限管理</a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->


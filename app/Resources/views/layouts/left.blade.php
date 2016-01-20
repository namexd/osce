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
                        <a href="#"><i class="fa fa-table"></i> <span class="nav-label">学生信息审核</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{route('msc.verify.student')}}">学生注册审核</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-table"></i> <span class="nav-label">楼栋信息管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{route('msc.admin.floor.index')}}">楼栋列表</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-table"></i> <span class="nav-label">实验室管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{route('msc.admin.laboratory.index')}}">实验室列表</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{route('msc.admin.LadMaintain.LaboratoryList')}}">实验室资源维护</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{route('msc.admin.laboratory.getLabClearnder')}}">开放日历管理</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{route('msc.admin.laboratory.getLabOrderList')}}">预约记录审核</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{route('msc.admin.laboratory.getLabOrderShow')}}">实验室预约查看</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="#"><i class="fa fa-table"></i> <span class="nav-label">系统码表管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{route('msc.admin.profession.ProfessionList')}}">专业列表</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{route('msc.Dept.DeptList')}}">科室列表</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{route('msc.admin.professionaltitle.JobTitleIndex')}}">职称列表</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{route('msc.admin.resources.ResourcesIndex')}}">资源列表</a>
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


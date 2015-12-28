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
                    <li class="active">
                        <a href="#"><i class="fa fa-laptop"></i> <span class="nav-label">资源管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="active J_menuItem" href="{{route('msc.admin.resourcesManager.getResourcesList')}}">现有资源</a>
                            </li>
                            <li><a  class="J_menuItem" href="{{route('msc.admin.resourcesManager.getAddResources')}}">新增资源</a>
                            </li>
                            <li><a  class="J_menuItem" href="{{route('msc.admin.resourcesManager.getResourcesCateList')}}">资源类别管理</a>
                            </li>
                        </ul>
                    </li>
                    <li class="">
                        <a href="#"><i class="fa fa-file-text-o"></i> <span class="nav-label">课程管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="active J_menuItem" href="{{route('msc.courses.NormalCoursesPlan')}}">课程安排</a>
                            </li>
                            <li><a class="active J_menuItem" href="{{url('/msc/admin/training/add-training')}}">培训安排</a>
                            </li>
                            <li><a class="active J_menuItem" href="{{route('msc.admin.courses.getClassObserve')}}">课程监管</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-picture-o"></i> <span class="nav-label">设备外借归还管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="J_menuItem" href="{{route('msc.admin.resourcesManager.getWaitExamineList')}}">审核申请</a>
                            </li>
                            <li><a  class="J_menuItem" href="{{route('msc.admin.resourcesManager.getBorrowedList')}}">现有外借设备</a>
                            </li>
                            <li><a class="J_menuItem" href="{{action('\Modules\Msc\Http\Controllers\Admin\ResourcesManagerController@getBorrowRecordList')}}">外借历史</a>
                            </li>
                        </ul>
                    </li>
					<li>
                        <a href="#"><i class="fa fa-picture-o"></i> <span class="nav-label">开放实验室管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="J_menuItem" href="{{route('msc.admin.lab.openLabApplyList')}}">审核申请</a>
                            </li>
                            <li><a  class="J_menuItem" href="{{route('msc.admin.lab.openLabApplyExaminedList')}}">已审核申请</a>
                            </li>
                            <li><a class="J_menuItem" href="{{route('msc.admin.lab.openLabHistoryList')}}">使用历史</a>
                            </li>
                            <li><a class="J_menuItem" href="{{route('msc.admin.lab.getHadOpenLabList')}}">现有实验室</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-picture-o"></i> <span class="nav-label">突发事件管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="J_menuItem" href="{{route('msc.admin.lab.getUrgentApplyList')}}">突发事件审核申请</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="#"><i class="fa fa-picture-o"></i> <span class="nav-label">开放设备管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="J_menuItem" href="{{route('msc.admin.lab-tools.getOpenLabToolsApplyList')}}">设备预约审核管理</a>
                            </li>
                            <li><a  class="J_menuItem" href="{{route('msc.admin.lab-tools.openLabToolsExaminedList')}}">已预约设备</a>
                            </li>
                            <li><a class="J_menuItem" href="{{route('msc.admin.lab-tools.getOpenLabToolsUseHistory')}}">设备预约使用历史</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="#"><i class="fa fa-table"></i> <span class="nav-label">学生信息审核</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a class="J_menuItem" href="{{route('msc.verify.student')}}">学生注册审核</a>
                            </li>
                            <li>
                                <a class="J_menuItem" href="{{route('msc.verify.teacher')}}">教师注册审核</a>
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


        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="profile-element">
                           <span style="font-size:20px;color:#fff;font-weight: bold;font-family: 微软雅黑">OSCE管理系统</span>
                           
                        </div>
                        <div class="logo-element">
                        </div>
                    </li>
                    <li class="active">
                        <a href="#"><i class="fa fa-laptop"></i> <span class="nav-label">考试安排</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="active J_menuItem" href="{{route('msc.admin.resourcesManager.getResourcesList')}}">考站管理</a>
                            </li>
                            <li><a  class="J_menuItem" href="{{route('msc.admin.resourcesManager.getAddResources')}}">考场安排</a>
                            </li>
                            <li><a  class="J_menuItem" href="{{route('msc.admin.resourcesManager.getResourcesCateList')}}">题库管理</a>
                            </li>
                            <li><a  class="J_menuItem" href="{{route('msc.admin.resourcesManager.getResourcesCateList')}}">腕表管理</a>
                            </li>
                            <li><a  class="J_menuItem" href="{{route('msc.admin.resourcesManager.getResourcesCateList')}}">监考设备管理</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-file-text-o"></i> <span class="nav-label">考试管理</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li><a class="active J_menuItem" href="{{route('msc.courses.NormalCoursesPlan')}}">候考</a>
                            </li>
                            <li><a class="active J_menuItem" href="{{url('/msc/admin/training/add-training')}}">监考</a>
                            </li>
                            <li><a class="active J_menuItem" href="{{route('msc.admin.courses.getClassObserve')}}">巡考</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->


<body>
    <div class="wrapper">
        <div class="sidebar" data-active-color="rose" data-background-color="black">
            <div class="logo">
                <a href="#" class="simple-text logo-mini">
                    PL
                </a>
                <a href="#" class="simple-text logo-normal">
                    Projekt Leave
                </a>
            </div>
            <div class="sidebar-wrapper">
                <div class="user">
                    <div class="photo">
                        <img src="/img/faces/nan.jpg" />
                    </div>
                    <div class="info">
                        <a data-toggle="collapse" href="#profile" class="collapsed">
                            <span>
                                Admin
                                <b class="caret"></b>
                            </span>
                        </a>
                        <div class="clearfix"></div>
                        <div class="collapse" id="profile">
                            <ul class="nav">
                                <li>
                                    <a href="{{ url('/admin/profile') }}">
                                        <span class="sidebar-mini"> P </span>
                                        <span class="sidebar-normal"> Profile </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <span class="sidebar-mini"> L </span>
                                        <span class="sidebar-normal"> Logout </span>
                                    </a>
                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                                        style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <ul class="nav">
                    {{-- 1. Dashboard --}}
                    <li
                        class="{{ request()->is('admin/dashboard*') || request()->is('admin/analytics*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/dashboard') }}">
                            <i class="material-icons">dashboard</i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    {{-- 2. All Roles --}}
                    <li class="{{ request()->is('admin/roles*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/roles') }}">
                            <i class="material-icons">admin_panel_settings</i>
                            <p>All Roles</p>
                        </a>
                    </li>

                    {{-- 3. All Departments --}}
                    <li class="{{ request()->is('admin/departments*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/departments') }}">
                            <i class="material-icons">business</i>
                            <p>All Departments</p>
                        </a>
                    </li>

                    {{-- 4. All Jobs --}}
                    <li class="{{ request()->is('admin/jobs*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/jobs') }}">
                            <i class="material-icons">work</i>
                            <p>All Jobs</p>
                        </a>
                    </li>

                    {{-- 5. All Staffs --}}
                    <li class="{{ request()->is('admin/staffs*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/staffs') }}">
                            <i class="material-icons">groups</i>
                            <p>All Staffs</p>
                        </a>
                    </li>

                    {{-- 6. All Leaves --}}
                    <li class="{{ request()->is('admin/leaves*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/leaves') }}">
                            <i class="material-icons">event_available</i>
                            <p>All Leaves</p>
                        </a>
                    </li>

                    {{-- 7. All Leave Types --}}
                    <li class="{{ request()->is('admin/leavetypes*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/leavetypes') }}">
                            <i class="material-icons">category</i>
                            <p>All Leave Types</p>
                        </a>
                    </li>

                    {{-- 8. Leave Levels --}}
                    <li class="{{ request()->is('admin/leavelevels*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/leavelevels') }}">
                            <i class="material-icons">stairs</i>
                            <p>Leave Levels</p>
                        </a>
                    </li>

                    {{-- 9. Reports --}}
                    <li class="{{ request()->is('admin/reports*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/reports') }}">
                            <i class="material-icons">summarize</i>
                            <p>Reports</p>
                        </a>
                    </li>

                    {{-- 10. Holidays --}}
                    <li class="{{ request()->is('admin/holidays*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/holidays') }}">
                            <i class="material-icons">celebration</i>
                            <p>Holidays</p>
                        </a>
                    </li>

                    {{-- 11. Calendar --}}
                    <li class="{{ request()->is('admin/calendar*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/calendar') }}">
                            <i class="material-icons">calendar_today</i>
                            <p>Calendar</p>
                        </a>
                    </li>

                    {{-- 12. Settings --}}
                    <li class="{{ request()->is('admin/settings*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/settings') }}">
                            <i class="material-icons">settings</i>
                            <p>Settings</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <nav class="navbar navbar-transparent navbar-absolute">
                <div class="container-fluid">
                    <div class="navbar-minimize">
                        <button id="minimizeSidebar" class="btn btn-round btn-white btn-fill btn-just-icon">
                            <i class="material-icons visible-on-sidebar-regular">more_vert</i>
                            <i class="material-icons visible-on-sidebar-mini">view_list</i>
                        </button>
                    </div>
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#"> Admin Panel </a>
                    </div>
                    <div class="collapse navbar-collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <a href="{{ url('/logout') }}" onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    <i class="material-icons">exit_to_app</i>
                                    <p class="hidden-lg hidden-md">Logout</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
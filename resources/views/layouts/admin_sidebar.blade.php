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
                    <li class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/dashboard') }}">
                            <i class="material-icons">dashboard</i>
                            <p> Dashboard </p>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/staffs*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/staffs') }}">
                            <i class="material-icons">people</i>
                            <p> Staff Management </p>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/roles*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/roles') }}">
                            <i class="material-icons">security</i>
                            <p> Roles </p>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/departments*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/departments') }}">
                            <i class="material-icons">business</i>
                            <p> Departments </p>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/jobs*') ? 'active' : '' }}">
                        <a href="{{ url('/admin/jobs') }}">
                            <i class="material-icons">work</i>
                            <p> Jobs </p>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/leaves*') ? 'active' : '' }}">
                        <a href="{{ url('admin/leaves') }}">
                            <i class="material-icons">library_books</i>
                            <p> All Leaves </p>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/leavetypes*') ? 'active' : '' }}">
                        <a href="{{ url('admin/leavetypes') }}">
                            <i class="material-icons">category</i>
                            <p> Leave Types </p>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/reports*') ? 'active' : '' }}">
                        <a href="{{ url('admin/reports') }}">
                            <i class="material-icons">assignment</i>
                            <p> Reports </p>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/calendar*') ? 'active' : '' }}">
                        <a href="{{ url('admin/calendar') }}">
                            <i class="material-icons">calendar_today</i>
                            <p> Calendar </p>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/settings*') ? 'active' : '' }}">
                        <a href="{{ url('admin/settings') }}">
                            <i class="material-icons">settings</i>
                            <p> Settings </p>
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
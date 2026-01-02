<body>
    <div class="wrapper">
        <div class="sidebar" data-active-color="purple" data-background-color="black">
            <!--
                Tip 1: You can change the color of active element of the sidebar using: data-active-color="purple | blue | green | orange | red | rose"
                Tip 2: you can also add an image using data-image tag
                Tip 3: you can change the color of the sidebar with data-background-color="white | black"
            -->
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
                        <img src="{{ Auth::user()->staff->picture ?? '/img/faces/nan.jpg' }}" />
                    </div>
                    <div class="info">
                        <a data-toggle="collapse" href="#profile" class="collapsed">
                            <span>
                                {{ Auth::user()->staff->firstname ?? '' }} {{ Auth::user()->staff->lastname ?? '' }}
                                <b class="caret"></b>
                            </span>
                        </a>
                        <div class="clearfix"></div>
                        <div class="collapse" id="profile">
                            <ul class="nav">
                                <li>
                                    <a href="{{ url('/staff/profile') }}">
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



                @php
                    $userRoleId = Auth::user()->staff->role_id ?? null;
                    $userDepartment = Auth::user()->staff->department ?? null;

                    // Permission groups based on user requirements
                    $isAdmin = $userRoleId === \App\Enums\RoleEnum::ADMIN->value;

                    // Admin/HR only - Settings access
                    $isAdminOrHR = in_array($userRoleId, [
                        \App\Enums\RoleEnum::ADMIN->value,
                        \App\Enums\RoleEnum::HR->value,
                    ]);

                    // Admin/HR/CEO - Roles, Jobs access
                    $canViewRolesAndJobs = in_array($userRoleId, [
                        \App\Enums\RoleEnum::ADMIN->value,
                        \App\Enums\RoleEnum::HR->value,
                        \App\Enums\RoleEnum::CEO->value,
                    ]);

                    // Admin/HR/CEO/OPS - Departments access
                    $canViewDepartments = in_array($userRoleId, [
                        \App\Enums\RoleEnum::ADMIN->value,
                        \App\Enums\RoleEnum::HR->value,
                        \App\Enums\RoleEnum::CEO->value,
                        \App\Enums\RoleEnum::OPS->value,
                    ]);

                    // Admin/HR/CEO/OPS/HOD - Staff, Leaves, Reports access (HOD sees only their department)
                    $canViewStaffAndLeaves = in_array($userRoleId, [
                        \App\Enums\RoleEnum::ADMIN->value,
                        \App\Enums\RoleEnum::HR->value,
                        \App\Enums\RoleEnum::CEO->value,
                        \App\Enums\RoleEnum::OPS->value,
                        \App\Enums\RoleEnum::HOD->value,
                    ]);
                @endphp

                <ul class="nav">
                    {{-- 1. Apply for Leave (All roles) --}}
                    <li class="{{ request()->is('leaves/apply*') ? 'active' : '' }}">
                        <a href="{{ url('/leaves/apply') }}">
                            <i class="material-icons">note_add</i>
                            <p>Apply for Leave</p>
                        </a>
                    </li>

                    {{-- 2. Dashboard (All roles) --}}
                    <li class="{{ request()->is('dashboard*') ? 'active' : '' }}">
                        <a href="{{ url('/dashboard') }}">
                            <i class="material-icons">dashboard</i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    {{-- 3. All Roles (Admin/HR/CEO) --}}
                    @if($canViewRolesAndJobs)
                        <li class="{{ request()->is('roles*') ? 'active' : '' }}">
                            <a href="{{ url('/roles') }}">
                                <i class="material-icons">admin_panel_settings</i>
                                <p>All Roles</p>
                            </a>
                        </li>
                    @endif

                    {{-- 4. All Departments (Admin/HR/CEO/OPS) --}}
                    @if($canViewDepartments)
                        <li class="{{ request()->is('departments*') ? 'active' : '' }}">
                            <a href="{{ url('/departments') }}">
                                <i class="material-icons">business</i>
                                <p>All Departments</p>
                            </a>
                        </li>
                    @endif

                    {{-- 5. All Jobs (Admin/HR/CEO) --}}
                    @if($canViewRolesAndJobs)
                        <li class="{{ request()->is('jobs*') || request()->is('admin/jobs*') ? 'active' : '' }}">
                            <a href="{{ url('/jobs') }}">
                                <i class="material-icons">work</i>
                                <p>All Jobs</p>
                            </a>
                        </li>
                    @endif

                    {{-- 6. All Staffs (Admin/HR/CEO/OPS/HOD - HOD sees department only) --}}
                    @if($canViewStaffAndLeaves)
                        <li class="{{ request()->is('staff') || request()->is('staff/*') ? 'active' : '' }}">
                            <a href="{{ url('/staff') }}">
                                <i class="material-icons">groups</i>
                                <p>All Staffs</p>
                            </a>
                        </li>
                    @endif

                    {{-- 7. All Leaves (Admin/HR/CEO/OPS/HOD - HOD sees department only) --}}
                    @if($canViewStaffAndLeaves)
                        <li class="{{ request()->is('all-leaves*') ? 'active' : '' }}">
                            <a href="{{ url('/all-leaves') }}">
                                <i class="material-icons">event_available</i>
                                <p>All Leaves</p>
                            </a>
                        </li>
                    @endif

                    {{-- 8. All Leave Types (Admin/HR/CEO) --}}
                    @if($canViewRolesAndJobs)
                        <li class="{{ request()->is('leavetypes*') || request()->is('admin/leavetypes*') ? 'active' : '' }}">
                            <a href="{{ url('/leavetypes') }}">
                                <i class="material-icons">category</i>
                                <p>All Leave Types</p>
                            </a>
                        </li>
                    @endif

                    {{-- 9. All Leave Reports (Admin/HR/CEO/OPS/HOD - HOD sees department only) --}}
                    @if($canViewStaffAndLeaves)
                        <li class="{{ request()->is('reports*') && !request()->is('staff/reports*') ? 'active' : '' }}">
                            <a href="{{ url('/reports') }}">
                                <i class="material-icons">summarize</i>
                                <p>Reports</p>
                            </a>
                        </li>
                    @endif

                    {{-- 10. Calendar (All roles) --}}
                    <li class="{{ request()->is('calendar*') && !request()->is('admin/*') ? 'active' : '' }}">
                        <a href="{{ url('/calendar') }}">
                            <i class="material-icons">calendar_today</i>
                            <p>Calendar</p>
                        </a>
                    </li>

                    {{-- 11. Holidays (All roles) --}}
                    <li class="{{ request()->is('holidays*') ? 'active' : '' }}">
                        <a href="{{ url('/holidays') }}">
                            <i class="material-icons">celebration</i>
                            <p>Holidays</p>
                        </a>
                    </li>

                    {{-- 12. My Department --}}
                    @if($userDepartment)
                        <li class="{{ request()->is('my-department*') ? 'active' : '' }}">
                            <a href="{{ url('/my-department') }}">
                                <i class="material-icons">people</i>
                                <p>My Department</p>
                            </a>
                        </li>
                    @endif

                    {{-- 13. My Leaves --}}
                    <li
                        class="{{ request()->is('leaves') || (request()->is('leaves/*') && !request()->is('leaves/apply*')) ? 'active' : '' }}">
                        <a href="{{ url('/leaves') }}">
                            <i class="material-icons">event_note</i>
                            <p>My Leaves</p>
                        </a>
                    </li>

                    {{-- 14. My Reports --}}
                    <li class="{{ request()->is('staff/reports*') ? 'active' : '' }}">
                        <a href="{{ url('/staff/reports') }}">
                            <i class="material-icons">assessment</i>
                            <p>My Reports</p>
                        </a>
                    </li>

                    {{-- 15. Settings (Admin/HR only) --}}
                    @if($isAdminOrHR)
                        <li class="{{ request()->is('settings*') || request()->is('admin/settings*') ? 'active' : '' }}">
                            <a href="{{ url('/admin/settings') }}">
                                <i class="material-icons">settings</i>
                                <p>Settings</p>
                            </a>
                        </li>
                    @endif
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
                        <a class="navbar-brand" href="#"> </a>
                    </div>

                    <div class="collapse navbar-collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <a href="#pablo" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="material-icons">dashboard</i>
                                    <p class="hidden-lg hidden-md">Dashboard</p>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="material-icons">notifications</i>
                                    @if($unreadNotifications && $unreadNotifications->count() > 0)
                                        <span class="notification">{{ $unreadNotifications->count() }}</span>
                                    @endif
                                    <p class="hidden-lg hidden-md">
                                        Notifications
                                        <b class="caret"></b>
                                    </p>
                                </a>
                                <ul class="dropdown-menu">
                                    @forelse($unreadNotifications as $notification)
                                        <li>
                                            <a
                                                href="{{ url($notification->link ?? '#') }}">{{ \Illuminate\Support\Str::limit($notification->message, 40) }}</a>
                                        </li>
                                    @empty
                                        <li>
                                            <a href="#">No new notifications</a>
                                        </li>
                                    @endforelse
                                    @if($unreadNotifications->count() > 0)
                                        <li role="separator" class="divider"></li>
                                        <li><a href="{{ url('/notifications') }}" class="text-center">View All</a></li>
                                    @endif
                                </ul>
                            </li>
                            <li>
                                <a href="#pablo" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="material-icons">person</i>
                                    <p class="hidden-lg hidden-md">Profile</p>
                                </a>
                            </li>
                            <li class="separator hidden-lg hidden-md"></li>
                        </ul>
                        <form class="navbar-form navbar-right" role="search" />
                        <div class="form-group form-search is-empty">
                            <input type="text" class="form-control" placeholder=" Search " />
                            <span class="material-input"></span>
                        </div>
                        <button type="submit" class="btn btn-white btn-round btn-just-icon">
                            <i class="material-icons">search</i>
                            <div class="ripple-container"></div>
                        </button>
                        </form>
                    </div>
                </div>
            </nav>
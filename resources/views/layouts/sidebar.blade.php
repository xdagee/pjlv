<body>
    <div class="wrapper">
        <div class="sidebar" data-active-color="red" data-background-color="black">
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
                        <img src="/img/faces/nan.jpg" />
                    </div>
                    <div class="info">
                        <a data-toggle="collapse" href="#profile" class="collapsed">
                            <span>
                                Obaa Papa Bi
                                <b class="caret"></b>
                            </span>
                        </a>
                        <div class="clearfix"></div>
                        <div class="collapse" id="profile">
                            <ul class="nav">
                                <li>
                                    <a href="{{ url ('/profile') }}">
                                        <span class="sidebar-mini"> P </span>
                                        <span class="sidebar-normal"> Profile </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url ('/logout') }}">
                                        <span class="sidebar-mini"> L </span>
                                        <span class="sidebar-normal"> Logout </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <ul class="nav">
                    <li>
                        <a href="{{ url ('/home') }}">
                            <i class="material-icons">dashboard</i>
                            <p> Dashboard </p>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url ('/employees') }}">
                            <i class="material-icons">people</i>
                            <p> Employees </p>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url ('/leaves') }}">
                            <i class="material-icons">library_books</i>
                            <p> Leaves </p>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url ('/calendar') }}">
                            <i class="material-icons">calendar_today</i>
                            <p> Calendar </p>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url ('/reports') }}">
                            <i class="material-icons">assignment</i>
                            <p> Reports </p>
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
                        <a class="navbar-brand" href="{{ url ('/home') }}"> Dashboard </a>
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
                                    <span class="notification">3</span>
                                    <p class="hidden-lg hidden-md">
                                        Notifications
                                        <b class="caret"></b>
                                    </p>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="#">Mike John responded to your email</a>
                                    </li>
                                    <li>
                                        <a href="#">You have 5 new tasks</a>
                                    </li>
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
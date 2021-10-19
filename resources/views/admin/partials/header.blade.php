<nav class="main-header navbar navbar-expand border-bottom navbar-dark bg-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-dark" data-widget="pushmenu" href="javascript:void(0)"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto text-dark">

        <fullscreen-button></fullscreen-button>

        <li class="nav-item d-none d-sm-inline-block">
            <a class="nav-link" href="{{ route('admin.notifications.index') }}">
                <i class="fa fa-bell mr-2 text-warning"></i>
                <count-listener
                class="badge-warning navbar-badge text-danger"
                fetch-url="{{ route('admin.counts.fetch.notifications') }}"
                event="update-notification-count"
                ></count-listener>
            </a>
        </li>

        <div class="btn-group">
            <button class="btn-sm btn" data-toggle="dropdown">
                <i class="fas fa-sliders-h"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <button class="dropdown-item" type="button">Account Settings</button>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" type="button" href="{{ route('admin.logout') }}">Logout</a>
            </div>
        </div>
    </ul>
</nav>

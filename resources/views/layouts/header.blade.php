<!-- Navbar -->
<nav class="main-header navbar navbar-expand-md navbar-light navbar-dark">
    <a href="{{ route('home') }}" class="navbar-brand">
         <img src="{{ route('home') }}/dist/img/logo-top.png" alt="Fire Technical Services" class="brand-image">
        <!-- <span class="brand-text text-dark font-weight-bold">Fire Technical Services </span> -->
    </a>

    <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    @include('layouts.sidebar')

    <!-- Right navbar links -->
    <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
        <!-- Notification Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
              <i class="far fa-bell text-maroon" style="font-size:18px;"></i>
              <span class="badge badge-danger navbar-badge" style="top:0px;">{{Auth::user()->unreadNotifications->count()}}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
              <span class="dropdown-item dropdown-header">{{ Auth::user()->unreadNotifications->count()}} Notifications</span>
              <div class="dropdown-divider"></div>
                @foreach(Auth::user()->unreadNotifications as $notification)
                    <a href="#" data-href="{{ $notification->data["action"] }}" data-notif-id="{{$notification->id}}" class="dropdown-item">
                        <i class="fas fa-file mr-2"></i> {!! $notification->data['title'] !!}
                        <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans()}}</span>
                    </a>
                @endforeach
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
          </li>
        <!-- User Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user text-maroon"></i>
                <span class="ml-1 text-maroon">{{ Auth()->user()->name}}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <a href="{!! route('users.user_profile',Auth()->user()->id) !!}" class="dropdown-item">Profile</a>
                <!-- <div class="dropdown-divider"></div> -->
                <a href="{!! url('/logout') !!}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item">Logout</a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->


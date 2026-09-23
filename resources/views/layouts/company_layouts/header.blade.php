<!-- Navbar -->
<nav class="main-header navbar navbar-expand-md navbar-light navbar-dark">
    <a href="{{ route('company.dashboard') }}" class="navbar-brand">
         <img src="{{ route('home') }}/dist/img/logo-top.png" alt="Fire Technical Services" class="brand-image">
    </a>

    <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    @include('layouts.company_layouts.sidebar')

    <!-- Right navbar links -->
    <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
        <!-- User Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user text-maroon"></i>
                <span class="ml-1 text-maroon">{{ Auth::guard('company')->user()->name}}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <a href="{!! route('company.user_profile',Auth::guard('company')->user()->id) !!}" class="dropdown-item">Profile</a>
                <!-- <div class="dropdown-divider"></div> -->
                <a href="{!! route('company.logout') !!}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item">Logout</a>
                <form id="logout-form" action="{{ route('company.logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->


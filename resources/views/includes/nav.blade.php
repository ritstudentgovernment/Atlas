
@include("includes.sg_nav")

<header id="naps-header" class="padding">
    <nav class="wrapper relative" uk-navbar>

        <div class="uk-navbar-left">
            <a href="{{ route('about') }}" class="desktop uk-icon-link padding-left"><span uk-icon="icon: info" class="padding-right"></span>About</a>
            <a href="{{ route('home') }}" class="mobile logo" title="Home | {{ env('APP_NAME') }}">
                <img src="https://naps.rit.edu/logo.svg" alt="logo">
            </a>
        </div>

        <div class="uk-navbar-center">
            <a id="logo" class="desktop logo" href="/" title="Home | {{ env('APP_NAME') }}">
                <img src="https://naps.rit.edu/logo.svg" alt="logo">
            </a>
        </div>

        <div class="uk-navbar-right">

            <div class="desktop">
                @if (Auth::guest())
                    <a href="/login">Login</a>
                @else
                    <a>Hello, {{ Auth::user()->first_name }} <span uk-icon="icon: chevron-down; ratio:1.2;"></span></a>
                    <div class="uk-navbar-dropdown" uk-dropdown>
                        <ul class="uk-nav uk-dropdown-nav">
                            @if(Auth::user()->can('administer'))
                                <li class="padding-bottom">
                                    <a href="/admin" class="uk-link-text uk-link-reset">
                                        Admin Panel
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="/logout" class="uk-link-text uk-link-reset">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
            <div class="mobile">
                <a class="uk-navbar-toggle" uk-navbar-toggle-icon></a>
                <div class="uk-navbar-dropdown" uk-dropdown>
                    <ul class="uk-nav uk-dropdown-nav">
                        <li class="padding-bottom">
                            <a href="/about" class="uk-link-text uk-link-reset">
                                About
                            </a>
                        </li>
                        @if (Auth::guest())
                            <li>
                                <a href="/login">Login</a>
                            </li>
                        @else
                            @if(Auth::user()->can('administer'))
                                <li class="padding-bottom">
                                    <a href="/admin" class="uk-link-text uk-link-reset">
                                        Admin Panel
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="/logout" class="uk-link-text uk-link-reset">
                                    Logout
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

        </div>

    </nav>
</header>

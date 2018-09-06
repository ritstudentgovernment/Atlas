
@include("includes.sg_nav")

<header id="naps-header" class="padding">
    <nav class="wrapper relative" uk-navbar>

        <div class="uk-navbar-left">
            <a href="/" class="uk-icon-link padding-left"><span uk-icon="icon: info" class="padding-right"></span>About</a>
            <a id="left-logo" href="/" title="Home | Naps - RIT Student Government">
                <img src="https://naps.rit.edu/logo.svg" alt="naps logo">
            </a>
        </div>

        <div class="uk-navbar-center">
            <a id="logo" href="/" title="Home | Naps - RIT Student Government">
                <img src="https://naps.rit.edu/logo.svg" alt="naps logo">
            </a>
        </div>

        <div class="uk-navbar-right">

            @if (Auth::guest())
                <a href="/login">Login</a>
			@else
			<a>Hello, {{ Auth::user()->name }} <span uk-icon="icon: chevron-down; ratio:1.2;"></span></a>
				<div class="uk-navbar-dropdown" uk-dropdown>
                    <ul class="uk-nav uk-navbar-dropdown-nav">
						<li>
							<a href="/logout">
								Logout 
							</a>
						</li>
                    </ul>
                </div>
            @endif

        </div>

    </nav>
</header>

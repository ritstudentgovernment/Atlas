@extends("layouts.main")

@section('title', 'About')

@section('page_head')

<link rel="stylesheet" href="{{ mix('/css/about.css') }}">

@endsection

@section("body")

    <div class="uk-container">
        <div class="white-background">
            <div id="about-tab-nav">
                <div class="center">
                    <ul class="uk-child-width-expand" uk-tab="connect: #about-content-switcher">
                        <li class="uk-active"><a>About</a></li>
                        <li><a>Policies</a></li>
                        <li><a>Privacy</a></li>
                        <li><a>Technology</a></li>
                    </ul>
                </div>
            </div>
            <div id="about-content">
                <ul id="about-content-switcher" class="uk-switcher">
                    <li>
                        <h5>What is Naps?</h5>
                        <p>
                            RIT Nap Spot Map is a mapping tool to mark the best places to take naps on campus. The content of this application is provided by RIT students and moderated by the RIT Student Government.
                        </p>

                        <h5>How can I contribute?</h5>
                        <p>
                            In RIT Nap Spot Map everyone can be a Naps guru! To contribute, click in Add an Entry, select a place were you regularly Nap on campus, fill in the extra information and add it to the map.
                            <br />
                            Once you add a Nap Spot, you will see it represented by a brown marker, this means that your spot is waiting to be reviewed by one of our moderators and will only be visible to you. Once your Nap spot gets reviewed, you will receive an email and your Nap Spot will turn into an orange marker, meaning that its public and visible to the entire Naps community.
                        </p>
                    </li>
                    <li>Policies</li>
                    <li>Privacy</li>
                    <li>Technology</li>
                </ul>
                <hr class="uk-divider-icon" />
            </div>
        </div>
    </div>

@endsection
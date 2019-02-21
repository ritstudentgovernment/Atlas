@extends("layouts.main")

@section('title', 'About')

@section('page_head')

<link rel="stylesheet" href="{{ mix('/css/about.css') }}">

@endsection

@section("body")

    <div class="uk-container">
        <div class="bright-background">
            <div id="about-tab-nav">
                <div class="center">
                    <ul class="uk-child-width-expand" uk-tab="connect: #about-content-switcher">
                        <li class="tab-about uk-active"><a>About</a></li>
                        <li class="tab-policies"><a>Policies</a></li>
                        <li class="tab-privacy"><a>Privacy</a></li>
                        <li class="tab-technology"><a>Technology</a></li>
                    </ul>
                </div>
            </div>
            <div id="about-content" class="padding-left padding-right">
                <ul id="about-content-switcher" class="uk-switcher padding-left padding-right">
                    <li>
                        <h4>What is Naps?</h4>
                        <p>
                            RIT Nap Spot Map is a mapping tool to mark the best places to take naps on campus. The content of this application is provided by RIT students and moderated by the RIT Student Government.
                        </p>

                        <h4>How can I contribute?</h4>
                        <p>
                            In RIT Nap Spot Map everyone can be a Naps guru! To contribute, click in Add an Entry, select a place were you regularly Nap on campus, fill in the extra information and add it to the map.
                            <br />
                            Once you add a Nap Spot, you will see it represented by a brown marker, this means that your spot is waiting to be reviewed by one of our moderators and will only be visible to you. Once your Nap spot gets reviewed, you will receive an email and your Nap Spot will turn into an orange marker, meaning that its public and visible to the entire Naps community.
                        </p>
                    </li>
                    <li>
                        <blockquote>
                            <p>"Since free and civil discourse is at the heart of a university community, users should communicate in a manner that advances the cause of learning and mutual understanding."</p>
                            <footer>RIT Code of Conduct for Computer and Network Use</footer>
                        </blockquote>
                        <p>Use of this site falls under the <a href="http://www.rit.edu/computerconduct/">RIT Code of Conduct for Computer and Network Use</a>.</p>
                        <p>Student Government reserves the right to remove any evaluation or user at any time for violating the Code of Conduct. This includes, but is not limited to, creating an intimidating, hostile or abusive environment for any member of the RIT community, or posting of any obscene, defamatory, threatening, or otherwise harassing evaluations.</p>
                        <p>Please exercise good judgment when using this service.</p>
                    </li>
                    <li>
                        <p>RIT Student Government takes your privacy seriously.</p>
                        <h4>Access to Naps Data</h4>
                        <p>In order to provide a great nap mapping experience, while respecting the needs of RIT's nap spots, the following protections are in place for nap spot data:</p>
                        <ul>
                            <li>Nap spots may not access any nap spot data.</li>
                            <li>The names of the nap spot mappers are kept anonymous.</li>
                        </ul>
                        <h4>Automated Emails</h4>
                        <p>For the purposes of reminding students to map nap spots, Naps automatically retrieves a list of all University-affiliated nap spots and their location. Naps does not take advantage of any privileged information sources. All of the information used by Naps is available to any member of the RIT community.</p>
                        <h4>Nap Spot Verification</h4>
                        <p>In order to protect the integrity of our data, the status of each nap spot is automatically verified by the system. Our system is only accessing publicly available nap spot information. Information automatically collected through this system is only used for the purposes of verifying nap spots, sending reminders and development of the system.</p>
                    </li>
                    <li>
                        <h4>Innovative Open Technology</h4>
                        <p>Innovative technology is at the heart of Student Government's Services offerings. By using the newest and most effective technologies, we save time and money, while delivering world-class software.</p>
                        <h4>Commitment to Open Source</h4>
                        <p>Like PawPrints, the RIT Bikeshare website, RITPedia and SG Sites, Naps relies on Open Source Software to deliver the high-quality experience Tigers have come to expect. Student Government has made a commitment to give back by contributing all of our software to the FOSS community. Find us on <a href="https://github.com/ritstudentgovernment/laravel-naps">GitHub</a>.</p>
                        <h4>Pioneering Advanced Technologies</h4>
                        <p>Naps relies on the following advanced technologies to deliver a cutting-edge high performance experience to students. See below for a partial list:</p>
                        <ul>
                            <li>Laravel</li>
                            <li>Google Maps</li>
                            <li>UIKit</li>
                            <li>Vue JS</li>
                        </ul>
                    </li>
                </ul>
                <hr class="uk-divider-icon center padding" />
            </div>
        </div>
    </div>

@endsection

@section("scripts")

    <script>

        function addEventListeners() {

            var tabNav = $('#about-tab-nav');
            tabNav.find('li a').click(function(){

                var tab = $(this).html();
                var link = tab.replace(' ', '-').toLowerCase();
                $.urlParam('tab', link);

            });

        }

        function determineTab() {

            let tab = $.urlParam('tab');

            if (tab) {

                $('#about-tab-nav').find('.uk-active').removeClass('uk-active');
                $('.tab-' + tab).addClass('uk-active');

            }

        }

        $(window).ready(function(){

            determineTab();
            addEventListeners();

        });

    </script>

@endsection
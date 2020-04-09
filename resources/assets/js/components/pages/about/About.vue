<template>
    <el-card id="about-page" shadow="never">
        <el-tabs v-model="activeName" @tab-click="handleClick">
            <el-tab-pane label="About" name="about">
                <h4>What is Atlas?</h4>
                <p>
                    Atlas is a mapping tool created for the RIT community to mark places of interest around campus. The content of this application is provided by RIT students and moderated by Student Government.
                </p>

                <h4>How can I contribute?</h4>
                <p>
                    The goal of Atlas was to allow everyone to contribute to the community! To contribute, log into the application and click on the 'plus' icon in the bottom-right-hand side of the homepage. Fill in information about your spot in the dialog to the right, click 'place' to use your location to mark a point of interest on the map, and then click 'create' to submit your spot for review.
                    <br />
                    Once you add a new Spot, you will see it represented by a red marker, this means that your spot is waiting to be reviewed by one of our moderators and will only be visible to you. Once your spot gets reviewed, you will receive an email and your spot will turn into an brown marker, meaning that its public and visible to the entire Atlas community.
                </p>
            </el-tab-pane>
            <el-tab-pane label="Policies" name="policies">
                <blockquote>
                    <p>"Since free and civil discourse is at the heart of a university community, users should communicate in a manner that advances the cause of learning and mutual understanding."</p>
                    <footer>RIT Code of Conduct for Computer and Network Use</footer>
                </blockquote>
                <p>Use of this site falls under the <a href="http://www.rit.edu/computerconduct/">RIT Code of Conduct for Computer and Network Use</a>.</p>
                <p>Student Government reserves the right to remove any evaluation or user at any time for violating the Code of Conduct. This includes, but is not limited to, creating an intimidating, hostile or abusive environment for any member of the RIT community, or posting of any obscene, defamatory, threatening, or otherwise harassing evaluations.</p>
                <p>Please exercise good judgment when using this service.</p>
            </el-tab-pane>
            <el-tab-pane label="Privacy" name="privacy">
                <h4>RIT Student Government takes your privacy seriously.</h4>
                <p>In order to provide a great mapping experience, while respecting the privacy needs of RIT's students, limited information is collected about you.<br />Data we collect is maintained secure, and only used in the context of providing the mapping service. The data we collect is outlined below:</p>
                <ul>
                    <li>Your name, first and last</li>
                    <li>Your RIT email address</li>
                    <li>Your location at the time of submitting a new spot</li>
                    <ul>
                        <li>This is stored with your consent in order to provide a quick way of positioning a new spot.</li>
                        <li>This information is only used in conjunction with placing the new spot, and is only stored when a new spot is successfully created.</li>
                    </ul>
                </ul>
                <p>The following protections are in place for Atlas's data:</p>
                <ul>
                    <li>The names of spot mappers are kept anonymous from the public.</li>
                    <li>Moderators will be able to see your name and email, which is the only identifying information we store.</li>
                    <li>Shibboleth authentication is used to verify all of our users are who they say they are.</li>
                    <li>Only approved spots are visible to the public.</li>
                </ul>
                <h4>Automated Emails</h4>
                <p>In order to keep our collaborators up-to-date on the status of the spots they submit, emails will be sent to spot authors throughout the lifecycle of their spots. Emails will only be sent for this purpose.</p>
                <h4>Spot Verification</h4>
                <p>In order to protect the integrity of our data, the status of each spot is verified by our moderators. Information collected through this application is only used for the purposes of verifying spots and sending updates to the spot's author.</p>
            </el-tab-pane>
            <el-tab-pane label="Technology" name="technology">
                <h4>Innovative Open Technology</h4>
                <p>Innovative technology is at the heart of Student Government's Services offerings. By using the newest and most effective technologies, we save time and money, while delivering world-class software.</p>
                <h4>Commitment to Open Source</h4>
                <p>Like PawPrints, the RIT Bikeshare website, RITPedia and SG Sites, Atlas relies on Open Source Software to deliver the high-quality experience Tigers have come to expect. Student Government has made a commitment to give back by contributing all of our software to the FOSS community. Find us on <a href="https://github.com/ritstudentgovernment/Atlas">GitHub</a>.</p>
                <h4>Pioneering Advanced Technologies</h4>
                <p>Naps relies on the following advanced technologies to deliver a cutting-edge high performance experience to students. See below for a partial list:</p>
                <ul>
                    <li><a href="https://vuejs.org">Vue JS</a></li>
                    <li><a href="https://laravel.com">Laravel</a></li>
                    <li><a href="https://laravel.com/docs/6.x/broadcasting#installing-laravel-echo">Laravel Echo</a></li>
                    <li><a href="https://forge.laravel.com">Laravel Forge</a></li>
                    <li><a href="https://laravel.com/docs/6.x/horizon">Laravel Horizon</a></li>
                    <li><a href="https://developers.google.com/maps/documentation/javascript/tutorial">Google Maps</a></li>
                    <li><a href="https://element.eleme.io/#/en-US">ElementUI</a></li>
                    <li><a href="https://pusher.com">Pusher</a></li>
                </ul>
            </el-tab-pane>
        </el-tabs>
        <hr class="uk-divider-icon center padding" />
    </el-card>
</template>

<script>
    export default {
        name: "About",
        data () {
            return {
                activeName: 'about'
            };
        },
        methods: {
            handleClick (tab) {
                $.urlParam('tab', tab.name);
                this.activeName = tab.name;
            },
            setup () {
                let activeTab = $.urlParam('tab');
                if (activeTab) {
                    this.activeName = activeTab;
                }
            }
        },
        created () {
            window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
            window.onLoadedQueue.push(this.setup);
        }
    }
</script>

<style lang="scss">
    @import '../../../../sass/variables';
    @import '~element-ui/lib/theme-chalk/index.css';
    #about-page {

        width: 95%;
        margin: 20px auto 0;

        .el-tabs {

            .el-tabs__header {

                margin-bottom: 20px;

            }

            .el-tabs__item {

                color: #999;
                font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
                font-weight: 400;
                font-size: 1.2rem;

                &.is-active {

                    color: $dark;

                }

                &:hover:not(.is-active) {

                    color: $dim;

                }

            }

            .el-tabs__active-bar {

                background-color: $highlight;

            }

        }

    }
</style>
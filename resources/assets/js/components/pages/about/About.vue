<template>
    <el-card id="about-page" shadow="never">
        <el-tabs v-model="activeName" @tab-click="handleClick">
            <el-tab-pane label="About" name="about">
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
            </el-tab-pane>
            <el-tab-pane label="Technology" name="technology">
                <h4>Innovative Open Technology</h4>
                <p>Innovative technology is at the heart of Student Government's Services offerings. By using the newest and most effective technologies, we save time and money, while delivering world-class software.</p>
                <h4>Commitment to Open Source</h4>
                <p>Like PawPrints, the RIT Bikeshare website, RITPedia and SG Sites, Naps relies on Open Source Software to deliver the high-quality experience Tigers have come to expect. Student Government has made a commitment to give back by contributing all of our software to the FOSS community. Find us on <a href="https://github.com/ritstudentgovernment/laravel-naps">GitHub</a>.</p>
                <h4>Pioneering Advanced Technologies</h4>
                <p>Naps relies on the following advanced technologies to deliver a cutting-edge high performance experience to students. See below for a partial list:</p>
                <ul>
                    <li>Vue JS</li>
                    <li>Laravel</li>
                    <li>ElementUI</li>
                    <li>Google Maps</li>
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
                activeName: 'first'
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
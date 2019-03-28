<template>
    <el-menu :default-openeds="defaultOpen" :default-active="defaultActive" :collapse="collapse" :style="menuWidth">
        <el-menu-item index="0" @click="handleNavClick('dashboard')">
            <i class="uk-icon margin-right" uk-icon="icon:home;ratio:0.8;"></i>
            <span>Dashboard</span>
        </el-menu-item>
        <el-menu-item index="1" @click='handleNavClick("spots.categories.all")'>
            <i class="el-icon-location-outline"></i>
            <span>Spot Categories</span>
        </el-menu-item>
        <el-submenu index="2">
            <template slot="title">
                <i class="uk-icon margin-right" uk-icon="icon:user;ratio:0.8;"></i>
                <span>Users</span>
            </template>
            <el-menu-item index="2-1" @click="handleNavClick('users.all')">All Users</el-menu-item>
            <el-menu-item index="2-2" @click="handleNavClick('users.staff')">Staff Manager</el-menu-item>
        </el-submenu>
        <el-menu-item index="3-1" @click="handleNavClick('settings')">
            <i class="el-icon-setting"></i>
            <span>Settings</span>
        </el-menu-item>
        <div id="nav-visibility" class="center">
            <el-button :icon="visibilityIcon" circle @click="toggleCollapse()"></el-button>
        </div>
    </el-menu>
</template>

<script>
    import ElementUI from 'element-ui';

    export default {
        name: "Nav",
        components: {
            ElementUI,
        },
        props: ['defaultActivated', 'defaultOpened', 'pageLinks'],
        data () {
            return {
                window: {
                    width: 0
                },
                manualCollapse: false
            }
        },
        computed: {
            defaultActive () {
                return this.defaultActivated;
            },
            defaultOpen () {
                return [this.defaultOpened];
            },
            collapse () {
                return this.window.width < 640 || this.manualCollapse;
            },
            visibilityIcon () {
                return this.collapse ? 'el-icon-arrow-right' : 'el-icon-arrow-left';
            },
            menuWidth () {
                return this.collapse ? '' : 'width: 200px';
            },
            navLinks () {
                return JSON.parse(this.pageLinks);
            }
        },
        methods: {
            toggleCollapse () {
                this.manualCollapse = !this.manualCollapse;
            },
            handleResize () {
                this.window.width = window.innerWidth;
            },
            handleNavClick (toPage) {
                window.location.href = this.navLinks["admin." + toPage];
            }
        },
        created() {
            window.snav = this;
            this.handleResize();
            window.addEventListener('resize', this.handleResize);
        },
        destroyed() {
            window.removeEventListener('resize', this.handleResize);
        },
    }
</script>

<style scoped lang="scss">
    @import '~element-ui/lib/theme-chalk/index.css';
    .el-menu {

        height: calc(100vh - 103px);

        #nav-visibility {

            position: absolute;
            display: block;
            bottom: 30px;
            width: 100%;

        }

    }
    a:hover {

        text-decoration: none;

    }
</style>
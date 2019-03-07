<template>
    <el-menu :default-openeds="defaultOpen" :default-active="defaultActive" :collapse="collapse" :style="menuWidth">
        <el-menu-item index="0">
            <i class="uk-icon margin-right" uk-icon="icon:home;ratio:0.8;"></i>
            <span>Dashboard</span>
        </el-menu-item>
        <el-submenu index="1">
            <template slot="title">
                <i class="el-icon-location-outline"></i>
                <span>Spots</span>
            </template>
            <el-menu-item index="1-1">Types</el-menu-item>
            <el-menu-item index="1-2">Classifications</el-menu-item>
            <el-menu-item index="1-3">Categories</el-menu-item>
            <el-menu-item index="1-4">Descriptors</el-menu-item>
        </el-submenu>
        <el-submenu index="2">
            <template slot="title">
                <i class="uk-icon margin-right" uk-icon="icon:user;ratio:0.8;"></i>
                <span>Users</span>
            </template>
            <el-menu-item index="2-1">All Users</el-menu-item>
            <el-menu-item index="2-2">Staff Manager</el-menu-item>
        </el-submenu>
        <el-menu-item index="3-1">
            <i class="el-icon-setting"></i>
            <span>Settings</span>
        </el-menu-item>
        <div id="nav-visibility" class="center">
            <el-button :icon="visibilityIcon" circle @click="manualCollapse = !manualCollapse"></el-button>
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
        props: ['defaultActivated', 'defaultOpened'],
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
            }
        },
        created() {
            window.addEventListener('resize', this.handleResize);
            this.handleResize();
        },
        destroyed() {
            window.removeEventListener('resize', this.handleResize);
        },
        methods: {
            handleResize() {
                this.window.width = window.innerWidth;
            }
        }
    }
</script>

<style scoped lang="scss">
    @import '~element-ui/lib/theme-chalk/index.css';
    .el-menu {
        height: 100vh;
        #nav-visibility {
            position: absolute;
            display: block;
            bottom: 120px;
            width: 100%; // Calc to prevent webkit bug
        }
    }
</style>
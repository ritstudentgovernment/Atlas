<template>
    <el-row :gutter="20">
        <el-col :md="6" :sm="24" class="margin-bottom">
            <stat-card>
                <span slot="header">Number of Spots</span>
                <span slot="subtext">spots</span>
                {{ totalNumberSpots }}
            </stat-card>
            <stat-card class="margin-top">
                <span slot="header">Number of Unapproved Spots</span>
                <span slot="subtext">spots</span>
                {{ numberUnapprovedSpots }}
            </stat-card>
            <stat-card class="margin-top">
                <span slot="header">Number of Users</span>
                <span slot="subtext">users</span>
                {{ numberUsers }}
            </stat-card>
        </el-col>
        <el-col :md="18" :sm="24">
            <category-graphs></category-graphs>
        </el-col>
    </el-row>
</template>

<script>
    import StatCard from './StatCard';
    import CategoryGraphs from './CategoryGraphs';

    export default {
        name: "Dashboard",
        components: {StatCard, CategoryGraphs},
        data () {
            return {
                numberUnapprovedSpots: 0,
                totalNumberSpots: 0,
                numberUsers: 0
            };
        },
        methods: {
            setupStatCards () {
                window.adminApi.get('users/')
                    .then((response) => {
                        this.numberUsers = response.data.length;
                    })
                    .catch((error) => {
                        //
                    });
                window.adminApi.get('spots/stats')
                    .then((response) => {
                        this.totalNumberSpots = response.data.totalNumberSpots;
                        this.numberUnapprovedSpots = response.data.numberUnapprovedSpots;
                    })
                    .catch((error) => {
                        //
                    });
            },
            setup () {
                this.setupStatCards();
            }
        },
        created () {
            window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
            window.onLoadedQueue.push(this.setup);
        }
    }
</script>

<style scoped>

</style>
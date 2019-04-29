<template>
    <el-row :gutter="20">
        <el-col :md="8" :sm="12" class="margin-bottom">
            <number-unapproved-spots></number-unapproved-spots>
        </el-col>
        <el-col :md="16" :sm="24">
            <category-graphs></category-graphs>
        </el-col>
    </el-row>
</template>

<script>
    import NumberUnapprovedSpots from './NumberUnapprovedSpots';
    import CategoryGraphs from './CategoryGraphs';
    export default {
        name: "Dashboard",
        components: {NumberUnapprovedSpots, CategoryGraphs}
    }
</script>

<style scoped>

</style>
<template>
    <el-row :gutter="20">
        <el-col :span="8">
            <number-unapproved-spots></number-unapproved-spots>
        </el-col>
        <el-col :span="16">
            <el-card shadow="never">

            </el-card>
        </el-col>
    </el-row>
</template>

<script>
    import NumberUnapprovedSpots from './NumberUnapprovedSpots';
    export default {
        name: "Dashboard",
        components: {NumberUnapprovedSpots}
    }
</script>

<style scoped>

</style>
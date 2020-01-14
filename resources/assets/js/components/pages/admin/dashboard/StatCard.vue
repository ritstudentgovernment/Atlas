<template>
    <el-card shadow="never">
        <div slot="header">
            <slot name="header"></slot>
        </div>
        <div class="big-number">
            <h1><slot></slot></h1>
            <h5><slot name="subtext"></slot></h5>
        </div>
    </el-card>
</template>

<script>
    export default {
        name: "StatCard"
    }
</script>

<style scoped lang="scss">
    .big-number {

        height: 100px;
        line-height: 100px;
        text-align: center;

        * {

            margin: 0;
            display: inline-block;
            vertical-align: middle;

        }

        h1 {

            font-size: 4em;

        }

        h5 {

            text-transform: uppercase;

        }

    }
</style>
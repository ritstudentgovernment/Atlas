<template>
    <el-card shadow="never">
        <div slot="header">
            Number of Unapproved Spots
        </div>
        <div class="big-number">
            <h1>{{ number }}</h1>
            <h5>spots</h5>
        </div>
    </el-card>
</template>

<script>
    export default {
        name: "NumberUnapprovedSpots",
        data () {
            return {
                number: 0
            };
        },
        methods: {
            setup () {
                window.adminApi.get('spots/unapproved')
                    .then((response) => {
                        this.number = response.data;
                    })
                    .catch((error) => {
                        //
                    });
            }
        },
        created () {
            window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
            window.onLoadedQueue.push(this.setup);
        }
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
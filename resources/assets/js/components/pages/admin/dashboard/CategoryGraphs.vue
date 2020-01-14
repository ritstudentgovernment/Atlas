<template>
    <el-card shadow="never">
        <div slot="header">Categories</div>
        <el-row :gutter="20">
            <el-col v-for="(category, index) in categories"
                    :key="category.name"
                    :lg="12" :md="24">
                <br v-if="index > 0" class="hidden-md-and-up" />
                <el-card shadow="never">
                    <div slot="header">{{ category.name }}</div>
                    <div>
                        <el-progress
                                v-for="classification in category.classifications"
                                :percentage="classificationPercentage(classification, category)"
                                :color="`#${classification.color}`"
                                :key="classification.name"
                                class="margin-right"
                                status="text"
                                type="circle">
                            <br />{{ classification.name }}<br />
                            <br />({{ classification.numSpots }} spot<span v-if="classification.numSpots > 1">s</span>)
                        </el-progress>
                    </div>
                </el-card>
            </el-col>
        </el-row>
    </el-card>
</template>

<script>
    import 'element-ui/lib/theme-chalk/display.css';
    export default {
        name: "CategoryGraphs",
        data () {
            return {
                categories: []
            };
        },
        methods: {
            classificationPercentage (classification, category) {
                return Math.round((classification.numSpots/category.numSpots) * 100);
            },
            setup () {
                window.adminApi.api.get('spots/categories')
                    .then((response) => {
                        console.log(response);
                        this.categories = response.data;
                    })
                    .catch(() => {
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
    .category-card {
        &:not(:last-of-type) {
            margin-bottom: 10px;
        }
    }
</style>
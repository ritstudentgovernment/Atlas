<template>
    <el-card shadow="never">
        <div slot="header">Categories</div>
        <el-row :gutter="20">
            <el-col v-for="(category, index) in categories"
                    :key="category.name"
                    :lg="12" :md="24"
                    class="margin-bottom">
                <br v-if="index > 0" class="hidden-md-and-up" />
                <el-card shadow="hover" class="material-hover cursor" @click.native="view(category.name)">
                    <div slot="header">{{ category.name }} Spots</div>
                    <el-row :gutter="0">
                        <el-col v-for="classification in category.classifications"
                                :key="category.name + '-' + classification.name"
                                :span="Math.ceil(24 / category.classifications.length)"
                                class="text-center">
                            <el-progress
                                    :percentage="classificationPercentage(classification, category)"
                                    :color="`#${classification.color}`"
                                    :key="classification.name"
                                    :width="95"
                                    type="circle">
                            </el-progress>
                            <p class="center margin-top">
                                {{ classification.name }} <br>
                                <el-badge class="mark" :value="classification.numSpots + ' Spots'" />
                            </p>
                        </el-col>
                    </el-row>
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
                        this.categories = response.data;
                    })
                    .catch(() => {
                        //
                    });
            },
            view (category) {
                window.location.href = 'admin/spots/categories/' + category;
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
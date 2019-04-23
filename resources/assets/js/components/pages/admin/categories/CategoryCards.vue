<template>
    <el-row :gutter="20">
        <el-col
                v-for="(category, index) in categories"
                :key="'category-'+index"
                :xl="6"
                :lg="7"
                :md="8"
                :sm="12"
                :xs="24">
            <category-card :category="category" @deleted="handleDeleted" class="material-hover margin-bottom"></category-card>
        </el-col>
        <el-col :sx="24" :sm="12" :md="8" :lg="7" :xl="6">
            <new-category-card class="margin-bottom" :active="newCategoryOpen"></new-category-card>
        </el-col>
    </el-row>
</template>

<script>
    import CategoryCard from "./CategoryCard";
    import ElementUI from "element-ui";
    import NewCategoryCard from "./NewCategoryCard";

    export default {
        name: 'CategoryCards',
        components: {
            NewCategoryCard,
            CategoryCard,
            ElementUI
        },
        props: ['rawCategories'],
        data () {
            return {
                categories: [],
                newCategoryOpen: false,
            };
        },
        methods: {
            handleDeleted (name) {
                this.categories = this.categories.filter((category) => {
                    return category.name !== name;
                });
            },
            setup () {
                if ($.urlParam('newCategory')) {
                    this.newCategoryOpen = true;
                }
            }
        },
        created () {
            window.cc = this;
            this.categories = JSON.parse(this.rawCategories);
            window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
            window.onLoadedQueue.push(this.setup);
        }
    }
</script>

<style scoped lang="scss">

</style>
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
            <new-category-card class="margin-bottom"></new-category-card>
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
                categories: []
            };
        },
        methods: {
            handleDeleted (name) {
                this.categories = this.categories.filter((category) => {
                    return category.name !== name;
                });
            }
        },
        created () {
            this.categories = JSON.parse(this.rawCategories);
            window.cc = this;
        }
    }
</script>

<style scoped lang="scss">

</style>
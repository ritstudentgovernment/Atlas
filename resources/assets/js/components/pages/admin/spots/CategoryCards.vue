<template>
    <el-row :gutter="20">
        <el-col v-for="(category, index) in categories" :key="'category-'+index" :span="4">
            <category-card :category="category" class="material-hover"></category-card>
        </el-col>
        <new-category-card></new-category-card>
    </el-row>
</template>

<script>
    import CategoryCard from "./CategoryCard";
    import ElementUI from "element-ui";
    import NewCategoryCard from "./NewCategoryCard";

    export default {
        name: "CategoryCards",
        components: {
            NewCategoryCard,
            CategoryCard,
            ElementUI
        },
        props: ["rawCategories"],
        computed: {
            categories () {
                return JSON.parse(this.rawCategories);
            },
        },
    }
</script>

<style scoped lang="scss">

</style>
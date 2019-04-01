<template>
    <el-card shadow="never">
        <el-form ref="form" :model="category" label-width="120px">
            <el-row>
                <el-col :md="6" :lg="4" :xl="3">
                    <el-form-item label="Icon">
                        <el-input v-model="category.icon" maxlength="1" @change="handleUpdateCategory('icon')"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :sm="22" :md="16" :lg="18" :xl="19">
                    <el-form-item label="Crowdsource" class="left">
                        <el-switch v-model="category.crowdsource" @change="handleUpdateCategory('crowdsource')"></el-switch>
                    </el-form-item>
                    <el-form-item label="Active" class="left">
                        <el-switch v-model="category.active" @change="handleUpdateCategory('active')"></el-switch>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-form-item label="Description">
                    <el-input type="textarea" v-model="category.description" :rows="4" @change="handleUpdateCategory('description')"></el-input>
                </el-form-item>
            </el-row>
            <el-row>
                <el-form-item label="Types">
                    <types :raw-types="category.types" :category-id="category.id"></types>
                </el-form-item>
            </el-row>
            <el-row>
                <el-form-item label="Classifications">
                    <classifications :raw-classifications="category.classifications" :category-id="category.id"></classifications>
                </el-form-item>
            </el-row>
            <el-row>
                <el-form-item label="Descriptors">
                    <descriptors :raw-descriptors="category.descriptors" :category-id="category.id"></descriptors>
                </el-form-item>
            </el-row>
        </el-form>
    </el-card>
</template>

<script>
    import Classifications from './Classifications';
    import Descriptors from './Descriptors';
    import Types from './Types';

    export default {
        name: "CategoryEditor",
        data () {
            return {
                category: {
                    icon: '',
                    crowdsource: true,
                    active: false,
                    types: [],
                    classifications: [],
                    descriptors: [],
                    description: '',
                }
            }
        },
        props: ["rawCategory"],
        components: {Descriptors, Classifications, Types },
        methods: {
            handleUpdateCategory (property) {
                let modified = {};
                modified[property] = this.category[property];
                window.adminApi.post(`spots/category/${this.category.name}/update`, modified)
                    .then(() => {
                        this.$notify.success({
                            title: 'Updated Successfully',
                            message: `Changed category ${property.toLowerCase()}.`
                        });
                    })
                    .catch(() => {
                        this.$notify.error({
                            title: 'Error',
                            message: `Failed updating category ${property.toLowerCase()}.`
                        });
                    });
            },

        },
        created () {
            let category = JSON.parse(this.rawCategory);
            console.log(category);
            this.category = Object.assign({}, this.category, category);
            this.category.classifications.map((classification)=>{
                classification.color = `#${classification.color}`;
                return classification;
            });
            window.ace = this;
        },
    }
</script>

<style scoped lang="scss">

</style>
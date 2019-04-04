<template>
    <el-card :shadow="shadow">
        <div v-if="!clicked" class="text-center">
            <el-button round icon="el-icon-plus" @click="toggleClicked"></el-button>
        </div>
        <div v-else>
            <h4 class="text-center">New Category</h4>
            <el-form ref="form" :model="newCategory" size="small">
                <el-form-item>
                    <el-input v-model="newCategory.name" placeholder="category name"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input v-model="newCategory.icon" placeholder="icon character" maxlength="1"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input
                            type="textarea"
                            :rows="3"
                            v-model="newCategory.description"
                            placeholder="category description (optional)">
                    </el-input>
                </el-form-item>
                <el-form-item size="large">
                    <el-row :gutter="20">


                        <el-col :span="12">
                            <el-button
                                    type="primary"
                                    :disabled="!validCategory"
                                    @click="handleCreate()">
                                Create
                            </el-button>
                        </el-col>
                        <el-col :span="12">
                            <el-button class="right" @click="toggleClicked">Cancel</el-button>
                        </el-col>
                    </el-row>
                </el-form-item>
            </el-form>
        </div>
    </el-card>
</template>

<script>
    export default {
        name: "NewCategoryCard",
        data () {
            return {
                clicked: false,
                newCategory: {
                    name: '',
                    icon: '',
                    description: '',
                },
            };
        },
        computed: {
            shadow () {
                return this.clicked ? "hover" : "never";
            },
            validCategory () {
                return !(this.newCategory.name.trim() === '' || this.newCategory.icon.trim() === '');
            }
        },
        methods: {
            toggleClicked () {
                this.clicked = !this.clicked;
            },
            handleCreate () {
                window.adminApi.post('spots/category/create', this.newCategory)
                    .then(() => {
                        window.location.href = `categories/${this.newCategory.name}`;
                    })
                    .catch((error) => {
                        this.$notify({
                            title: "Error Creating Category",
                            message: error
                        });
                    });
            }
        },
    }
</script>

<style scoped lang="scss">
    .el-card{

        &.is-never-shadow {

            background-color: transparent;
            border: 1px solid #d7d7d9;
            padding: 37px;

            button {

                margin: 0 auto;
                border-radius: 50% !important;
                padding: 22px !important;

                &:hover {

                    cursor: pointer;

                }

            }

        }

        .el-form-item {

            &:last-of-type {

                margin-bottom: 0;

            }

        }

    }
</style>
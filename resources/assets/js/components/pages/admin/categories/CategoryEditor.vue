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
                <el-form-item label="Category Types">
                    <el-card shadow="never">
                        <el-tag
                                v-for="(type, index) in category.types"
                                :key="'category-type-'+index"
                                closable
                                :disable-transitions="false"
                                @close="handleRemoveType(type.id)">
                            {{ type.name }}
                        </el-tag>
                        <el-input
                                v-if="state.typeInputVisible"
                                v-model="state.newTypeName"
                                size="mini"
                                @keyup.enter.native="handleNewType"
                                @blur="handleNewType"
                                ref="typeInput"
                                class="input-new-type"
                        >
                        </el-input>
                        <el-button v-else class="button-new-type" size="small" @click="showTypeInput">+ New Type</el-button>
                    </el-card>
                </el-form-item>
            </el-row>
            <el-row>
                <el-form-item label="Classifications">
                    <el-card shadow="never">
                        <el-table :data="category.classifications">
                            <el-table-column type="expand">
                                <template slot-scope="scope">
                                    <el-form-item label="View Permission" label-width="140px">
                                        <el-input v-model="scope.row.view_permission" @change="handleUpdateClassification(scope.row)"></el-input>
                                    </el-form-item>
                                    <el-form-item label="Create Permission" label-width="140px" class="margin-top">
                                        <el-input v-model="scope.row.create_permission" @change="handleUpdateClassification(scope.row)"></el-input>
                                    </el-form-item>
                                </template>
                            </el-table-column>
                            <el-table-column label="Name">
                                <template slot-scope="scope">
                                    <el-input v-model="scope.row.name" @change="handleUpdateClassification(scope.row)"></el-input>
                                </template>
                            </el-table-column>
                            <el-table-column label="Color">
                                <template slot-scope="scope">
                                    <el-color-picker v-model="scope.row.color"></el-color-picker>
                                </template>
                            </el-table-column>
                            <el-table-column align="right">
                                <template slot-scope="scope">
                                    <el-button
                                            v-if="scope.row.temp"
                                            size="mini"
                                            :disabled="!newClassificationIsValid"
                                            @click="handleNewClassification(scope.row)">
                                        Save
                                    </el-button>
                                    <el-button
                                            size="mini"
                                            type="danger"
                                            @click="handleDelete(scope.$index, scope.row)">
                                        Delete
                                    </el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                        <div style="margin-top: 20px">
                            <el-button
                                    type="text"
                                    v-if="!hasTempClassification"
                                    @click="insertTempClassification()">
                                Add New Classification
                            </el-button>
                        </div>
                    </el-card>
                </el-form-item>
            </el-row>
            <el-row>
                <el-form-item label="Descriptors">
                    <el-card shadow="never">
                        <el-table :data="category.descriptors">
                            <el-table-column type="expand">
                                <template slot-scope="props">
                                    <strong>Default Value:</strong> {{ props.row.default_value }} <br />
                                    <strong>Allowed Values:</strong> {{ props.row.allowed_values.split('|').join(', ') }}
                                </template>
                            </el-table-column>
                            <el-table-column label="Name" prop="name"></el-table-column>
                            <el-table-column label="Icon" prop="icon"></el-table-column>
                            <el-table-column label="Type" prop="value_type"></el-table-column>
                            <el-table-column align="right">
                                <template slot-scope="scope">
                                    <el-button
                                            size="mini"
                                            @click="handleEdit(scope.$index, scope.row)">
                                        Edit
                                    </el-button>
                                    <el-button
                                            size="mini"
                                            type="danger"
                                            @click="handleDelete(scope.$index, scope.row)">
                                        Delete
                                    </el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                    </el-card>
                </el-form-item>
            </el-row>
        </el-form>
    </el-card>
</template>

<script>
    export default {
        name: "CategoryEditor",
        data() {
            return {
                state: {
                    typeInputVisible: false,
                    newTypeName: '',
                },
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
        computed: {
            hasTempClassification () {
                let classifications = this.category.classifications,
                    classification = classifications[classifications.length - 1];

                return classification.temp ? classification : false;
            },
            newClassificationIsValid () {
                let classification = this.hasTempClassification;

                if (classification) {
                    if (classification.name.trim() === "" || classification.color === null) {
                        return false;
                    }
                }
                return true;
            }
        },
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
            showTypeInput () {
                this.state.typeInputVisible = true;
                this.$nextTick(() => {
                    this.$refs.typeInput.$refs.input.focus();
                });
            },
            handleNewType () {
                let name = this.state.newTypeName,
                    category = this.category.id;
                if (name.trim() !== '') {
                    window.adminApi.post('spots/type/create', {
                        name: name,
                        category: category
                    })
                        .then((response) => {
                            this.category.types.push({
                                'name': response.data.name,
                                'id': response.data.id
                            });
                        })
                        .catch((error) => {
                            this.$notify.error({
                                title: 'Error',
                                message: 'Failed to create type'
                            });
                        });
                }
                this.state.newTypeName = '';
                this.state.typeInputVisible = false;
            },
            handleRemoveType (id) {
                this.$confirm('This will permanently delete the type. Continue?', 'Warning', {
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    type: 'danger'
                }).then(() => {
                    if (id < (new Date).getTime() - 86400000) {
                        window.adminApi.post(`spots/type/${id}/delete/`)
                            .then(() => {
                                this.category.types = this.category.types.filter((type) => {
                                    return type.id !== id;
                                });
                            })
                            .catch(() => {
                                this.$notify.error({
                                    title: 'Error',
                                    message: 'Failed to delete type'
                                });
                            });
                    } else {
                        this.category.types = this.category.types.filter((type) => {
                            return type.id !== id;
                        });
                    }
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Delete canceled'
                    });
                });
            },
            handleUpdateClassification (updatedClassification) {
                console.log(updatedClassification);
            },
            insertTempClassification () {
                this.category.classifications.push({
                    category_id: this.category.id,
                    color: null,
                    create_permission: null,
                    id: (new Date()).getTime(),
                    name: "",
                    view_permission: null,
                    temp: true
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

    .el-tag + .el-tag {

        margin-left: 10px;

    }
    .button-new-type {

        margin-left: 10px;
        height: 32px;
        line-height: 30px;
        padding-top: 0;
        padding-bottom: 0;
    }
    .input-new-type {

        width: 90px;
        margin-left: 10px;
        vertical-align: bottom;

    }

</style>
<template>
    <el-card shadow="never">
        <el-form ref="form" :model="category" label-width="120px">
            <el-row>
                <el-col :md="6" :lg="4" :xl="3">
                    <el-form-item label="Icon">
                        <el-input v-model="category.icon" maxlength="1"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :sm="22" :md="16" :lg="18" :xl="19">
                    <el-form-item label="Crowdsource" class="left">
                        <el-switch v-model="category.crowdsource"></el-switch>
                    </el-form-item>
                    <el-form-item label="Active" class="left">
                        <el-switch v-model="category.active"></el-switch>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-form-item label="Description">
                    <el-input type="textarea" v-model="category.description" :rows="4"></el-input>
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
        methods: {
            showTypeInput () {
                this.state.typeInputVisible = true;
                this.$nextTick(() => {
                    this.$refs.typeInput.$refs.input.focus();
                });
            },
            handleNewType () {
                let name = this.state.newTypeName;
                if (name.trim() !== '') {
                    this.category.types.push({'name': this.state.newTypeName, 'id': (new Date).getTime()});
                }
                this.state.newTypeName = '';
                this.state.typeInputVisible = false;
            },
            handleRemoveType (id) {
                this.category.types = this.category.types.filter((type) => {
                    return type.id !== id;
                })
            }
        },
        created () {
            let category = JSON.parse(this.rawCategory);
            console.log(category);
            this.category = Object.assign({}, this.category, category);
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
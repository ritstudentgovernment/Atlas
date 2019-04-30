<template>
    <el-card shadow="never">
        <el-tag
                v-for="(type, index) in types"
                :key="'category-type-'+index"
                closable
                :disable-transitions="false"
                @close="handleRemoveType(type.id)">
            {{ type.name }}
        </el-tag>
        <el-input
                v-if="typeInputVisible"
                v-model="newTypeName"
                size="mini"
                @keyup.enter.native="handleNewType"
                @blur="handleNewType"
                ref="typeInput"
                class="input-new-type"
        >
        </el-input>
        <el-button v-else class="button-new-type" size="small" @click="showTypeInput">+ New Type</el-button>
    </el-card>
</template>

<script>
    export default {
        name: 'types',
        props: ['rawTypes', 'categoryId'],
        data () {
            return {
                types: [],
                newTypeName: '',
                typeInputVisible: false
            };
        },
        methods: {
            showTypeInput () {
                this.typeInputVisible = true;
                this.$nextTick(() => {
                    this.$refs.typeInput.$refs.input.focus();
                });
            },
            handleRemoveType (id) {
                this.$confirm('This will permanently delete the type. Continue?', 'Warning', {
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    showClose: false,
                    type: 'warning'
                }).then(() => {
                    window.adminApi.post(`spots/type/${id}/delete/`)
                        .then(() => {
                            this.types = this.types.filter((type) => {
                                return type.id !== id;
                            });
                        })
                        .catch(() => {
                            this.$notify.error({
                                title: 'Error',
                                message: 'Failed to delete type'
                            });
                        });
                }).catch(() => {
                    this.$notify.info('Delete canceled');
                });
            },
            handleNewType () {
                let name = this.newTypeName,
                    category = this.categoryId;
                if (name.trim() !== '') {
                    window.adminApi.post('spots/type/create', {
                        name: name,
                        category: category
                    })
                        .then((response) => {
                            this.types.push({
                                'name': response.data.name,
                                'id': response.data.id
                            });
                        })
                        .catch((error) => {
                            console.error(error);
                            this.$notify.error({
                                title: 'Error',
                                message: 'Failed to create type'
                            });
                        });
                }
                this.newTypeName = '';
                this.typeInputVisible = false;
            },
        },
        created () {
            this.types = this.rawTypes;
        }
    }
</script>

<style scoped>

    .el-tag {

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
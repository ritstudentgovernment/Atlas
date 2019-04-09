<template>
    <el-card shadow="never">
        <el-table :data="classifications" :default-sort="{prop: 'id', order: 'ascending'}">
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
            <el-table-column label="ID" type="index" prop="id" sortable></el-table-column>
            <el-table-column label="Name">
                <template slot-scope="scope">
                    <el-input v-model="scope.row.name" @change="handleUpdateClassification(scope.row)"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="Type">
                <template slot-scope="scope">
                    <el-select v-model="scope.row.type" @change="handleUpdateClassification(scope.row)">
                        <el-option label="Public" value="public"></el-option>
                        <el-option label="Designated" value="designated"></el-option>
                        <el-option label="Under Review" value="under review"></el-option>
                    </el-select>
                </template>
            </el-table-column>
            <el-table-column label="Color">
                <template slot-scope="scope">
                    <el-color-picker v-model="scope.row.color" @change="handleUpdateClassification(scope.row)"></el-color-picker>
                </template>
            </el-table-column>
            <el-table-column align="right">
                <template slot-scope="scope">
                    <el-button
                            v-if="scope.row.temp"
                            size="mini"
                            :disabled="!newClassificationIsValid"
                            @click="handleNewClassification(scope.$index, scope.row)">
                        Save
                    </el-button>
                    <el-button
                            size="mini"
                            type="danger"
                            :disabled="scope.row.temp && classifications.length === 1"
                            @click="handleDeleteClassification(scope.$index, scope.row)">
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
</template>

<script>
    export default {
        name: "classifications",
        props: ["rawClassifications", "categoryId"],
        data () {
            return {
                classifications: []
            };
        },
        computed: {
            hasTempClassification () {
                let classifications = this.classifications,
                    classification = classifications[classifications.length - 1];

                return classification.temp ? classification : false;
            },
            newClassificationIsValid () {
                let classification = this.hasTempClassification;

                if (classification) {
                    return !(
                        classification.name.trim() === "" ||
                        classification.color === null
                    );

                }
                return true;
            }
        },
        methods: {
            insertTempClassification () {
                this.classifications.push({
                    id: (new Date()).getTime(),
                    category_id: this.categoryId,
                    create_permission: null,
                    view_permission: null,
                    color: null,
                    temp: true,
                    type: "",
                    name: ""
                });
            },
            handleUpdateClassification (updatedClassification) {
                if (!updatedClassification.temp) {
                    let color = updatedClassification.color ? updatedClassification.color : "",
                        classification = {
                        create_permission: updatedClassification.create_permission,
                        view_permission: updatedClassification.view_permission,
                        color: color.replace("#", ""),
                        type: updatedClassification.type,
                        name: updatedClassification.name
                    };
                    window.adminApi.post(`spots/classification/${updatedClassification.id}/update`, classification)
                        .then(() => {
                            this.$notify.success({
                                title: "Updated Classification",
                                message: ""
                            });
                        })
                        .catch((error) => {
                            console.log(error);
                            this.$notify.error({
                                title: "Error Updating Classification",
                                message: error
                            })
                        });
                }
            },
            handleDeleteClassification (index, classification) {
                if (classification.temp) {
                    this.classifications.splice(index, 1);
                } else {
                    this.$confirm('Choose Delete Method:', 'Warning: This action is irreversible!', {
                        distinguishCancelAndClose: true,
                        confirmButtonText: 'Delete Classifications and Spots',
                        confirmButtonClass: 'el-button--danger margin-top-important',
                        cancelButtonText: 'Delete Classifications Only',
                        center: true
                    })
                        .then(() => {
                            window.adminApi.post(`spots/classification/${classification.id}/delete`)
                                .then(() => {
                                    this.$notify.success({
                                        title: "Deleted Classification and Spots Successfully",
                                        message: ""
                                    });
                                    this.classifications.splice(index, 1);
                                })
                                .catch((error) => {
                                    this.$notify.error({
                                        title: "Error Deleting Classifications and Spots",
                                        message: error
                                    });
                                });
                        })
                        .catch((action) => {
                            if (action === 'cancel') { // soft delete
                                window.adminApi.post(`spots/classification/${classification.id}/delete/soft`)
                                    .then(() => {
                                        this.$notify.success({
                                            title: "Deleted Classification Successfully",
                                            message: "Spots with the classification remain unchanged."
                                        });
                                        this.classifications.splice(index, 1);
                                    })
                                    .catch((error) => {
                                        this.$notify.error({
                                            title: "Error Deleting Classifications",
                                            message: error
                                        });
                                    });
                            }
                        });
                }
            },
            handleNewClassification (index, classification) {
                let newClassification = {
                    category_id: this.categoryId,
                    create_permission: classification.create_permission,
                    view_permission: classification.view_permission,
                    color: classification.color.replace('#', ''),
                    name: classification.name
                };
                window.adminApi.post('spots/classification/create', newClassification)
                    .then((response) => {
                        this.$notify.success({
                            title: "Classification Created Successfully",
                            message: ""
                        });
                        this.classifications[index].id = response.id;
                        this.classifications[index].temp = false;
                    })
                    .catch((error) => {
                        this.$notify.error({
                            title: "Error Creating Classification",
                            message: error
                        });
                    });
            }
        },
        created () {
            this.classifications = this.rawClassifications;
            if (this.classifications.length === 0) {
                this.insertTempClassification();
            }
        }
    }
</script>

<style scoped lang="scss">

</style>
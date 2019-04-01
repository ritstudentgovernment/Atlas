<template>
    <el-card shadow="never">
        <el-table :data="classifications">
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
                            @click="handleNewClassification(scope.$index, scope.row)">
                        Save
                    </el-button>
                    <el-button
                            size="mini"
                            type="danger"
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
                    if (classification.name.trim() === "" || classification.color === null) {
                        return false;
                    }
                }
                return true;
            }
        },
        methods: {
            handleUpdateClassification (updatedClassification) {
                console.log(updatedClassification);
            },
            insertTempClassification () {
                this.classifications.push({
                    category_id: this.categoryId,
                    color: null,
                    create_permission: null,
                    id: (new Date()).getTime(),
                    name: "",
                    view_permission: null,
                    temp: true
                });
            },
            handleDeleteClassification (index, classification) {
                // classification.temp ?:
                //   true -> remove index
                //   false:
                //     Api -> delete (classification.id):
                //       success -> remove index
                //       failure -> display error
            },
            handleNewClassification (index, classification) {
                // filter out classification.id
                // Api -> create(classification):
                //   success:
                //     update classification.id to response.id
                //     set classification.temp to false
                //   failure -> display error
            }
        },
        created () {
            this.classifications = this.rawClassifications;
        }
    }
</script>

<style scoped>

</style>
<template>
    <el-card shadow="never">
        <el-table :data="descriptors" :default-sort = "{prop: 'id', order: 'descending'}">
            <el-table-column type="expand">
                <template slot-scope="scope">
                    <el-form-item label="Default Value" label-width="140px">
                        <el-input v-model="scope.row.default_value" @change="handleUpdate(scope.row)"></el-input>
                    </el-form-item>
                    <el-form-item label="Allowed Values" label-width="140px" class="margin-top">
                        <el-input v-model="scope.row.allowed_values" @change="handleUpdate(scope.row)"></el-input>
                    </el-form-item>
                </template>
            </el-table-column>
            <el-table-column label="Name">
                <template slot-scope="scope">
                    <el-input v-model="scope.row.name" @change="handleUpdate(scope.row)"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="Icon" prop="icon">
                <template slot-scope="scope">
                    <el-input v-model="scope.row.icon" @change="handleUpdate(scope.row)"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="Type">
                <template slot-scope="scope">
                    <el-select v-model="scope.row.value_type" @change="handleUpdate(scope.row)">
                        <el-option label="Select" value="select"></el-option>
                        <el-option label="Number" value="number"></el-option>
                    </el-select>
                </template>
            </el-table-column>
            <el-table-column align="right">
                <template slot-scope="scope">
                    <el-button
                            v-if="scope.row.temp"
                            size="mini"
                            :disabled="!newDescriptorIsValid"
                            @click="handleNewDescriptor(scope.$index, scope.row)">
                        Save
                    </el-button>
                    <el-button
                            size="mini"
                            type="danger"
                            :disabled="scope.row.temp && descriptors.length === 1"
                            @click="handleDelete(scope.$index, scope.row)">
                        Delete
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <div style="margin-top: 20px">
            <el-button
                    type="text"
                    v-if="!hasTempDescriptor"
                    @click="insertTempDescriptor()">
                Add New Descriptor
            </el-button>
        </div>
        <div class="dim-text text-center">
            A list of icons to choose from can be found <a href="https://getuikit.com/docs/icon#library" target="_blank" class="el-button--text">here</a>.
        </div>
    </el-card>
</template>

<script>
    export default {
        name: "Descriptors",
        props: ["rawDescriptors", "categoryId"],
        data () {
            return {
                descriptors: []
            };
        },
        computed: {
            hasTempDescriptor () {
                let descriptor = this.descriptors[this.descriptors.length - 1];
                return descriptor.temp ? descriptor : false;
            },
            newDescriptorIsValid () {
                let descriptor = this.hasTempDescriptor;

                if (descriptor) {
                    return !(
                        descriptor.name.trim() === "" ||
                        descriptor.icon.trim() === "" ||
                        descriptor.value_type.trim() === "" ||
                        descriptor.default_value.trim() === "" ||
                        descriptor.allowed_values.trim() === ""
                    );
                }
                return true;
            },
        },
        methods: {
            insertTempDescriptor () {
                this.descriptors.push({
                    id: (new Date()).getTime(),
                    allowed_values: "",
                    default_value: "",
                    value_type: "",
                    temp: true,
                    name: "",
                    icon: ""
                });
            },
            parseAllowedValues (descriptor) {
                return descriptor.allowed_values.split(",").map((value) => {
                    return value.trim();
                }).join("|");
            },
            handleNewDescriptor (index, descriptor) {
                let newDescriptor = {
                    category_id: this.categoryId,
                    name: descriptor.name,
                    value_type: descriptor.value_type,
                    default_value: descriptor.default_value,
                    allowed_values: this.parseAllowedValues(descriptor),
                    icon: descriptor.icon
                };
                window.adminApi.post('spots/descriptor/create', newDescriptor)
                    .then((response) => {
                        this.$notify.success({
                            title: "Descriptor Created Successfully",
                            message: ""
                        });
                        this.descriptors[index].id = response.id;
                        this.descriptors[index].temp = false;
                    })
                    .catch((error) => {
                        this.$notify.error({
                            title: "Error Creating Descriptor",
                            message: error
                        });
                    });
            },
            handleUpdate (descriptor) {
                if (!descriptor.temp) {
                    let updatedDescriptor = {
                        name: descriptor.name,
                        value_type: descriptor.value_type,
                        default_value: descriptor.default_value,
                        allowed_values: this.parseAllowedValues(descriptor),
                        icon: descriptor.icon
                    };
                    window.adminApi.post(`spots/descriptor/${descriptor.id}/update`, updatedDescriptor)
                        .then(() => {
                            this.$notify.success({
                                title: "Updated Descriptor",
                                message: ""
                            });
                        })
                        .catch((error) => {
                            console.log(error);
                            this.$notify.error({
                                title: "Error Updating Descriptor",
                                message: error
                            })
                        });
                }
            },
            handleDelete (index, descriptor) {
                if (descriptor.temp) {
                    this.descriptors.splice(index, 1);
                } else {
                    this.$confirm(`Delete Descriptor: ${descriptor.name}`, 'Warning: This action is irreversible!', {
                        confirmButtonText: 'Delete',
                        confirmButtonClass: 'el-button--danger margin-top-important',
                        cancelButtonText: 'Cancel',
                        center: true
                    })
                        .then(() => {
                            window.adminApi.post(`spots/descriptor/${descriptor.id}/delete`)
                                .then(() => {
                                    this.$notify.success({
                                        title: "Deleted Descriptor Successfully",
                                        message: ""
                                    });
                                    this.descriptors.splice(index, 1);
                                })
                                .catch((error) => {
                                    this.$notify.error({
                                        title: "Error Deleting Descriptor",
                                        message: error
                                    });
                                });
                        })
                        .catch(() => {
                            this.$notify.info('Delete canceled');
                        });
                }
            }
        },
        created () {
            this.descriptors = this.rawDescriptors;
            this.descriptors.map((descriptor) => {
                descriptor.allowed_values = descriptor.allowed_values.split("|").join(", ");
                return descriptor;
            });
            if (this.descriptors.length === 0) {
                this.insertTempDescriptor();
            }
        }
    }
</script>

<style scoped>

</style>
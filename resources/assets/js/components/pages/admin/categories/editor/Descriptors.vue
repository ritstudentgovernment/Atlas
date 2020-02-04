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
                    <el-autocomplete
                            class="inline-input full-width"
                            v-model="scope.row.name"
                            :fetch-suggestions="suggestions"
                            :trigger-on-focus="false"
                            @select="handleSelect(scope.$index, scope.row)"
                            @change="handleUpdate(scope.row)"
                    ></el-autocomplete>
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
                        <el-option label="Multi-Select" value="multiSelect"></el-option>
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
                        Remove
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
                descriptors: [],
                allDescriptors: []
            };
        },
        computed: {
            hasTempDescriptor () {
                let descriptor = this.descriptors[this.descriptors.length - 1];
                return descriptor.temp ? descriptor : false;
            },
            newDescriptorIsValid () {
                let descriptor = this.hasTempDescriptor;
                let existingDescriptors = this.allDescriptors;

                if (descriptor) {
                    return !(
                        descriptor.name.trim() === "" ||
                        descriptor.icon.trim() === "" ||
                        descriptor.value_type.trim() === "" ||
                        descriptor.default_value.trim() === "" ||
                        descriptor.allowed_values.trim() === "" ||
                        existingDescriptors.includes(descriptor.name)
                    );
                }

                return true;
            },
        },
        methods: {
            searchFilter (queryString) {
                return (descriptor) => {
                    return (descriptor.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
                };
            },
            suggestions (queryString, cb) {
                let results = queryString ? this.allDescriptors.filter(this.searchFilter(queryString)) : this.allDescriptors;
                cb(results);
            },
            handleSelect (index, descriptor) {
                window.adminApi.get('spots/descriptor/' + descriptor.name)
                    .then((response) => {
                        response.data.allowed_values.split("|").join(", ");
                        this.$set(this.descriptors, index, response.data);
                        this.handleUpdate(this.descriptors[index], true);
                    });
            },
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
            splitTrimJoin (string, splitOn, joinWith) {
                return string.split(splitOn).map((value) => {
                    return value.trim();
                }).join(joinWith);
            },
            // method called by parseAllowedValues when parsing a "select" descriptor
            parseAllowedSelectValues (descriptor) {
                return this.splitTrimJoin(descriptor.allowed_values, ",", "|");
            },
            parseAllowedMultiSelectValues (descriptor) {
                return this.parseAllowedSelectValues(descriptor);
            },
            parseAllowedNumberValues (descriptor) {
                return this.splitTrimJoin(descriptor.allowed_values, "-", "-");
            },
            parseAllowedValues (descriptor) {
                let parser = 'parseAllowed' + window.capitalize(descriptor.value_type) + 'Values';
                return this[parser] ? this[parser](descriptor) : descriptor.allowed_values;
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
            handleUpdate (descriptor, reuseDescriptor = false) {
                if (!descriptor.temp) {
                    let updatedDescriptor = {
                        name: descriptor.name,
                        value_type: descriptor.value_type,
                        default_value: descriptor.default_value,
                        allowed_values: this.parseAllowedValues(descriptor),
                        icon: descriptor.icon,
                        category_id: reuseDescriptor ? this.categoryId : null
                    };
                    window.adminApi.post(`spots/descriptor/${descriptor.id}/update`, updatedDescriptor)
                        .then(() => {
                            this.$notify.success({
                                title: "Updated Descriptor",
                                message: ""
                            });
                        })
                        .catch((error) => {
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
                    this.$confirm(`Remove the '${descriptor.name}' descriptor?`, '', {
                        confirmButtonText: 'Remove it!',
                        confirmButtonClass: 'el-button--danger margin-top-important',
                        cancelButtonText: 'Nope, keep it.',
                        center: true
                    })
                        .then(() => {
                            let params = { category_id: this.categoryId };
                            window.adminApi.post(`spots/descriptor/${descriptor.id}/delete`, params)
                                .then((response) => {
                                    this.$notify.success({
                                        title: response.data,
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
            },
            setup () {
                window.adminApi.get('spots/descriptor/list')
                    .then((response) => {
                        this.allDescriptors = response.data.map((descriptor) => {
                             return { 'value' : descriptor.name };
                        });
                    });
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

            window.dsc = this;
            window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
            window.onLoadedQueue.push(this.setup);
        }
    }
</script>

<style scoped>

</style>
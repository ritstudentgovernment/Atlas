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
                    <el-select v-model="scope.row.value_type">
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
                        descriptor.type.trim() === "" ||
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
            handleNewDescriptor (index, descriptor) {

            },
            handleUpdate (index, descriptor) {

            },
            handleDelete (index, descriptor) {

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
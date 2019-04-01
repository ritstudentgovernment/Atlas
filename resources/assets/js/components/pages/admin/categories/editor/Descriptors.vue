<template>
    <el-card shadow="never">
        <el-table :data="descriptors">
            <el-table-column type="expand">
                <template slot-scope="scope">
                    <strong>Default Value:</strong> {{ scope.row.default_value }} <br />
                    <strong>Allowed Values:</strong> {{ scope.row.allowed_values.split('|').join(', ') }}
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
        methods: {
            handleEdit (index, descriptor) {

            },
            handleDelete (index, descriptor) {

            }
        },
        created () {
            this.descriptors = this.rawDescriptors;
        }
    }
</script>

<style scoped>

</style>
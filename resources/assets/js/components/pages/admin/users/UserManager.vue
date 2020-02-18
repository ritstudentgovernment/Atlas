<template>
    <el-card>
        <el-table
                :default-sort="{prop: 'id', order: 'ascending'}"
                :data="users.filter(data => !search || data.first_name.toLowerCase().includes(search.toLowerCase()))"
                style="width: 100%">
            <el-table-column
                    label="User ID"
                    prop="id"
                    sortable>
            </el-table-column>
            <el-table-column
                    label="First Name"
                    prop="first_name"
                    sortable>
            </el-table-column>
            <el-table-column
                    label="Last Name"
                    prop="last_name"
                    sortable>
            </el-table-column>
            <el-table-column
                    label="Email"
                    prop="email"
                    sortable>
            </el-table-column>
            <el-table-column
                    label="Number of Spots"
                    prop="numSpotsCreated"
                    sortable>
            </el-table-column>
            <el-table-column align="right">
                <template slot="header" slot-scope="scope">
                    <el-input
                            size="mini"
                            v-model="search"
                            placeholder="Type to search"
                            clearable/>
                </template>
            </el-table-column>
        </el-table>
    </el-card>
</template>

<script>
    import SearchUsers from "./SearchUsers";
    export default {
        name: "UserManager",
        components: {SearchUsers},
        props: ["rawUsers"],
        data () {
            return {
                user: null,
                users: [],
                search: "",
            };
        },
        methods: {
            handleUserSelected (user) {
                this.user = user;
            }
        },
        created () {
            this.users = this.rawUsers;
        }
    }
</script>

<style scoped>

</style>
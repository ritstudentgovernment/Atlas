<template>
    <div>
        <el-card>
            <h3>Find A User</h3>
            <el-row :gutter="20">
                <el-col :sm="12" :md="14" :lg="18">
                    <search-users remote="users/" @user-selected="handleUserSelected" :selected="user"></search-users>
                </el-col>
                <el-col :sm="12" :md="10" :lg="6">
                    <el-row :gutter="20">
                        <el-col :span="12">
                            <el-button v-if="user == null || !isAdmin" :disabled="user == null || isReviewer" @click="updateUserRole('admin', true)" type="primary" class="full-width">Make Admin</el-button>
                            <el-button v-else @click="updateUserRole('admin', false)" type="primary" class="full-width">Remove Admin</el-button>
                        </el-col>
                        <el-col :span="12">
                            <el-button v-if="user == null || !isReviewer" :disabled="user == null || isAdmin" @click="updateUserRole('reviewer', true)" type="success" class="full-width">Make Reviewer</el-button>
                            <el-button v-else @click="updateUserRole('reviewer', false)" type="success" class="full-width">Remove Reviewer</el-button>
                        </el-col>
                    </el-row>
                </el-col>
            </el-row>
        </el-card>
        <el-card class="margin-top">
            <h3>Staff Users</h3>
            <el-table
                    :default-sort="{prop: 'id', order: 'ascending'}"
                    :data="users.filter(data => !search || data.name.toLowerCase().includes(search.toLowerCase()))"
                    style="width: 100%">
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
                <el-table-column label="Role">
                    <template slot-scope="scope">
                        <el-tag
                                v-for="role in scope.row.roles"
                                :key="'role-' + role.name"
                                closable
                                class="capitalize"
                                :type="role.name === 'admin' ? 'primary' : 'success'"
                                @close="updateUserRole(role.name, false, scope.row)">
                            {{role.name}}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="right">
                    <template slot="header">
                        <el-input
                                v-model="search"
                                size="mini"
                                placeholder="Type to search"/>
                    </template>
                    <template slot-scope="scope">
                        <el-button
                                size="mini"
                                @click="handleEdit(scope.row)">
                            Edit
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script>
    import SearchUsers from "./SearchUsers";
    export default {
        name: "StaffManager",
        components: {SearchUsers},
        props: ["rawUsers"],
        data () {
            return {
                user: null,
                users: [],
                search: ''
            };
        },
        computed: {
            isAdmin () {
                return this.user ? this.user.isAdmin : false;
            },
            isReviewer () {
                return this.user ? this.user.isReviewer : false;
            }
        },
        methods: {
            handleUserSelected (selected) {
                this.user = null;

                if (selected) {
                    this.user = selected.user;
                }
            },
            updateUserRole (role, value, row = null) {
                this.user = row ? row : this.user;
                let promoteOrDemote = value ? "promote" : "demote";
                window.adminApi.post(`users/${this.user.id}/${promoteOrDemote}/${role}`)
                    .then((response) => {
                        this.updateUserProperties(response.data, this);
                        let userWasStaff = this.users.indexOf(this.user) !== -1;
                        if (value && !userWasStaff) {
                            this.users.push(this.user);
                        }
                        this.users = this.users.filter((user) => {
                            return user.isAdmin || user.isReviewer;
                        });
                    })
                    .catch((error) => {
                        this.$notify.error({
                            title: "Error changing user role",
                            message: error.response.data
                        });
                    });
            },
            handleEdit (user) {
                this.user = user;
            },
            adminFormatter (value) {
                return value.isAdmin ? "Yes" : "No";
            },
            reviewerFormatter (value) {
                return value.isReviewer ? "Yes" : "No";
            },
            updateUserProperties (newUser, reference) {
                Object.keys(newUser).forEach(function(key, _) {
                   reference.user[key] = newUser[key];
                });
            }
        },
        created () {
            this.users = this.rawUsers.map((user) => {
                user.name = `${user.first_name} ${user.last_name}`;
                return user;
            });
        }
    }
</script>

<style scoped lang="scss">
    .margin-top {

        margin-top: 20px;

    }
</style>
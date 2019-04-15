<template>
    <el-card>
        <el-autocomplete
                class="full-width"
                v-model="user"
                :fetch-suggestions="queryUser"
                placeholder="User Search"
                @select="handleSelect"
        ></el-autocomplete>
    </el-card>
</template>

<script>
    export default {
        name: "SearchAllUsers",
        data () {
            return {
                user: "",
                users: []
            };
        },
        methods: {
            queryUser (searchString, cb) {
                let users = this.users,
                    results = searchString ? users.filter(this.createFilter(searchString)) : users;
                cb(results);
            },
            createFilter (searchString) {
                return (row) => {
                    let searchSpace = (row.user.first_name + row.user.last_name + row.user.email).toLowerCase();
                    return searchSpace.indexOf(searchString.toLowerCase()) > -1;
                };
            },
            handleSelect (user) {
                this.$emit('user-selected', user);
            },
            loadUsers () {
                let self = this;
                window.adminApi.get('users/').then((response) => {
                    self.users = response.data.map((user) => {
                        return {
                            "value": `${user.first_name} ${user.last_name} (${user.email})`,
                            "user": user
                        };
                    });
                });
            }
        },
        created () {
            window.sau = this;
            window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
            window.onLoadedQueue.push(this.loadUsers);
        }
    }
</script>

<style scoped>

</style>
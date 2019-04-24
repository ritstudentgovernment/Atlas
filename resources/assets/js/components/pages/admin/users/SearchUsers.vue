<template>
    <el-autocomplete
            class="full-width"
            v-model="user"
            :fetch-suggestions="queryUser"
            placeholder="User Search"
            @select="handleSelect"
            :clearable="true"
    ></el-autocomplete>
</template>

<script>
    export default {
        name: "SearchUsers",
        props: {
            remote: {
                type: [Boolean, String],
                default: false
            },
            rawUsers: {
                type: Array
            },
            selected: {
                type: Object,
                default: null
            }
        },
        data () {
            return {
                user: "",
                users: []
            };
        },
        watch: {
            selected (user) {
                this.user = user ? `${user.first_name} ${user.last_name} (${user.email})` : "";
            },
            user (user) {
                if (!user) {
                    this.$emit('user-selected', null);
                }
            }
        },
        methods: {
            queryUser (searchString, cb) {
                let users = this.users,
                    results = searchString ? users.filter(this.createFilter(searchString)) : users;
                cb(results);
            },
            createFilter (searchString) {
                return (row) => {
                    let searchSpace = `${row.user.first_name} ${row.user.last_name} (${row.user.email})`.toLowerCase();
                    return searchSpace.indexOf(searchString.toLowerCase()) > -1;
                };
            },
            handleSelect (user) {
                this.$emit('user-selected', user);
            },
            mapRawUsers (users) {
                return users.map((user) => {
                    return {
                        "value": `${user.first_name} ${user.last_name} (${user.email})`,
                        "user": user
                    };
                });
            },
            loadUsers () {
                let self = this;
                window.adminApi.get(this.remote).then((response) => {
                    self.users = this.mapRawUsers(response.data)
                });
            }
        },
        created () {
            window.sau = this;
            if (this.remote) {
                if (window.adminApi && window.adminApi.api) {
                    this.loadUsers();
                } else {
                    window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
                    window.onLoadedQueue.push(this.loadUsers);
                }
            } else {
                this.users = this.mapRawUsers(this.rawUsers);
            }
        }
    }
</script>

<style scoped>

</style>
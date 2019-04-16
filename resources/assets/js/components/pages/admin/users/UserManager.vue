<template>
    <search-users :raw-users="users" @user-selected="handleUserSelected"></search-users>
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
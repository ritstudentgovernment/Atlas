<template>
    <v-popover
            id="new-spot"
            :disabled="!isEnabled"
    >
        <button class="tooltip-target cursor circle material-hover transition" title="Add a new spot">
            <span class="material-icon-container"><i class="material-icons">add</i></span>
        </button>

        <template slot="popover">
            <div class="padding-bottom">
                <input class="tooltip-content" v-model="spotTitle" placeholder="Tooltip content" />
            </div>
            <el-radio-group v-model="spotCategory" size="mini">
                <el-radio-button v-for="category in availableCategories" :key="category.id" :label="category.name"></el-radio-button>
            </el-radio-group>
        </template>
    </v-popover>
</template>

<script>
    import VTooltip from 'v-tooltip';
    import ElementUI from 'element-ui';
    import 'element-ui/lib/theme-chalk/index.css';

    export default {
        name: "new-spot-component",
        components: {
            VTooltip,
            ElementUI
        },
        created() {
            let self = this;
            window.loaded();
            window.axios.get('api/spots/create/').then((response) => {
                let index = 0;
                let data = response.data;
                window.categories = data.availableCategories;
                self.availableCategories = data.availableCategories.map((category) => {
                    index += 1;
                    return {'name': category.name, 'id': index};
                });
            }).catch((error) => {
                console.error(error);
            });
        },
        data() {
            return {
                isEnabled: true,
                spotTitle: 'Spot Title',
                spotCategory: '',
                spotType: '',
                spotDescriptors: '',
                availableCategories: [],
                availableTypes: [],
            };
        }
    }
</script>

<style scoped>

</style>
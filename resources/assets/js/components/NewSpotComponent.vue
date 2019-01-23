<template>
    <el-popover
            id="new-spot"
            :disabled="!isEnabled"
    >
        <button slot="reference" class="cursor circle material-hover transition" title="Add a new spot">
            <span class="material-icon-container"><i class="material-icons">add</i></span>
        </button>

        <div id="new-spot-popup">
            <el-input placeholder="Spot Title" v-model="spotTitle" size="mini" class="padding-bottom" />
            <el-radio-group v-model="spotCategory" size="mini" :fill="fillColor" @change="changeFillColor">
                <el-radio-button v-for="category in availableCategories" :key="category.id" :label="category.name"></el-radio-button>
            </el-radio-group>
        </div>
    </el-popover>
</template>

<script>
    import ElementUI from 'element-ui';
    import 'element-ui/lib/theme-chalk/index.css';

    export default {
        name: "new-spot-component",
        components: {
            ElementUI
        },
        methods: {
            changeFillColor(categoryName) {
                console.log(this.fillColor);
                console.log(this.availableCategories);
                let category = this.availableCategories.filter((category) => {
                    return category.name === categoryName;
                })[0];
                console.log(category);
                this.fillColor = category.color;
            }
        },
        created() {
            let self = this;
            window.loaded();
            window.axios.get('api/spots/create/').then((response) => {
                let index = 0;
                let data = response.data;
                console.log(data);
                self.availableCategories = data.availableCategories.map((category) => {
                    index += 1;
                    window.category = category;
                    console.log(category);
                    let publicClassification = category.classifications.filter((category) => {
                        return category.name === "Public";
                    })[0];
                    let color = publicClassification ? "#" + publicClassification.color : '';
                    return {'name': category.name, 'id': index, 'color': color};
                });
                let defaultCategory = self.availableCategories[0];
                self.spotCategory = defaultCategory.name;
                self.fillColor = defaultCategory.color;
                console.log(self.fillColor);
            }).catch((error) => {
                console.error(error);
            });
        },
        data() {
            return {
                isEnabled: true,
                spotTitle: '',
                spotCategory: '',
                spotType: '',
                spotDescriptors: '',
                availableCategories: [],
                availableTypes: [],
                fillColor: '',
            };
        }
    }
</script>

<style scoped>

</style>
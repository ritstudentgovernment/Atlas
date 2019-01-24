<template>
    <el-popover
            id="new-spot"
            :disabled="!isEnabled"
    >
        <button slot="reference" class="cursor circle material-hover transition" title="Add a new spot">
            <span class="material-icon-container"><i class="material-icons">add</i></span>
        </button>

        <div id="new-spot-popup">
            <el-input placeholder="Spot Title" v-model="spotTitle" size="mini" />
            <el-radio-group v-model="spotCategory" size="mini" :fill="fillColor" @change="changeCategory">
                <el-radio-button v-for="(category, index) in availableCategories" :key="index" :label="category.name"></el-radio-button>
            </el-radio-group>
            <el-radio-group v-model="spotType" size="mini" :fill="fillColor">
                <el-radio-button v-for="(type, index) in availableTypes" :key="index" :label="type.name"></el-radio-button>
            </el-radio-group>
            <el-select
                    v-for="(descriptor, index) in requiredDescriptors"
                    v-model="spotDescriptors[descriptor.name]"
                    :key="index"
                    :placeholder="descriptor.name"
                    size="mini"
                    value="test"
            >
                <el-option
                        v-for="(item, index) in descriptor.allowed_values.split('|')"
                        :key="index"
                        :label="item"
                        :value="item">
                </el-option>
            </el-select>
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
            setupDescriptors() {
                this.requiredDescriptors.forEach((descriptor)=>{
                    this.$set(this.spotDescriptors, descriptor.name, '');
                });
            },
            loadData(data) {
                console.log(data);
                this.availableTypes = data.availableTypes;
                this.fillColor = this.activeCategory.color;
                this.spotType = this.availableTypes[0].name;
                this.requiredDescriptors = data.requiredDescriptors;
                this.setupDescriptors();
            },
            changeCategory(categoryName) {
                let self = this;

                self.activeCategory = self.availableCategories.filter((category) => {
                    return category.name === categoryName;
                })[0];

                window.axios.get('api/spots/create/?category='+categoryName).then((response) => {
                    self.loadData(response.data);
                });
            }
        },
        created() {
            let self = this;
            window.loaded();
            window.axios.get('api/spots/create/').then((response) => {
                let data = response.data;
                self.availableCategories = data.availableCategories.map((category) => {
                    window.category = category;
                    let publicClassification = category.classifications.filter((category) => {
                        return category.name === "Public";
                    })[0];
                    let color = publicClassification ? "#" + publicClassification.color : '';
                    return {'name': category.name, 'color': color};
                });
                self.activeCategory = self.availableCategories[0];
                self.spotCategory = self.activeCategory.name;
                self.loadData(data);
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
                spotDescriptors: {},
                availableCategories: [],
                availableTypes: [],
                requiredDescriptors: [],
                activeCategory: '',
                fillColor: '',
            };
        }
    }
</script>

<style scoped>
    #new-spot-popup div:not(:last-of-type){
        padding-bottom: 10px;
    }
</style>
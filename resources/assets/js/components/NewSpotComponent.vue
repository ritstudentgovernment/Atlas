<template>
    <el-popover
            id="new-spot"
            trigger="manual"
            :visible="visible"
            v-model="visible"
    >
        <button
                slot="reference"
                class="cursor circle material-hover transition"
                title="Add a new spot"
                @click="visible = !visible"
        >
            <span class="material-icon-container"><i class="material-icons">add</i></span>
        </button>

        <div id="new-spot-popup" class="dim-text">
            <h4>New Spot</h4>
            <div>
                <h5>Category</h5>
                <el-radio-group
                        size="mini"
                        :fill="fillColor"
                        v-model="spotCategory"
                        @change="changeCategory"
                >
                    <el-radio-button v-for="(category, index) in availableCategories" :key="index" :label="category.name"></el-radio-button>
                </el-radio-group>
            </div>
            <div>
                <h5>Type</h5>
                <el-radio-group v-model="spotType" size="mini" :fill="fillColor">
                    <el-radio-button v-for="(type, index) in availableTypes" :key="index" :label="type.name"></el-radio-button>
                </el-radio-group>
            </div>
            <div>
                <h5>Descriptors</h5>
                <el-select
                        value=""
                        size="small"
                        v-for="(descriptor, index) in requiredDescriptors"
                        v-model="spotDescriptors[descriptor.name]"
                        :key="index"
                        :placeholder="descriptor.name"
                >
                    <el-option
                            v-for="(item, index) in descriptor.allowed_values.split('|')"
                            :key="index"
                            :label="item"
                            :value="item">
                    </el-option>
                </el-select>
            </div>
            <div>
                <h5>Notes</h5>
                <el-input
                        type="textarea"
                        placeholder="Notes (optional)"
                        v-model="spotNotes"
                        :rows="3">
                </el-input>
            </div>
            <div>
                <el-button
                        size="medium"
                        :style="'color: #fff;background-color:'+fillColor"
                        :disabled="!formCompleted"
                        @click="submit">
                    Create
                </el-button>
                <el-button type="text" @click="cancel">Cancel</el-button>
                <el-button @click="plop" :disabled="true">Place</el-button>
            </div>
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
        data() {
            return {
                visible: false,
                spotCategory: '',
                spotType: '',
                spotNotes: '',
                spotDescriptors: {},
                activeCategory: '',
                activeClassification: {name:'', color:''},
                availableCategories: [],
                availableTypes: [],
                requiredDescriptors: [],
                requiredData: {},
                fillColor: '',
                spot: '',
            };
        },
        computed: {
            formCompleted: () => {
                return false;
            }
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
                this.requiredData = data.requiredData;
            },
            setup() {
                let self = this;
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
            changeCategory(categoryName) {
                let self = this;

                self.activeCategory = self.availableCategories.filter((category) => {
                    return category.name === categoryName;
                })[0];

                window.axios.get('api/spots/create/?category='+categoryName).then((response) => {
                    self.loadData(response.data);
                });
            },
            cancel() {
                this.visible = false;
                this.spotNotes = '';
                this.setup();
            },
            makeSpot() {
                let category = {
                        name: this.activeCategory.name,
                        icon: this.activeCategory.icon,
                        description: this.activeCategory.description,
                    },
                    type = {
                        name: this.spotType.name,
                        category: category,
                    },
                    classification = {
                        name: this.activeClassification.name,
                        color: this.fillColor,
                    },
                    center = JSON.parse(getMeta('googleMapsCenter')),
                    spotData = {
                        type: type,
                        classification: classification,
                        notes: this.spotNotes,
                        descriptors: this.spotDescriptors,
                        draggable: true,
                        lat: center.lat,
                        lng: center.lng
                    };
                return window.builder.newSpot(spotData);
            },
            plop() {
                if (this.spot !== '') {
                    // Remove the last dropped spot, which should be the previously created newSpot marker
                    window.builder.removeLastSpot();
                }

                let spot = this.makeSpot(),
                    builder = window.builder;

                console.log(spot);

                spot.buildIcon(builder.canvasBuilder);

                let marker = spot.drop();

                this.spot = spot;
                builder.markers.push(marker);
            },
            verifyInput() {

            },
            submit() {

            },
        },
        created() {
            window.loaded();
            this.setup();
        }
    }
</script>

<style scoped>
    #new-spot-popup *{
        color: inherit;
    }
    #new-spot-popup div:not(:last-of-type){
        padding-bottom: 10px;
    }
    #new-spot-popup h4{
        border-bottom: 1px solid #ccc;
        text-align: center;
        margin-bottom: 10px;
        padding-bottom: .5em;
    }
    #new-spot-popup h5{
        margin-bottom: 5px;
    }
</style>
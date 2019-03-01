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
                <el-radio-group
                        size="mini"
                        :fill="fillColor"
                        v-model="spotType"
                        @change="updatePloppedSpot"
                >
                    <el-radio-button v-for="(type, index) in availableTypes" :key="index" :label="type.name"></el-radio-button>
                </el-radio-group>
            </div>
            <div v-if="availableClassifications.length > 1">
                <h5>Classification</h5>
                <el-radio-group
                        size="mini"
                        :fill="fillColor"
                        v-model="spotClassification"
                        @change="changeClassification"
                >
                    <el-radio-button
                            v-for="(classification, index) in availableClassifications"
                            :key="index"
                            :label="classification.name"
                    ></el-radio-button>
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
                        @change="updatePloppedSpot"
                >
                    <el-option
                            v-for="(item, index) in descriptor.allowed_values.split('|')"
                            :key="index"
                            :label="item"
                            :value="item"
                    >
                    </el-option>
                </el-select>
            </div>
            <div>
                <h5>Notes</h5>
                <el-input
                        type="textarea"
                        placeholder="Notes (optional)"
                        v-model="spotNotes"
                        :rows="3"
                        @change="updatePloppedSpot"
                ></el-input>
            </div>
            <div>
                <el-button
                        size="medium"
                        :style="'color: #fff;background-color:'+fillColor"
                        v-if="formCompleted"
                        @click="submit"
                >
                    Create
                </el-button>
                <el-button v-if="!formCompleted" @click="plop">Place</el-button>
                <el-button type="text" @click="cancel">Cancel</el-button>
            </div>
        </div>
    </el-popover>
</template>

<script>
    import ElementUI from 'element-ui';
    import 'element-ui/lib/theme-chalk/index.css';

    export default {
        name: "new-spot",
        components: {
            ElementUI
        },
        data() {
            return {
                visible: false,
                spotCategory: '',
                spotClassification: '',
                spotType: '',
                spotNotes: '',
                spotDescriptors: {},
                activeCategory: '',
                activeClassification: {name:'', color:''},
                availableCategories: [],
                availableClassifications: [],
                availableTypes: [],
                requiredDescriptors: [],
                requiredData: {},
                fillColor: '',
                isPlopped: false,
                ploppedMarker: null,
                location: null,
            };
        },
        computed: {
            formCompleted() {
                return this.verifyInput() && this.isPlopped;
            },
            formattedDescriptors() {
                return this.requiredDescriptors.map((descriptor) => {
                    let value = descriptor.default_value;
                    if (this.spotDescriptors[descriptor.name]) {
                        value = this.spotDescriptors[descriptor.name];
                    }
                    descriptor.pivot.value = value;
                    return descriptor;
                });
            },
            spotLocation() {
                if (this.isPlopped) {
                    let position = this.ploppedMarker.position;
                    this.location = {lat: position.lat(), lng: position.lng()};
                }
                return this.location;
            },
        },
        methods: {
            setupDescriptors() {
                this.requiredDescriptors.forEach((descriptor)=>{
                    this.$set(this.spotDescriptors, descriptor.name, '');
                });
            },
            loadData(data) {
                this.availableClassifications = data.availableClassifications;
                this.activeClassification = this.availableClassifications.filter((classification) => {
                    return classification.name === "Public";
                })[0];
                this.spotClassification = this.activeClassification.name;
                this.availableTypes = data.availableTypes;
                this.fillColor = "#" + this.activeClassification.color;
                this.spotType = this.availableTypes[0].name;
                this.requiredDescriptors = data.requiredDescriptors;
                this.setupDescriptors();
                this.requiredData = data.requiredData;
                this.updatePloppedSpot();
            },
            setup() {
                let self = this;
                window.spotsApi.get('create/').then((response) => {
                    let data = response.data;
                    self.availableCategories = data.availableCategories;
                    self.activeCategory = self.availableCategories[0];
                    self.spotCategory = self.activeCategory.name;
                    self.availableClassifications = data.availableClassifications;
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

                window.spotsApi.get(`create/?category=${categoryName}`).then((response) => {
                    self.loadData(response.data);
                    self.$nextTick(()=>{
                        window.dispatchEvent(new Event('resize'));
                    });
                });
            },
            changeClassification(classificationName) {
                this.activeClassification = this.availableClassifications.filter((classification) => {
                    return classification.name === classificationName;
                })[0];

                this.fillColor = "#" + this.activeClassification.color;
                this.updatePloppedSpot();
            },
            updatePloppedSpot() {
                if (this.isPlopped) {
                    this.plop();
                }
            },
            cancel() {
                this.visible = false;
                this.spotNotes = '';
                if (this.isPlopped) {
                    window.builder.removeLastSpot();
                    this.isPlopped = false;
                    this.ploppedMarker = null;
                }
                this.location = null;
                this.setup();
            },
            getDefaultLocation(callback) {
                let self = this;
                if (!this.isPlopped) {
                    this.location = getMeta('googleMapsCenter');
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition((position)=>{
                            self.location = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            };
                            callback();
                        });
                        return true;
                    }
                }
                callback();
                return false;
            },
            makeSpot() {
                let category = {
                        name: this.activeCategory.name,
                        icon: this.activeCategory.icon,
                        description: this.activeCategory.description,
                    },
                    type = {
                        name: this.spotType,
                        category: category,
                    },
                    classification = {
                        name: this.activeClassification.name,
                        color: this.fillColor.replace('#', ''),
                    },
                    spotData = {
                        type: type,
                        classification: classification,
                        notes: this.spotNotes,
                        descriptors: this.formattedDescriptors,
                        draggable: true,
                        lat: this.location.lat,
                        lng: this.location.lng
                    };
                return window.builder.newSpot(spotData, true);
            },
            plop() {
                let self = this,
                    animateDrop = true;

                if (this.isPlopped) {
                    // Remove the last dropped spot, which should be the previously created newSpot marker
                    window.builder.removeLastSpot();
                    this.ploppedMarker = null;
                    animateDrop = false;
                }

                this.getDefaultLocation(()=>{
                    let spot = self.makeSpot(),
                        builder = window.builder;

                    console.log(spot);

                    spot.buildIcon(builder.canvasBuilder);

                    let marker = spot.drop(true, animateDrop);

                    marker.addListener('dragend', () => {
                        return self.spotLocation; // This is a computed property call that updates the location data of the spot
                    });

                    self.isPlopped = true;
                    self.ploppedMarker = marker;
                    builder.markers.push(marker);
                });
            },
            verifyDescriptors() {
                let allDescriptorsCompleted = true;
                this.requiredDescriptors.forEach((descriptor)=>{
                    let value = this.spotDescriptors[descriptor.name];
                    if (value === '' || !(descriptor.allowed_values.includes(value))) {
                        allDescriptorsCompleted = false;
                    }
                });
                return allDescriptorsCompleted;
            },
            verifyInput() {
                return this.verifyDescriptors();
            },
            parseDescriptors() {
                let descriptorIDtoValue = {};
                this.requiredDescriptors.forEach((descriptor)=>{
                    descriptorIDtoValue[descriptor.id] = this.spotDescriptors[descriptor.name];
                });
                return descriptorIDtoValue;
            },
            submit() {
                let self = this;
                let data = {
                    'notes': self.spotNotes,
                    'type_name': self.spotType,
                    'descriptors': self.parseDescriptors(),
                    'lat': self.location.lat,
                    'lng': self.location.lng,
                    'classification_id': self.activeClassification.id,
                };
                window.spotsApi.post('create/', data).then((response) => {
                    self.cancel();
                    window.builder.build(false);
                    if (response.status === 201) {
                        this.$notify({
                            title: 'Spot Created',
                            message: response.data.message[0],
                            type: 'success',
                            duration: 30000
                        });
                    } else {
                        this.$notify.error({
                            title: 'Error Creating Spot',
                            message: response.data.message[0],
                            duration: 30000
                        });
                    }
                });
            },
        },
        mounted() {
            window.nsp = this;
            window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
            window.onLoadedQueue.push(this.setup);
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
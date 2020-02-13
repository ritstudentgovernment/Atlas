<template>
    <el-popover
            id="new-spot"
            trigger="manual"
            :visible="visible"
            v-model="visible"
            v-if="availableCategories.length > 0"
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
            <div id="new-spot-data">
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
                    <div v-for="(descriptor, index) in requiredDescriptors">
                        <el-select
                                value=""
                                size="small"
                                class="full-width"
                                v-if="descriptor.value_type.toLowerCase().includes('select')"
                                v-model="spotDescriptors[descriptor.name]"
                                :multiple="descriptor.value_type === 'multiSelect'"
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
                        <div v-if="descriptor.value_type === 'number'" class="overflow-hidden numeric-descriptor">
                            <el-row>
                                <el-col :span="6">
                                    <span class="left">{{ descriptor.name }}:</span>
                                </el-col>
                                <el-col :span="18">
                                    <el-input-number
                                            class="left"
                                            size="small"
                                            :value="Number(descriptor.default_value)"
                                            :min="Number(descriptor.allowed_values.split('-')[0])"
                                            :max="Number(descriptor.allowed_values.split('-')[1])"
                                            v-model="spotDescriptors[descriptor.name]"
                                            @change="updatePloppedSpot"
                                    ></el-input-number>
                                </el-col>
                            </el-row>
                        </div>
                    </div>
                </div>
                <div>
                    <h5>Image</h5>
                    <el-upload
                            class="full-width"
                            drag
                            action="/api/spots/create/upload"
                            :on-success="handleImageUploadSuccess"
                            :headers="apiHeaders"
                            :show-file-list="false">
                        <img v-if="imageUrl" :src="imageUrl" class="avatar">
                        <div v-else>
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">Drop file here or click to upload<br />(optional)</div>
                        </div>
                        <div v-if="!imageUrl" class="el-upload__tip text-center" slot="tip">jpg/png files, less than 500kb</div>
                    </el-upload>
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
            </div>
            <div id="new-spot-buttons">
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
                apiHeaders: null,
                imageUrl: '',
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
                        if (Array.isArray(value)) {
                            value = value.join(', ');
                        }
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
                this.requiredDescriptors.forEach((descriptor) => {
                    this.$set(this.spotDescriptors, descriptor.name, '');
                });
            },
            loadData(data) {
                this.availableClassifications = data.availableClassifications;
                this.activeClassification = this.availableClassifications.filter((classification) => {
                    return classification.type === "public";
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
            handleImageUploadSuccess(response) {
                this.imageUrl = response;
            },
            setup() {
                let self = this;

                window.spotsApi.get('create/').then((response) => {
                    let data = response.data;
                    self.availableCategories = data.availableCategories;
                    if (self.availableCategories.length > 0) {
                        self.activeCategory = self.availableCategories[0];
                        self.spotCategory = self.activeCategory.name;
                        self.availableClassifications = data.availableClassifications;
                        self.loadData(data);
                    }
                }).catch((error) => {
                    console.error(error);
                });

                this.apiHeaders = window.axios.defaults.headers.common;
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
                        navigator.geolocation.getCurrentPosition((position) => {
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
                        lng: this.location.lng,
                        approved: true,
                        image_url: this.imageUrl,
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
                this.requiredDescriptors.forEach((descriptor) => {
                    let value = this.spotDescriptors[descriptor.name];
                    if (value === '') {
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
                this.requiredDescriptors.forEach((descriptor) => {
                    let descriptorValue = this.spotDescriptors[descriptor.name];
                    descriptorIDtoValue[descriptor.id] =
                        Array.isArray(descriptorValue) ?
                            descriptorValue.join(', ') :
                            descriptorValue;
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
                    'image_url': self.imageUrl,
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
        created() {
            window.nsp = this;
            window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
            window.onLoadedQueue.push(this.setup);
        }
    }
</script>

<style lang="scss">
    @import '../../../../sass/variables';

    #new-spot-popup {

        #new-spot-data {

            overflow-y: auto;
            margin-bottom: 10px;
            max-height: calc(55vh - 10px);
            border-bottom: 1px dotted #d0d0d0;

            .el-upload, .el-upload-dragger {

                width: 100%;
                height: 120px;

                .el-icon-upload {

                    margin: 6px 0 16px;

                }

            }

            .el-upload__tip {

                margin: 0;

            }

        }

        .el-button--text, h5 {

            color: inherit;

        }

        div:not(:last-of-type) {

            padding-bottom: 10px;

        }

        h4 {

            text-align: center;
            margin-bottom: 10px;
            padding-bottom: .5em;
            border-bottom: 1px solid #ccc;

        }

        h5 {

            margin-bottom: 5px;

        }

        .numeric-descriptor {

            div {

                padding-bottom: 0;

            }

            .el-input-number--small {

                width: 100%;

            }

            span {

                line-height: 32px;
                display: block;
                height: 32px;

            }

        }

    }
</style>
<template>
    <el-card>
        <div slot="header">
            Bulk Spot Import
        </div>
        <el-steps simple :active="currentStep" finish-status="success" class="margin-bottom">
            <el-step title="Choose Category"></el-step>
            <el-step title="Choose Author"></el-step>
            <el-step title="Upload Spots CSV"></el-step>
            <el-step title="Upload Descriptors CSV"></el-step>
            <el-step title="Finalize Import"></el-step>
        </el-steps>
        <el-card shadow="never" class="margin-top">
            <el-row :gutter="14"  v-if="currentStep === 0">
                <p>
                    Choose the Category, Classification, and Type that the new spots will be a part of.
                </p>
                <el-col :span="200">
                    <el-select
                            v-model="category"
                            @change="categoryChosen"
                            value-key="name"
                            placeholder="Choose a Category">
                        <el-option
                                v-for="categoryOption in categories"
                                :key="categoryOption.name"
                                :label="categoryOption.name"
                                :value="categoryOption">
                        </el-option>
                    </el-select>
                </el-col>
                <el-col :span="200" v-if="category == null">
                    or create a
                    <el-button @click="goToCategories" type="text">New Category</el-button>
                </el-col>
                <el-col :span="400" v-else>
                    <el-select
                            v-model="classification"
                            @change="classificationChosen"
                            value-key="name"
                            placeholder="Choose a Classification">
                        <el-option
                                v-for="classificationOption in category.classifications"
                                :key="classificationOption.name"
                                :label="classificationOption.name"
                                :value="classificationOption">
                        </el-option>
                    </el-select>
                    <el-select v-model="type" value-key="name" class="margin-left" placeholder="Choose a Type">
                        <el-option
                                v-for="typeOption in category.types"
                                :key="typeOption.name"
                                :label="typeOption.name"
                                :value="typeOption">
                        </el-option>
                    </el-select>
                    <img v-if="icon !== null" :src="icon" class="spot-icon" />
                </el-col>
            </el-row>
            <el-row v-else-if="currentStep === 1" :gutter="10">
                <p>
                    Now choose the person who will be the author of the spots (this does not show up on the frontend,
                    but is used in the admin panel).
                </p>
                <search-users remote="users" @user-selected="handleUserSelected"></search-users>
            </el-row>
            <el-row v-else-if="currentStep === 2" :gutter="10">
                <p>
                    Upload a CSV containing the following columns:
                    <br />
                    <em>lat, lng, notes</em>
                </p>
                <el-col>
                    <el-upload
                            drag
                            key="spots"
                            name="spotsCsv"
                            :headers="headers"
                            :file-list="fileList"
                            :on-error="handleUploadFailure"
                            :on-success="handleUploadSuccess"
                            action="/api/admin/spots/upload/">
                        <i class="el-icon-upload"></i>
                        <div class="el-upload__text">
                            Drop file here or <em>click to upload</em>
                        </div>
                        <div class="el-upload__tip" slot="tip">
                            Upload only one CSV file (the most recent file will be processed).
                            Be sure to keep the order of spots consistent for the next section.
                        </div>
                    </el-upload>
                </el-col>
            </el-row>
            <el-row v-else-if="currentStep === 3" :gutter="10">
                <el-row>
                    The category of spots you are uploading to requires the following descriptors:
                    <br />
                    <el-tag
                            v-for="descriptor in category.descriptors"
                            :key="descriptor.name"
                            type="info"
                            class="margin-right">
                        <span :uk-icon="descriptor.icon"></span> {{ descriptor.name }}
                    </el-tag>
                </el-row>
                <el-row>
                    Thus, you need to upload a file with the following columns:
                    <br />
                    <span>
                        <span v-for="(descriptor, index) in category.descriptors">
                            <em>{{ descriptor.name }}</em><span v-if="(index + 1) < category.descriptors.length">,</span>&nbsp;
                        </span>
                    </span>
                </el-row>
                <el-row>
                    Each with a subsequent value for every spot (row, from the last CSV) that exists in the below table:
                    <el-table
                            :data="descriptorsTableData"
                            border>
                        <el-table-column
                                v-for="descriptor in category.descriptors"
                                :key="descriptor.name"
                                :prop="descriptor.name"
                                :label="descriptor.name">
                        </el-table-column>
                    </el-table>
                </el-row>
                <el-col>
                    <el-upload
                            drag
                            key="descriptors"
                            name="descriptorsCsv"
                            :headers="headers"
                            :file-list="fileList"
                            :on-error="handleUploadFailure"
                            :on-success="handleUploadSuccess"
                            action="/api/admin/spots/upload/descriptors">
                        <i class="el-icon-upload"></i>
                        <div class="el-upload__text">
                            Drop file here or <em>click to upload</em>
                        </div>
                        <div class="el-upload__tip" slot="tip">
                            Upload only one CSV file (the most recent file will be processed).
                            Be sure to keep the order of spots consistent with that from the last section.
                        </div>
                    </el-upload>
                </el-col>
            </el-row>
            <el-row v-if="currentStep === 4">
                <el-row>
                    If you are confident in the data you've provided press the button below to execute the import.
                    <br />
                    Otherwise, you may use this time to go back and modify any of the data in the previous section.
                </el-row>
                <el-row>
                    <el-card shadow="never">
                        Category: <em>{{ category.name }}</em><br />
                        Type: <em>{{ type.name }}</em><br />
                        Classification: <em>{{ classification.name }}</em><br />
                        Author: <em>{{ author.first_name }} {{ author.last_name }}</em><br />
                        Icon Preview: <img :src="icon" class="spot-icon" />
                    </el-card>
                    <el-button
                            @click="runImport"
                            :loading="loading"
                            type="primary"
                            class="margin-top">
                        Run Import
                    </el-button>
                </el-row>
            </el-row>
        </el-card>
        <el-row class="margin-top">
            <el-col>
                <el-button
                        class="left"
                        :disabled="currentStep === 0"
                        @click="nextStep(currentStep - 1)">
                    Previous Step
                </el-button>
                <el-button
                        class="right"
                        :disabled="nextStepDisabled"
                        @click="nextStep(currentStep + 1)">
                    Next Step
                </el-button>
            </el-col>
        </el-row>
    </el-card>
</template>

<script>
    import CanvasBuilder from "../../../../classes/CanvasBuilder";
    import SearchUsers from "../users/SearchUsers";
    export default {
        name: "BulkSpotUpload",
        components: {SearchUsers},
        data () {
            return {
                headers: {},
                currentStep: 0,
                category: null,
                categories: [],
                classification: null,
                type: null,
                author: null,
                descriptorsTableData: [],
                spotsCsvPath: null,
                descriptorsCsvPath: null,
                fileList: [],
                icon: null,
                loading: false,
                exportData: null
            };
        },
        computed: {
            nextStepDisabled () {
                return !this.stepValidator(this.currentStep);
            }
        },
        methods: {
            getApiHeaders () {
                this.headers = window.adminApi.api.axios.defaults.headers.common;
            },
            getAvailableCategories () {
                // Need to access data from the Spots API, not the Admin API
                window.adminApi.api.get('spots/categories/')
                    .then((response) => {
                        this.categories = response.data;
                    })
                    .catch((error) => {
                        console.error(error);
                    });
            },
            setup () {
                this.getApiHeaders();
                this.getAvailableCategories();
            },
            stepValidator (step) {
                return {
                    0: (
                        this.category !== null &&
                        this.classification !== null &&
                        this.type !== null
                    ),
                    1: this.author !== null,
                    2: this.spotsCsvPath !== null,
                    3: this.descriptorsCsvPath !== null
                }[step];
            },
            goToCategories () {
                window.location.href = "/admin/spots/categories?newCategory=true";
            },
            categoryChosen () {
                this.classification = null;
                this.type = null;
            },
            classificationChosen () {
                let builder = new CanvasBuilder();
                builder.initialize();
                this.icon = builder.makeImage(this.category.icon, this.classification.color);
            },
            setDescriptorTableData () {
                this.category.descriptors.forEach((descriptor) => {
                    if (this.descriptorsTableData.length === 0) {
                        this.descriptorsTableData.push({});
                    }
                    let value = descriptor.allowed_values.split("|").join(", ");
                    this.descriptorsTableData[0][descriptor.name] = `Allowed Values: ${value}`;
                });
            },
            nextStep (step) {
                if (step === 3) {
                   this.setDescriptorTableData();
                }
                this.currentStep = step;
            },
            handleUserSelected (userSelected) {
                this.author = userSelected.user;
            },
            handleUploadSuccess (url) {
                if (url.indexOf('spots') > 0) {
                    this.spotsCsvPath = url;
                } else if (url.indexOf('descriptors') > 0) {
                    this.descriptorsCsvPath = url;
                } else {
                    console.log(url);
                }
            },
            handleUploadFailure (error) {
                console.log(error);
                this.$notify.error({
                    title: "Error Uploading File",
                    message: error.message
                });
            },
            runImport () {
                this.exportData = {
                    type: this.type.name,
                    author: this.author.id,
                    category: this.category.name,
                    classification: this.classification.id,
                    descriptorsCsvPath: this.descriptorsCsvPath,
                    spotsCsvPath: this.spotsCsvPath
                };
                this.loading = true;
                window.adminApi.post("spots/upload/run", this.exportData)
                    .then((response) => {
                        console.log(response);
                        this.loading = false;
                        this.$notify.success({
                            title: "Import Completed Successfully",
                            message: ""
                        });
                    })
                    .catch((error) => {
                        this.loading = false;
                        this.$notify.error({
                            title: "Error Running Import",
                            message: error.response.data
                        });
                    });
            }
        },
        created () {
            window.bsu = this;
            window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
            window.onLoadedQueue.push(this.setup);
        }
    }
</script>

<style lang="scss">
    .spot-icon {

        width: 33px;
        height: 38px;
        margin-left: 10px;

    }
    .el-step__title, .el-table .cell{

        word-break: normal !important;
        white-space: normal !important;

    }
    .el-upload {

        width: 100%;

        * {

            width: 100%;

        }

    }
    .el-upload__tip {

        margin-top: 10px;

    }
</style>
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
                    <el-select v-model="classification" value-key="name" placeholder="Choose a Classification">
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

                </el-col>
            </el-row>
        </el-card>
        <el-row class="margin-top">
            <el-col>
                <el-button
                        class="left"
                        :disabled="currentStep === 0"
                        @click="currentStep--">
                    Previous Step
                </el-button>
                <el-button
                        class="right"
                        :disabled="nextStepDisabled"
                        @click="currentStep++">
                    Next Step
                </el-button>
            </el-col>
        </el-row>
    </el-card>
</template>

<script>
    import SearchUsers from "../users/SearchUsers";
    export default {
        name: "BulkSpotUpload",
        components: {SearchUsers},
        data () {
            return {
                currentStep: 0,
                category: null,
                categories: [],
                classification: null,
                type: null,
                author: null,
                spotsCsvPath: null,
                descriptorsCsvPath: null,
                done: false
            };
        },
        computed: {
            nextStepDisabled () {
                return !this.stepValidator(this.currentStep);
            }
        },
        methods: {
            getApiHeaders () {
                this.apiHeaders = window.adminApi.api.axios.defaults.headers.common;
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
                window.location.href = "/admin/spots/categories?newCategory=truew";
            },
            categoryChosen () {
                this.classification = null;
                this.type = null;
            },
            handleUserSelected (user) {
                this.author = user.id;
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
    .el-step.is-simple {

        .el-step__title {

            word-break: normal !important;
            white-space: normal !important;

        }

    }
</style>
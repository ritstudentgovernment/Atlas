<template>
    <el-card>
        <div slot="header">
            Bulk Spot Import
        </div>
        <el-button
                :disabled="nextStepDisabled"
                @click="currentStep++">
            Next Step
        </el-button>
        <el-card shadow="never" class="margin-top">
            <div v-if="currentStep === 0">
                 <el-button @click="goToCategories" type="text">New Category</el-button>
            </div>
        </el-card>
        <el-steps simple :active="currentStep" finish-status="success" class="margin-top">
            <el-step title="Choose Category For Spots"></el-step>
            <el-step title="Upload Spots CSV"></el-step>
            <el-step title="Upload Descriptors CSV"></el-step>
            <el-step title="Finalize Import"></el-step>
        </el-steps>
    </el-card>
</template>

<script>
    export default {
        name: "BulkSpotUpload",
        data () {
            return {
                currentStep: 0,
                category: null,
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
            handleRemove (file, fileList) {
                console.log(file, fileList);
            },
            handlePreview (file) {
                console.log(file);
            },
            stepValidator (step) {
                return {
                    0: this.category !== null,
                    1: this.spotsCsvPath !== null,
                    2: this.descriptorsCsvPath !== null
                }[step];
            },
            goToCategories () {
                window.location.href = "/admin/spots/categories?newCategory=true";
            }
        },
        created () {
            window.bsu = this;
            window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
            window.onLoadedQueue.push(this.getApiHeaders);
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
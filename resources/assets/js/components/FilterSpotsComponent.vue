<template>
    <div id="filter-spots" class="padding-left padding-right" :style="'bottom:'+position+'px'">
        <el-collapse v-model="show">
            <el-collapse-item title="Filter Spots Visible" name="list">
                <el-checkbox-group v-model="selectedSpotCategories" @change="handleSpotsFilterChange">
                    <el-checkbox v-for="spot in spotCategories" :label="spot" :value="spot" :key="spot" :border="true" size="mini">{{ spot }}</el-checkbox>
                </el-checkbox-group>
            </el-collapse-item>
        </el-collapse>
    </div>
</template>

<script>
    import ElementUI from 'element-ui';
    import 'element-ui/lib/theme-chalk/index.css';

    export default {
        name: "filter-spots",
        components: {
            ElementUI
        },
        computed: {
            position() {
                // Render Key is only here to force vue to re-compute this property when forceRender is called.
                // (it's ugly, I know)
                let renderKey = this.renderKey;
                let windowHeight = $(window).height();
                let topOfLegend = $("#legend").position().top;
                return windowHeight - topOfLegend + 7;
            }
        },
        data() {
            return {
                show: [],
                renderKey: 0,
                spotCategories: [],
                selectedSpotCategories: []
            };
        },
        methods: {
            handleSpotsFilterChange(val) {
                let filteredSpotData = window.spotData.filter((spot) => {
                    return val.indexOf(spot.type.category.name) !== -1;
                });
                window.builder.build(true, filteredSpotData);
            },
            toggleVisibility() {
                this.show = !this.show;
            },
            forceRender(){
                this.renderKey++;
            },
            setup() {
                let self = this;
                window.spotsApi.get('categories').then((response) => {
                    let categories = response.data.map((category) => { return category.name; });
                    self.spotCategories = categories;
                    self.selectedSpotCategories = categories;
                });
            }
        },
        created() {
            window.fsc = this;
            window.onLoadedQueue = window.onLoadedQueue ? window.onLoadedQueue : [];
            window.onLoadedQueue.push(this.setup);
        }
    }
</script>

<style scoped>
    #filter-spots{
        background-color: #fff;
        position: absolute;
        left: 10px;
        z-index: 1;
        width: 140px;
    }
    #filter-spots label{
        width: 140px;
        float: left;
        margin: 0 auto 10px;
        display: inline-block;
    }
    #filter-spots .el-collapse{
        border: none;
    }
</style>
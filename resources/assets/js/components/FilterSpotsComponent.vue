<template>
    <div id="filter-spots" class="padding" :style="'bottom:'+position+'px'">
        <h3>Filter Spots Visible</h3>
        <el-checkbox-group v-model="selectedSpotCategories" @change="handleSpotsFilterChange">
            <el-checkbox v-for="spot in spotCategories" :label="spot" :value="spot" :key="spot">{{ spot }}</el-checkbox>
        </el-checkbox-group>
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
            forceRender(){
                this.renderKey++;
            }
        },
        created() {
            let self = this;
            window.fsc = self;
            window.spotsApi.get('categories').then((response) => {
                let categories = response.data.map((category) => { return category.name; });
                self.spotCategories = categories;
                self.selectedSpotCategories = categories;
            });
        }
    }
</script>

<style scoped>
    #filter-spots{
        background-color: #fff;
        position: absolute;
        left: 10px;
        z-index: 1;
    }
    #filter-spots h3{
        font-size: 13px;
        margin-bottom: 0;
    }
    #filter-spots label{
        display: block;
    }
</style>
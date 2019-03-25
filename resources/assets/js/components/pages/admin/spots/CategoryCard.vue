<template>
    <el-card shadow="hover" @click.native="handleClick">
        <div slot="header">
            <h4>
                {{ category.name }} Spots
                <el-badge class="mark" :value="category.numSpots" />
                <span class="right">
                    <img v-for="image in classificationImages" :src="image" />
                </span>
            </h4>
        </div>
        <div>
            <p>
                Types:
                <span v-for="(type, index) in category.types">
                    {{ type.name }}<span v-if="(index < category.types.length - 1)">,</span>
                </span>
            </p>
        </div>
    </el-card>
</template>

<script>
    import ElementUI from "element-ui";
    import CanvasBuilder from "../../../../classes/CanvasBuilder";

    export default {
        name: "category-card",
        components: {
            ElementUI
        },
        props: ['category'],
        data () {
            return {
                'classificationImages': []
            };
        },
        methods: {
            handleClick () {
                window.location.href = window.location.href + '/' + this.category.name;
            }
        },
        mounted () {
            let builder = new CanvasBuilder();
            builder.initialize();
            this.category.classifications.forEach((classification) => {
                if (classification.name !== "Under Review") {
                    this.classificationImages.push(builder.makeImage(this.category.icon, classification.color));
                }
            });
        }
    }
</script>

<style scoped lang="scss">

    .el-card {

        .el-card__header {

            padding-top: 5px;
            padding-bottom: 5px;

            h4 {

                margin: 0;

                img {

                    height: 30px;
                    width: 27px;
                    margin-right: 10px;

                }

            }

        }

        p {

            margin: 0;

        }

        &:hover {

            cursor: pointer;

        }

    }

</style>
<template>
    <el-card shadow="hover" @click.native="handleClick">
        <div slot="header">
            <h4>
                {{ category.name }} Spots
                <span class="right">
                    <el-button
                            @click.stop="handleDelete"
                            icon="el-icon-delete"
                            size="small"
                            circle>
                    </el-button>
                </span>
            </h4>
        </div>
        <div>
            <p v-if="category.types.length > 0">
                Types:
                <span v-for="(type, index) in category.types">
                    {{ type.name }}<span v-if="(index < category.types.length - 1)">,</span>
                </span>
            </p>
            <div class="images" v-if="classificationImages.length > 0">
                <img v-for="image in classificationImages" :src="image" />
                <el-badge class="mark right" :value="category.numSpots + ' Spots'" />
            </div>
        </div>
    </el-card>
</template>

<script>
    import ElementUI from "element-ui";
    import CanvasBuilder from "../../../../classes/CanvasBuilder";

    export default {
        name: 'category-card',
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
            },
            handleDelete () {
                let cname = this.category.name;
                this.$confirm(`This will permanently delete the '${cname}' category and all spots associated with it.`, 'Warning', {
                    confirmButtonText: 'Delete',
                    confirmButtonClass: 'el-button--danger',
                    cancelButtonText: 'Cancel',
                }).then(() => {
                    window.adminApi.post(`spots/category/${this.category.name}/delete`)
                        .then(() => {
                            this.$emit('deleted', this.category.name);
                        })
                        .catch((error) => {
                            this.$notify.error({
                                title: "Error Creating Category",
                                message: error
                            });
                        });
                }).catch((error) => {

                });
            }
        },
        mounted () {
            let builder = new CanvasBuilder();
            builder.initialize();
            this.category.classifications.sort((a, b) => b.type < a.type ? 1 : -1).forEach((classification) => {
                this.classificationImages.push(builder.makeImage(this.category.icon, classification.color));
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
                position: relative;

                .el-button {

                    padding: 0 !important;
                    position: absolute;
                    right: 0;
                    top: 0;
                    width: 30px;
                    height: 30px;

                }

            }

        }

        p {

            margin: 0;

        }

        .images {

            margin-top: 15px;

            img {

                width: 27px;
                height: 30px;
                margin-right: 10px;

            }

            .el-badge {

                line-height: 2.5;

            }

        }

        &:hover {

            cursor: pointer;

        }

    }

</style>
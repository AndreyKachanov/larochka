<template>
    <div class="row groups-block">
        <div class="col-sm-6 col-md-5 left">
            <label for="days" class="col-form-label">
                <b>Count days</b> (0 - current day, 1 - yesterday, etc):
            </label>
            <input id="days" v-model="days" type="number" min="0" max="30" :class="[errors.days ? 'is-invalid' : '', 'form-control input-days']" name="days">
            <div v-for="(error) in errors.days" class="invalid-feedback" v-html="error"></div>

            <label for="vk_group" class="col-form-label main-label"><b>Vk groups:</b></label>
            <div v-for="(group, index) in groups" class="input-block">
                <input type="text" v-model="group.name" name="groups[]"  :class="[errors['groups.' + index + '.name'] ? 'is-invalid' : '', 'form-control']" id="vk_group" v-bind:tabindex="index+1"
                       :disabled="parsing_start">
                <button @click="deleteUser(index)" class="btn">
                    <i class="fa fa-remove" aria-hidden="true"></i>
                </button>
                <div v-for="(error) in errors['groups.' + index + '.name']" class="invalid-feedback" v-html="error"></div>
            </div>
            <button @click="addGroup" class="btn btn-success btn-sm btn-add-group" :disabled="parsing_start">
                Add Group
            </button>
        </div>
        <div class="col-sm-6 col-md-7">
            <label for="keywords_buy" class="col-form-label"><b>Keywords</b> (comma separated):</label>
            <div class="keywords-block">
                <label>
                    <input type="radio" name="radio_keywords" v-model="radio_keywords" value="buy">
                </label>
                <input :class="[error_keywords_buy ? 'is-invalid' : '', 'form-control']" type="text" id="keywords_buy" v-model="keywords_buy" name="keywords_buy">
                <div v-for="(error) in errors.keywords" class="invalid-keywords invalid-feedback" v-html="error"></div>
            </div>
            <div class="keywords-block">
                <label>
                    <input type="radio" name="radio_keywords" v-model="radio_keywords" value="sale">
                </label>
                <input :class="[error_keywords_sale ? 'is-invalid' : '', 'form-control']" type="text" id="keywords_sale" v-model="keywords_sale" name="keywords_sale">
                <div v-for="(error) in errors.keywords" class="invalid-keywords invalid-feedback" v-html="error"></div>
            </div>
            <div class="button-block">
                <img class="money-img" src="/assets/images/money-bag.svg" title="" alt="">
                <button @click="start" class="btn btn-success">Start parse</button>
                <img class="money-img" src="/assets/images/money-bag.svg" title="" alt="">
            </div>
<!--            <div class="img-block">-->
<!--                <img class="money-img" src="/assets/images/money-bag.svg" title="" alt="">-->
<!--            </div>-->
        </div>
        <div class="table-responsive">
            <table v-if="posts.length" class="table table-striped table-messages">
                <thead>
                <tr class="d-flex">
                    <th class="col-2">Date</th>
                    <th class="col-3">Author</th>
                    <th class="col-4">Post</th>
                    <th class="col-3">Vk group</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item) in posts" class="d-flex">
                    <td class="col-2">{{ item.date }}</td>
                    <td class="col-3">
                        <a :href="item.user_src" target="_blank" class="user-link">
                            <img width="30px;" :src="item.photo_50" alt="">&nbsp;&nbsp;{{ item.first_name }}&nbsp;&nbsp;{{ item.last_name }}
                        </a>
                    </td>
                    <td class="col-4">{{ item.text }}</td>
                    <td class="col-3">
                        <a :href="item.group_src" target="_blank" class="group-link" :title="item.group_name">
                            <img width="30px;" :src="item.group_photo_50" alt="">&nbsp;&nbsp;{{ item.group_screen_name }}
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</template>

<script>
    import Vue from 'vue';
    // Import component
    //import Loading from 'vue-loading-overlay';
    // Import stylesheet
    //import 'vue-loading-overlay/dist/vue-loading.css';
    // Init plugin
    //Vue.use(Loading);


    export default {
        name: "Currencies",
        props: ['route_parse', 'user'],
        data: function () {
            return {
                groups: [
                    {name: 'obmenvalut_donetsk'},
                    {name: 'obmen_valut_donetsk'},
                    {name: 'donfinance'},
                    {name: 'donetsk'},
                    {name: 'club156050748'},
                    {name: 'moneydonetsk'},
                    {name: 'obmenvalyut_dpr'},
                    {name: 'donetsk_obmen_valyuta'},
                    {name: 'kursvalut_donetsk'}
                ],
                errors: [],
                error_keywords_buy: false,
                error_keywords_sale: false,
                parsing_start: false,
                //keywords_buy: 'куплю бн, куплю б.н, куплю безнал, куплю б/н, куплю б\\н, куплю приват, куплю гривну, куплю безналичную, куплю грн',
                keywords_buy: 'куплю бн',
                keywords_sale: 'продам бн, продам б.н, продам безнал, продам б/н, продам б\\н продам приват, продам гривну, продам безналичную, продам грн',
                radio_keywords: 'buy',
                days: 0,
                fullPage: false,
                posts: []
            };
        },
        computed: {
            channel() {
                return window.Echo.private('room.' + this.user.id);
            }
        },
        mounted() {
            this.channel
                .listen('SendPostToPusherWithQueue', (response) => {
                    console.log(1);
                    this.posts.push(response.post)
                });
        },
        methods: {
            start() {
                // let loader = this.$loading.show({
                //     // Optional parameters
                //     container: this.fullPage ? null : this.$refs.formContainer,
                //     loader: 'bars',
                //     color: '#4db24d',
                //     width: 128,
                //     height: 128,
                //     backgroundColor: '#b6b3ff'
                // });

                let obj = {};

                obj['keywords'] = (this.radio_keywords === 'buy') ? this.keywords_buy : this.keywords_sale;
                obj['groups'] = this.groups;
                obj['days'] = this.days;

                axios.post(this.route_parse, obj).then((response) => {
                    console.log(response.data.error);
                    // setTimeout(() => loader.hide(), 1200);
                    this.errors = [];
                }).catch(error => {
                    //loader.hide();
                    //console.log(error.response.data.response_error);
                    //console.log(error.response.data.message);
                    if (typeof error.response.data.response_error !== 'undefined') {
                        alert(error.response.data.response_error);
                    } else {
                        this.errors = error.response.data.errors;
                        if (typeof this.errors.keywords !== 'undefined') {
                            if (this.radio_keywords === 'buy')  {
                                this.error_keywords_buy = true;
                                this.error_keywords_sale = false;
                            } else {
                                this.error_keywords_sale = true;
                                this.error_keywords_buy = false;
                            }
                        }

                        //console.log(1);
                    }
                    //console.log(error.response);

                }).then(() => {});
            },
            addGroup: function () {
                this.groups.push({name: ''});
            },
            deleteUser: function (index) {
                if (this.groups.length !== 1) {
                    this.groups.splice(index, 1);
                }
            },
            removePosts: function (index) {
                this.posts.splice(index, 1);
            }
        }
    }
</script>

<style lang="scss" scoped>
    .groups-block {
        .left {
            margin-bottom: 20px;

            .main-label {
                margin-top: 10px;
            }
        }

        .table-responsive {
            margin-top: 25px;
        }

        .alert-block {
            padding: 0.4rem 0.8rem;
            p {
                margin-bottom: 0;
            }
        }

        input[type='number'] {
            margin-top: 7px;
        }
        input[name='keywords_buy'], input[name='days'] {
            margin-top: 0;
        }

        button.btn:focus {
            box-shadow: none;
        }

        .input-days {
        }

        .left input[type='text'], .left .input-days {
            width: calc(100% - 17px);
            //@include media-breakpoint-up(xs) {
            //    width: calc(100% - 17px);
            //}
            //
            //@include media-breakpoint-up(sm) {
            //    width: 92%;
            //}
            //@include media-breakpoint-up(md) {
            //    width: 93%;
            //}
            //@include media-breakpoint-up(lg) {
            //    width: 95%;
            //}
        }
        .input-block {
            position: relative;
            //border: 1px solid red;
            //display: flex;
            //flex-wrap: wrap;
            margin-bottom: 18px;

            button {
                position: absolute;
                top: 0;
                right: -20px;
                //display: none;
                //position: absolute;
                //float: right;
                //border: 1px solid #000;
                //right: -39px;
                //top: 0;
                //width: 10%;
                //flex-grow: 1;
            }

            input[type='text'] {
            }
        }

        .btn-add-group {
            //margin-top: 10px;
        }

        .form-control {
            background-image: none;
            //padding: 0.375rem 0.75rem;
        }

        .keywords-block {
            //border: 1px solid red;
            margin-bottom: 18px;
            //margin-left: -10px;
            position: relative;
            label {
                @include media-breakpoint-down(xs) {
                    margin-left: -20px !important;
                }
                //border: 1px solid #000;
                //position: relative;
                //width: 40px;
                margin: 0 !important;
                padding: 0.375rem 0.75rem;
                input[type='radio'] {
                    //position: absolute;
                    //top: 0;
                    //left: 0;
                    //right: 0;
                    //bottom: 0;
                    //margin: auto;
                    &:hover {
                        cursor: pointer;
                    }
                }
                &:hover {
                    cursor: pointer;
                }
            }

            input[type='text'] {
                position: absolute;
                top: 0;
                right: 0;
                width: calc(100% - 38px);
                @include media-breakpoint-down(xs) {
                    width: calc(100% - 19px);
                }
            }

            .invalid-keywords {
                margin-left: 38px;
                @include media-breakpoint-down(xs) {
                    margin-left: 18px;
                }
            }
        }

        .button-block {
            //border: 1px solid black;
            //margin-top: 20px;
            display: flex;
            justify-content: space-around;
            //flex-wrap: nowrap;

            .money-img {
                max-width: 70px;
                //max-height: 35px;

                @include media-breakpoint-down(xs) {
                    max-width: 50px;
                }

                @include media-breakpoint-down(md) {
                    max-width: 50px;
                }
                @include media-breakpoint-down(sm) {
                    max-width: 40px;
                }
            }

            button {
                height: 50%;
                width: 50%;
                align-self: center;
                @include media-breakpoint-down(md) {
                    width: 40%;
                }
                @include media-breakpoint-down(sm) {
                    width: 50%;
                }
            }
        }


    }

    .user-link:hover, .group-link:hover {
        text-decoration: none;
    }
</style>

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
                <button @click="start" class="btn btn-danger">Start parse</button>
                <img class="money-img" src="/assets/images/money-bag.svg" title="" alt="">
            </div>
        </div>
        <div class="tbl">
            <table v-if="posts.length">
<!--            <table>-->
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Author</th>
                        <th>Post</th>
                        <th>Vk group</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item) in posts">
                        <td data-title="Date">{{ item.date }}</td>
                        <td data-title="Author">
                            <a :href="item.user_src" target="_blank">
                                <img v-if="item.photo_50 === 'https://vk.com/images/camera_50.png?ava=1' "
                                    src="/assets/images/camera_50.png" alt="">
                                <img v-else :src="item.photo_50" alt="">

                                <span :title="item.first_name + ' ' + item.last_name">
                                    {{ item.first_name }}&nbsp;&nbsp;{{ item.last_name }}
                                </span>
                            </a>
                        </td>
                        <td data-title="Post">
                            {{ item.text }}
                        </td>
                        <td data-title="Vk group">
                            <a :href="item.group_src" target="_blank" class="group-link" :title="item.group_name">
                                <img :src="item.group_photo_50" alt="">
                                <span>{{ item.group_screen_name }}</span>
                            </a>
                        </td>
                    </tr>
<!--                    <tr>-->
<!--                        <td data-title="Date">08.09.20 17:09:08</td>-->
<!--                        <td data-title="Author">-->
<!--                            <a href="https://vk.com/id462314657" target="_blank">-->
<!--                                <img src="https://sun9-67.userapi.com/impg/c206724/v206724086/f9b9e/mLfb0OfLGvU.jpg?size=50x0&amp;quality=88&amp;crop=0,0,756,756&amp;sign=0c1ee710a2b864ee36d307460f40b3e7&amp;c_uniq_tag=hZdUgJYMA72-KgtCSY33CpczEnMyNZhDfZ9541yQz80&amp;ava=1" alt="">-->
<!--                                <span>Максим Донецкий</span>-->
<!--                            </a>-->
<!--                        </td>-->
<!--                        <td data-title="Post">Продам БН приват 24т по 2.65 , куплю безнал ошад приват, куплю 4 тыс синего дол по 75 Макеевка 0713933138</td>-->
<!--                        <td data-title="Vk group">-->
<!--                            <a data-v-4109c2ca="" href="https://vk.com/obmenvalut_donetsk" target="_blank" title="Курс валют обмен в Донецке! [Обменные пункты]">-->
<!--                                <img data-v-4109c2ca="" src="https://sun9-62.userapi.com/impf/c630317/v630317746/194b/BKf7CyuO0Zg.jpg?size=50x0&amp;quality=88&amp;crop=57,36,350,350&amp;sign=a99ce4c7e62409cc124542a08bf7a3a8&amp;c_uniq_tag=NmI9US0nOyYduujD2BtzBs_9nktQj-JrI2vA1MeLouE&amp;ava=1" alt="">-->
<!--                                <span>obmenvalut_donetsk</span>-->
<!--                            </a>-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                    <tr>-->
<!--                        <td data-title="Date">08.09.20 17:09:08</td>-->
<!--                        <td data-title="Author">-->
<!--                            <a href="https://vk.com/id462314657" target="_blank">-->
<!--                                <img src="https://sun9-67.userapi.com/impg/c206724/v206724086/f9b9e/mLfb0OfLGvU.jpg?size=50x0&amp;quality=88&amp;crop=0,0,756,756&amp;sign=0c1ee710a2b864ee36d307460f40b3e7&amp;c_uniq_tag=hZdUgJYMA72-KgtCSY33CpczEnMyNZhDfZ9541yQz80&amp;ava=1" alt="">-->
<!--                                <span>Максим Донецкий Максим Донецкий Максим Донецкий</span>-->
<!--                            </a>-->
<!--                        </td>-->
<!--                        <td data-title="Post">Продам БН приват 24т по 2.65 , куплю безнал ошад приват, куплю 4 тыс синего дол по 75 Макеевка 0713933138</td>-->
<!--                        <td data-title="Vk group">-->
<!--                            <a data-v-4109c2ca="" href="https://vk.com/obmenvalut_donetsk" target="_blank" title="Курс валют обмен в Донецке! [Обменные пункты]">-->
<!--                                <img data-v-4109c2ca="" src="https://sun9-62.userapi.com/impf/c630317/v630317746/194b/BKf7CyuO0Zg.jpg?size=50x0&amp;quality=88&amp;crop=57,36,350,350&amp;sign=a99ce4c7e62409cc124542a08bf7a3a8&amp;c_uniq_tag=NmI9US0nOyYduujD2BtzBs_9nktQj-JrI2vA1MeLouE&amp;ava=1" alt="">-->
<!--                                <span>obmenvalut_donetsk_obmenvalut_donetsk</span>-->
<!--                            </a>-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                    <tr>-->
<!--                        <td data-title="Date">08.09.20 17:09:08</td>-->
<!--                        <td data-title="Author">-->
<!--                            <a href="https://vk.com/id462314657" target="_blank">-->
<!--                                <img src="https://sun9-67.userapi.com/impg/c206724/v206724086/f9b9e/mLfb0OfLGvU.jpg?size=50x0&amp;quality=88&amp;crop=0,0,756,756&amp;sign=0c1ee710a2b864ee36d307460f40b3e7&amp;c_uniq_tag=hZdUgJYMA72-KgtCSY33CpczEnMyNZhDfZ9541yQz80&amp;ava=1" alt="">-->
<!--                                <span>Максим Донецкий</span>-->
<!--                            </a>-->
<!--                        </td>-->
<!--                        <td data-title="Post">Продам БН приват 24т по 2.65 , куплю безнал ошад приват, куплю 4 тыс синего дол по 75 Макеевка 0713933138</td>-->
<!--                        <td data-title="Vk group">-->
<!--                            <a data-v-4109c2ca="" href="https://vk.com/obmenvalut_donetsk" target="_blank" title="Курс валют обмен в Донецке! [Обменные пункты]">-->
<!--                                <img data-v-4109c2ca="" src="https://sun9-62.userapi.com/impf/c630317/v630317746/194b/BKf7CyuO0Zg.jpg?size=50x0&amp;quality=88&amp;crop=57,36,350,350&amp;sign=a99ce4c7e62409cc124542a08bf7a3a8&amp;c_uniq_tag=NmI9US0nOyYduujD2BtzBs_9nktQj-JrI2vA1MeLouE&amp;ava=1" alt="">-->
<!--                                <span>obmenvalut_donetsk</span>-->
<!--                            </a>-->
<!--                        </td>-->
<!--                    </tr>-->
                </tbody>
            </table>
        </div>
    </div>

</template>

<script>
    // Import component
    import Loading from 'vue-loading-overlay';
    // Import stylesheet
    import 'vue-loading-overlay/dist/vue-loading.css';
    // Init plugin
    Vue.use(Loading);


    export default {
        name: "Currencies",
        props: ['route_parse', 'user'],
        data: function () {
            return {
                groups: [
                    {name: 'obmenvalut_donetsk'},
                    {name: 'obmen_valut_donetsk'},
                    {name: 'donfinance'},
                    // {name: 'donetsk'},
                    // {name: 'club156050748'},
                    // {name: 'moneydonetsk'},
                    // {name: 'obmenvalyut_dpr'},
                    // {name: 'donetsk_obmen_valyuta'},
                    // {name: 'kursvalut_donetsk'}
                ],
                errors: [],
                error_keywords_buy: false,
                error_keywords_sale: false,
                parsing_start: false,
                keywords_buy: 'куплю бн, куплю б.н, куплю безнал, куплю б/н, куплю б\\н, куплю приват, куплю гривну, куплю безналичную, куплю грн',
                // keywords_buy: 'куплю бн',
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
                let loader = this.$loading.show({
                    // Optional parameters
                    container: this.fullPage ? null : this.$refs.formContainer,
                    loader: 'bars',
                    color: '#4db24d',
                    width: 128,
                    height: 128,
                    backgroundColor: '#b6b3ff'
                });

                let obj = {};

                obj['keywords'] = (this.radio_keywords === 'buy') ? this.keywords_buy : this.keywords_sale;
                obj['groups'] = this.groups;
                obj['days'] = this.days;

                axios.post(this.route_parse, obj).then((response) => {
                    // console.log(response.data.error);
                    setTimeout(() => loader.hide(), 1200);
                    this.errors = [];
                }).catch(error => {
                    loader.hide();
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
                    }
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
            .main-label {
                margin-top: 10px;
            }
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

        .left input[type='text'], .left .input-days {
            width: calc(100% - 17px);
        }
        .input-block {
            position: relative;
            margin-bottom: 18px;

            button {
                position: absolute;
                top: 0;
                right: -20px;
            }

            input[type='text'] {
            }
        }

        .form-control {
            background-image: none;
        }

        .keywords-block {
            margin-bottom: 18px;
            position: relative;
            label {
                @include media-breakpoint-down(xs) {
                    margin-left: -20px !important;
                }
                margin: 0 !important;
                padding: 0.375rem 0.75rem;
                input[type='radio'] {
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
            display: flex;
            justify-content: space-around;

            @include media-breakpoint-down(sm) {
                justify-content: flex-end;
            }

            .money-img {
                max-width: 70px;

                @include media-breakpoint-down(xs) {
                    max-width: 50px;
                }

                @include media-breakpoint-down(md) {
                    max-width: 50px;
                }
                @include media-breakpoint-down(sm) {
                    display: none;
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
                    width: calc(100% - 38px);
                }

                @include media-breakpoint-down(xs) {
                    width: calc(100% - 18px);
                }
            }
        }


        .tbl {
            margin-top: 20px;
            margin-bottom: 0;
            table {
                border-left: 1px solid #dee2e6;
                border-right: 1px solid #dee2e6;
                border-bottom: 1px solid #dee2e6;
                border-collapse: separate;
                border-spacing: 0;
                border-radius: 4px;

                th, td {
                    padding: 4px 5px;
                    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                    font-size: 13px;
                    line-height: 18px;
                    border-top: 1px solid #dee2e6;
                }

                th {
                    text-align: center;
                }

                tbody tr td:first-child {
                    text-align: center;
                }

                tbody tr:nth-child(odd) td, tbody tr:nth-child(odd) th {
                    background-color: #f2f2f2;
                }
                thead tr:first-child th:first-child,
                tbody tr:first-child td:first-child {
                }

                th+th, td+td, th+td, td+th {
                    border-left: 1px solid #dee2e6;
                }

                tbody tr td:nth-child(2), tbody tr td:last-child {
                    a {
                        display: flex;
                        align-items: center;
                        min-width: 170px;
                        max-width: 170px;
                        color: #2a5885;
                        &:hover {
                            text-decoration: none;
                        }
                        img {
                            outline: none;
                            border-radius: 50%;
                            max-width: 30px;
                            margin-right: 3px;
                        }
                        span {
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                            margin-left: 3px;
                            &:hover {
                                text-decoration: underline;
                            }
                        }
                    }
                }
            }

            @include media-breakpoint-down(sm) {
                margin-left: -6px;
                margin-right: -6px;
                width: auto;
                margin-bottom: -21px;

                table, thead, tbody, th, td, tr {
                    display: block;
                }

                table {
                    border: none;

                    td + td {
                        border-left: none;
                    }

                    thead tr {
                        position: absolute;
                        top: -9999px;
                        left: -9999px;
                    }

                    tbody tr {
                        position: relative;
                        border: 1px solid #dee2e6;
                        background-color: #f2f2f2;
                        margin-bottom: 15px;

                        &:last-child {
                            margin-bottom: 0;
                            border-radius: 0 0 4px 4px;
                        }
                    }

                    tbody tr td:first-child {
                        padding: 0;
                        position: absolute;
                        z-index: 1;
                        border: none;
                        left: 75px;
                        top: 33px;
                        color: rgb(129, 140, 153);
                        font-size: 13px;

                        @include media-breakpoint-down(xs) {
                            left: 53px;
                            top: 28px;
                        }
                    }

                    tbody tr td:last-child {
                        //border: 1px solid black;
                        width: 33%;
                        position: absolute;
                        right: 5px;
                        top: 5px;
                        padding: 0;

                        @include media-breakpoint-down(xs) {
                            width: calc(45% - 60px);
                        }

                        a {
                            img {
                                @include media-breakpoint-down(xs) {
                                    max-width: 23px;
                                }
                            }
                            //border: 1px solid red;
                            min-width: auto !important;
                            max-width: none !important;
                        }
                    }

                    tbody tr td:nth-child(2) a {
                        display: block;

                        img {
                            max-width: 48px;

                            @include media-breakpoint-down(xs) {
                                max-width: 32px;
                            }
                        }

                        span {
                            position: absolute;
                            left: 72px;
                            top: 12px;
                            color: #2C2D2E;
                            font-size: 15px;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                            max-width: 160px;

                            @include media-breakpoint-down(xs) {
                                left: 51px;
                                top: 9px;
                                font-size: 14px;
                                max-width: 145px;
                            }
                        }
                    }

                    td {
                        padding-top: 6px;
                        padding-left: 16px;
                        border: none;
                        position: relative;
                        white-space: normal;
                        text-align: left !important;

                        @include media-breakpoint-down(xs) {
                            padding-left: 10px;
                        }
                    }
                }
            }
        }
    }

    .user-link:hover, .group-link:hover {
        text-decoration: none;
    }
</style>

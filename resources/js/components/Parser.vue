<template>
    <div class="row groups-block">
        <div class="col-sm-4">

            <label for="vk_group" class="col-form-label main-label">Vk group name:</label>
            <div v-for="(group, index) in groups" class="input-block">
                <input type="text" v-model="group.name" name="groups[]"  :class="[errors['groups.' + index + '.name'] ? 'is-invalid' : '', 'form-control col-8']" id="vk_group" v-bind:tabindex="index+1"
                       :disabled="parsing_start ? true : false">
                <button @click="deleteUser(index)" class="btn">
                    <i class="fa fa-remove" aria-hidden="true"></i>
                </button>
                <div v-for="(error) in errors['groups.' + index + '.name']" class="invalid-feedback">{{ error }}</div>
            </div>
            <button @click="addGroup" class="btn btn-success btn-sm btn-add-group" :disabled="parsing_start ? true : false">
                Add Group
            </button>

        </div>
        <div class="col-sm-4">

            <label for="keywords_buy" class="col-form-label">Keywords (comma separated):</label>

            <div class="keywords-block">
                <input type="radio" name="radio_keywords" v-model="radio_keywords" value="buy">
                <input :class="[error_keywords_buy ? 'is-invalid' : '', 'form-control col-11']" type="text" id="keywords_buy" v-model="keywords_buy" name="keywords_buy">
                <div v-for="(error) in errors.keywords" class="invalid-keywords invalid-feedback">{{ error }}</div>
            </div>

            <div class="keywords-block">
                <input type="radio" name="radio_keywords" v-model="radio_keywords" value="sale">
                <input :class="[error_keywords_sale ? 'is-invalid' : '', 'form-control col-11']" type="text" id="keywords_sale" v-model="keywords_sale" name="keywords_sale">
                <div v-for="(error) in errors.keywords" class="invalid-keywords invalid-feedback">{{ error }}</div>
            </div>

            <label for="days" class="col-form-label days">Count days (0 - current day, 1 - yesterday, etc):</label>
            <input id="days" v-model="days" type="number" min="0" max="1000" :class="[errors.days ? 'is-invalid' : '', 'form-control col-11']" name="days">
            <div v-for="(error) in errors.days" class="invalid-feedback">{{ error }}</div>


        </div>
        <div class="col-sm-4 right-block">
            <img class="money-img" src="/assets/images/money-bag.svg" title="">
            <button @click="start" class="btn btn-danger">Start parse</button>
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
                    {name: 'donetsk'},
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
                    setTimeout(() => loader.hide(), 1200);
                    this.errors = [];
                }).catch(error => {
                    loader.hide();
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
        .table-responsive {
            margin-top: 25px;
        }

        padding-bottom: 15px;
        .alert-block {
            padding: 0.4rem 0.8rem;
            p {
                margin-bottom: 0;
            }
        }
        label {
            padding: 0 !important;
            margin-bottom: 7px;
        }
        .days {
            margin-top: 22px;
        }

        input[type='number'] {
            margin-top: 7px;
        }
        input[name='keywords_buy'], input[name='days'] {
            margin-top: 0;
        }

        input[name='keywords_sale'] {
            margin-top: 7px;
        }

        button.btn:focus {
            box-shadow: none;
        }

        .input-block {
            input[type='text'] {
                margin-bottom: 7px;
            }

            display: flex;
            flex-wrap: wrap;
            button {
                flex-basis: 10%;
                flex-grow: 0;
            }
            input[type='text'] {
                flex-grow: 1;
                flex-basis: 80%;

            }
            .invalid-feedback {
                flex-grow: 1;
            }
            .form-control.is-invalid {

            }
            /*> * {*/
                /*flex-grow: 1;*/
            /*}*/

        }

        .btn-add-group {
            margin-top: 10px;
        }

        .form-control {
            background-image: none;
            padding: 0.375rem 0.75rem;
        }

        .keywords-block {
            display: flex;
            flex-wrap: wrap;
            align-items: center;

            input[type='radio'] {
                margin-right: 10px;
                &:hover {
                    cursor: pointer;
                }
            }
            .invalid-keywords {
                margin-left: 23px;
            }
        }
    }

    .right-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        .money-img {
            display: block;
            /*flex-basis: 50%;*/
            max-width: 80px;
        }

        button {
            /*flex-grow: 0;*/
            margin-top: 40px;
            width: 200px;
        }
    }

    .user-link:hover, .group-link:hover {
        text-decoration: none;
    }

</style>
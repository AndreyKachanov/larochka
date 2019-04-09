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

            <label for="keywords" class="col-form-label">Keywords (comma separated):</label>
            <input :class="[errors.keywords ? 'is-invalid' : '', 'form-control col-8']" type="text" id="keywords" v-model="keywords" name="keywords">
            <div v-for="(error) in errors.keywords" class="invalid-feedback">{{ error }}</div>

            <label for="days" class="col-form-label days">Count days:</label>
            <input id="days" v-model="days" type="number" min="1" max="1000" :class="[errors.days ? 'is-invalid' : '', 'form-control col-8']" name="days">
            <div v-for="(error) in errors.days" class="invalid-feedback">{{ error }}</div>


        </div>
        <div class="col-sm-4 right-block">
            <img class="money-img" src="/assets/images/money-bag.svg" title="">
            <!--<img class="money-img" src="/assets/images/wallet.svg" title="">-->
            <button @click="start" class="btn btn-danger">Start parse</button>
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
        props: ['route_parse'],
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
                    {name: 'kursvalut_donetsk'},
                ],
                errors: [],
                parsing_start: false,
                keywords: 'куплю безнал',
                days: 1,
                fullPage: false,
            };
        },
        methods: {
            start() {
                axios.post(this.route_parse, {
                    'groups': this.groups,
                    'keywords': this.keywords,
                    'days': this.days
                }).then((response) => {
                    loader.hide();
                    this.errors = [];
                }).catch(error => {
                    this.errors = error.response.data.errors;
                    // console.log(this.errors);
                });

                let loader = this.$loading.show();
            },
            addGroup: function () {
                this.groups.push({name: ''});
            },
            deleteUser: function (index) {
                if (this.groups.length !== 1) {
                    this.groups.splice(index, 1);
                }
                // if (index === 0)
                //     this.addGroup()
            }
        }
    }
</script>

<style lang="scss" scoped>

    .groups-block {
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
        input[name='keywords'], input[name='days'] {
            margin-top: 0;
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

</style>
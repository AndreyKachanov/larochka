<template>
    <div class="row groups-block">

        <!--<div v-if="errors.length" class="alert alert-danger alert-block col-12">-->
            <!--<p class="text-sm-center" v-for="error in errors">{{ error }}</p>-->
        <!--</div>-->

        <div v-if="feedback" class="alert alert-danger alert-block col-12">
            <p  class="text-sm-center" v-text="feedback"></p>
        </div>

        <div v-if="parsing_start" class="alert alert-success alert-block col-12">
            <p class="text-sm-center">Parsing start!</p>
        </div>
        <div class="col-12 empty-col"></div>
        <div class="col-12">
            <label for="vk_group" class="col-form-label main-label col-4">Vk group name:</label>
            <label for="keywords" class="col-form-label col-5">Keywords. Words or numbers (comma separated):</label>
        </div>

        <div class="col-sm-4">
            <div v-for="(group, index) in groups" class="input-block">
                <input type="text" v-model="group.name" name="groups[]" class="form-control" id="vk_group" v-bind:tabindex="index+1"
                       :disabled="parsing_start ? true : false">
                <button @click="deleteUser(index)" class="btn">
                    <i class="fa fa-remove" aria-hidden="true"></i>
                </button>
            </div>
            <button @click="addUser" class="btn btn-success btn-sm" :disabled="parsing_start ? true : false">
                Add Group
            </button>
        </div>
        <div class="col-sm-4">
            <input class="form-control" type="text" id="keywords" v-model="keywords" name="keywords">
        </div>
        <div class="col-sm-4">
            <button @click="start" class="btn btn-primary">Start parsing</button>
            <button @click="stop" class="btn btn-primary">Stop</button>
        </div>

    </div>
</template>

<script>
    export default {
        name: "Currencies",
        props: ['route_start_parse', 'route_stop_parse'],
        data: function () {
            return {
                groups: [{name: 'obmenvalut_donetsk'}, {name: 'obmen_valut_donetsk'}, {name: 'donfinance'}, {name: 'donetsk'}],
                errors: [],
                error_msg: 'The vk group name and keywords is required.',
                parsing_start: false,
                keywords: 'куплю безнал, куплю бн, безнал, куплю',
                feedback: "",
            };
        },
        methods: {
            start() {
                axios.post(this.route_start_parse, {
                    'groups': this.groups,
                    'keywords': this.keywords
                }).then((response) => {
                    // console.log(response);
                }).catch(error => {
                    this.feedback = error.response.data.errors;
                });

                // let items = [];
                // items = this.groups.map(
                //     function (group) {
                //         return group.name;
                //     }
                // );
                //
                // if (this.keywords === '') {
                //     this.errors.push(this.error_msg);
                //     return false;
                // }
                //
                // if (items.indexOf('') !== -1) {
                //     if (this.errors.indexOf(this.error_msg)) {
                //         this.errors.push(this.error_msg);
                //         return false;
                //     }
                //
                //
                // } else {
                //     this.parsing_start = true;
                //     // console.log(this.keywords);
                //     axios.post(this.route_start_parse, {
                //         'groups': this.groups,
                //         'keywords': this.keywords
                //     }).then((response) => {
                //         // console.log(response.data);
                //     }).catch(function (error) {
                //         console.log(error);
                //     });
                // }
            },
            stop() {
                axios.post(this.route_stop_parse).then((response) => {
                    console.log(response.data);
                // }).catch(function (error) {
                });

            },
            addUser: function () {
                this.groups.push({name: ''});
            },
            deleteUser: function (index) {
                // console.log(index);
                this.groups.splice(index, 1);
                if (index === 0)
                    this.addUser()
            }
        }
    }
</script>

<style lang="scss" scoped>

    .groups-block {
        .alert-block {
            padding: 0.4rem 0.8rem;
            /*position: absolute;*/
            /*width: 99%;*/
            /*top: 57px;*/
            p {
                margin-bottom: 0;
            }
        }
        .empty-col {
            margin-top: 35px;
        }
        .main-label {
            padding: 0;
            margin: 7px 0;
        }

        input[type='text'] {
            margin-bottom: 7px;
        }

        button.btn:focus {
            box-shadow: none;
        }

        .input-block {
            display: flex;
            button {
                margin-bottom: 7px;
            }
        }
    }

</style>
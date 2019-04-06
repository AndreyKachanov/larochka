<template>
    <div class="row groups-block">

        <div v-if="errors.length" class="alert alert-danger alert-block col-12">
            <p class="text-sm-center" v-for="error in errors">{{ error }}</p>
        </div>

        <div v-if="parsing_start" class="alert alert-success alert-block col-12">
            <p class="text-sm-center">Parsing start!</p>
        </div>
        <div class="col-12 empty-col"></div>
        <div class="col-12">
            <label for="vk_group" class="col-form-label main-label col-4">Vk group name:</label>
            <label for="keywords" class="col-form-label col-5">Keywords (words or numbers, comma separated):</label>
        </div>

        <div class="col-sm-4">
            <div v-for="(group, index) in groups" class="input-block">
                <input type="text" v-model="group.name" name="groups[]"  :class="[  errors['groups.' + index + '.name'] ? 'is-invalid' : '', 'form-control' ]" id="vk_group" v-bind:tabindex="index+1"
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
            <input :class="[ errors.keywords ? 'is-invalid' : '', 'form-control' ]" type="text" id="keywords" v-model="keywords" name="keywords">
            <div v-for="(error) in errors.keywords" class="invalid-feedback">{{ error }}</div>
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
                parsing_start: false,
                keywords: 'куплю безнал',
            };
        },
        methods: {
            start() {
                axios.post(this.route_start_parse, {
                    'groups': this.groups,
                    'keywords': this.keywords
                }).then((response) => {
                    this.errors = [];
                }).catch(error => {
                    this.errors = error.response.data.errors;
                    // console.log(this.errors);
                });
            },
            stop() {
                axios.post(this.route_stop_parse).then((response) => {
                    console.log(response.data);
                });

            },
            addGroup: function () {
                this.groups.push({name: ''});
            },
            deleteUser: function (index) {
                this.groups.splice(index, 1);
                // if (index === 0)
                //     this.addGroup()
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
            margin-top: 7px;
        }

        button.btn:focus {
            box-shadow: none;
        }

        .input-block {
            display: flex;
            flex-wrap: wrap;
            /*justify-content: center;*/
            /*flex-direction: column;*/
            button {
                flex-basis: 15%;
                flex-grow: 0;
                /*margin-bottom: 7px;*/
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

</style>
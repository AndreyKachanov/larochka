<template>
    <div class="row groups-block">
        <div v-if="errors.length" class="col-12 error">
            <p class="text-sm-center" v-for="error in errors">{{ error }}</p>
        </div>
        <div class="col-sm-7">
            <label for="vk_group" class="col-form-label col-12 main-label">Vk group name:</label>
            <div v-for="(user, index) in users" style="display: flex">
                <input type="text" v-model="user.name" class="form-control col-5" id="vk_group" required>
                <button @click="deleteUser(index)" class="btn">
                    <i class="fa fa-remove" aria-hidden="true"></i>
                </button>
            </div>

            <button @click="addUser" class="btn btn-success btn-sm">
                Add Group
            </button>

        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <button @click="run" class="btn btn-primary">Search</button>
            </div>
        </div>
        <!--<table class="table table-striped table-messages">-->
            <!--<thead>-->
            <!--<tr class="d-flex">-->
                <!--<th class="col-1">Date</th>-->
                <!--<th class="col-2">Post</th>-->
                <!--<th class="col-2">Author</th>-->
            <!--</tr>-->
            <!--</thead>-->
            <!--<tbody>-->

            <!--</tbody>-->
        <!--</table>-->
        <!--<pre>{{ $data }}</pre>-->
    </div>
</template>

<script>
    export default {
        name: "Currencies",
        data: function () {
            return {
                users: [{name: 'obmenvalut_donetsk'}],
                errors: [],
                error_msg: 'The vk group name is required.',
            };
        },
        methods: {
            run() {
                let items = [];
                items = this.users.map(
                    function (user) {
                        return user.name;
                    }
                );

                if (items.indexOf('') !== -1) {
                    if (this.errors.indexOf(this.error_msg)) {
                        this.errors.push(this.error_msg);
                        return false;
                    }
                } else {
                    axios.post('/cabinet/run_currencies', this.users).then((response) => {
                        // console.log(response.data);
                    }).catch(function (error) {
                        console.log(error);
                    });
                }
            },
            addUser: function () {
                this.users.push({name: ''});
            },
            deleteUser: function (index) {
                console.log(index);
                this.users.splice(index, 1);
                if (index === 0)
                    this.addUser()
            }
        }
    }
</script>

<style lang="scss" scoped>

    .groups-block {
        padding-top: 40px;
        /*min-height: 50vh;*/
        .error {
            position: absolute;
            top: 66px;
            p {
                color: red;
            }
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
    }

</style>
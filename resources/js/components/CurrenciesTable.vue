<template>
    <div class="table-responsive">
        <table v-if="posts" class="table table-striped table-messages">
            <thead>
            <tr class="d-flex">
                <th class="col-2">Date</th>
                <th class="col-2">Author</th>
                <th class="col-8" style="text-align:center;">Post</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item) in posts" class="d-flex">
                <td class="col-2">{{ item.date }}</td>
                <td class="col-2">{{ item.from_id }}</td>
                <td class="col-8">{{ item.text }}</td>
            </tr>
            </tbody>
        </table>
        <!--<pre>{{ $data }}</pre>-->
    </div>

</template>

<script>
    export default {
        name: "CurrenciesTable.vue",
        data: function () {
            return {
                posts: []
            }
        },
        mounted() {
            window.Echo.private('test')
                .listen('PrivateTest', (response) => {
                    this.posts.push(response.post)
                });
        }
    }
</script>

<style scoped>

</style>
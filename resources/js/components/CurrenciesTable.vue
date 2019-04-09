<template>
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

</template>

<script>
    export default {
        name: "CurrenciesTable.vue",
        props: ['user'],
        data: function () {
            return {
                posts: []
            }
        },
        computed: {
            channel() {
                return window.Echo.private('room.' + this.user.id);
            }
        },
        mounted() {
            this.channel
                .listen('SendPostToPusherWithoutQueue', (response) => {
                    this.posts.push(response.post)
                });
        }
    }
</script>

<style scoped>
    .user-link:hover, .group-link:hover {
        text-decoration: none;
    }
</style>
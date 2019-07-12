<template>
    <div class="row groups-block">
    </div>

</template>

<script>

    import Autocomplete from '@trevoreyre/autocomplete-vue'
    import '@trevoreyre/autocomplete-vue/dist/style.css'

    Vue.use(Autocomplete)


    export default {
        el: '#app',
        components: {
            Autocomplete
        },
        methods: {
            search(input) {
                if (input.length < 1) { return [] }
                return countries.filter(country => {
                    return country.toLowerCase()
                        .startsWith(input.toLowerCase())
                })
            }
        }
    }
</script>

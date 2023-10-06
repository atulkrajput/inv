<template>
    <div class="row">
        <div class="col-md-12">
            <form action="">
                <label for="search" class="search-label"> Filter Records</label>
                <input class="form-control" type="text" id="searchbox" :placeholder="placer" v-model="search" @keyup="searchIt" width="300px">
            </form>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['placer'],
        data() {
            return {
                search: '',
            }
        },
        methods: {
            searchIt() {
                this.$parent.asearch = this.search;
                Fire.$emit('searching');
            },
        },
        created() {

        }
    }
</script>
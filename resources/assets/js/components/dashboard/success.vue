<!-- Modal -->
<template lang="html">

    <div>
        <div v-show="in_progress" class="alert alert-info" >
            Synchronizing . . .
        </div>
        <div v-show="success_messages.length >= 1" v-for="msg in success_messages" :msg="msg" class="alert alert-success" role="alert">
            {{ msg }}
        </div>
    </div>
</template>


<script>
    export default {
        props: ['success_messages', 'in_progress']
    }
</script>

<style lang="css">
</style>
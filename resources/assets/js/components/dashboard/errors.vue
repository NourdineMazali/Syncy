<!-- Modal -->
<template lang="html">

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Errors</h4>
            </div>
            <div class="modal-body">
                <div v-for="error in errors" :error="error" class="alert alert-danger" role="alert"> {{ error.toString() }}  </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
</template>


<script>
    export default {
        props: ['errors']
    }
</script>

<style lang="css">
</style>